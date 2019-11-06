from flask import Flask, jsonify, request, send_from_directory, g
import os
import time
import pickle
import requests

def create_app():
    """Create and configure an instance of the Flask application."""
    app = Flask(__name__, static_folder='react')
    app.secret_key = b'cs3235privatepartsyo'

    # Initialize firebase
    # register api commands
    from . import api
    app.register_blueprint(api.bp)

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

    return app
