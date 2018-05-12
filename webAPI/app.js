const express = require('express');
const morgan = require('morgan');  // Automatic logs
const bodyParser = require('body-parser');  // Automatic logs
const { Client } = require('pg');

const app = express();
const client = new Client({
    host: '127.0.0.1',
    port: 5432,
    user: 'barba',
    password: 'barba0001',
    database: 'CTB_DOM'
  });

client.connect().then((err) => {
  console.log('Connected to Postgre DB');

  // client.query('SELECT $1::text as name, ', ['value111'])
  // client.query('select id, pos, name, amount from real_pots', [])
  //   .then(result => {
  //     console.log(result);

  //   }).catch(e => console.error(e.stack)).then(() => client.end())
  client.end();


})
.catch((err) => {
  console.error('connection error', err.stack)
});


const realPotsRoute = require('./api/routes/realPots');

app.use(morgan('dev'));
app.use(bodyParser.urlencoded({extendend:false}));
app.use(bodyParser.json());

app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header(
        'Access-Control-Allow-Headers', 
        'Origin, X-Requested-With, Content-Type, Accept, Authorization'
    );
    if (req.method === 'OPTIONS') {
        res.header('Access-Control-Allow-Methods', 'PUT, POST, PATCH, DELETE, GET');
        return res.status(200).json({});
    }
    next();
});


app.use('/api/v1/real_pots', realPotsRoute);

app.use((req, res, next) => {
    const error = new Error('Not Found');
    error.status= 404;
    next(error);
});

app.use((error, req, res, next) => {
    res.status(error.status || 500);
    res.json({
        error: {
            message: error.message
        }
    })
});



module.exports = app;