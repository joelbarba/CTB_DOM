# `ngAppSeed` — Joel's seed for AngularJS 1 apps

This project contains the generic application skeleton for a typical [AngularJS][angularjs] web app.

The seed contains a sample AngularJS application and is preconfigured to install the Angular
framework and a bunch of development and testing tools for instant web development gratification.

## Getting Started

Clone the `ngAppSeed` repository
Run bower install into the `./app/core` folder
Run npm install into the `./app/core` folder
Run npm install into the `./` folder

You can run the app running `npm start` or `http-server -a localhost -p 8000 -c-1 ./app`

### Prerequisites

You need:
- Git to clone the repository. You can get git from [here][git].
- Node.js and its package manager (npm). You can get them from [here][node].
- Bower to manage your packages. You can get `bower` from [client-side code package manager][bower].

### Clone `ngAppSeed`

Cd into the folder where you want your app to be.
Run git clone there:

```
git clone https://github.com/joelbarba/ngAppSeed <your-project-name>
```

If you just want to start a new project without the commit history then you can also do:

```
git clone --depth=1 git clone https://github.com/joelbarba/ngAppSeed <your-project-name>
```

The `depth=1` tells git to only pull down the last commit, and ignore the historical data.

### Core

In order to pack all our bower components and other main modules that will be userd throughout the app, there is a `core` module set at a different level. All bower components must be added there. We can add our main components and scss files here as well.
Once compiled, we add them to the index.html file of the app through 4 files:
- core/dist/styles/vendor.css
- core/dist/styles/core.css
- core/dist/scripts/vendor.js
- core/dist/scripts/core.js


### Run the Application

We have preconfigured the project with a simple development web server. The simplest way to start
this server is:

```
npm start
```

Now browse to the app at [`localhost:8000/index.html`][local-app-url].


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
    view2.js              --> the controller logic
  app.js                --> main application module
  index.html            --> app layout file (the main html template file of the app)
```

