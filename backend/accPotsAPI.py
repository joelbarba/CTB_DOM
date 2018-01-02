from flask import Blueprint, jsonify, abort, make_response, request
from accPotsModel import AccPots

acc_pots_api = Blueprint('acc_pots_api', __name__)


# Retrieve the list of Accountant Pots
@acc_pots_api.route('/', defaults={'parent_id': None}, methods=['GET'])
@acc_pots_api.route('/<uuid:parent_id>', methods=['GET'])
def get_acc_pots(parent_id):
    resp = {'acc_pots': []}

    if not parent_id:
        acc_pots_list = AccPots.query.all()
        for acc_pot in acc_pots_list:
            resp['acc_pots'].append(acc_pot.get_row())

    else:
        acc_pots_list = AccPots.query.all()
        for acc_pot in acc_pots_list:
            resp['acc_pots'].append(acc_pot.get_row())

    return jsonify(resp)


# Retrieve the info of the requested acc_pot
@acc_pots_api.route('/<uuid:acc_pot_id>', methods=['GET'])
def get_acc_pot(acc_pot_id):
    acc_pot = AccPots.query.filter_by(id=acc_pot_id).first()
    if not acc_pot:
        abort(404)

    resp = {'acc_pot': acc_pot.get_full_row()}
    return jsonify(resp)


# Create a new acc_pot
@acc_pots_api.route('/', methods=['POST'])
def create_acc_pot():
    if not request.json or not 'name' in request.json:
        abort(400)

    new_acc_pot = AccPots(request.json['pos'], request.json.get('name'), request.json.get('amount'))
    error = new_acc_pot.add(new_acc_pot)
    if not error:
        return jsonify({'acc_pot': new_acc_pot.get_full_row()}), 201
    else:
        return jsonify({'error': error}), 400


# Delete an existing acc_pot
@acc_pots_api.route('/<uuid:acc_pot_id>', methods=['DELETE'])
def delete_acc_pot(acc_pot_id):
    acc_pot = AccPots.query.filter_by(id=acc_pot_id).first()
    if not acc_pot:
        abort(404)

    error = acc_pot.delete(acc_pot)
    if not error:
        return make_response(jsonify({}), 204)
    else:
        return jsonify({'error': error}), 400


# Update an existing acc_pot
@acc_pots_api.route('/<uuid:acc_pot_id>', methods=['PUT', 'POST'])
def update_acc_pot(acc_pot_id):
    acc_pot = AccPots.query.filter_by(id=acc_pot_id).first()
    if not acc_pot:
        abort(404)

    if not request.json:
        abort(400)

    if 'pos' in request.json:
        acc_pot.pos = request.json['pos']

    if 'name' in request.json:
        if type(request.json['name']) != unicode:
            abort(400)
        acc_pot.name = request.json['name']

    if 'amount' in request.json:
        try:
            acc_pot.amount = float(request.json['amount'])
        except:
            return jsonify({'error': 'amount not valid'}), 400

    error = acc_pot.update()
    if not error:
        return jsonify({'acc_pot': acc_pot.get_full_row()}), 200
    else:
        return jsonify({'error': error}), 400