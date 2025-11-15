const counters = new Map();

function incrementCallCount(apiId) {
  const current = counters.get(apiId) || 0;
  const updated = current + 1;
  counters.set(apiId, updated);
  return updated;
}

function getCallCount(apiId) {
  return counters.get(apiId) || 0;
}

function resetCallCount(apiId) {
  if (typeof apiId === 'undefined') {
    counters.clear();
    return;
  }
  counters.delete(apiId);
}

module.exports = {
  incrementCallCount,
  getCallCount,
  resetCallCount
};
