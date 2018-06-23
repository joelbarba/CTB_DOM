const express = require('express');
const { Client } = require('pg');
// const realPotsModel = require('../models/realPotsModel');
const router = express.Router();

const client = new Client({
  host: '127.0.0.1',
  port: 5432,
  user: 'barba',
  password: 'barba0001',
  database: 'CTB_DOM'
});

// Connection to DB
var connectPromise = client.connect().then((err) => {
  console.log('Connected to Postgre DB');
}).catch((err) => {
  console.error('connection error', err.stack)
});


// Function to check if any SQL error and rollback
const shouldAbort = (err) => {
  if (err) {
    console.error('Error in transaction', err.stack)
    client.query('ROLLBACK', (err) => {
      if (err) {
        console.error('Error rolling back client', err.stack)
      }
      // release the client back to the pool
      // done()
    })
  }
  return !!err
}







// Retrieves the list of the whole ledger
router.get('/', (req, res, next) => {
  connectPromise.then(() => {
    client.query('select id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, '
               + '       mov_group_id, total_real_post, total_acc_post'
               + '  from movements order by mov_num', []).then((result) => {
      console.log('AFTER QUERY');
      res.status(200).json({ledger: result.rows});

    }).catch(next);
  });
});

// Retrieves one Movement
router.get('/:movId', (req, res, next) => {
  const movId = req.params.movId;
  connectPromise.then(() => {
    client.query('select id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, '
              + '        mov_group_id, total_real_post, total_acc_post'
              + '  from movements t1 '
              + ' where id = $1'
          , [movId]).then((result) => {
      res.status(200).json({movement: result.rows[0]});
    }).catch(() => {
      res.status(404).json({});
    });
  });
});


// Edit an existing movement
router.patch('/:movId', (req, res, next) => {
  const currentMov = {
    id: req.params.movId,
  };
  if (req.body.hasOwnProperty('mov_num'))       { currentMov.mov_num      = req.body.mov_num; }
  if (req.body.hasOwnProperty('mov_date'))      { currentMov.mov_date     = req.body.mov_date; }
  if (req.body.hasOwnProperty('description'))   { currentMov.description  = req.body.description; }
  if (req.body.hasOwnProperty('amount'))        { currentMov.amount       = req.body.amount; }
  if (req.body.hasOwnProperty('real_pot_id'))   { currentMov.real_pot_id  = req.body.real_pot_id; }
  if (req.body.hasOwnProperty('acc_pot_id'))    { currentMov.acc_pot_id   = req.body.acc_pot_id; }
  if (req.body.hasOwnProperty('mov_group_id'))  { currentMov.mov_group_id = req.body.mov_group_id; }

  if (Object.keys(currentMov).length > 1) {
    connectPromise.then(() => {
      client.query('BEGIN', (err) => {
        if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

        var updateQuery = 'UPDATE movements SET ';
        var fieldNum = 1;
        if (currentMov.hasOwnProperty('mov_num'))       { updateQuery += ' num_mov = $' + fieldNum++; }
        if (currentMov.hasOwnProperty('mov_num'))       { updateQuery += ' num_mov = $' + fieldNum++; }
  
        client.query('UPDATE movements SET pos = $2, name = $3, amount = $4 '
                        + 'WHERE id = $1'
                        + 'RETURNING id, pos, name, amount', 
                    [currentPot.id, currentPot.pos, currentPot.name, currentPot.amount], (err, result) => {
          if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };
          
          console.log('AFTER UPDATE', result);
          
          client.query('COMMIT', (err) => {
            if (err) { res.status(400).json({error: 'DB error'}); return; };
            console.log('New real_pot updated successfully');
            res.status(201).json({real_pot: result.rows[0]});
          })
  
        });
      });
    });
  }

});



module.exports = router;