const express = require('express');
const { Client } = require('pg');
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







// Retrieves the list of all acc pots recursively, from parent null and linking children[] by parent_id = id
router.get('/', (req, res, next) => {
  connectPromise.then(() => {
    getAccPotsLevel().then((allLevelsList) => {
      // console.log('Retreving the list of pots', allLevelsList);
      res.status(200).json({acc_pots: allLevelsList});
    });
  });
});


// Access recursively to a level of acc pots
function getAccPotsLevel(parent_id) {
  var paramsSql = [];
  var querySql = 
    'select t1.id          as id, '
  + '      t1.pos         as pos, '
  + '      t1.name        as name, '
  + '      t1.amount      as amount, '
  + '      t1.parent_id   as parent_id, '
  + '      (select count(*)'
  + '         from acc_pots '
  + '        where parent_id = t1.id) as children_count '
  + ' from acc_pots t1 ';
  if (parent_id) {
    querySql += 'where parent_id = $1 ';
    paramsSql.push(parent_id);
  } else {
    querySql += 'where parent_id is null ';
  }
  
  
  return client.query(querySql, paramsSql).then((result) => {
    var allPromises = [];
    var levelList = result.rows;
    
    levelList.forEach((accPot) => { // Loop all pots with the same parent
      // console.log('Pot', accPot.pos, accPot.name);
      
      accPot.children = [];
      if (parseInt(accPot.children_count) > 0) {
        // console.log('   It has children --->', accPot.children_count);
        allPromises.push(getAccPotsLevel(accPot.id).then((levelList) => {
          accPot.children = levelList;
        }));
      }

    });

    if (allPromises.length > 0) { // If children, wait to fetch recursively
      return Promise.all(allPromises).then(() => {
        return levelList;
      });
    } else { // If no more levels, return the list
      return levelList;
    }
  });
}


// Retrieves one Accountant Pot
router.get('/:accPotId', (req, res, next) => {
  const accPotId = req.params.accPotId;
  connectPromise.then(() => {
    client.query('select id, pos, name, amount, parent_id, '
              + '        (select name from acc_pots where id = t1.parent_id) as parent_name, '
              + '        (select count(*) from acc_pots where parent_id = t1.id) as children_count '
              + ' from acc_pots t1 '
              + 'where id = $1'
          , [accPotId]).then((result) => {
      res.status(200).json({acc_pot: result.rows[0]});
    }).catch(() => {
      res.status(404).json({});
    });
  });
});


// Add a new Accountant Pot
router.post('/', (req, res, next) => {
  const newPot = {
    pos       : req.body.pos,
    name      : req.body.name,
    amount    : req.body.amount,
    parent_id : null
  };
  if (req.body.parent_id) {
    newPot.parent_id = req.body.parent_id;
  } 

  connectPromise.then(() => {
    client.query('BEGIN', (err) => {
      if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

      var querySql = 'INSERT INTO acc_pots(id, pos, name, amount, parent_id) ';
      var queryValues = [newPot.pos, newPot.name, newPot.amount];

      if (!!newPot.parent_id) {
        querySql += ' VALUES (uuid_generate_v4(), $1, $2, $3, $4)'
        queryValues.push(newPot.parent_id);
      } else {
        querySql += ' VALUES (uuid_generate_v4(), $1, $2, $3, null)'
      }
      querySql += ' RETURNING id, pos, name, amount, parent_id';

      client.query(querySql, queryValues, (err, result) => {
        if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };
        client.query('COMMIT', (err) => {
          if (err) { res.status(400).json({error: 'DB error'}); return; };
          console.log('New acc_pot inserted successfully');
          res.status(201).json({acc_pot: result.rows[0]});
        })

      });
    });
  });
});


// Delete an existing Real Pot
router.delete('/:accPotId', (req, res, next) => {
  const currentPotId = req.params.accPotId;
  

  connectPromise.then(() => {
    client.query('BEGIN', (err) => {
      if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

      client.query('DELETE FROM acc_pots WHERE id = $1', [currentPotId], (err, result) => {
        if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

        if (result.rowCount === 0){
          if (err) { res.status(404).json({error: 'This pot does not exist'}); return; };
        } else {
          client.query('COMMIT', (err) => {
            if (err) { res.status(400).json({error: 'DB error'}); return; };
            console.log('acc_pot delete successfully');
            res.status(204).json();
          });
        }
      });
    });
  });
});


// Edit an existing Accountant Pot
router.patch('/:accPotId', (req, res, next) => {
  const currentPot = {
      id      : req.params.accPotId,
      name    : req.body.name,
      amount  : req.body.amount,
      pos     : req.body.pos
  };

  connectPromise.then(() => {
    client.query('BEGIN', (err) => {
      if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };

      client.query('UPDATE acc_pots SET pos = $2, name = $3, amount = $4 '
                      + 'WHERE id = $1'
                      + 'RETURNING id, pos, name, amount, parent_id', 
                  [currentPot.id, currentPot.pos, currentPot.name, currentPot.amount], (err, result) => {
        if (shouldAbort(err)) { res.status(400).json({error: 'DB error'}); return; };
        
        // console.log('AFTER UPDATE', result);
        var acc_pot = result.rows[0];
        var parentPath = {};
        updateParentAmount(result.rows[0].parent_id, parentPath).then(function() {
          acc_pot.parent = parentPath;
          client.query('COMMIT', (err) => {
            if (err) { res.status(400).json({error: 'DB error'}); return; };
            console.log('New acc_pot updated successfully', acc_pot);
            res.status(201).json({acc_pot: acc_pot});
          });

        });
      });
    });
  });
});

// Update the amount of the pot, and its parents recusively until root
function updateParentAmount(potId, parentPath) {
  if (!potId) {
    parentPath = {};
    return Promise.resolve();

  } else {
    return client.query('update acc_pots t1 '
               + '   set amount = (select sum(amount) from acc_pots t2 where t2.parent_id = t1.id)'
               + ' where id = $1 '
               + ' RETURNING parent_id, amount ', [potId]).then((result) => {
                 
      parentPath.id = potId;
      parentPath.amount = result.rows[0].amount;
      parentPath.parent = {};
      
      var prom = updateParentAmount(result.rows[0].parent_id, parentPath.parent);
      return prom;

    }).catch((err) => {
      console.log('error');
    });
  }
}


module.exports = router;