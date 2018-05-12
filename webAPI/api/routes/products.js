const express = require('express');
const router = express.Router();

router.get('/', (req, res, next) => {
    res.status(200).json({
        message: 'hello world -> GET'
    });
});

router.post('/', (req, res, next) => {
    const product = {
        name: req.body.name,
        price: req.body.price
    }
    console.log('product ---> ', product);
    res.status(201).json({
        message: 'hello world -> POST',
        createdProduct: product
    });
});

router.get('/:productId', (req, res, next) => {
    const id = req.params.productId;
    res.status(200).json({
        message: 'hello world -> GET of ' + id
    });
});

router.patch('/:productId', (req, res, next) => {
    const id = req.params.productId;
    res.status(200).json({
        message: 'hello world -> PATCH oooof ' + id
    });
});

router.delete('/:productId', (req, res, next) => {
    const id = req.params.productId;
    console.log('deleting', id);
    res.status(204).json();
});



module.exports = router;