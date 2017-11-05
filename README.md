<h1>CTB_DOM (Comptabilitat Domestica)</h1>

This is a project to track domestic expenses using accountant methods

The project is based on the `ngFlaskAppSeed` from Joel's seed for AngularJS 1 + Flask Web Apps

## Prerequisites:
Install Python: (should be installed by default in Ubuntu, type python -v)
Install PIP: sudo apt-get install python-pip
Install virtualenv: pip install virtualenv


## Getting Started

Clone Repo:                 `git clone https://github.com/joelbarba/CTB_DOM`
Install node modules:       `npm install`
Install bower modules:      `cd ngApp` + `bower install`
Generate app:               `grunt prepare`
Create virtual environment: `virtualenv env`
# Install Flask:              `env/bin/pip install flask`
Execution permissions:      `chmod a+x app.py`
Run the app:                `sh run.sh`



Run npm install into the `./` folder
Run bower install into the `./app/core` folder




## Directory Layout

```
app/                    --> all of the source files for the application
  app.css               --> default stylesheet
  core/                 --> core modules  
    dist/                 --> compiled core
    src/                  --> source code for the core
  components/           --> all app specific modules
    version/              --> version related components
  view1/                --> the view1 view template and logic
    view1.html            --> the partial template
    view1.js              --> the controller logic
  view2/                --> the view2 view template and logic
    view2.html            --> the partial template
    view3.js              --> the controller logic
  app.js                --> main application module
  index.html            --> app layout file (the main html template file of the app)
```

