import json
from datetime import datetime, date, timedelta

import pytest

from app import create_app, db
from app.auth import generate_token_pair
from app.models import (
    RemoteToken,
    APIMetadata,
    FriendLink,
    APIRanking,
    FeedbackThread,
    FeedbackMessage,
    DailyStat,
    Announcement,
)


@pytest.fixture(scope="function")
def app():
    app = create_app("config.TestingConfig")
    with app.app_context():
        db.create_all()
        yield app
        db.session.remove()
        db.drop_all()


@pytest.fixture(scope="function")
def client(app):
    return app.test_client()


@pytest.fixture(scope="function")
def token_with_all_scopes(app):
    with app.app_context():
        raw_token, token_hash, secret_key = generate_token_pair()
        token = RemoteToken(
            name="Test Token - All Scopes",
            token_hash=token_hash,
            secret_key=secret_key,
            scopes=json.dumps([
                "api:search",
                "friend_links:read",
                "friend_links:approve",
                "rankings:read",
                "feedback:read",
                "feedback:reply",
                "stats:read",
                "announcements:read",
            ]),
            rate_limit_per_minute=60,
            created_by="test",
        )
        db.session.add(token)
        db.session.commit()
        token_id = token.id
        yield raw_token, token_id
        
        token_obj = db.session.get(RemoteToken, token_id)
        if token_obj:
            db.session.delete(token_obj)
            db.session.commit()


@pytest.fixture(scope="function")
def token_with_limited_scopes(app):
    with app.app_context():
        raw_token, token_hash, secret_key = generate_token_pair()
        token = RemoteToken(
            name="Test Token - Limited Scopes",
            token_hash=token_hash,
            secret_key=secret_key,
            scopes=json.dumps(["api:search"]),
            rate_limit_per_minute=60,
        )
        db.session.add(token)
        db.session.commit()
        token_id = token.id
        yield raw_token, token_id

        token_obj = db.session.get(RemoteToken, token_id)
        if token_obj:
            db.session.delete(token_obj)
            db.session.commit()


@pytest.fixture(scope="function")
def revoked_token(app):
    with app.app_context():
        raw_token, token_hash, secret_key = generate_token_pair()
        token = RemoteToken(
            name="Revoked Token",
            token_hash=token_hash,
            secret_key=secret_key,
            scopes=json.dumps(["api:search"]),
            revoked=True,
        )
        db.session.add(token)
        db.session.commit()
        token_id = token.id
        yield raw_token, token_id

        token_obj = db.session.get(RemoteToken, token_id)
        if token_obj:
            db.session.delete(token_obj)
            db.session.commit()


@pytest.fixture(scope="function")
def sample_api_metadata(app):
    with app.app_context():
        apis = [
            APIMetadata(
                name="Weather API",
                description="Weather data",
                category="environment",
                tags="weather,forecast",
                version="v1",
                endpoint="/api/weather",
                status="active",
            ),
            APIMetadata(
                name="Maps API",
                description="Mapping services",
                category="location",
                tags="maps,geocode",
                version="v2",
                endpoint="/api/maps",
                status="active",
            ),
        ]
        db.session.bulk_save_objects(apis)
        db.session.commit()


@pytest.fixture(scope="function")
def sample_friend_links(app):
    with app.app_context():
        links = [
            FriendLink(
                name="TechDaily",
                url="https://techdaily.example.com",
                description="Tech news",
                status="approved",
                approved_at=datetime.utcnow(),
            ),
            FriendLink(
                name="APIHub",
                url="https://apihub.example.com",
                description="API community",
                status="pending",
            ),
        ]
        db.session.bulk_save_objects(links)
        db.session.commit()


@pytest.fixture(scope="function")
def sample_rankings(app, sample_api_metadata):
    with app.app_context():
        apis = APIMetadata.query.all()
        for api in apis:
            ranking = APIRanking(
                api=api,
                calls_count=1000,
                unique_users=200,
                period="daily",
            )
            db.session.add(ranking)
        db.session.commit()


@pytest.fixture(scope="function")
def sample_feedback(app):
    with app.app_context():
        thread = FeedbackThread(
            subject="Test Issue",
            status="open",
            created_by="user@test.com",
        )
        db.session.add(thread)
        db.session.flush()
        
        message = FeedbackMessage(
            thread_id=thread.id,
            author="user@test.com",
            body="I have a problem",
            is_admin=False,
        )
        db.session.add(message)
        db.session.commit()
        yield thread.id


@pytest.fixture(scope="function")
def sample_stats(app):
    with app.app_context():
        today = date.today()
        for i in range(7):
            stat = DailyStat(
                stat_date=today - timedelta(days=i),
                visitors=500 - i * 10,
                api_calls=2000 - i * 50,
            )
            db.session.add(stat)
        db.session.commit()


@pytest.fixture(scope="function")
def sample_announcements(app):
    with app.app_context():
        announcements = [
            Announcement(
                title="Active Announcement",
                body="This is an active announcement",
                is_active=True,
            ),
            Announcement(
                title="Inactive Announcement",
                body="This is inactive",
                is_active=False,
            ),
        ]
        db.session.bulk_save_objects(announcements)
        db.session.commit()
