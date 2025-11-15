import json


def test_health_check_with_token(client, token_with_all_scopes):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/health", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["status"] == "healthy"
    assert "timestamp" in data


def test_health_check_without_token(client):
    response = client.get("/admin/api/health")
    assert response.status_code == 401
    data = response.get_json()
    assert data["error"] == "Unauthorized"


def test_health_check_with_revoked_token(client, revoked_token):
    raw_token, _ = revoked_token
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/health", headers=headers)
    assert response.status_code == 403
    data = response.get_json()
    assert data["error"] == "Forbidden"
    assert "revoked" in data["message"]


def test_health_check_with_invalid_token(client):
    headers = {"Authorization": "Bearer invalid_token_xyz"}
    response = client.get("/admin/api/health")
    assert response.status_code == 401


def test_api_search_with_valid_token(client, token_with_all_scopes, sample_api_metadata):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/search?q=weather", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert "items" in data
    assert len(data["items"]) > 0
    names = [item["name"] for item in data["items"]]
    assert "Weather API" in names


def test_api_search_without_scope(client, token_with_limited_scopes, sample_api_metadata):
    raw_token, _ = token_with_limited_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/search?q=weather", headers=headers)
    assert response.status_code == 200


def test_api_search_pagination(client, token_with_all_scopes, sample_api_metadata):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/search?per_page=1&page=1", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["per_page"] == 1
    assert data["page"] == 1
    assert data["total"] >= 1


def test_friend_links_list(client, token_with_all_scopes, sample_friend_links):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/friend-links", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert "items" in data
    assert len(data["items"]) == 2


def test_friend_links_list_without_scope(client, token_with_limited_scopes, sample_friend_links):
    raw_token, _ = token_with_limited_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/friend-links", headers=headers)
    assert response.status_code == 403
    data = response.get_json()
    assert data["error"] == "Forbidden"


def test_friend_links_filter_by_status(client, token_with_all_scopes, sample_friend_links):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/friend-links?status=pending", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert len(data["items"]) == 1
    assert data["items"][0]["status"] == "pending"


def test_friend_links_pending(client, token_with_all_scopes, sample_friend_links):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/friend-links/pending", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert len(data["items"]) == 1


