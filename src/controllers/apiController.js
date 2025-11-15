const { listApis, getApiById } = require('../catalog');
const callTracker = require('../services/callTracker');
const { buildParameterGroups, buildDefaultPayload } = require('../utils/parameterUtils');
const { proxyRequest, ProxyError } = require('../services/proxyService');

const buildCurlCommand = (apiMeta) => {
  if (!apiMeta || !apiMeta.sample) {
    return null;
  }

  const sample = apiMeta.sample;
  const method = (sample.method || 'GET').toUpperCase();
  const url = new URL(apiMeta.endpoint);
  const queryParams = new URLSearchParams(url.search);

  Object.entries(sample.query || {}).forEach(([key, value]) => {
    if (Array.isArray(value)) {
      value.forEach((item) => queryParams.append(key, item));
    } else if (typeof value === 'object') {
      queryParams.set(key, JSON.stringify(value));
    } else {
      queryParams.set(key, value);
    }
  });

  const finalUrl = `${url.origin}${url.pathname}${queryParams.toString() ? `?${queryParams.toString()}` : ''}`;
  const segments = [`curl -X ${method} '${finalUrl}'`];

  Object.entries(sample.headers || {}).forEach(([name, value]) => {
    segments.push(` \\\n  -H '${name}: ${typeof value === 'object' ? JSON.stringify(value) : value}'`);
  });

  if (method !== 'GET' && sample.body && Object.keys(sample.body).length > 0) {
    segments.push(` \\\n  -d '${JSON.stringify(sample.body)}'`);
  }

  return segments.join('');
};

const toCatalogSummary = (api) => ({
  id: api.id,
  name: api.name,
  summary: api.summary,
  status: api.status,
  category: api.category,
  endpoint: api.endpoint,
  allowedMethods: api.allowedMethods,
  callCount: callTracker.getCallCount(api.id)
});

const getCatalog = (req, res) => {
  const entries = listApis().map(toCatalogSummary);
  res.json({ items: entries });
};

const getApiDetails = (req, res) => {
  const { id } = req.params;
  const apiMeta = getApiById(id);

  if (!apiMeta) {
    return res.status(404).json({ message: 'API definition not found.' });
  }

  const parameterGroups = buildParameterGroups(apiMeta);
  const defaultPayload = buildDefaultPayload(apiMeta);

  res.json({
    ...apiMeta,
    parameterGroups,
    defaultPayload,
    callCount: callTracker.getCallCount(id),
    sampleCurl: buildCurlCommand(apiMeta)
  });
};

const executeProxy = async (req, res) => {
  try {
    const result = await proxyRequest({
      apiId: req.params.id,
      method: req.body.method,
      query: req.body.query,
      body: req.body.body,
      headers: req.body.headers,
      useSample: Boolean(req.body.useSample)
    });

    res.json(result);
  } catch (error) {
    if (error instanceof ProxyError) {
      const payload = {
        message: error.message,
        status: error.status,
        details: error.details
      };
      return res.status(error.status || 500).json(payload);
    }

    res.status(500).json({
      message: 'Unexpected error executing the API request.',
      status: 500
    });
  }
};

module.exports = {
  getCatalog,
  getApiDetails,
  executeProxy
};
