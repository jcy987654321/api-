const structuredClone = (value) => {
  return value === undefined ? undefined : JSON.parse(JSON.stringify(value));
};

function normaliseParameter(parameter, isRequired) {
  const {
    name,
    description = '',
    type = 'string',
    location = 'query',
    defaultValue,
    sampleValue
  } = parameter || {};

  return {
    name,
    description,
    type,
    location,
    required: Boolean(isRequired),
    defaultValue: structuredClone(defaultValue),
    sampleValue: structuredClone(sampleValue)
  };
}

function buildParameterGroups(apiMeta = {}) {
  const params = apiMeta.parameters || {};
  const requiredParams = Array.isArray(params.required) ? params.required : [];
  const optionalParams = Array.isArray(params.optional) ? params.optional : [];

  return {
    required: requiredParams.map((param) => normaliseParameter(param, true)),
    optional: optionalParams.map((param) => normaliseParameter(param, false))
  };
}

function buildDefaultPayload(apiMeta = {}) {
  const groups = buildParameterGroups(apiMeta);
  const result = { query: {}, body: {} };

  const insertValue = (param) => {
    if (!param || !param.name) {
      return;
    }
    const target = param.location === 'body' ? result.body : result.query;
    if (typeof param.defaultValue !== 'undefined') {
      target[param.name] = structuredClone(param.defaultValue);
    } else if (typeof param.sampleValue !== 'undefined') {
      target[param.name] = structuredClone(param.sampleValue);
    }
  };

  [...groups.required, ...groups.optional].forEach(insertValue);

  return result;
}

module.exports = {
  buildParameterGroups,
  buildDefaultPayload
};
