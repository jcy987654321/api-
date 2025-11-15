const app = require('./app');

const PORT = process.env.PORT || 3000;

app.listen(PORT, () => {
  /* eslint-disable no-console */
  console.log(`API Viewer running on http://localhost:${PORT}`);
  /* eslint-enable no-console */
});
