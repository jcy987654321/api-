const apiCatalog = require('./apiCatalog');

function listApis() {
  return apiCatalog.slice();
}

function getApiById(id) {
  if (!id) {
    return null;
  }
  return apiCatalog.find((api) => api.id === id) || null;
}

module.exports = {
  listApis,
  getApiById
};
