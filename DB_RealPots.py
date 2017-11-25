#!flask/bin/python
from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from sqlalchemy.exc import SQLAlchemyError
from sqlalchemy_utils import UUIDType
import uuid

STATIC_FOLDER = 'ngApp'
app = Flask(__name__, static_folder=STATIC_FOLDER)

# dialect+driver://username:password@host:port/database
app.config['SQLALCHEMY_DATABASE_URI'] = 'postgresql://barba:barba0001@localhost/CTB_DOM'
db = SQLAlchemy(app)

# Database model
class RealPots(db.Model):
    __tablename__ = "real_pots"

    # id          = db.Column(db.Integer,      primary_key=True, autoincrement=True)
    id          = db.Column(UUIDType(binary=False), primary_key=True)
    pos         = db.Column(db.Integer,       unique=False)
    name        = db.Column(db.String(1000),  unique=False)
    amount      = db.Column(db.Float, unique=False)

    def __init__(self, name):
        self.name = name
        self.amount = 0

    def get_row(self):
        resp = {
            'id'      : self.id,
            'pos'     : self.pos,
            'name'    : self.name,
            'amount'  : self.amount
        }
        return resp

    def get_full_row(self):
        resp = {
            'id'      : self.id,
            'pos'     : self.pos,
            'name'    : self.name,
            'amount'  : self.amount
        }
        return resp

    def add(self, pot):
        self.id = uuid.uuid4()
        self.amount = 0
        db.session.add(pot)
        return session_commit()

    def delete(self, pot):
        db.session.delete(pot)
        return session_commit()

    def update(self):
        return session_commit()



def session_commit():
    try:
        db.session.commit()
    except SQLAlchemyError as e:
        reason = str(e)
        return reason

if __name__ == '__main__':
    app.run(debug=True)
