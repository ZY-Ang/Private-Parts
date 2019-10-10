## Backend Config

Skeleton Rest API endpoints written in Python 3.6.4 using Flask but should work fine with Python versions >=3.6

It is advisable to create a virtual environment for this flask application. To use create one,
use [conda](https://docs.conda.io/projects/conda/en/latest/user-guide/install/) or `pyenv`

### CLI
1. Install dependencies in your favourite python environment: `pip install -r requirements.txt`

2. Download private key file for firebase admin API (Service Account) from our project at [console.firebase.google.com](https://console.firebase.google.com)
    - Go to our project dashboard, then `Click the settings icon in top left`>`Project Settings`>`Service accounts`>`Generate new private key`

3. [Set environment variables](https://flask.palletsprojects.com/en/1.1.x/tutorial/factory/) for application:
     - Windows
         - `set FLASK_APP=backend`,
         - `set FLASK_ENV=development`,
         - `set GOOGLE_APPLICATION_CREDENTIALS=<Path To Private Key File>`
     - Linux/ Debian
         - `FLASK_APP=backend`,
         - `FLASK_ENV=development`,
         - `GOOGLE_APPLICATION_CREDENTIALS=<Path To Private Key File>`

Run Flash application: `flask run`

### With IDEA

In `File` > `Project Structure`, add the virtual environment as project SDK.

In `Run Configuration`:
 1. Add new `Flask Server` configuration
 2. `Target type` check `Script path`
 3. `Target` set to `absolute\path\to\__init__.py`
 4. `FLASK_ENV` set to `development`
 5. Check `FLASK_DEBUG`
 6. `Python interpreter` set to virtual environment Python interpreter/SDK of current project
 7. `Working directory` set to `absolute\path\to\Private-Parts\backend`