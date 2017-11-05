#!flask/bin/python
from flask import Blueprint, jsonify, abort, make_response, request
from DB_tasks import Task, app

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


# Retrieve the list of tasks
@app.route('/api/v1/tasks', methods=['GET'])
def get_tasks():
    task_list = Task.query.all()
    resp = {'tasks': []}
    for task in task_list:
        resp['tasks'].append(task.get_row())
    return jsonify(resp)


# Retrieve the info of the requested task
@app.route('/api/v1/tasks/<uuid:task_id>', methods=['GET'])
def get_task(task_id):
    task = Task.query.filter_by(id=task_id).first()
    if not task:
        abort(404)

    resp = {'task': task.get_full_row()}
    return jsonify(resp)


# Create a new task
@app.route('/api/v1/tasks', methods=['POST'])
def create_task():
    if not request.json or not 'title' in request.json:
        abort(400)

    new_task = Task(request.json['title'], request.json.get('description', ""))
    error = new_task.add(new_task)
    if not error:
        return jsonify({'task': new_task.get_full_row()}), 201
    else:
        return jsonify({'error': error}), 400


# Delete an existing task
@app.route('/api/v1/tasks/<uuid:task_id>', methods=['DELETE'])
def delete_task(task_id):
    task = Task.query.filter_by(id=task_id).first()
    if not task:
        abort(404)

    error = task.delete(task)
    if not error:
        return make_response(jsonify({}), 204)
    else:
        return jsonify({'error': error}), 400


# Update an existing task
@app.route('/api/v1/tasks/<uuid:task_id>', methods=['PUT', 'POST'])
def update_task(task_id):
    task = Task.query.filter_by(id=task_id).first()
    if not task:
        abort(404)

    if not request.json:
        abort(400)

    if 'title' in request.json:
        if type(request.json['title']) != unicode:
            abort(400)
        task.title = request.json['title']

    if 'description' in request.json:
        if type(request.json['description']) is not unicode:
            abort(400)
        task.description = request.json['description']

    if 'done' in request.json:
        if type(request.json['done']) is not bool:
            abort(400)

        if request.json['done']:
            task.done = 'True'
        else:
            task.done = 'False'

    error = task.update()
    if not error:
        return jsonify({'task': task.get_full_row()}), 200
    else:
        return jsonify({'error': error}), 400


if __name__ == '__main__':
    app.run(debug=True)

    