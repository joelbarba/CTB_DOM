const express = require('express');
const morgan = require('morgan');  // Automatic logs
const bodyParser = require('body-parser');  // Automatic logs

const app = express();

const realPotsRoute = require('./api/routes/realPots');
const accPotsRoute = require('./api/routes/accPots');



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
app.use('/api/v1/acc_pots', accPotsRoute);

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