#!flask/bin/python
from flask import Blueprint
from config import app
from realPotsAPI import real_pots_api

app.register_blueprint(real_pots_api, url_prefix='/api/v1/real_pots')

@app.route('/test')
def test():
    return "Hey, the APP is up and running!"


if __name__ == '__main__':
    app.run(debug=True)