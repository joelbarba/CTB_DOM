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

// This is to avoid the try/catch syntax for await (see https://blog.grossman.io/how-to-write-async-await-without-try-catch-blocks-in-javascript/)
// Use it like: [result, err] = await to(connectPromise);
function to(promise) {
  return promise.then((data) => {
     return { data: data, err: null };
  }).catch((err) => {
    return { data: null, err: err };
  });
}








// Retrieves the list of the whole ledger
router.get('/', async (req, res, next) => {

  result = await to(connectPromise);
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  
  result = await to(client.query(
    'select id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, '
    + '       mov_group_id, total_real_post, total_acc_post'
    + '  from movements order by mov_num', []));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('AFTER QUERY');
  res.status(200).json({ledger: result.data.rows});

});

// Retrieves one Movement
router.get('/:movId', async (req, res, next) => {
  const movId = req.params.movId;

  result = await to(connectPromise);
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  
  result = await to(client.query(
    'select id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, '
              + '        mov_group_id, total_real_post, total_acc_post'
              + '  from movements t1 '
              + ' where id = $1', [movId]));
  if (shouldAbort(result.err)) {  res.status(404).json({}); };
  res.status(200).json({movement: result.data.rows[0]});

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

        // If moving, update the ovelapping movement too (if there is)
        if (currentMov.hasOwnProperty('mov_num')) {
          updateQuery = 'update movements set mov_num = (select mov_num from movements where id = $1) ';
          updateQuery += ' where and mov_num = $2'
          client.query(updateQuery, [currentMov.id, currentMov.mov_num], (err, result) => {
            console.log('okeeeeeeeeeeey');
          });
        }

        var updateQuery = 'UPDATE movements SET updated = now() ';
        var fieldNum = 1;
        var queryParams = [];
        if (currentMov.hasOwnProperty('mov_num'))       { updateQuery += ', num_mov = $'       + fieldNum++; queryParams.push(currentMov.mov_num);      }
        if (currentMov.hasOwnProperty('mov_date'))      { updateQuery += ', mov_date = $'      + fieldNum++; queryParams.push(currentMov.mov_date);     }
        if (currentMov.hasOwnProperty('description'))   { updateQuery += ', description = $'   + fieldNum++; queryParams.push(currentMov.description);  }
        if (currentMov.hasOwnProperty('amount'))        { updateQuery += ', amount = $'        + fieldNum++; queryParams.push(currentMov.amount);       }
        if (currentMov.hasOwnProperty('real_pot_id'))   { updateQuery += ', real_pot_id = $'   + fieldNum++; queryParams.push(currentMov.real_pot_id);  }
        if (currentMov.hasOwnProperty('acc_pot_id'))    { updateQuery += ', acc_pot_id = $'    + fieldNum++; queryParams.push(currentMov.acc_pot_id);   }
        if (currentMov.hasOwnProperty('mov_group_id'))  { updateQuery += ', mov_group_id = $'  + fieldNum++; queryParams.push(currentMov.mov_group_id); }
        updateQuery += ' WHERE id = $' + fieldNum++;
        queryParams.push(currentMov.id);
        updateQuery += ' RETURNING id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, mov_group_id ';

        console.log('updateQuery', updateQuery);

        client.query(updateQuery, queryParams, (err, result) => {
          if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };
          
          console.log('AFTER UPDATE', result);
          
          client.query('COMMIT', (err) => {
            if (err) { res.status(400).json({error: 'DB error'}); return; };
            console.log('Movement updated successfully');
            res.status(201).json({movement: result.rows[0]});
          })
  
        });
      });
    });
  }

});


// Push one movement down (after the next mov)
router.patch('/:movId/push_down', async (req, res, next) => {
  const currentMov = {
    id: req.params.movId,
    mov_num: req.body.mov_num,
    mov_date: req.body.mov_date
  };
  
  result = await to(connectPromise);
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  
  result = await to(client.query('BEGIN'));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  
  // Select the next movement
  var nextMov = {};
  result = await to(client.query(
    'select id, mov_date, mov_num from movements where mov_num > $1 order by mov_num limit 1', 
    [currentMov.mov_num]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');

  if (!result.data.rows.length) { // If no mov, that is already the last one
    console.log('Next move selected ----> None');
    res.status(200).json({ movements: [] });
    return;

  } else {
    nextMov = result.data.rows[0];
    console.log('Next move selected ----> ', nextMov.mov_num, nextMov.mov_date);
  }

  // Update the mov number (.5 after the next)
  updateQuery = 'update movements set mov_num = $1 + 0.5, mov_date = $2 where id = $3';
  result = await to(client.query(updateQuery, [nextMov.mov_num, nextMov.mov_date, currentMov.id]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');
  console.log('Mov num updated');

  // Resequence all mov nums wiht the same date
  updateQuery = 'update movements t1 ' 
              + '   set mov_num = ((mov_date - date \'2000-01-01\') * 10000) + 1 + '
              + '                  + (select count(*) from movements t2 where t2.mov_date = t1.mov_date and t2.mov_num < t1.mov_num) '
              + ' where mov_date = $1 ';
  updateQuery += ' RETURNING id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, mov_group_id ';
  result = await to(client.query(updateQuery, [nextMov.mov_date]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');
  console.log('Movs with the same date resequenced');
  var requestResponse = result.data.rows;

  // Commit
  result = await to(client.query('COMMIT'));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');
  console.log('Movement updated successfully');
  res.status(201).json({ movements: requestResponse });

});


// Push one movement up (before the previous mov)
router.patch('/:movId/push_up', async (req, res, next) => {
  const currentMov = {
    id: req.params.movId,
    mov_num: req.body.mov_num,
    mov_date: req.body.mov_date
  };
  
  result = await to(connectPromise);
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  
  result = await to(client.query('BEGIN'));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  
  // Select the next movement
  var nextMov = {};
  result = await to(client.query(
    'select id, mov_date, mov_num from movements where mov_num < $1 order by mov_num desc limit 1', 
    [currentMov.mov_num]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');

  if (!result.data.rows.length) { // If no mov, that is already the first one
    console.log('Next move selected ----> None');
    res.status(200).json({ movements: [] });
    return;

  } else {
    nextMov = result.data.rows[0];
    console.log('Next move selected ----> ', nextMov.mov_num, nextMov.mov_date);
  }

  // Update the mov number (.5 after the next)
  updateQuery = 'update movements set mov_num = $1 - 0.5, mov_date = $2 where id = $3';
  result = await to(client.query(updateQuery, [nextMov.mov_num, nextMov.mov_date, currentMov.id]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');
  console.log('Mov num updated');

  // Resequence all mov nums wiht the same date
  updateQuery = 'update movements t1 ' 
              + '   set mov_num = ((mov_date - date \'2000-01-01\') * 10000) + 1 + '
              + '                  + (select count(*) from movements t2 where t2.mov_date = t1.mov_date and t2.mov_num < t1.mov_num) '
              + ' where mov_date = $1 ';
  updateQuery += ' RETURNING id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, mov_group_id ';
  result = await to(client.query(updateQuery, [nextMov.mov_date]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');
  console.log('Movs with the same date resequenced');
  var requestResponse = result.data.rows;

  // Commit
  result = await to(client.query('COMMIT'));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  console.log('');
  console.log('Movement updated successfully');
  res.status(201).json({ movements: requestResponse });

});



module.exports = router;