from flask import Blueprint, jsonify, abort, make_response, request

items_api = Blueprint('items_blueprint', __name__, url_prefix='/api')

items = [
    { 'id':  1, 'name': 'First',  'desc': 'The first element' },
    { 'id':  2, 'name': 'Second', 'desc': 'The second element' },
    { 'id':  3, 'name': 'Third',  'desc': 'The third element' },
    { 'id':  4, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id':  5, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id':  6, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id':  7, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id':  8, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id':  9, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id': 10, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id': 11, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id': 12, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id': 13, 'name': 'Fourth', 'desc': 'The last element' },
    { 'id': 14, 'name': 'Fourth', 'desc': 'The last element' }
]


# Retrieve the list of items
@items_api.route('/v1/items', methods=['GET'])
def get_items():
    return jsonify({'items': items})


# Retrieve the info of the requested item
@items_api.route('/v1/items/<int:item_id>', methods=['GET'])
def get_item(item_id):
    item = [item for item in items if item['id'] == item_id]
    if len(item) == 0:
        abort(404)
    return jsonify({'item': item[0]})


# Create a new item
@items_api.route('/v1/items', methods=['POST'])
def create_item():
    if not request.json or not 'name' in request.json:
        abort(400)
    item = {
        'id': items[-1]['id'] + 1,
        'name': request.json['name'],
        'desc': request.json.get('desc', "")
    }
    items.append(item)
    return jsonify({'item': item}), 201


# Delete an existing item
@items_api.route('/v1/items/<int:item_id>', methods=['DELETE'])
def delete_item(item_id):
    item = [item for item in items if item['id'] == item_id]
    if len(item) == 0:
        abort(404)
    items.remove(item[0])
    return jsonify({'result': True})


# Update an existing item
@items_api.route('/v1/items/<int:item_id>', methods=['PUT', 'POST'])
def update_item(item_id):
    item = [item for item in items if item['id'] == item_id]
    if len(item) == 0:
        abort(404)
    if not request.json:
        abort(400)
    if 'name' in request.json and type(request.json['name']) != unicode:
        abort(400)
    if 'desc' in request.json and type(request.json['desc']) is not unicode:
        abort(400)

    item[0]['name'] = request.json.get('name', item[0]['name'])
    item[0]['desc'] = request.json.get('desc', item[0]['desc'])
    return jsonify({'item': item[0]})