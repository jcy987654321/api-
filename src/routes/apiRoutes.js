const express = require('express');
const controller = require('../controllers/apiController');
const testerLimiter = require('../middleware/rateLimiter');

const router = express.Router();

router.get('/catalog', controller.getCatalog);
router.get('/catalog/:id', controller.getApiDetails);
router.post('/catalog/:id/run', testerLimiter, controller.executeProxy);

module.exports = router;
