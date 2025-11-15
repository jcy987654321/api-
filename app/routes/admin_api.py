from datetime import datetime, timedelta, date

from flask import Blueprint, request, jsonify
from sqlalchemy import func, desc, or_, and_

from app import db
from app.auth import require_token
from app.models import (
    APIMetadata,
    FriendLink,
    APIRanking,
    FeedbackThread,
    FeedbackMessage,
    DailyStat,
    Announcement,
)
from app.utils import build_api_response

bp = Blueprint("admin_api", __name__)


@bp.route("/search", methods=["GET"])
@require_token("api:search")
def api_search():
    query = request.args.get("q", "").strip()
    category = request.args.get("category", "").strip()
    status = request.args.get("status", "").strip()
    page = max(1, int(request.args.get("page", 1)))
    per_page = min(100, max(1, int(request.args.get("per_page", 20))))

    filters = []
    if query:
        filters.append(
            or_(
                APIMetadata.name.ilike(f"%{query}%"),
                APIMetadata.description.ilike(f"%{query}%"),
            )
        )
    if category:
        filters.append(APIMetadata.category == category)
    if status:
        filters.append(APIMetadata.status == status)

    query_obj = APIMetadata.query
    if filters:
        query_obj = query_obj.filter(and_(*filters))

    pagination = query_obj.order_by(desc(APIMetadata.created_at)).paginate(
        page=page, per_page=per_page, error_out=False
    )

    return build_api_response(
        {
            "items": [item.to_dict() for item in pagination.items],
            "total": pagination.total,
            "page": pagination.page,
            "per_page": pagination.per_page,
            "total_pages": pagination.pages,
        }
    )


@bp.route("/friend-links", methods=["GET"])
@require_token("friend_links:read")
def list_friend_links():
    status = request.args.get("status", "").strip()
    page = max(1, int(request.args.get("page", 1)))
    per_page = min(100, max(1, int(request.args.get("per_page", 20))))

    query_obj = FriendLink.query
    if status:
        query_obj = query_obj.filter_by(status=status)

    pagination = query_obj.order_by(desc(FriendLink.created_at)).paginate(
        page=page, per_page=per_page, error_out=False
    )

    return build_api_response(
        {
            "items": [link.to_dict() for link in pagination.items],
            "total": pagination.total,
            "page": pagination.page,
            "per_page": pagination.per_page,
            "total_pages": pagination.pages,
        }
    )


@bp.route("/friend-links/pending", methods=["GET"])
@require_token("friend_links:read")
def pending_friend_links():
    links = FriendLink.query.filter_by(status="pending").order_by(FriendLink.created_at).all()
    return build_api_response({"items": [link.to_dict() for link in links]})


@bp.route("/friend-links/<int:link_id>/approve", methods=["POST"])
@require_token("friend_links:approve")
def approve_friend_link(link_id):
    link = FriendLink.query.get_or_404(link_id)
    link.status = "approved"
    link.approved_at = datetime.utcnow()
    db.session.commit()
    return build_api_response({"message": "Friend link approved", "link": link.to_dict()})


@bp.route("/friend-links/<int:link_id>/reject", methods=["POST"])
@require_token("friend_links:approve")
def reject_friend_link(link_id):
    link = FriendLink.query.get_or_404(link_id)
    link.status = "rejected"
    db.session.commit()
    return build_api_response({"message": "Friend link rejected", "link": link.to_dict()})


@bp.route("/rankings", methods=["GET"])
@require_token("rankings:read")
def api_rankings():
    period = request.args.get("period", "daily").strip()
    limit = min(100, max(1, int(request.args.get("limit", 50))))

    rankings = (
        APIRanking.query.filter_by(period=period)
        .order_by(desc(APIRanking.calls_count))
        .limit(limit)
        .all()
    )

    return build_api_response({"items": [rank.to_dict() for rank in rankings], "period": period})


