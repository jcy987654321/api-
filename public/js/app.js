const state = {
  catalog: [],
  currentApi: null
};

const elements = {
  backHome: document.getElementById('back-home'),
  runSample: document.getElementById('run-sample'),
  copyCurl: document.getElementById('copy-curl'),
  catalogView: document.getElementById('catalog-view'),
  catalogList: document.getElementById('catalog-list'),
  catalogLoader: document.getElementById('catalog-loader'),
  catalogError: document.getElementById('catalog-error'),
  detailView: document.getElementById('detail-view'),
  detailLoader: document.getElementById('detail-loader'),
  detailError: document.getElementById('detail-error'),
  detailArticle: document.getElementById('api-details'),
  apiName: document.getElementById('api-name'),
  apiSummary: document.getElementById('api-summary'),
  apiStatus: document.getElementById('api-status'),
  apiEndpoint: document.getElementById('api-endpoint'),
  apiMethods: document.getElementById('api-methods'),
  apiCategory: document.getElementById('api-category'),
  apiCallCount: document.getElementById('api-call-count'),
  apiInstructions: document.getElementById('api-instructions'),
  parameterGroups: document.getElementById('parameter-groups'),
  testerForm: document.getElementById('tester-form'),
  testerMethod: document.getElementById('tester-method'),
  testerParameters: document.getElementById('tester-parameters'),
  testerHeaders: document.getElementById('tester-headers'),
  testerStatus: document.getElementById('tester-status'),
  testerSubmit: document.getElementById('tester-submit'),
  testerError: document.getElementById('tester-error'),
  testerLoader: document.getElementById('tester-loader'),
  responsePreview: document.getElementById('response-preview'),
  responseStatus: document.getElementById('response-status'),
  responseDuration: document.getElementById('response-duration'),
  responseSize: document.getElementById('response-size'),
  responseContentType: document.getElementById('response-content-type'),
  responseJson: document.getElementById('response-json'),
  responseRaw: document.getElementById('response-raw'),
  mediaPreview: document.getElementById('media-preview')
};

const tabButtons = Array.from(document.querySelectorAll('.tab-button'));
const tabContents = Array.from(document.querySelectorAll('.tab-content'));

const show = (el) => el.classList.remove('hidden');
const hide = (el) => el.classList.add('hidden');

const syntaxHighlight = (jsonString) => {
  if (!jsonString) {
    return '';
  }
  const escaped = jsonString
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  return escaped.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(?::)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, (match) => {
    let cls = 'number';
    if (/^"/.test(match)) {
      cls = /:$/.test(match) ? 'key' : 'string';
    } else if (/true|false/.test(match)) {
      cls = 'boolean';
    } else if (/null/.test(match)) {
      cls = 'null';
    }
    return `<span class="json-${cls}">${match}</span>`;
  });
};

const formatBytes = (bytes) => {
  if (!bytes && bytes !== 0) {
    return '';
  }
  if (bytes < 1024) {
    return `${bytes} B`;
  }
  if (bytes < 1024 * 1024) {
    return `${(bytes / 1024).toFixed(1)} KB`;
  }
  return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
};

const clearChildren = (node) => {
  while (node.firstChild) {
    node.removeChild(node.firstChild);
  }
};

const renderCatalog = (items) => {
  clearChildren(elements.catalogList);
  if (!items.length) {
    const empty = document.createElement('li');
    empty.textContent = 'No APIs are registered yet.';
    elements.catalogList.appendChild(empty);
    return;
  }

  items.forEach((item) => {
    const li = document.createElement('li');
    li.className = 'catalog-item';
    li.innerHTML = `
      <h3>${item.name}</h3>
      <p>${item.summary}</p>
      <p class="meta">${item.category || 'Uncategorised'} · ${item.status || 'unknown'} · Calls: ${item.callCount}</p>
    `;
    li.addEventListener('click', () => {
      window.location.hash = `api/${item.id}`;
    });
    elements.catalogList.appendChild(li);
  });
};

const serializeParamValue = (param, value) => {
  if (value === undefined || value === null) {
    return '';
  }
  if (param.type === 'object' || param.type === 'array') {
    return JSON.stringify(value, null, 2);
  }
  return value;
};

