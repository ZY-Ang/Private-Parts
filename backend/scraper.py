import os
import firebase_admin
from firebase_admin import credentials, db
import time
from urllib.parse import urlparse
from random import randrange
import json
import re
from bs4 import BeautifulSoup
import requests
import PyPDF2
import textract


class Scraper:
    @staticmethod
    def get_hostname(url_string):
        """
        Gets hostname or netloc part of a url string
        """
        print("get_hostname(" + str(url_string) + ")")
        parsed_url = urlparse(url_string)
        return parsed_url.hostname or parsed_url.netloc

    @staticmethod
    def get_site(url_string):
        """
        :param url_string: A url to be accessed
        :return: site data for a given urlString. Performs all necessary low level socket-http stuff
        Format: {
            response_time: <float>seconds,
            response: <string>httpResponse
        }
        """
        print("get_site(" + str(url_string) + ")")
        url = urlparse(url_string)
        return_value = {
            "url": url_string,
        }

        try:
            response = requests.get(url_string)
            if "text/html" in response.headers['content-type']:
                add_to_queue = []
                soup = BeautifulSoup(response.content)
                return_value["content_type"] = "html"
                return_value["text"] = soup.body.text
                for link in soup.findAll('a'):
                    try:
                        href = link.get('href')
                        current_scheme_prefix = url.scheme + "://"
                        if not (href.startswith('http://') or href.startswith('https://')):
                            href = current_scheme_prefix + href
                        if ".edu" in url.netloc:
                            add_to_queue.append(href)
                    except:
                        pass
                return_value["urlqueue"] = add_to_queue
            elif "application/pdf" in response.headers['content-type']:
                fileurl = "./tmp.pdf"
                with open(fileurl, "wb") as f:
                    f.write(response.content)
                with open(fileurl, "rb") as f:
                    pdf_reader = PyPDF2.PdfFileReader(f)
                    num_pages = pdf_reader.numPages
                    count = 0
                    text = ""
                    # The while loop will read each page
                    while count < num_pages:
                        page_object = pdf_reader.getPage(count)
                        count += 1
                        text += page_object.extractText()
                    # This if statement exists to check if the above library returned #words. It's done because
                    # PyPDF2 cannot read scanned files.
                    if text != "":
                        text = text
                    # If the above returns as False, we run the OCR library textract to #convert scanned/image based
                    # PDF files into text
                    else:
                        text = textract.process(fileurl, method='tesseract', language='eng')
                    # Now we have a text variable which contains all the text derived #from our PDF file. Type print(
                    # text) to see what it contains. It #likely contains a lot of spaces, possibly junk such as '\n'
                    # etc. Now, we will clean our text variable, and return it as a list of keywords.
                    all_web_or_relative_urls_regex = r'(?:(?:http|https):\/\/|www\.|ftp\.)(?:\([-A-Z0-9+&@#\/%=~_|$?!:,' \
                                                     r'.]*\)|[-A-Z0-9+&@#\/%=~_|$?!:,.])*(?:\([-A-Z0-9+&@#\/%=~_|$?!:,' \
                                                     r'.]*\)|[A-Z0-9+&@#\/%=~_|$])'
                    urls_on_pdf = re.findall(all_web_or_relative_urls_regex, text, re.IGNORECASE | re.MULTILINE)
                    return_value["text"] = text
                    return_value["urlqueue"] = urls_on_pdf
                return_value["content_type"] = "pdf"
        except:
            return None
        return return_value

    @staticmethod
    def add_site_to_firebase(site_data):
        """
        :param site_data: to be added to firebase
        """
        print("add_to_firebase(" + json.dumps({
            "response_time": site_data['response_time'],
            "response": '...',
            "url": site_data['url'],
        }) + ")")
        db.reference('edu_data').push(site_data)

    @staticmethod
    def get_urls(site_data):
        print("get_urls(" + json.dumps({
            "response_time": site_data['response_time'],
            "response": '...',
            "url": site_data['url'],
        }) + ")")
        return site_data["urlqueue"]

    @staticmethod
    def add_urls_to_queue_firebase(urls_to_add):
        """
        Pushes a url into the queue on firebase database
        """
        print("add_url_to_queue_firebase([...len = " + str(len(urls_to_add)) + "...])")
        queue_ref = db.reference('queue')
        for url in urls_to_add:
            existing_url = db.reference('data').order_by_child('url').equal_to(url).get()
            if len(existing_url.keys()) == 0:
                queue_ref.push(url)

    def start(self):
        has_next_url = True
        while has_next_url:
            next_element_snapshot = db.reference('queue').order_by_key().limit_to_first(1).get()
            if len(next_element_snapshot.keys()) != 1:
                has_next_url = False
            else:
                key, url = [(k, v) for k, v in next_element_snapshot.items()][0]
                existing_url = db.reference('data').order_by_child('url').equal_to(url).get()
                # Similar to queue 'pop' but follows eventual consistency model for multi-threading.
                db.reference('queue/' + key).delete()
                # Only perform request if unvisited
                if len(existing_url.keys()) == 0:
                    print("=========================== " + url + " ===========================")
                    try:
                        site_data = Scraper.get_site(url)
                        if site_data is not None:
                            Scraper.add_site_to_firebase(site_data)
                            urls_to_add = Scraper.get_urls(site_data)
                            Scraper.add_urls_to_queue_firebase(urls_to_add)
                    except Exception as e:
                        print("Failed: ", e, flush=True)
            time.sleep(randrange(1, 5))

        print("No more pages to scrape. Probably need to seed")
        return 0


# To multi-thread, just run more times e.g. python3 __main__.py & python3 __main__.py & python3 __main__.py & ...
def main():
    # Just use firebase to store results.
    cred = credentials.Certificate(os.environ['GOOGLE_APPLICATION_CREDENTIALS'])
    firebase_admin.initialize_app(cred, {'databaseURL': 'https://private-parts.firebaseio.com'})
    scraper = Scraper()
    scraper.start()


if __name__ == '__main__':
    main()
