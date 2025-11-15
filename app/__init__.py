from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from flask_migrate import Migrate

db = SQLAlchemy()
migrate = Migrate()


def create_app(config_object="config.Config"):
    app = Flask(__name__)
    app.config.from_object(config_object)
    
    db.init_app(app)
    migrate.init_app(app, db)

    from app.seed import seed_initial_data
    from app.errors import register_error_handlers

    with app.app_context():
        from app.routes import admin_api, admin_ui

        app.register_blueprint(admin_api.bp, url_prefix="/admin/api")
        app.register_blueprint(admin_ui.bp, url_prefix="/admin")

        register_error_handlers(app)

        db.create_all()
        if not app.config.get("TESTING"):
            seed_initial_data()

    return app
