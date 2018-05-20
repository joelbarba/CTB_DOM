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







// Retrieves the list of all real pots
router.get('/', (req, res, next) => {
  connectPromise.then(() => {
    client.query('select id, pos, name, amount from real_pots', []).then((result) => {
      console.log('AFTER QUERY');
      res.status(200).json({real_pots: result.rows});

    }).catch(next);
  });
});


// Retrieves one real pot
router.get('/:realPotId', (req, res, next) => {
  const realPotId = req.params.realPotId;
  connectPromise.then(() => {
    client.query('select id, pos, name, amount from real_pots where id = $1', [realPotId]).then((result) => {
      res.status(200).json({real_pot: result.rows[0]});
    }).catch(() => {
      res.status(404).json({});
    });
  });
});


// Add a new Real Pot
router.post('/', (req, res, next) => {
  const newPot = {
      name    : req.body.name,
      amount  : req.body.amount,
      pos     : req.body.pos
  };

  connectPromise.then(() => {
    client.query('BEGIN', (err) => {
      if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

      client.query('INSERT INTO real_pots(id, pos, name, amount) '
                      + 'VALUES (uuid_generate_v4(), $1, $2, $3)'
                      + 'RETURNING id, pos, name, amount', 
                  [newPot.pos, newPot.name, newPot.amount], (err, result) => {
        if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };
        
        // console.log('AFTER INSERT', result);
        
        client.query('COMMIT', (err) => {
          if (err) { res.status(400).json({error: 'DB error'}); return; };
          console.log('New real_pot inserted successfully');
          res.status(201).json({real_pot: result.rows[0]});
        })

      });
    });
  });
});


// Delete an existing Real Pot
router.delete('/:realPotId', (req, res, next) => {
  const currentPotId = req.params.realPotId;
  

  connectPromise.then(() => {
    client.query('BEGIN', (err) => {
      if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

      client.query('DELETE FROM real_pots WHERE id = $1', [currentPotId], (err, result) => {
        if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };
        
        console.log('AFTER DELETE', result);
        
        client.query('COMMIT', (err) => {
          if (err) { res.status(400).json({error: 'DB error'}); return; };
          console.log('New real_pot updated successfully');
          res.status(204).json();
        });

      });
    });
  });
});


// Edit an existing Real Pot
router.patch('/:realPotId', (req, res, next) => {
  const currentPot = {
      id      : req.params.realPotId,
      name    : req.body.name,
      amount  : req.body.amount,
      pos     : req.body.pos
  };

  connectPromise.then(() => {
    client.query('BEGIN', (err) => {
      if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

      client.query('UPDATE real_pots SET pos = $2, name = $3, amount = $4 '
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
});


module.exports = router;