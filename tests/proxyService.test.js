const nock = require('nock');
const { proxyRequest, ProxyError } = require('../src/services/proxyService');
const callTracker = require('../src/services/callTracker');

describe('proxyService', () => {
  beforeEach(() => {
    nock.cleanAll();
    callTracker.resetCallCount();
  });

  afterAll(() => {
    nock.restore();
  });

  it('throws when API definition is missing', async () => {
    await expect(proxyRequest({ apiId: 'unknown' })).rejects.toBeInstanceOf(ProxyError);
  });

  it('successfully proxies GET requests and increments call count', async () => {
    const scope = nock('https://httpbin.org')
      .get('/get')
      .query({ sample: 'override', note: 'optional-note' })
      .reply(200, { ok: true }, { 'Content-Type': 'application/json' });

    const result = await proxyRequest({
      apiId: 'httpbin-get',
      method: 'GET',
      query: { sample: 'override' }
    });

    expect(scope.isDone()).toBeTruthy();
    expect(result.status).toBe(200);
    expect(result.body.json).toEqual({ ok: true });
    expect(result.callCount).toBe(1);
  });

  it('rejects disallowed HTTP methods', async () => {
    await expect(
      proxyRequest({
        apiId: 'httpbin-get',
        method: 'POST'
      })
    ).rejects.toMatchObject({ status: 405 });
  });

  it('surface errors returned by upstream API', async () => {
    const scope = nock('https://httpbin.org')
      .get('/get')
      .query(true)
      .reply(500, 'error', { 'Content-Type': 'text/plain' });

    await expect(
      proxyRequest({ apiId: 'httpbin-get' })
    ).rejects.toMatchObject({ status: 500 });

    expect(scope.isDone()).toBeTruthy();
  });
});
