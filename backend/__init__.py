from flask import Flask, jsonify, request, send_from_directory
import os
import time
import firebase_admin
from firebase_admin import credentials, db
from lxml import html
import requests
from urllib.parse import urlparse
from random import randrange
import json
import re


def create_app():
    """Create and configure an instance of the Flask application."""
    app = Flask(__name__, static_folder='react')
    app.secret_key = b'cs3235privatepartsyo'

    # Initialize firebase
    cred = credentials.Certificate(os.environ['GOOGLE_APPLICATION_CREDENTIALS'])
    firebase_admin.initialize_app(cred, {'databaseURL': 'https://private-parts.firebaseio.com'})
    # register api commands
    from . import api
    app.register_blueprint(api.bp)

    @app.route('/tasks')
    def tasks():
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

    @app.route('/', defaults={'path': ''})
    @app.route('/<path:path>')
    def index(path):
        if path != "" and os.path.exists(app.static_folder + '/' + path):
            return send_from_directory(app.static_folder, path)
        else:
            return send_from_directory(app.static_folder, 'index.html')

    # @app.errorhandler(404)
    # def error404():
    #     return send_from_directory(app.static_folder, 'index.html')

    return app
