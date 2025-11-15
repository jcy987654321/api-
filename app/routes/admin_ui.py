import json

from flask import Blueprint, render_template, request, redirect, url_for, flash, jsonify
from datetime import datetime

from app import db
from app.auth import generate_token_pair
from app.models import RemoteToken, TokenAuditLog

bp = Blueprint("admin_ui", __name__)


AVAILABLE_SCOPES = [
    {"value": "api:search", "label": "API Search - Query metadata"},
    {"value": "friend_links:read", "label": "Friend Links - Read"},
    {"value": "friend_links:approve", "label": "Friend Links - Approve/Reject"},
    {"value": "rankings:read", "label": "API Rankings - Read"},
    {"value": "feedback:read", "label": "Feedback - Read"},
    {"value": "feedback:reply", "label": "Feedback - Reply"},
    {"value": "stats:read", "label": "Stats - Read"},
    {"value": "announcements:read", "label": "Announcements - Read"},
]


@bp.route("/tokens")
def tokens_list():
    tokens = RemoteToken.query.order_by(RemoteToken.created_at.desc()).all()
    return render_template("admin/tokens_list.html", tokens=tokens)


@bp.route("/tokens/create", methods=["GET", "POST"])
def tokens_create():
    if request.method == "POST":
        name = request.form.get("name", "").strip()
        scopes = request.form.getlist("scopes")
        rate_limit = int(request.form.get("rate_limit", 60))
        created_by = request.form.get("created_by", "admin").strip()

        if not name:
            flash("Token name is required", "error")
            return redirect(url_for("admin_ui.tokens_create"))

        if not scopes:
            flash("At least one scope must be selected", "error")
            return redirect(url_for("admin_ui.tokens_create"))

        raw_token, token_hash, secret_key = generate_token_pair()

        new_token = RemoteToken(
            name=name,
            token_hash=token_hash,
            secret_key=secret_key,
            scopes=json.dumps(scopes),
            rate_limit_per_minute=rate_limit,
            created_by=created_by,
        )

        db.session.add(new_token)
        db.session.commit()

        return render_template(
            "admin/token_created.html",
            token=new_token,
            raw_token=raw_token,
        )

    return render_template("admin/tokens_create.html", available_scopes=AVAILABLE_SCOPES)


@bp.route("/tokens/<int:token_id>/revoke", methods=["POST"])
def tokens_revoke(token_id):
    token = RemoteToken.query.get_or_404(token_id)
    token.revoked = True
    db.session.commit()
    flash(f"Token '{token.name}' has been revoked", "success")
    return redirect(url_for("admin_ui.tokens_list"))


@bp.route("/tokens/<int:token_id>/activate", methods=["POST"])
def tokens_activate(token_id):
    token = RemoteToken.query.get_or_404(token_id)
    token.revoked = False
    db.session.commit()
    flash(f"Token '{token.name}' has been activated", "success")
    return redirect(url_for("admin_ui.tokens_list"))


@bp.route("/tokens/<int:token_id>")
def tokens_detail(token_id):
    token = RemoteToken.query.get_or_404(token_id)
    logs = (
        TokenAuditLog.query.filter_by(token_id=token.id)
        .order_by(TokenAuditLog.created_at.desc())
        .limit(100)
        .all()
    )
    return render_template("admin/token_detail.html", token=token, logs=logs)


@bp.route("/tokens/<int:token_id>/delete", methods=["POST"])
def tokens_delete(token_id):
    token = RemoteToken.query.get_or_404(token_id)
    token_name = token.name
    db.session.delete(token)
    db.session.commit()
    flash(f"Token '{token_name}' has been deleted", "success")
    return redirect(url_for("admin_ui.tokens_list"))


@bp.route("/audit-logs")
def audit_logs():
    page = max(1, int(request.args.get("page", 1)))
    per_page = 50

    pagination = TokenAuditLog.query.order_by(TokenAuditLog.created_at.desc()).paginate(
        page=page, per_page=per_page, error_out=False
    )

    return render_template("admin/audit_logs.html", pagination=pagination)
