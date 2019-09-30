from flask import Flask, jsonify


def create_app():
    """Create and configure an instance of the Flask application."""
    app = Flask(__name__)
    app.secret_key = b'cs3235privatepartsyo'

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
        return jsonify({'tasks': tasks})
    return app
