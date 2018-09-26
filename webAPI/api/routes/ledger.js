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

const cc = {
  reset: '\x1b[0m ',
  green: '\x1b[32m ',
  Bright : "\x1b[1m ",
  Dim : "\x1b[2m ",
  Underscore : "\x1b[4m ",
  Blink : "\x1b[5m ",
  Reverse : "\x1b[7m ",
  Hidden : "\x1b[8m ",

  FgBlack : "\x1b[30m ",
  FgRed : "\x1b[31m ",
  FgGreen : "\x1b[32m ",
  FgYellow : "\x1b[33m ",
  FgBlue : "\x1b[34m ",
  FgMagenta : "\x1b[35m ",
  FgCyan : "\x1b[36m ",
  FgWhite : "\x1b[37m ",

  BgBlack : "\x1b[40m ",
  BgRed : "\x1b[41m ",
  BgGreen : "\x1b[42m ",
  BgYellow : "\x1b[43m ",
  BgBlue : "\x1b[44m ",
  BgMagenta : "\x1b[45m ",
  BgCyan : "\x1b[46m ",
  BgWhite : "\x1b[47m ",
}

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

function blueLog() {
  console.log(cc.FgMagenta, '-- ', ...arguments, cc.reset);
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
  blueLog('AFTER QUERY');
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
router.patch('/:movId', async (req, res, next) => {
  const currentMov = {};
  const newMov = {
    id: req.params.movId,
  };
  if (req.body.hasOwnProperty('mov_num'))       { newMov.mov_num      = req.body.mov_num; }
  if (req.body.hasOwnProperty('mov_date'))      { newMov.mov_date     = req.body.mov_date; }
  if (req.body.hasOwnProperty('description'))   { newMov.description  = req.body.description; }
  if (req.body.hasOwnProperty('amount'))        { newMov.amount       = req.body.amount; }
  if (req.body.hasOwnProperty('real_pot_id'))   { newMov.real_pot_id  = req.body.real_pot_id; }
  if (req.body.hasOwnProperty('acc_pot_id'))    { newMov.acc_pot_id   = req.body.acc_pot_id; }
  if (req.body.hasOwnProperty('mov_group_id'))  { newMov.mov_group_id = req.body.mov_group_id; }

  if (Object.keys(newMov).length < 1) {
    res.status(201).json({ movements: [] });
  } else {

    result = await to(connectPromise);
    if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
    
    result = await to(client.query('BEGIN'));
    if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };

    // If moving position check if overlapping. If so, push it at the end of the date
    // if (newMov.hasOwnProperty('mov_num') || newMov.hasOwnProperty('mov_date')) {
    //   result = await to(client.query(
    //       'select 1'
    //     + '  from movements t1,'
    //     + '       movements t2 '
    //     + ' where t2.id      = $1 '
    //     + '   and t2.id     != t1.id'
    //     + '   and t2.mov_num = t1.mov_num ',
    //     [newMov.id, newMov.mov_num]));
    //   if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
    //   console.log('');
    // }
    if (newMov.hasOwnProperty('mov_num')) { // If changing the move, add half to avoid collision (will resequence later)
      newMov.mov_num += '.5';
      blueLog('newMov.mov_num', newMov.mov_num);
    }
    if (newMov.hasOwnProperty('mov_date')) { // If changing date, add it to the last always
      newMov.mov_num = 999999999;
    }

    var updateQuery = 'UPDATE movements SET updated = now() ';
    var fieldNum = 1;
    var queryParams = [];
    if (newMov.hasOwnProperty('mov_num'))       { updateQuery += ', mov_num = $'       + fieldNum++; queryParams.push(newMov.mov_num);     }
    if (newMov.hasOwnProperty('mov_date'))      { updateQuery += ', mov_date = $'      + fieldNum++; queryParams.push(newMov.mov_date);     }
    if (newMov.hasOwnProperty('description'))   { updateQuery += ', description = $'   + fieldNum++; queryParams.push(newMov.description);  }
    if (newMov.hasOwnProperty('amount'))        { updateQuery += ', amount = $'        + fieldNum++; queryParams.push(newMov.amount);       }
    if (newMov.hasOwnProperty('real_pot_id'))   { updateQuery += ', real_pot_id = $'   + fieldNum++; queryParams.push(newMov.real_pot_id);  }
    if (newMov.hasOwnProperty('acc_pot_id'))    { updateQuery += ', acc_pot_id = $'    + fieldNum++; queryParams.push(newMov.acc_pot_id);   }
    if (newMov.hasOwnProperty('mov_group_id'))  { updateQuery += ', mov_group_id = $'  + fieldNum++; queryParams.push(newMov.mov_group_id); }
    updateQuery += ' WHERE id = $' + fieldNum++;
    queryParams.push(newMov.id);
    updateQuery += ' RETURNING id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, mov_group_id ';

    result = await to(client.query(updateQuery, queryParams));
    if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; }
    
    // Resequence all mov nums wiht the same date
    result = await reseqDateMovs(result.data.rows[0].mov_date);
    if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
    var requestResponse = result.data.rows;

    // Commit
    result = await to(client.query('COMMIT'));
    if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
    blueLog('Movement updated successfully');
    res.status(201).json({ movements: requestResponse });    
  
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

  if (!result.data.rows.length) { // If no mov, that is already the last one
    blueLog('Next move selected ----> None');
    res.status(200).json({ movements: [] });
    return;

  } else {
    nextMov = result.data.rows[0];
    blueLog('Next move selected ----> ', nextMov.mov_num, nextMov.mov_date);
  }

  // Update the mov number (.5 after the next)
  updateQuery = 'update movements set mov_num = $1 + 0.5, mov_date = $2 where id = $3';
  result = await to(client.query(updateQuery, [nextMov.mov_num, nextMov.mov_date, currentMov.id]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  blueLog('Mov num updated');

  // Resequence all mov nums wiht the same date
  result = await reseqDateMovs(nextMov.mov_date);
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  var requestResponse = result.data.rows;

  // Commit
  result = await to(client.query('COMMIT'));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  blueLog('Movement updated successfully');
  res.status(201).json({ movements: requestResponse });

});

// Resequence all mov nums wiht the same given date 
async function reseqDateMovs(movDate) {
  var updateQuery = 'update movements t1 ' 
  + '   set mov_num = ((mov_date - date \'2000-01-01\') * 10000) + 1 + '
  + '                  + (select count(*) from movements t2 where t2.mov_date = t1.mov_date and t2.mov_num < t1.mov_num) '
  + ' where mov_date = $1 ';
  updateQuery += ' RETURNING id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, mov_group_id ';
  result = await to(client.query(updateQuery, [movDate]));
  blueLog('Movs with the same date resequenced', movDate);
  return result;
}


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

  if (!result.data.rows.length) { // If no mov, that is already the first one
    blueLog('Next move selected ----> None');
    res.status(200).json({ movements: [] });
    return;

  } else {
    nextMov = result.data.rows[0];
    blueLog('Next move selected ----> ', nextMov.mov_num, nextMov.mov_date);
  }

  // Update the mov number (.5 after the next)
  updateQuery = 'update movements set mov_num = $1 - 0.5, mov_date = $2 where id = $3';
  result = await to(client.query(updateQuery, [nextMov.mov_num, nextMov.mov_date, currentMov.id]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  blueLog('Mov num updated');

  // Resequence all mov nums wiht the same date
  updateQuery = 'update movements t1 ' 
              + '   set mov_num = ((mov_date - date \'2000-01-01\') * 10000) + 1 + '
              + '                  + (select count(*) from movements t2 where t2.mov_date = t1.mov_date and t2.mov_num < t1.mov_num) '
              + ' where mov_date = $1 ';
  updateQuery += ' RETURNING id, mov_num, mov_date, description, amount, real_pot_id, acc_pot_id, mov_group_id ';
  result = await to(client.query(updateQuery, [nextMov.mov_date]));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  blueLog('Movs with the same date resequenced');
  var requestResponse = result.data.rows;

  // Commit
  result = await to(client.query('COMMIT'));
  if (shouldAbort(result.err)) { res.status(400).json({error: 'DB error'}); return; };
  blueLog('Movement updated successfully');
  res.status(201).json({ movements: requestResponse });

});



module.exports = router;