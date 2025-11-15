const apiCatalog = [
  {
    id: 'httpbin-get',
    name: 'HTTPBin Echo (GET)',
    summary: 'Returns request data, useful for testing query parameters and headers.',
    description: 'A simple diagnostic endpoint that reflects query parameters and headers back to the caller.',
    status: 'stable',
    category: 'Diagnostics',
    endpoint: 'https://httpbin.org/get',
    allowedMethods: ['GET'],
    parameters: {
      required: [
        {
          name: 'sample',
          description: 'Sample required key that will be echoed back.',
          type: 'string',
          location: 'query',
          defaultValue: 'required-value',
          sampleValue: 'required-value'
        }
      ],
      optional: [
        {
          name: 'note',
          description: 'Optional note to include in the request.',
          type: 'string',
          location: 'query',
          defaultValue: 'optional-note',
          sampleValue: 'optional-note'
        }
      ]
    },
    headers: [
      {
        name: 'User-Agent',
        description: 'Identifies the caller. Defaults to API Viewer.',
        required: false,
        sampleValue: 'API-Viewer'
      }
    ],
    sample: {
      method: 'GET',
      query: {
        sample: 'required-value',
        note: 'optional-note'
      },
      body: {},
      headers: {
        'User-Agent': 'API-Viewer'
      }
    },
    sampleResponse: {
      contentType: 'application/json',
      body: {
        args: {
          sample: 'required-value',
          note: 'optional-note'
        },
        headers: {
          'User-Agent': 'API-Viewer'
        },
        url: 'https://httpbin.org/get?sample=required-value&note=optional-note'
      }
    },
    instructions: [
      'Provide the required "sample" query parameter to see it reflected in the response.',
      'Optional parameters can be added to observe how they are handled.',
      'Use the Copy cURL action to share the request with teammates.'
    ]
  },
  {
    id: 'httpbin-post-json',
    name: 'HTTPBin JSON (POST)',
    summary: 'Accepts JSON payloads and returns them in the response.',
    description: 'Submit structured JSON data and inspect what the server receives. Helpful for testing POST bodies.',
    status: 'beta',
    category: 'Diagnostics',
    endpoint: 'https://httpbin.org/post',
    allowedMethods: ['POST'],
    parameters: {
      required: [
        {
          name: 'userId',
          description: 'Unique identifier of the user.',
          type: 'string',
          location: 'body',
          defaultValue: 'user-123',
          sampleValue: 'user-123'
        },
        {
          name: 'payload',
          description: 'Nested object payload.',
          type: 'object',
          location: 'body',
          defaultValue: {
            enabled: true,
            score: 42
          },
          sampleValue: {
            enabled: true,
            score: 42
          }
        }
      ],
      optional: [
        {
          name: 'tags',
          description: 'Optional tags for categorisation.',
          type: 'array',
          location: 'body',
          defaultValue: ['sample'],
          sampleValue: ['sample']
        }
      ]
    },
    headers: [
      {
        name: 'Content-Type',
        description: 'Indicates JSON payload.',
        required: true,
        sampleValue: 'application/json'
      }
    ],
    sample: {
      method: 'POST',
      query: {},
      body: {
        userId: 'user-123',
        payload: {
          enabled: true,
          score: 42
        },
        tags: ['sample']
      },
      headers: {
        'Content-Type': 'application/json'
      }
    },
    sampleResponse: {
      contentType: 'application/json',
      body: {
        json: {
          userId: 'user-123',
          payload: {
            enabled: true,
            score: 42
          },
          tags: ['sample']
        }
      }
    },
    instructions: [
      'This endpoint expects JSON in the request body.',
      'Feel free to adjust the payload to test nested objects or arrays.',
      'Use Run Sample to populate the defaults and execute instantly.'
    ]
  }
];

module.exports = apiCatalog;
