#!flask/bin/python
from flask import Blueprint, jsonify, abort, make_response, request
from DB_RealPots import RealPots, app

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

################################################################################################


# Retrieve the list of Real Pots
@app.route('/api/v1/real_pots', methods=['GET'])
def get_real_pots():
    real_pots_list = RealPots.query.all()
    resp = {'real_pots': []}
    for real_pot in real_pots_list:
        resp['real_pots'].append(real_pot.get_row())
    return jsonify(resp)


# Retrieve the info of the requested real_pot
@app.route('/api/v1/real_pots/<uuid:real_pot_id>', methods=['GET'])
def get_real_pot(real_pot_id):
    real_pot = RealPots.query.filter_by(id=real_pot_id).first()
    if not real_pot:
        abort(404)

    resp = {'real_pot': real_pot.get_full_row()}
    return jsonify(resp)


# Create a new real_pot
@app.route('/api/v1/real_pots', methods=['POST'])
def create_real_pot():
    if not request.json or not 'title' in request.json:
        abort(400)

    new_real_pot = RealPots(request.json['title'], request.json.get('description', ""))
    error = new_real_pot.add(new_real_pot)
    if not error:
        return jsonify({'real_pot': new_real_pot.get_full_row()}), 201
    else:
        return jsonify({'error': error}), 400


# Delete an existing real_pot
@app.route('/api/v1/real_pots/<uuid:real_pot_id>', methods=['DELETE'])
def delete_real_pot(real_pot_id):
    real_pot = RealPots.query.filter_by(id=real_pot_id).first()
    if not real_pot:
        abort(404)

    error = real_pot.delete(real_pot)
    if not error:
        return make_response(jsonify({}), 204)
    else:
        return jsonify({'error': error}), 400


# Update an existing real_pot
@app.route('/api/v1/real_pots/<uuid:real_pot_id>', methods=['PUT', 'POST'])
def update_real_pot(real_pot_id):
    real_pot = RealPots.query.filter_by(id=real_pot_id).first()
    if not real_pot:
        abort(404)

    if not request.json:
        abort(400)

    if 'title' in request.json:
        if type(request.json['title']) != unicode:
            abort(400)
        real_pot.title = request.json['title']

    if 'description' in request.json:
        if type(request.json['description']) is not unicode:
            abort(400)
        real_pot.description = request.json['description']

    if 'done' in request.json:
        if type(request.json['done']) is not bool:
            abort(400)

        if request.json['done']:
            real_pot.done = 'True'
        else:
            real_pot.done = 'False'

    error = real_pot.update()
    if not error:
        return jsonify({'real_pot': real_pot.get_full_row()}), 200
    else:
        return jsonify({'error': error}), 400


if __name__ == '__main__':
    app.run(debug=True)

    