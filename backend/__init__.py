from flask import Flask, jsonify, request
import os
import time
import firebase_admin
from firebase_admin import credentials, db
import requests


def create_app():
    """Create and configure an instance of the Flask application."""
    app = Flask(__name__)
    app.secret_key = b'cs3235privatepartsyo'

    # Initialize firebase
    cred = credentials.Certificate(os.environ['GOOGLE_APPLICATION_CREDENTIALS'])
    firebase_admin.initialize_app(cred, {'databaseURL': 'https://private-parts.firebaseio.com'})
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

    @app.route('/pwn/<path:route>')
    def pwn(route):
        url = 'https://haveibeenpwned.com/api/v3/' + route
        response = requests.get(
            url,
            headers={
                'hibp-api-key': '2c73716c824b40fd9382cdbadbba3ddc',
                'user-agent': 'private-parts'
            }
        )
        return jsonify(response.json())

    return app
