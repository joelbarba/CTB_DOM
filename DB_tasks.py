#!flask/bin/python
from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy.exc import SQLAlchemyError
from sqlalchemy_utils import UUIDType
import uuid

STATIC_FOLDER = 'ngApp'
app = Flask(__name__, static_folder=STATIC_FOLDER)

# dialect+driver://username:password@host:port/database
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://barba:barba0001@localhost/testing'
db = SQLAlchemy(app)

# Database model
class Task(db.Model):
    __tablename__ = "tasks"

    # id          = db.Column(db.Integer,      primary_key=True, autoincrement=True)
    id          = db.Column(UUIDType(binary=False), primary_key=True)
    title       = db.Column(db.String(120),  unique=False)
    description = db.Column(db.String(1000), unique=False)
    done        = db.Column(db.String(5),    unique=False)

    def __init__(self, title, description):
        self.title = title
        self.description = description

    def get_row(self):
        resp = {
            'id':           self.id,
            'title':        self.title,
            'description':  self.description,
            'done':         self.done
        }
        return resp

    def get_full_row(self):
        resp = {
            'id':          self.id,
            'title':       self.title,
            'description': self.description,
            'done':        self.done
        }
        return resp

    def add(self, task):
        self.id = uuid.uuid4()
        self.done = 'False'
        db.session.add(task)
        return session_commit()

    def delete(self, task):
        db.session.delete(task)
        return session_commit()

    def update(self):
        return session_commit()

    # def __repr__(self):
    #     return '%r' % (self.title)


def session_commit():
    try:
        db.session.commit()
    except SQLAlchemyError as e:
        reason = str(e)
        return reason

if __name__ == '__main__':
    app.run(debug=True)
