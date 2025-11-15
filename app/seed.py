import json
from datetime import datetime, timedelta, date

from app import db
from app.models import (
    APIMetadata,
    FriendLink,
    APIRanking,
    FeedbackThread,
    FeedbackMessage,
    DailyStat,
    Announcement,
)


def seed_initial_data():
    if APIMetadata.query.count() == 0:
        metadata_items = [
            APIMetadata(
                name="Weather API",
                description="Provides current weather and forecast data",
                category="environment",
                tags="weather,forecast,climate",
                version="v1",
                endpoint="/api/weather",
            ),
            APIMetadata(
                name="Maps API",
                description="Mapping and geocoding services",
                category="location",
                tags="maps,geocode",
                version="v2",
                endpoint="/api/maps",
            ),
            APIMetadata(
                name="Payments API",
                description="Handles payment processing and settlements",
                category="finance",
                tags="payments,finance",
                version="v3",
                endpoint="/api/payments",
            ),
        ]
        db.session.bulk_save_objects(metadata_items)

    if FriendLink.query.count() == 0:
        links = [
            FriendLink(name="TechDaily", url="https://techdaily.example.com", description="Latest technology news", status="approved", approved_at=datetime.utcnow()),
            FriendLink(name="APIHub", url="https://apihub.example.com", description="API developer community", status="pending"),
        ]
        db.session.bulk_save_objects(links)

    if APIRanking.query.count() == 0:
        metadata = APIMetadata.query.all()
        for api in metadata:
            ranking = APIRanking(
                api=api,
                calls_count=1000 // (api.id or 1),
                unique_users=200 // (api.id or 1),
                period="daily",
            )
            db.session.add(ranking)

    if FeedbackThread.query.count() == 0:
        thread = FeedbackThread(subject="Issue with Weather API", created_by="user@example.com")
        db.session.add(thread)
        db.session.flush()
        messages = [
            FeedbackMessage(thread_id=thread.id, author="user@example.com", body="Cannot retrieve forecast", is_admin=False),
            FeedbackMessage(thread_id=thread.id, author="Support Agent", body="We are investigating the issue.", is_admin=True),
        ]
        db.session.bulk_save_objects(messages)

    if DailyStat.query.count() == 0:
        today = date.today()
        for i in range(30):
            stat_date = today - timedelta(days=i)
            stat = DailyStat(
                stat_date=stat_date,
                visitors=500 - i * 5,
                api_calls=2000 - i * 20,
            )
            db.session.add(stat)

    if Announcement.query.count() == 0:
        announcements = [
            Announcement(
                title="New Payment API Release",
                body="We have released version 3.0 of the Payments API with improved fraud detection.",
                is_active=True,
            ),
            Announcement(
                title="Maintenance Notice",
                body="Scheduled maintenance on Saturday from 02:00 to 04:00 UTC.",
                is_active=True,
                expires_at=datetime.utcnow() + timedelta(days=7),
            ),
        ]
        db.session.bulk_save_objects(announcements)

    db.session.commit()
