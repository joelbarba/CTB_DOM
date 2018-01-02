#!flask/bin/python
from flask import Flask

STATIC_FOLDER = 'ngApp'
app = Flask(__name__, static_folder=STATIC_FOLDER)

# dialect+driver://username:password@host:port/database
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://barba:barba0001@localhost/CTB_DOM'