import pickle
import nltk


class Classifier:
    def __init__(self):
        with open('/home/ubuntu/backend/scraped_data_classifier', 'rb') as handle:
            self.classifier = pickle.load(handle)

    def classify(self, name):
        return self.classifier.classify(self.feature_extraction(name))

    def feature_extraction(self, word):
        return {'feature': word}