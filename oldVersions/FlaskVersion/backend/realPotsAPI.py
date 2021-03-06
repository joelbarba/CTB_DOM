from flask import Blueprint, jsonify, abort, make_response, request
from realPotsModel import RealPots

real_pots_api = Blueprint('real_pots_api', __name__)


# Retrieve the list of Real Pots
@real_pots_api.route('', methods=['GET'])
def get_real_pots():
    real_pots_list = RealPots.query.all()
    resp = {'real_pots': []}
    for real_pot in real_pots_list:
        resp['real_pots'].append(real_pot.get_row())
    return jsonify(resp)


# Retrieve the info of the requested real_pot
@real_pots_api.route('/<uuid:real_pot_id>', methods=['GET'])
def get_real_pot(real_pot_id):
    real_pot = RealPots.query.filter_by(id=real_pot_id).first()
    if not real_pot:
        abort(404)

    resp = {'real_pot': real_pot.get_full_row()}
    return jsonify(resp)


# Create a new real_pot
@real_pots_api.route('', methods=['POST'])
def create_real_pot():
    if not request.json or not 'name' in request.json:
        abort(400)

    new_real_pot = RealPots(request.json['pos'], request.json.get('name'), request.json.get('amount'))
    error = new_real_pot.add(new_real_pot)
    if not error:
        return jsonify({'real_pot': new_real_pot.get_full_row()}), 201
    else:
        return jsonify({'error': error}), 400


# Delete an existing real_pot
@real_pots_api.route('/<uuid:real_pot_id>', methods=['DELETE'])
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
@real_pots_api.route('/<uuid:real_pot_id>', methods=['PUT', 'POST'])
def update_real_pot(real_pot_id):
    real_pot = RealPots.query.filter_by(id=real_pot_id).first()
    if not real_pot:
        abort(404)

    if not request.json:
        abort(400)

    if 'pos' in request.json:
        real_pot.pos = request.json['pos']

    if 'name' in request.json:
        real_pot.name = request.json['name']

    if 'amount' in request.json:
        try:
            real_pot.amount = float(request.json['amount'])
        except:
            return jsonify({'error': 'amount not valid'}), 400

    error = real_pot.update()
    if not error:
        return jsonify({'real_pot': real_pot.get_full_row()}), 200
    else:
        return jsonify({'error': error}), 400
