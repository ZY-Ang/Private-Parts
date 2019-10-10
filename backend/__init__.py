from flask import Flask, jsonify, request
import os
import time
import firebase_admin
from firebase_admin import credentials, db


def create_app():
    """Create and configure an instance of the Flask application."""
    app = Flask(__name__)
    app.secret_key = b'cs3235privatepartsyo'

    # Initialize firebase
    cred = credentials.Certificate(os.environ['GOOGLE_APPLICATION_CREDENTIALS'])
    firebase_admin.initialize_app(cred, {'databaseURL': os.environ['FIREBASE_DATABASE_URL']})
    # register api commands
    from . import api
    app.register_blueprint(api.bp)

    @app.route('/')
    def index():
        # Use the firebase admin for python docs for "realtime database"
        tasks_snapshot = db.reference('tasks').get()
        return jsonify(tasks_snapshot)

    @app.route('/team', methods=['GET', 'POST'])
    def add_team():
        if request.method == 'POST':
            team = [
                "Alex",
                "Mingxian",
                "Zixian",
                "Jasmund",
                "Esmond"
            ]
            for member in team:
                # .push creates an auto id, sortable by insertion order.
                db.reference('team').push({
                    "name": member,
                    "timestamp": time.time()
                })
        return jsonify(db.reference('team').get())

    return app
