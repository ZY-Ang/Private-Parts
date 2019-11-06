import PyPDF2
import textract
import os
import json
import re
import requests
import time
import firebase_admin
from firebase_admin import credentials, db
from urllib.parse import urlparse
from bs4 import BeautifulSoup
from fake_useragent import UserAgent

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
                soup = BeautifulSoup(response.content, "html.parser")
                return_value["content_type"] = "html"
                return_value["text"] = soup.body.text
                print("body.text = " + return_value["text"])
                for link in soup.findAll('a'):
                    try:
                        href = link.get('href')
                        current_scheme_prefix = url.scheme + "://"
                        parsed_href = urlparse(href)
                        if not parsed_href.netloc:
                            href = url.netloc + href
                            parsed_href = urlparse(href)
                        if not parsed_href.scheme:
                            href = current_scheme_prefix + href
                            parsed_href = urlparse(href)
                        if ".edu.sg" in parsed_href.netloc or "moe.gov.sg" in parsed_href.netloc:
                            add_to_queue.append(href)
                    except Exception as e:
                        print("HTML Parse failed: ", e, flush=True)
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
        except Exception as e:
            print("get_site failed: ", e, flush=True)
            return None
        return return_value

    @staticmethod
    def add_site_to_firebase(site_data):
        """
        :param site_data: to be added to firebase
        """
        print("add_to_firebase(" + json.dumps({
            "url": site_data['url'],
        }) + ")")
        db.reference('data').push({
            "content_type": site_data["content_type"],
            "text": site_data["text"],
            "url": site_data["url"]
        })

    @staticmethod
    def get_urls(site_data):
        print("get_urls(" + json.dumps({
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
            try:
                existing_url_data = db.reference('data').order_by_child('url').equal_to(url).get()
                existing_url_queue = db.reference('data').order_by_value().equal_to(url).get()
                print(url + " added to queue")
                if len(existing_url_data.keys()) == 0 and len(existing_url_queue.keys()) == 0:
                    queue_ref.push(url)
            except Exception as e:
                print("URL " + url + " Add to queue failed: ", e, flush=True)


    def start(self):
        has_next_url = True
        while has_next_url:
            next_element_snapshot = db.reference('queue').order_by_key().limit_to_first(1).get()
            try:
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
                        site_data = Scraper.get_site(url)
                        if site_data is not None:
                            Scraper.add_site_to_firebase(site_data)
                            urls_to_add = Scraper.get_urls(site_data)
                            Scraper.add_urls_to_queue_firebase(urls_to_add)
            except Exception as e:
                print("Failed: ", e, flush=True)

        print("No more pages to scrape. Probably need to seed")
        return 0


# # To multi-thread, just run more times e.g. python3 __main__.py & python3 __main__.py & python3 __main__.py & ...
# def main():
#     # Just use firebase to store results.
#     cred = credentials.Certificate(os.environ['GOOGLE_APPLICATION_CREDENTIALS'])
#     firebase_admin.initialize_app(cred, {'databaseURL': 'https://private-parts.firebaseio.com'})
#     scraper = Scraper()
#     scraper.start()
#
#
# if __name__ == '__main__':
#     main()




class TextScraper:
    def __init__(self):
        self.ua = UserAgent()

    def clean_html(self, soup):
        """
        Remove all javascript and stylesheet code and
        cleanup whitespaces
        """

        # remove all javascript and stylesheet code
        for script in soup(["script", "style"]):
            script.extract()

        text = soup.get_text()
        # break into lines and remove leading and trailing space on each
        lines = (line.strip() for line in text.splitlines())
        # break multi-headlines into a line each
        chunks = (phrase.strip() for line in lines for phrase in line.split("  "))
        # drop blank lines
        text = ' '.join(chunk for chunk in chunks if chunk)
        return text

    def generate_header(self):
        headers = {
            "accept" : "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "accept-encoding" : "gzip, deflate, sdch, br",
            "accept-language" : "en-US,en;q=0.8,ms;q=0.6",
            "user-agent" : self.ua.random
        }
        return headers

    def scrape_site(self, url_string):
        """
        :param url_string: A url to be accessed
        :return: site data for a given urlString. Performs all necessary low level socket-http stuff
        """
        print("get_site(" + str(url_string) + ")")
        url = urlparse(url_string)
        return_value = {
            "url": url_string,
        }

        try:
            headers = self.generate_header()
            response = requests.get(url_string, verify=False, headers=headers)
            if "text/html" in response.headers['content-type']:
                add_to_queue = []
                soup = BeautifulSoup(response.content, "html.parser")
                return_value["content_type"] = "html"
                return_value["text"] = self.clean_html(soup)

                for link in soup.findAll('a'):
                    try:
                        href = link.get('href')
                        current_scheme_prefix = url.scheme + "://"
                        parsed_href = urlparse(href)
                        if not parsed_href.netloc:
                            href = url.netloc + href
                            parsed_href = urlparse(href)
                        if not parsed_href.scheme:
                            href = current_scheme_prefix + href
                            parsed_href = urlparse(href)
                        if not "instagram.com" in parsed_href.netloc:
                            add_to_queue.append(href)
                    except Exception as e:
                        print("HTML Parse failed: ", e, flush=True)
                        pass
                return_value["urlqueue"] = add_to_queue

        except Exception as e:
            print("get_site failed: ", e, flush=True)
            return None
        return return_value

    def get_urls(self, site_data):
        print("get_urls(" + json.dumps({
            "url": site_data['url'],
        }) + ")")
        return site_data["urlqueue"]

    def start(self, main_url_list, full_name_caps, full_name_lower, time_limit=1):
        """
        Each url in the the main_url_list is a site found from google search
        Each url in the sub_url_list is a site found from a url in the main_url_list
        If for time_limit seconds we cannot find anything relevant we stop searching in this "domain"
        Go to the next url in the main_url_list
        """
        sub_url_list = []
        visited_url_list = []
        start_time = time.time()
        text = []
        url_found = set()
        while len(main_url_list) > 0 or len(sub_url_list) > 0:
            try:
                url = None
                if len(sub_url_list) > 0:
                    url = sub_url_list.pop(0)
                elif len(main_url_list) > 0:
                    url = main_url_list.pop(0)
                print(url)
                visited_url_list.append(url)
                print("=========================== " + url + " ===========================")
                site_data = self.scrape_site(url)
                if site_data is not None:
                    for site in site_data["urlqueue"]:
                        if site not in visited_url_list:
                            sub_url_list.append(site)
                        if all(name in site_data["text"] for name in full_name_caps) or all(name in site_data["text"] for name in full_name_lower):
                            # reset timer if we found something related
                            start_time = time.time()
                            url_found.add(site_data["url"])
                            text.append(site_data["text"])
                time_taken = time.time() - start_time
                if time_taken > time_limit:
                    sub_url_list.clear()
            except Exception as e:
                print("Failed: ", e, flush=True)

        print("No more pages to scrape. Probably need to seed")
        return text, url_found