def test_friend_link_approve(client, token_with_all_scopes, sample_friend_links):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    
    from app import db
    from app.models import FriendLink
    with client.application.app_context():
        pending_link = FriendLink.query.filter_by(status="pending").first()
        link_id = pending_link.id
    
    response = client.post(f"/admin/api/friend-links/{link_id}/approve", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["message"] == "Friend link approved"
    assert data["link"]["status"] == "approved"


def test_friend_link_reject(client, token_with_all_scopes, sample_friend_links):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    
    from app import db
    from app.models import FriendLink
    with client.application.app_context():
        pending_link = FriendLink.query.filter_by(status="pending").first()
        link_id = pending_link.id
    
    response = client.post(f"/admin/api/friend-links/{link_id}/reject", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["message"] == "Friend link rejected"
    assert data["link"]["status"] == "rejected"


def test_friend_link_approve_without_scope(client, token_with_limited_scopes, sample_friend_links):
    raw_token, _ = token_with_limited_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    
    response = client.post("/admin/api/friend-links/1/approve", headers=headers)
    assert response.status_code == 403
    data = response.get_json()
    assert data["error"] == "Forbidden"


def test_rankings_list(client, token_with_all_scopes, sample_rankings):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/rankings?period=daily", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert "items" in data
    assert data["period"] == "daily"
    assert len(data["items"]) > 0


def test_rankings_without_scope(client, token_with_limited_scopes, sample_rankings):
    raw_token, _ = token_with_limited_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/rankings", headers=headers)
    assert response.status_code == 403
    data = response.get_json()
    assert data["error"] == "Forbidden"


def test_feedback_threads_list(client, token_with_all_scopes, sample_feedback):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/feedback/threads", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert "items" in data
    assert len(data["items"]) > 0


def test_feedback_thread_detail(client, token_with_all_scopes, sample_feedback):
    raw_token, _ = token_with_all_scopes
    thread_id = sample_feedback
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get(f"/admin/api/feedback/threads/{thread_id}", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["subject"] == "Test Issue"
    assert "messages" in data


def test_feedback_threads_without_scope(client, token_with_limited_scopes, sample_feedback):
    raw_token, _ = token_with_limited_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/feedback/threads", headers=headers)
    assert response.status_code == 403
    data = response.get_json()
    assert data["error"] == "Forbidden"


def test_feedback_thread_detail_without_scope(client, token_with_limited_scopes, sample_feedback):
    raw_token, _ = token_with_limited_scopes
    thread_id = sample_feedback
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get(f"/admin/api/feedback/threads/{thread_id}", headers=headers)
    assert response.status_code == 403


def test_feedback_thread_reply(client, token_with_all_scopes, sample_feedback):
    raw_token, _ = token_with_all_scopes
    thread_id = sample_feedback
    headers = {"Authorization": f"Bearer {raw_token}", "Content-Type": "application/json"}
    payload = {"body": "We will look into it", "author": "Admin"}
    response = client.post(
        f"/admin/api/feedback/threads/{thread_id}/reply",
        headers=headers,
        data=json.dumps(payload),
    )
    assert response.status_code == 201
    data = response.get_json()
    assert data["message"] == "Reply posted"
    assert data["reply"]["body"] == "We will look into it"


def test_feedback_thread_reply_missing_body(client, token_with_all_scopes, sample_feedback):
    raw_token, _ = token_with_all_scopes
    thread_id = sample_feedback
    headers = {"Authorization": f"Bearer {raw_token}", "Content-Type": "application/json"}
    payload = {"author": "Admin"}
    response = client.post(
        f"/admin/api/feedback/threads/{thread_id}/reply",
        headers=headers,
        data=json.dumps(payload),
    )
    assert response.status_code == 400


def test_feedback_thread_close(client, token_with_all_scopes, sample_feedback):
    raw_token, _ = token_with_all_scopes
    thread_id = sample_feedback
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.post(f"/admin/api/feedback/threads/{thread_id}/close", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["thread"]["status"] == "closed"


def test_stats_summary(client, token_with_all_scopes, sample_stats):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/stats?days=7", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["period_days"] == 7
    assert "total_visitors" in data
    assert "total_api_calls" in data
    assert "daily_breakdown" in data


def test_stats_without_scope(client, token_with_limited_scopes, sample_stats):
    raw_token, _ = token_with_limited_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/stats", headers=headers)
    assert response.status_code == 403
    data = response.get_json()
    assert data["error"] == "Forbidden"


def test_announcements_list(client, token_with_all_scopes, sample_announcements):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/announcements", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert "items" in data
    assert len(data["items"]) == 2


def test_announcements_active_only(client, token_with_all_scopes, sample_announcements):
    raw_token, _ = token_with_all_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/announcements?active_only=true", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert len(data["items"]) == 1
    assert data["items"][0]["is_active"] is True


def test_announcements_without_scope(client, token_with_limited_scopes, sample_announcements):
    raw_token, _ = token_with_limited_scopes
    headers = {"Authorization": f"Bearer {raw_token}"}
    response = client.get("/admin/api/announcements", headers=headers)
    assert response.status_code == 403
    data = response.get_json()
    assert data["error"] == "Forbidden"


def test_encrypted_response(client, token_with_all_scopes):
    raw_token, _ = token_with_all_scopes
    headers = {
        "Authorization": f"Bearer {raw_token}",
        "X-Encrypt-Response": "true",
    }
    response = client.get("/admin/api/health", headers=headers)
    assert response.status_code == 200
    data = response.get_json()
    assert data["encrypted"] is True
    assert "payload" in data
    assert "signature" in data
    assert data["algorithm"] == "HMAC-SHA256"


def test_rate_limiting(client, app):
    from app.auth import generate_token_pair
    from app.models import RemoteToken
    from app import db
    
    with app.app_context():
        raw_token, token_hash, secret_key = generate_token_pair()
        token = RemoteToken(
            name="Rate Limited Token",
            token_hash=token_hash,
            secret_key=secret_key,
            scopes=json.dumps(["api:search"]),
            rate_limit_per_minute=2,
        )
        db.session.add(token)
        db.session.commit()
    
    headers = {"Authorization": f"Bearer {raw_token}"}
    
    response1 = client.get("/admin/api/health", headers=headers)
    assert response1.status_code == 200
    
    response2 = client.get("/admin/api/health", headers=headers)
    assert response2.status_code == 200
    
    response3 = client.get("/admin/api/health", headers=headers)
    assert response3.status_code == 429
    data = response3.get_json()
    assert data["error"] == "Too Many Requests"
