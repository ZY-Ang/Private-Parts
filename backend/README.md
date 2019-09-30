## Backend Config

Skeleton Rest API endpoints written in Python 3.6.4 using Flask but should work fine with Python versions >=3.6

It is advisable to create a virtual environment for this flask application. To use create one,
use [conda](https://docs.conda.io/projects/conda/en/latest/user-guide/install/) or `pyenv`

### Without IDEA
Install Flask in the virtual environment: `pip install flask`

[Set variables](https://flask.palletsprojects.com/en/1.1.x/tutorial/factory/) from command line for Flask application (For Windows): `set FLASK_APP=backend`, `set FLASK_ENV=development` 

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