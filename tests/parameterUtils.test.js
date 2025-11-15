const { buildParameterGroups, buildDefaultPayload } = require('../src/utils/parameterUtils');

const fixture = {
  parameters: {
    required: [
      {
        name: 'id',
        description: 'Identifier',
        type: 'string',
        location: 'query',
        defaultValue: 'abc'
      },
      {
        name: 'payload',
        description: 'Body payload',
        type: 'object',
        location: 'body',
        sampleValue: { enabled: true }
      }
    ],
    optional: [
      {
        name: 'debug',
        type: 'boolean',
        location: 'query'
      }
    ]
  }
};

describe('parameterUtils', () => {
  it('builds grouped parameter metadata', () => {
    const groups = buildParameterGroups(fixture);
    expect(groups.required).toHaveLength(2);
    expect(groups.required[0]).toMatchObject({
      name: 'id',
      required: true,
      location: 'query',
      defaultValue: 'abc'
    });
    expect(groups.optional).toHaveLength(1);
    expect(groups.optional[0]).toMatchObject({
      name: 'debug',
      required: false
    });
  });

  it('builds default payload using defaults and sample values', () => {
    const payload = buildDefaultPayload(fixture);
    expect(payload.query).toEqual({ id: 'abc' });
    expect(payload.body).toEqual({ payload: { enabled: true } });
  });
});
