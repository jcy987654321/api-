const rateLimit = require('express-rate-limit');

const testerLimiter = rateLimit({
  windowMs: 60 * 1000,
  max: 20,
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    message: 'You have reached the rate limit for the online tester. Please try again in a moment.'
  }
});

module.exports = testerLimiter;
