import json
import re
import nltk
from nltk.corpus import stopwords
from nltk.stem import PorterStemmer
from flask import Blueprint, jsonify, request, g
from googlesearch import search
from fake_useragent import UserAgent

from .scraper import TextScraper
from .model import Classifier

bp = Blueprint('api', __name__, url_prefix='/api')


@bp.route('/scrape', methods=('GET', 'POST'))
def google_search_name():
    def clean_text(scraped_text):
        regex = re.compile('[^a-zA-Z]')
        stop_words = set(stopwords.words('english'))
        stemmer = PorterStemmer()
        text = []

        for paragraph in scraped_text:
            words = paragraph.split()
            words = [regex.sub('', word) for word in words if not word in stop_words]
            words = [word for word in words if len(word) > 0]
            [text.append(stemmer.stem(word))for word in words]

        return text

    def feature_extraction(word):
        return {'feature': word}
    full_name = request.form["full_name"]
    known_as = request.form["known_as"]
    full_name_caps = []
    full_name_lower = []
    scrapped_url = []

    for token in known_as.split():
        full_name_caps.append(token.capitalize())
        full_name_lower.append(token.lower())

    try:
        ua = UserAgent()
        scraper = TextScraper()
        url_list = [url for url in search(full_name, stop=20, user_agent=ua.random)]
        _, scrapped_url = scraper.start(url_list, full_name_caps, full_name_lower)
    except Exception:
        print("HI")

    classifier = Classifier()
    name_token = [token for token in full_name.split()]
    classification = [classifier.classify(token) for token in name_token]

    scraped_set = {}
    for asdf in scrapped_url:
        scraped_set[asdf] = True
    return jsonify({"url": scraped_set, "classification": classification})


@bp.route('/test', methods=('GET', 'POST'))
def end_point_test():
    tasks = [
        {
            'id': 1,
            'title': u'Buy groceries',
            'description': u'This is private',
        }
    ]

    return jsonify(tasks)