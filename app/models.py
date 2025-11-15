import json
from datetime import datetime, date

from app import db


class RemoteToken(db.Model):
    __tablename__ = "remote_tokens"

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(120), nullable=False)
    token_hash = db.Column(db.String(128), nullable=False, unique=True, index=True)
    secret_key = db.Column(db.String(128), nullable=False)
    scopes = db.Column(db.Text, nullable=False)
    revoked = db.Column(db.Boolean, default=False, nullable=False)
    rate_limit_per_minute = db.Column(db.Integer, default=60, nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)
    last_used_at = db.Column(db.DateTime)
    created_by = db.Column(db.String(120))

    audit_logs = db.relationship("TokenAuditLog", backref="token", lazy=True)

    def scope_list(self) -> list:
        try:
            return json.loads(self.scopes or "[]")
        except json.JSONDecodeError:
            return []

    def has_scopes(self, required_scopes: list[str]) -> bool:
        token_scopes = set(self.scope_list())
        return all(scope in token_scopes for scope in required_scopes)

    def to_dict(self, include_secret=False) -> dict:
        payload = {
            "id": self.id,
            "name": self.name,
            "scopes": self.scope_list(),
            "revoked": self.revoked,
            "rate_limit_per_minute": self.rate_limit_per_minute,
            "created_at": self.created_at.isoformat(),
            "last_used_at": self.last_used_at.isoformat() if self.last_used_at else None,
            "created_by": self.created_by,
        }
        if include_secret:
            payload["secret_key"] = self.secret_key
        return payload


class TokenAuditLog(db.Model):
    __tablename__ = "token_audit_logs"

    id = db.Column(db.Integer, primary_key=True)
    token_id = db.Column(db.Integer, db.ForeignKey("remote_tokens.id"))
    provided_token_hash = db.Column(db.String(128))
    method = db.Column(db.String(10), nullable=False)
    path = db.Column(db.String(255), nullable=False)
    ip_address = db.Column(db.String(45))
    user_agent = db.Column(db.String(512))
    success = db.Column(db.Boolean, default=False, nullable=False)
    status_code = db.Column(db.Integer)
    detail = db.Column(db.String(255))
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "token_id": self.token_id,
            "provided_token_hash": self.provided_token_hash,
            "method": self.method,
            "path": self.path,
            "ip_address": self.ip_address,
            "user_agent": self.user_agent,
            "success": self.success,
            "status_code": self.status_code,
            "detail": self.detail,
            "created_at": self.created_at.isoformat(),
        }


class APIMetadata(db.Model):
    __tablename__ = "api_metadata"

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(200), nullable=False, index=True)
    description = db.Column(db.Text)
    category = db.Column(db.String(120))
    tags = db.Column(db.String(255))
    version = db.Column(db.String(50))
    endpoint = db.Column(db.String(255))
    status = db.Column(db.String(50), default="active")
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)

    rankings = db.relationship("APIRanking", backref="api", lazy=True)

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "name": self.name,
            "description": self.description,
            "category": self.category,
            "tags": self.tags.split(",") if self.tags else [],
            "version": self.version,
            "endpoint": self.endpoint,
            "status": self.status,
            "created_at": self.created_at.isoformat(),
        }


class FriendLink(db.Model):
    __tablename__ = "friend_links"

    id = db.Column(db.Integer, primary_key=True)
    name = db.Column(db.String(180), nullable=False)
    url = db.Column(db.String(512), nullable=False)
    description = db.Column(db.Text)
    status = db.Column(db.String(32), default="pending", nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)
    approved_at = db.Column(db.DateTime)

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "name": self.name,
            "url": self.url,
            "description": self.description,
            "status": self.status,
            "created_at": self.created_at.isoformat(),
            "approved_at": self.approved_at.isoformat() if self.approved_at else None,
        }


class APIRanking(db.Model):
    __tablename__ = "api_rankings"

    id = db.Column(db.Integer, primary_key=True)
    api_id = db.Column(db.Integer, db.ForeignKey("api_metadata.id"), nullable=False)
    calls_count = db.Column(db.Integer, default=0, nullable=False)
    unique_users = db.Column(db.Integer, default=0, nullable=False)
    period = db.Column(db.String(32), default="daily", nullable=False)
    recorded_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "api_id": self.api_id,
            "api_name": self.api.name if self.api else None,
            "calls_count": self.calls_count,
            "unique_users": self.unique_users,
            "period": self.period,
            "recorded_at": self.recorded_at.isoformat(),
        }


class FeedbackThread(db.Model):
    __tablename__ = "feedback_threads"

    id = db.Column(db.Integer, primary_key=True)
    subject = db.Column(db.String(200), nullable=False)
    status = db.Column(db.String(20), default="open", nullable=False)
    created_by = db.Column(db.String(200))
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)

    messages = db.relationship("FeedbackMessage", backref="thread", lazy=True, cascade="all, delete-orphan")

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "subject": self.subject,
            "status": self.status,
            "created_by": self.created_by,
            "created_at": self.created_at.isoformat(),
            "messages": [message.to_dict() for message in sorted(self.messages, key=lambda m: m.created_at)],
        }


class FeedbackMessage(db.Model):
    __tablename__ = "feedback_messages"

    id = db.Column(db.Integer, primary_key=True)
    thread_id = db.Column(db.Integer, db.ForeignKey("feedback_threads.id"), nullable=False)
    author = db.Column(db.String(200))
    body = db.Column(db.Text, nullable=False)
    is_admin = db.Column(db.Boolean, default=False, nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "thread_id": self.thread_id,
            "author": self.author,
            "body": self.body,
            "is_admin": self.is_admin,
            "created_at": self.created_at.isoformat(),
        }


class DailyStat(db.Model):
    __tablename__ = "daily_stats"

    id = db.Column(db.Integer, primary_key=True)
    stat_date = db.Column(db.Date, default=date.today, nullable=False, unique=True)
    visitors = db.Column(db.Integer, default=0, nullable=False)
    api_calls = db.Column(db.Integer, default=0, nullable=False)

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "stat_date": self.stat_date.isoformat(),
            "visitors": self.visitors,
            "api_calls": self.api_calls,
        }


class Announcement(db.Model):
    __tablename__ = "announcements"

    id = db.Column(db.Integer, primary_key=True)
    title = db.Column(db.String(200), nullable=False)
    body = db.Column(db.Text, nullable=False)
    is_active = db.Column(db.Boolean, default=True, nullable=False)
    created_at = db.Column(db.DateTime, default=datetime.utcnow, nullable=False)
    expires_at = db.Column(db.DateTime)

    def to_dict(self) -> dict:
        return {
            "id": self.id,
            "title": self.title,
            "body": self.body,
            "is_active": self.is_active,
            "created_at": self.created_at.isoformat(),
            "expires_at": self.expires_at.isoformat() if self.expires_at else None,
        }
