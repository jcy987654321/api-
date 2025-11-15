const axios = require('axios');
const { getApiById } = require('../catalog');
const callTracker = require('./callTracker');
const { buildDefaultPayload } = require('../utils/parameterUtils');

class ProxyError extends Error {
  constructor(message, status = 400, details) {
    super(message);
    this.name = 'ProxyError';
    this.status = status;
    this.details = details;
  }
}

const deepClone = (value) => {
  if (value === undefined) {
    return undefined;
  }
  return JSON.parse(JSON.stringify(value));
};

const mergeDeep = (target, source) => {
  if (Array.isArray(source)) {
    return source.map((item) => mergeDeep(undefined, item));
  }
  if (source === null || typeof source !== 'object') {
    return source;
  }

  const base = target && typeof target === 'object' && !Array.isArray(target)
    ? { ...target }
    : {};

  return Object.keys(source).reduce((acc, key) => {
    acc[key] = mergeDeep(base[key], source[key]);
    return acc;
  }, base);
};

const sanitizeHeaders = (inputHeaders = {}, apiMeta) => {
  const allowedHeaders = new Set(
    (apiMeta.headers || []).map((header) => header.name.toLowerCase())
  );

  const sanitized = {};
  const addHeader = (name, value) => {
    if (typeof value === 'undefined' || value === null || value === '') {
      return;
    }
    sanitized[name] = value;
  };

  Object.entries(inputHeaders).forEach(([name, value]) => {
    const lower = name.toLowerCase();
    if (allowedHeaders.has(lower)) {
      addHeader(name, value);
    }
  });

  if (apiMeta.sample && apiMeta.sample.headers) {
    Object.entries(apiMeta.sample.headers).forEach(([name, value]) => {
      const lower = name.toLowerCase();
      if (allowedHeaders.has(lower) && !sanitized[name]) {
        addHeader(name, value);
      }
    });
  }

  if (!sanitized['User-Agent']) {
    addHeader('User-Agent', 'API-Viewer/1.0');
  }

  return sanitized;
};

const buildExecutionPayload = (apiMeta, options) => {
  const defaults = buildDefaultPayload(apiMeta);
  const sample = apiMeta.sample || {};
  const payload = {
    method: options.method || sample.method || (apiMeta.allowedMethods || [])[0] || 'GET',
    query: mergeDeep({}, defaults.query || {}),
    body: mergeDeep({}, defaults.body || {}),
    headers: {}
  };

  if (options.useSample && sample.query) {
    payload.query = mergeDeep(payload.query, sample.query);
  }
  if (options.useSample && sample.body) {
    payload.body = mergeDeep(payload.body, sample.body);
  }

  payload.query = mergeDeep(payload.query, options.query || {});
  payload.body = mergeDeep(payload.body, options.body || {});

  const sanitisedHeaders = sanitizeHeaders(
    { ...(options.headers || {}), ...(options.useSample ? sample.headers || {} : {}) },
    apiMeta
  );

  payload.headers = sanitisedHeaders;

  payload.method = (payload.method || 'GET').toUpperCase();
  return payload;
};

async function proxyRequest({
  apiId,
  method,
  query = {},
  body = {},
  headers = {},
  useSample = false
}) {
  const apiMeta = getApiById(apiId);

  if (!apiMeta) {
    throw new ProxyError('Unknown API definition. Please return to the catalog.', 404);
  }

  const execution = buildExecutionPayload(apiMeta, {
    method,
    query,
    body,
    headers,
    useSample
  });

  const allowedMethods = apiMeta.allowedMethods || [];
  if (!allowedMethods.includes(execution.method)) {
    throw new ProxyError(`Method ${execution.method} is not allowed for this API.`, 405);
  }

  const url = new URL(apiMeta.endpoint);

  const requestQuery = new URLSearchParams(url.search);
  Object.entries(execution.query || {}).forEach(([key, value]) => {
    if (typeof value === 'undefined' || value === null) {
      return;
    }
    if (Array.isArray(value)) {
      value.forEach((item) => requestQuery.append(key, item));
    } else if (typeof value === 'object') {
      requestQuery.set(key, JSON.stringify(value));
    } else {
      requestQuery.set(key, value);
    }
  });

  const finalUrl = `${url.origin}${url.pathname}?${requestQuery.toString()}`.replace(/[?&]$/, '');

  const requestConfig = {
    url: finalUrl,
    method: execution.method,
    headers: execution.headers,
    responseType: 'arraybuffer',
    validateStatus: () => true,
    data: undefined
  };

  if (execution.method === 'POST' || execution.method === 'PUT' || execution.method === 'PATCH') {
    requestConfig.data = execution.body;
  }

  const startedAt = Date.now();
  let response;
  try {
    response = await axios(requestConfig);
  } catch (error) {
    throw new ProxyError('Failed to reach target API. Please try again later.', 502, {
      originalError: error.message
    });
  }

  const durationMs = Date.now() - startedAt;
  const buffer = Buffer.from(response.data);
  const contentType = response.headers['content-type'] || 'text/plain';
  const size = buffer.byteLength;

  /* eslint-disable no-console */
  console.info('[Proxy] Request', {
    apiId,
    method: requestConfig.method,
    url: requestConfig.url
  });
  console.info('[Proxy] Response', {
    status: response.status,
    durationMs,
    size
  });
  /* eslint-enable no-console */

  let textBody = buffer.toString('utf-8');
  let parsedJson = null;

  if (/application\/(json|.*\+json)|text\/json/.test(contentType)) {
    try {
      parsedJson = JSON.parse(textBody);
      textBody = JSON.stringify(parsedJson, null, 2);
    } catch (error) {
      parsedJson = null;
    }
  }

  const result = {
    status: response.status,
    statusText: response.statusText,
    durationMs,
    size,
    contentType,
    headers: response.headers,
    body: {
      text: textBody,
      json: parsedJson,
      base64: buffer.toString('base64')
    },
    callCount: callTracker.getCallCount(apiId)
  };

  if (response.status >= 400) {
    throw new ProxyError('Target API returned an error.', response.status, result);
  }

  result.callCount = callTracker.incrementCallCount(apiId);
  return result;
}

module.exports = {
  proxyRequest,
  ProxyError
};
