import hashlib
import secrets
from datetime import datetime, timedelta
from functools import wraps
from typing import Optional

from flask import request, jsonify, g, current_app

from app import db
from app.models import RemoteToken, TokenAuditLog


def hash_token(token: str) -> str:
    return hashlib.sha256(token.encode()).hexdigest()


def generate_token_pair() -> tuple[str, str]:
    raw_token = secrets.token_urlsafe(48)
    hashed = hash_token(raw_token)
    secret_key = secrets.token_urlsafe(32)
    return raw_token, hashed, secret_key


def get_client_ip() -> str:
    if request.headers.get("X-Forwarded-For"):
        return request.headers.get("X-Forwarded-For").split(",")[0].strip()
    return request.remote_addr or "unknown"


def get_user_agent() -> str:
    return request.headers.get("User-Agent", "")[:512]


def log_audit(
    token_id: Optional[int],
    provided_token_hash: str,
    success: bool,
    status_code: int,
    detail: str = "",
):
    audit = TokenAuditLog(
        token_id=token_id,
        provided_token_hash=provided_token_hash,
        method=request.method,
        path=request.path,
        ip_address=get_client_ip(),
        user_agent=get_user_agent(),
        success=success,
        status_code=status_code,
        detail=detail,
    )
    db.session.add(audit)
    db.session.commit()


def check_rate_limit(token: RemoteToken) -> bool:
    one_minute_ago = datetime.utcnow() - timedelta(minutes=1)
    recent_count = (
        TokenAuditLog.query.filter(
            TokenAuditLog.token_id == token.id,
            TokenAuditLog.created_at >= one_minute_ago,
            TokenAuditLog.success == True,
        )
        .count()
    )
    return recent_count < token.rate_limit_per_minute


def require_token(*required_scopes):
    def decorator(f):
        @wraps(f)
        def decorated_function(*args, **kwargs):
            auth_header = request.headers.get("Authorization")
            if not auth_header or not auth_header.startswith("Bearer "):
                log_audit(None, "", False, 401, "Missing or invalid Authorization header")
                return jsonify({"error": "Unauthorized", "message": "Missing or invalid Authorization header"}), 401

            raw_token = auth_header[7:]
            token_hash = hash_token(raw_token)

            token = RemoteToken.query.filter_by(token_hash=token_hash).first()

            if not token:
                log_audit(None, token_hash, False, 401, "Token not found")
                return jsonify({"error": "Unauthorized", "message": "Invalid token"}), 401

            if token.revoked:
                log_audit(token.id, token_hash, False, 403, "Token revoked")
                return jsonify({"error": "Forbidden", "message": "Token has been revoked"}), 403

            if not check_rate_limit(token):
                log_audit(token.id, token_hash, False, 429, "Rate limit exceeded")
                return jsonify({"error": "Too Many Requests", "message": "Rate limit exceeded"}), 429

            if required_scopes and not token.has_scopes(required_scopes):
                log_audit(token.id, token_hash, False, 403, f"Missing required scopes: {required_scopes}")
                return jsonify({"error": "Forbidden", "message": f"Missing required scopes: {required_scopes}"}), 403

            token.last_used_at = datetime.utcnow()
            db.session.commit()

            g.current_token = token

            result = f(*args, **kwargs)

            status_code = 200
            if hasattr(result, "status_code"):
                status_code = result.status_code
            elif isinstance(result, tuple):
                if len(result) >= 2 and isinstance(result[1], int):
                    status_code = result[1]

            log_audit(token.id, token_hash, True, status_code, "Success")

            return result

        return decorated_function

    return decorator
