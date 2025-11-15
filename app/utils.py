import base64
import json
import hmac
import hashlib

from flask import jsonify, g, request


def build_api_response(payload, status_code=200):
    token = getattr(g, "current_token", None)
    encrypt_requested = request.headers.get("X-Encrypt-Response", "").lower() in {"1", "true", "yes"}

    if encrypt_requested and token is not None:
        raw = json.dumps(payload, default=str).encode("utf-8")
        encoded = base64.b64encode(raw).decode("utf-8")
        signature = hmac.new(token.secret_key.encode("utf-8"), raw, hashlib.sha256).hexdigest()
        response_payload = {
            "encrypted": True,
            "payload": encoded,
            "signature": signature,
            "algorithm": "HMAC-SHA256",
        }
        return jsonify(response_payload), status_code

    return jsonify(payload), status_code
