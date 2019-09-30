from flask import Blueprint, jsonify

bp = Blueprint('private-api', __name__, url_prefix='/private-api')


@bp.route('/', methods=('GET', 'POST'))
def get_some_private_api():
    tasks = [
        {
            'id': 1,
            'title': u'Buy groceries',
            'description': u'This is private',
            'done': False
        }
    ]
    return jsonify(tasks)