@bp.route("/feedback/threads", methods=["GET"])
@require_token("feedback:read")
def feedback_threads():
    status = request.args.get("status", "").strip()
    page = max(1, int(request.args.get("page", 1)))
    per_page = min(100, max(1, int(request.args.get("per_page", 20))))

    query_obj = FeedbackThread.query
    if status:
        query_obj = query_obj.filter_by(status=status)

    pagination = query_obj.order_by(desc(FeedbackThread.created_at)).paginate(
        page=page, per_page=per_page, error_out=False
    )

    return build_api_response(
        {
            "items": [thread.to_dict() for thread in pagination.items],
            "total": pagination.total,
            "page": pagination.page,
            "per_page": pagination.per_page,
            "total_pages": pagination.pages,
        }
    )


@bp.route("/feedback/threads/<int:thread_id>", methods=["GET"])
@require_token("feedback:read")
def feedback_thread_detail(thread_id):
    thread = FeedbackThread.query.get_or_404(thread_id)
    return build_api_response(thread.to_dict())


@bp.route("/feedback/threads/<int:thread_id>/reply", methods=["POST"])
@require_token("feedback:reply")
def feedback_thread_reply(thread_id):
    thread = FeedbackThread.query.get_or_404(thread_id)
    data = request.get_json()

    if not data or "body" not in data:
        return jsonify({"error": "Bad Request", "message": "Missing 'body' field"}), 400

    message = FeedbackMessage(
        thread_id=thread.id,
        author=data.get("author", "Admin"),
        body=data["body"],
        is_admin=True,
    )
    db.session.add(message)
    db.session.commit()

    return build_api_response({"message": "Reply posted", "reply": message.to_dict()}, 201)


@bp.route("/feedback/threads/<int:thread_id>/close", methods=["POST"])
@require_token("feedback:reply")
def close_feedback_thread(thread_id):
    thread = FeedbackThread.query.get_or_404(thread_id)
    thread.status = "closed"
    db.session.commit()
    return build_api_response({"message": "Thread closed", "thread": thread.to_dict()})


@bp.route("/stats", methods=["GET"])
@require_token("stats:read")
def stats_summary():
    days = min(90, max(1, int(request.args.get("days", 30))))
    start_date = date.today() - timedelta(days=days - 1)

    daily_stats = (
        DailyStat.query.filter(DailyStat.stat_date >= start_date)
        .order_by(DailyStat.stat_date)
        .all()
    )

    total_visitors = db.session.query(func.sum(DailyStat.visitors)).filter(DailyStat.stat_date >= start_date).scalar() or 0
    total_calls = db.session.query(func.sum(DailyStat.api_calls)).filter(DailyStat.stat_date >= start_date).scalar() or 0

    return build_api_response(
        {
            "period_days": days,
            "start_date": start_date.isoformat(),
            "end_date": date.today().isoformat(),
            "total_visitors": total_visitors,
            "total_api_calls": total_calls,
            "daily_breakdown": [stat.to_dict() for stat in daily_stats],
        }
    )


@bp.route("/announcements", methods=["GET"])
@require_token("announcements:read")
def announcements():
    active_only = request.args.get("active_only", "false").lower() in {"1", "true", "yes"}
    page = max(1, int(request.args.get("page", 1)))
    per_page = min(100, max(1, int(request.args.get("per_page", 20))))

    query_obj = Announcement.query
    if active_only:
        now = datetime.utcnow()
        query_obj = query_obj.filter(
            Announcement.is_active == True,
            or_(Announcement.expires_at == None, Announcement.expires_at > now),
        )

    pagination = query_obj.order_by(desc(Announcement.created_at)).paginate(
        page=page, per_page=per_page, error_out=False
    )

    return build_api_response(
        {
            "items": [ann.to_dict() for ann in pagination.items],
            "total": pagination.total,
            "page": pagination.page,
            "per_page": pagination.per_page,
            "total_pages": pagination.pages,
        }
    )


@bp.route("/health", methods=["GET"])
@require_token()
def health_check():
    return build_api_response({"status": "healthy", "timestamp": datetime.utcnow().isoformat()})
