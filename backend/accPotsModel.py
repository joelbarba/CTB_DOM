#!flask/bin/python
from flask import Flask
from flask_sqlalchemy   import SQLAlchemy
from sqlalchemy.exc     import SQLAlchemyError
from sqlalchemy_utils   import UUIDType
from sqlalchemy.sql     import select
from sqlalchemy         import create_engine
from sqlalchemy         import Table, Column, Integer, String, MetaData, ForeignKey
from config import app
import uuid


db = SQLAlchemy(app)

engine = create_engine(app.config['SQLALCHEMY_DATABASE_URI'])
conn = engine.connect()
metadata = MetaData()


acc_pots = Table('acc_pots', metadata,
    Column('id',        UUIDType(binary=False), primary_key=True),
    Column('pos',       db.Integer,       unique=False),
    Column('name',      db.String(1000),  unique=False),
    Column('amount',    db.Float, unique=False),
    Column('parent_id', UUIDType(binary=False))
)





# Database model
class AccPots(db.Model):
    __tablename__ = "acc_pots"

    # id          = db.Column(db.Integer,      primary_key=True, autoincrement=True)
    id          = db.Column(UUIDType(binary=False), primary_key=True)
    pos         = db.Column(db.Integer,       unique=False)
    name        = db.Column(db.String(1000),  unique=False)
    amount      = db.Column(db.Float, unique=False)
    parent_id   = db.Column(UUIDType(binary=False))

    def __init__(self, id, pos, name, amount, parent_id):
        self.id = id
        self.pos = pos
        self.name = name
        self.amount = amount
        self.parent_id = parent_id

    def get_row(self):
        resp = {
            'id'        : self.id,
            'pos'       : self.pos,
            'name'      : self.name,
            'amount'    : self.amount,
            'parent_id' : self.parent_id
        }
        return resp

    def get_full_row(self):
        resp = {
            'id'        : self.id,
            'pos'       : self.pos,
            'name'      : self.name,
            'amount'    : self.amount,
            'parent_id' : self.parent_id
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


# Custom functions
def get_root_list(parent_id=None):
    if parent_id is None:
        sql_sent = select([acc_pots], from_obj=acc_pots, whereclause='parent_id is null')
    else:
        sql_sent = select([acc_pots], from_obj=acc_pots, whereclause="parent_id = '" + parent_id + '"')

    result = conn.execute(sql_sent)
    acc_pots_list=[]
    for row in result:
        acc_pots_list.append({
            'id'        : row.id,
            'pos'       : row.pos,
            'name'      : row.name,
            'amount'    : row.amount,
            'parent_id' : row.parent_id
        })

    return acc_pots_list



def session_commit():
    try:
        db.session.commit()
    except SQLAlchemyError as e:
        reason = str(e)
        return reason