const createInputField = (param) => {
  const wrapper = document.createElement('div');
  wrapper.className = 'form-field';

  const label = document.createElement('label');
  label.textContent = `${param.name}${param.required ? ' *' : ''}`;
  label.setAttribute('for', `param-${param.location}-${param.name}`);

  let input;
  const defaultValue = serializeParamValue(param, param.defaultValue ?? param.sampleValue ?? '');
  if (param.type === 'object' || param.type === 'array') {
    input = document.createElement('textarea');
  } else {
    input = document.createElement('input');
    input.type = 'text';
  }
  input.id = `param-${param.location}-${param.name}`;
  input.dataset.name = param.name;
  input.dataset.location = param.location;
  input.dataset.type = param.type;
  input.value = defaultValue || '';

  const helper = document.createElement('small');
  helper.className = 'muted';
  helper.textContent = param.description || '';

  wrapper.appendChild(label);
  wrapper.appendChild(input);
  if (param.description) {
    wrapper.appendChild(helper);
  }
  return wrapper;
};

const renderParameterGroups = (groups) => {
  clearChildren(elements.parameterGroups);
  const createTable = (title, items) => {
    if (!items.length) {
      return null;
    }
    const container = document.createElement('div');
    container.className = 'param-group';

    const heading = document.createElement('h4');
    heading.textContent = title;
    container.appendChild(heading);

    const table = document.createElement('table');
    table.innerHTML = `
      <thead>
        <tr>
          <th>Name</th>
          <th>Location</th>
          <th>Type</th>
          <th>Description</th>
          <th>Default</th>
        </tr>
      </thead>
    `;
    const tbody = document.createElement('tbody');
    items.forEach((param) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${param.name}${param.required ? ' *' : ''}</td>
        <td>${param.location}</td>
        <td>${param.type}</td>
        <td>${param.description || ''}</td>
        <td><code>${serializeParamValue(param, param.defaultValue ?? param.sampleValue ?? '')}</code></td>
      `;
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    container.appendChild(table);
    return container;
  };

  const requiredTable = createTable('Required', groups.required || []);
  if (requiredTable) {
    elements.parameterGroups.appendChild(requiredTable);
  }
  const optionalTable = createTable('Optional', groups.optional || []);
  if (optionalTable) {
    elements.parameterGroups.appendChild(optionalTable);
  }
};

const renderTesterParameters = (groups) => {
  clearChildren(elements.testerParameters);
  ['required', 'optional'].forEach((groupKey) => {
    const params = groups[groupKey];
    if (!params || !params.length) {
      return;
    }
    const container = document.createElement('div');
    container.className = 'param-group';
    const heading = document.createElement('h4');
    heading.textContent = `${groupKey.charAt(0).toUpperCase()}${groupKey.slice(1)} Parameters`;
    container.appendChild(heading);

    params.forEach((param) => {
      container.appendChild(createInputField(param));
    });

    elements.testerParameters.appendChild(container);
  });
};

const renderTesterHeaders = (headers) => {
  clearChildren(elements.testerHeaders);
  if (!headers || !headers.length) {
    return;
  }

  const container = document.createElement('div');
  container.className = 'header-group';
  const heading = document.createElement('h4');
  heading.textContent = 'Headers';
  container.appendChild(heading);

  headers.forEach((header) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'form-field';

    const label = document.createElement('label');
    label.textContent = `${header.name}${header.required ? ' *' : ''}`;
    label.setAttribute('for', `header-${header.name}`);

    const input = document.createElement('input');
    input.type = 'text';
    input.id = `header-${header.name}`;
    input.dataset.headerName = header.name;
    input.value = header.sampleValue ?? '';

    const helper = document.createElement('small');
    helper.className = 'muted';
    helper.textContent = header.description || '';

    wrapper.appendChild(label);
    wrapper.appendChild(input);
    if (header.description) {
      wrapper.appendChild(helper);
    }
    container.appendChild(wrapper);
  });

  elements.testerHeaders.appendChild(container);
};

const renderInstructions = (instructions) => {
  clearChildren(elements.apiInstructions);
  (instructions || []).forEach((instruction) => {
    const li = document.createElement('li');
    li.textContent = instruction;
    elements.apiInstructions.appendChild(li);
  });
};

const setTab = (tab) => {
  tabButtons.forEach((button) => {
    button.classList.toggle('active', button.dataset.tab === tab);
  });
  tabContents.forEach((content) => {
    content.classList.toggle('active', content.dataset.tab === tab);
  });
};

tabButtons.forEach((button) => {
  button.addEventListener('click', () => setTab(button.dataset.tab));
});

const buildPayloadFromForm = () => {
  const payload = {
    method: elements.testerMethod.value,
    query: {},
    body: {},
    headers: {}
  };

  const parseValue = (param, value) => {
    if (value === '') {
      return undefined;
    }
    switch (param.type) {
      case 'number':
        return Number(value);
      case 'boolean':
        return value === 'true' || value === '1';
      case 'object':
      case 'array':
        try {
          return JSON.parse(value);
        } catch (error) {
          throw new Error(`Invalid JSON for ${param.name}.`);
        }
      default:
        return value;
    }
  };

  const paramDivs = Array.from(elements.testerParameters.querySelectorAll('.form-field input, .form-field textarea'));
  paramDivs.forEach((input) => {
    const param = {
      name: input.dataset.name,
      location: input.dataset.location,
      type: input.dataset.type
    };
    try {
      const parsed = parseValue(param, input.value.trim());
      if (typeof parsed !== 'undefined') {
        const target = param.location === 'body' ? payload.body : payload.query;
        target[param.name] = parsed;
      }
    } catch (error) {
      throw error;
    }
  });

  const headerInputs = Array.from(elements.testerHeaders.querySelectorAll('input[data-header-name]'));
  headerInputs.forEach((input) => {
    const value = input.value.trim();
    if (value) {
      payload.headers[input.dataset.headerName] = value;
    }
  });

  return payload;
};

const handleTesterSubmit = async (event) => {
  event.preventDefault();
  if (!state.currentApi) {
    return;
  }

  hide(elements.testerError);
  elements.testerError.textContent = '';
  hide(elements.responsePreview);
  elements.testerStatus.textContent = 'Preparing request…';

  let payload;
  try {
    payload = buildPayloadFromForm();
  } catch (error) {
    elements.testerStatus.textContent = '';
    elements.testerError.textContent = error.message;
    show(elements.testerError);
    return;
  }

  await executeTest(payload);
};

const executeTest = async (payload) => {
  hide(elements.testerError);
  elements.testerError.textContent = '';
  show(elements.testerLoader);
  elements.testerStatus.textContent = 'Executing…';
  elements.testerSubmit.disabled = true;

  try {
    const response = await fetch(`/api/catalog/${state.currentApi.id}/run`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });

    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
      const message = data && data.message ? data.message : 'Unexpected error executing request.';
      throw new Error(message);
    }

    elements.testerStatus.textContent = 'Success';
    renderResponse(data);
    updateCallCount(data.callCount);
  } catch (error) {
    elements.testerError.textContent = error.message;
    show(elements.testerError);
    elements.testerStatus.textContent = '';
  } finally {
    hide(elements.testerLoader);
    elements.testerSubmit.disabled = false;
  }
};

const updateCallCount = (count) => {
  if (typeof count === 'number') {
    elements.apiCallCount.textContent = count;
  }
};

const renderMediaPreview = (contentType, base64) => {
  clearChildren(elements.mediaPreview);
  if (!contentType || !base64) {
    const message = document.createElement('p');
    message.textContent = 'Media preview not available for this response.';
    elements.mediaPreview.appendChild(message);
    return;
  }

  if (contentType.startsWith('image/')) {
    const img = document.createElement('img');
    img.src = `data:${contentType};base64,${base64}`;
    img.alt = 'API response image';
    elements.mediaPreview.appendChild(img);
    return;
  }

  if (contentType.startsWith('video/')) {
    const video = document.createElement('video');
    video.controls = true;
    video.src = `data:${contentType};base64,${base64}`;
    elements.mediaPreview.appendChild(video);
    return;
  }

  if (contentType.startsWith('audio/')) {
    const audio = document.createElement('audio');
    audio.controls = true;
    audio.src = `data:${contentType};base64,${base64}`;
    elements.mediaPreview.appendChild(audio);
    return;
  }

  if (contentType.includes('text/html')) {
    const iframe = document.createElement('iframe');
    iframe.src = `data:text/html;base64,${base64}`;
    elements.mediaPreview.appendChild(iframe);
    return;
  }

  const fallback = document.createElement('p');
  fallback.textContent = 'No media renderer available for this content type.';
  elements.mediaPreview.appendChild(fallback);
};

const renderResponse = (data) => {
  hide(elements.testerError);
  show(elements.responsePreview);
  setTab('json');

  elements.responseStatus.textContent = `Status: ${data.status} ${data.statusText || ''}`;
  elements.responseDuration.textContent = `Time: ${data.durationMs} ms`;
  elements.responseSize.textContent = `Size: ${formatBytes(data.size)}`;
  elements.responseContentType.textContent = `Content-Type: ${data.contentType}`;

  if (data.body && data.body.json) {
    elements.responseJson.innerHTML = syntaxHighlight(JSON.stringify(data.body.json, null, 2));
  } else {
    elements.responseJson.innerHTML = 'No JSON available.';
  }

  if (data.body && data.body.text) {
    elements.responseRaw.textContent = data.body.text;
  } else {
    elements.responseRaw.textContent = 'No raw response available.';
  }

  renderMediaPreview(data.contentType, data.body ? data.body.base64 : null);
};

const populateMethodSelect = (methods) => {
  clearChildren(elements.testerMethod);
  (methods || ['GET']).forEach((method) => {
    const option = document.createElement('option');
    option.value = method;
    option.textContent = method;
    elements.testerMethod.appendChild(option);
  });
};

const renderApiDetail = (api) => {
  state.currentApi = api;
  elements.apiName.textContent = api.name;
  elements.apiSummary.textContent = api.summary || '';
  elements.apiStatus.textContent = `Status: ${api.status}`;
  elements.apiEndpoint.textContent = api.endpoint;
  elements.apiMethods.textContent = (api.allowedMethods || []).join(', ');
  elements.apiCategory.textContent = api.category || 'Uncategorised';
  updateCallCount(api.callCount || 0);

  renderParameterGroups(api.parameterGroups || { required: [], optional: [] });
  renderTesterParameters(api.parameterGroups || { required: [], optional: [] });
  renderTesterHeaders(api.headers);
  renderInstructions(api.instructions);
  populateMethodSelect(api.allowedMethods);
  if (api.sample && api.sample.method) {
    elements.testerMethod.value = api.sample.method;
  }

  elements.runSample.disabled = !api.sample;
  elements.copyCurl.disabled = !api.sampleCurl;

  elements.testerStatus.textContent = '';
  hide(elements.testerError);
  elements.testerError.textContent = '';
  hide(elements.testerLoader);
  hide(elements.responsePreview);
  elements.responseJson.innerHTML = '';
  elements.responseRaw.textContent = '';
  clearChildren(elements.mediaPreview);
  elements.testerSubmit.disabled = false;
};

const fetchCatalog = async () => {
  hide(elements.catalogError);
  elements.catalogError.textContent = '';
  show(elements.catalogLoader);
  try {
    const response = await fetch('/api/catalog');
    if (!response.ok) {
      throw new Error('Failed to load API catalog.');
    }
    const data = await response.json();
    state.catalog = data.items || [];
    renderCatalog(state.catalog);
  } catch (error) {
    elements.catalogError.textContent = error.message;
    show(elements.catalogError);
  } finally {
    hide(elements.catalogLoader);
  }
};

const fetchApiDetail = async (id) => {
  hide(elements.detailError);
  elements.detailError.textContent = '';
  hide(elements.responsePreview);
  show(elements.detailLoader);
  hide(elements.detailArticle);

  try {
    const response = await fetch(`/api/catalog/${id}`);
    if (!response.ok) {
      throw new Error('Could not load API details.');
    }
    const data = await response.json();
    renderApiDetail(data);
    show(elements.detailArticle);
  } catch (error) {
    elements.detailError.textContent = error.message;
    show(elements.detailError);
  } finally {
    hide(elements.detailLoader);
  }
};

const showCatalog = () => {
  show(elements.catalogView);
  hide(elements.detailView);
  elements.runSample.disabled = true;
  elements.copyCurl.disabled = true;
};

const showDetail = (id) => {
  hide(elements.catalogView);
  show(elements.detailView);
  fetchApiDetail(id);
};

const handleHashChange = () => {
  const hash = window.location.hash.slice(1);
  if (hash.startsWith('api/')) {
    const id = hash.split('/')[1];
    if (id) {
      showDetail(id);
      return;
    }
  }
  showCatalog();
};

elements.backHome.addEventListener('click', () => {
  window.location.hash = '';
});

elements.runSample.addEventListener('click', () => {
  if (!state.currentApi) {
    return;
  }
  executeTest({ useSample: true, method: state.currentApi.sample?.method });
});

elements.copyCurl.addEventListener('click', async () => {
  if (!state.currentApi || !state.currentApi.sampleCurl) {
    return;
  }
  try {
    await navigator.clipboard.writeText(state.currentApi.sampleCurl);
    elements.testerStatus.textContent = 'cURL copied to clipboard.';
  } catch (error) {
    elements.testerStatus.textContent = 'Unable to copy cURL. Please copy manually.';
  }
});

elements.testerForm.addEventListener('submit', handleTesterSubmit);

window.addEventListener('hashchange', handleHashChange);

fetchCatalog().then(() => {
  handleHashChange();
});
