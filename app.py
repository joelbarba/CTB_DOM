#!flask/bin/python
from flask import Blueprint, make_response, jsonify
from config import app
from backend.realPotsAPI import real_pots_api
from backend.accPotsAPI import acc_pots_api

app.register_blueprint(real_pots_api, url_prefix='/api/v1/real_pots')
app.register_blueprint(acc_pots_api, url_prefix='/api/v1/acc_pots')

@app.route('/test')
def test():
    return "Hey, the APP is up and running!"

@app.route('/')
@app.route('/<path:path>')
def index(path=None):
    if not path:
        return app.send_static_file('index.html')
    return app.send_static_file(path)

@app.errorhandler(404)
def not_found(error):
    return make_response(jsonify({'error': 'Not found'}), 404)


if __name__ == '__main__':
    app.run(debug=True)