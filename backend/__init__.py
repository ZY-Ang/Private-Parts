from flask import Flask, jsonify
import os
import firebase_admin
from firebase_admin import credentials, db


def create_app():
    """Create and configure an instance of the Flask application."""
    app = Flask(__name__)
    app.secret_key = b'cs3235privatepartsyo'

    # Initialize firebase
    cred = credentials.Certificate(os.environ['GOOGLE_APPLICATION_CREDENTIALS'])
    firebase_admin.initialize_app(cred, {
        'databaseURL': os.environ['FIREBASE_DATABASE_URL']  # Or whatever your database URL is
    })
    # register api commands
    from . import api
    app.register_blueprint(api.bp)

    tasks = [
        {
            'id': 1,
            'title': u'Buy groceries',
            'description': u'Milk, Cheese, Pizza, Fruit, Tylenol',
            'done': False
        },
        {
            'id': 2,
            'title': u'Learn Python',
            'description': u'Need to find a good Python tutorial on the web',
            'done': False
        }
    ]

    @app.route('/')
    def index():
        tasks_snapshot = db.reference('tasks').get()
        return jsonify(tasks_snapshot)

    return app
