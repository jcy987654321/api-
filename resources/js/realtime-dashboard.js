(function () {
    const root = document.getElementById('realtime-dashboard');
    if (!root) {
        return;
    }

    const streamUrl = root.getAttribute('data-stream-url');
    const realtimeConfig = safeJsonParse(root.getAttribute('data-realtime-config')) || {};
    const reconnect = realtimeConfig.reconnect || {};

    const maxRetries = Number.isFinite(reconnect.max_retries) ? reconnect.max_retries : 10;
    const baseDelayMs = Number.isFinite(reconnect.base_delay_ms) ? reconnect.base_delay_ms : 500;
    const maxDelayMs = Number.isFinite(reconnect.max_delay_ms) ? reconnect.max_delay_ms : 15000;

    const statusDot = root.querySelector('[data-rt-status-dot]');
    const statusText = root.querySelector('[data-rt-status-text]');
    const updatedAt = root.querySelector('[data-rt-updated-at]');

    const numberEls = Array.from(root.querySelectorAll('[data-rt-number][data-key]'));
    const textEls = Array.from(root.querySelectorAll('[data-rt-text][data-key]'));
    const deltaEls = Array.from(root.querySelectorAll('[data-rt-delta][data-key]'));

    const topApisEl = root.querySelector('[data-rt-top-apis]');
    const activitiesEl = root.querySelector('[data-rt-activities]');

    const state = {
        source: null,
        reconnectTimer: null,
        retryCount: 0,
        data: {},
        lastDelta: {},
        lastSeenAt: null,
    };

    function safeJsonParse(value) {
        if (!value) return null;
        try {
            return JSON.parse(value);
        } catch {
            return null;
        }
    }

    function setStatus(type, message) {
        if (!statusDot || !statusText) return;

        statusDot.classList.remove('rt-dot-connected', 'rt-dot-connecting', 'rt-dot-error');
        if (type === 'connected') statusDot.classList.add('rt-dot-connected');
        if (type === 'connecting') statusDot.classList.add('rt-dot-connecting');
        if (type === 'error') statusDot.classList.add('rt-dot-error');

        statusText.textContent = message;
    }

    function formatNumber(value, format) {
        if (value === null || value === undefined || value === '') return '--';

        if (format === 'percent') {
            const n = Number(value);
            if (!Number.isFinite(n)) return '--';
            return `${n.toFixed(2)}%`;
        }

        if (typeof value === 'number') {
            return String(Math.round(value));
        }

        return String(value);
    }

    function getByPath(obj, path) {
        const parts = String(path).split('.');
        let cur = obj;
        for (const p of parts) {
            if (!cur || typeof cur !== 'object' || !(p in cur)) {
                return undefined;
            }
            cur = cur[p];
        }
        return cur;
    }

    function isPlainObject(value) {
        return !!value && typeof value === 'object' && !Array.isArray(value);
    }

    function deepMerge(target, patch) {
        for (const [key, val] of Object.entries(patch || {})) {
            if (isPlainObject(val) && isPlainObject(target[key])) {
                deepMerge(target[key], val);
            } else {
                target[key] = val;
            }
        }
        return target;
    }

    function animateNumber(el, toValue, format) {
        const prevRaw = el.getAttribute('data-rt-prev');
        const from = prevRaw === null ? null : Number(prevRaw);
        const to = Number(toValue);

        if (!Number.isFinite(to) || (from !== null && !Number.isFinite(from))) {
            el.textContent = formatNumber(toValue, format);
            el.setAttribute('data-rt-prev', String(toValue));
            return;
        }

        if (from === null) {
            el.textContent = formatNumber(to, format);
            el.setAttribute('data-rt-prev', String(to));
            return;
        }

        if (from === to) {
            el.textContent = formatNumber(to, format);
            el.setAttribute('data-rt-prev', String(to));
            return;
        }

        const start = performance.now();
        const duration = 260;

        function tick(now) {
            const t = Math.min(1, (now - start) / duration);
            const current = from + (to - from) * t;

            if (format === 'percent') {
                el.textContent = `${current.toFixed(2)}%`;
            } else {
                el.textContent = String(Math.round(current));
            }

            if (t < 1) {
                requestAnimationFrame(tick);
            } else {
                el.setAttribute('data-rt-prev', String(to));
            }
        }

        requestAnimationFrame(tick);
    }

    function flash(el, direction) {
        el.classList.remove('rt-up', 'rt-down');
        if (direction === 'up') el.classList.add('rt-up');
        if (direction === 'down') el.classList.add('rt-down');

        el.classList.add('rt-flash');
        window.setTimeout(() => el.classList.remove('rt-flash'), 220);
        window.setTimeout(() => el.classList.remove('rt-up', 'rt-down'), 900);
    }

    function renderDelta(delta, format) {
        const n = Number(delta);
        if (!Number.isFinite(n) || Math.abs(n) < 0.00001) {
            return { text: '', dir: null };
        }

        const dir = n > 0 ? 'up' : 'down';
        const arrow = n > 0 ? '↑' : '↓';
        const abs = Math.abs(n);

        if (format === 'percent') {
            return { text: `${arrow}${abs.toFixed(2)}%`, dir };
        }

        return { text: `${arrow}${Math.round(abs)}`, dir };
    }

    function render() {
        for (const el of numberEls) {
            const key = el.getAttribute('data-key');
            const format = el.getAttribute('data-format');
            const value = getByPath(state.data, key);
            const delta = getByPath(state.lastDelta, key);

            animateNumber(el, value, format);

            const { dir } = renderDelta(delta, format);
            if (dir) {
                flash(el, dir);
            }
        }

        for (const el of textEls) {
            const key = el.getAttribute('data-key');
            const value = getByPath(state.data, key);
            el.textContent = value === undefined ? '--' : String(value);
        }

        for (const el of deltaEls) {
            const key = el.getAttribute('data-key');
            const pairedValueEl = numberEls.find((n) => n.getAttribute('data-key') === key);
            const format = pairedValueEl?.getAttribute('data-format');
            const delta = getByPath(state.lastDelta, key);
            const { text, dir } = renderDelta(delta, format);

            el.textContent = text;
            el.classList.remove('rt-up', 'rt-down');
            if (dir === 'up') el.classList.add('rt-up');
            if (dir === 'down') el.classList.add('rt-down');
        }

        if (topApisEl) {
            const list = getByPath(state.data, 'api.top_apis') || [];
            topApisEl.innerHTML = '';

            if (!Array.isArray(list) || list.length === 0) {
                const li = document.createElement('li');
                li.className = 'rt-muted';
                li.textContent = '--';
                topApisEl.appendChild(li);
            } else {
                list.forEach((item) => {
                    const li = document.createElement('li');
                    li.textContent = `${item.endpoint} (${item.calls})`;
                    topApisEl.appendChild(li);
                });
            }
        }

        if (activitiesEl) {
            const list = getByPath(state.data, 'activities') || [];
            activitiesEl.innerHTML = '';

            if (!Array.isArray(list) || list.length === 0) {
                const li = document.createElement('li');
                li.className = 'rt-muted';
                li.textContent = '--';
                activitiesEl.appendChild(li);
            } else {
                list.forEach((item) => {
                    const li = document.createElement('li');
                    li.innerHTML = `<span class="rt-muted">[${escapeHtml(item.timestamp)}]</span> ${escapeHtml(item.message)}`;
                    activitiesEl.appendChild(li);
                });
            }
        }

        if (updatedAt) {
            updatedAt.textContent = state.lastSeenAt ? new Date(state.lastSeenAt).toLocaleTimeString() : '--';
        }
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function onInit(payload) {
        state.data = payload.data || {};
        state.lastDelta = {};
        state.lastSeenAt = payload.timestamp || new Date().toISOString();
        render();
    }

    function onUpdate(payload) {
        const changed = payload.changed || {};
        const delta = payload.delta || {};

        deepMerge(state.data, changed);
        state.lastDelta = delta;
        state.lastSeenAt = payload.timestamp || new Date().toISOString();
        render();
    }

    function connect() {
        if (!streamUrl) {
            setStatus('error', 'Missing stream URL');
            return;
        }

        if (state.source) {
            state.source.close();
            state.source = null;
        }

        if (state.reconnectTimer) {
            clearTimeout(state.reconnectTimer);
            state.reconnectTimer = null;
        }

        if (!window.EventSource) {
            setStatus('connecting', 'EventSource not supported, falling back to polling');
            startPolling();
            return;
        }

        setStatus('connecting', 'Connecting');

        const source = new EventSource(streamUrl);
        state.source = source;

        source.addEventListener('init', (event) => {
            state.retryCount = 0;
            setStatus('connected', 'Connected');

            const payload = safeJsonParse(event.data) || {};
            onInit(payload);
        });

        source.addEventListener('update', (event) => {
            const payload = safeJsonParse(event.data) || {};
            onUpdate(payload);
        });

        source.addEventListener('ping', (event) => {
            const payload = safeJsonParse(event.data) || {};
            state.lastSeenAt = payload.timestamp || new Date().toISOString();
            if (updatedAt) {
                updatedAt.textContent = new Date(state.lastSeenAt).toLocaleTimeString();
            }
        });

        source.addEventListener('close', (event) => {
            const payload = safeJsonParse(event.data) || {};
            logDebug('Server requested close', payload);
            source.close();
            scheduleReconnect();
        });

        source.onerror = () => {
            if (source.readyState === EventSource.CLOSED) {
                scheduleReconnect();
                return;
            }

            setStatus('error', 'Connection error');
            source.close();
            scheduleReconnect();
        };
    }

    function scheduleReconnect() {
        if (state.retryCount >= maxRetries) {
            setStatus('error', `Disconnected (max retries ${maxRetries})`);
            return;
        }

        const delay = Math.min(maxDelayMs, baseDelayMs * Math.pow(2, state.retryCount));
        state.retryCount += 1;

        setStatus('connecting', `Reconnecting in ${Math.round(delay / 100) / 10}s (attempt ${state.retryCount}/${maxRetries})`);

        state.reconnectTimer = setTimeout(() => {
            state.reconnectTimer = null;
            connect();
        }, delay);
    }

    function startPolling() {
        const intervalMs = Math.max(1000, Number(realtimeConfig.update_interval_ms || 1000));

        async function tick() {
            try {
                const res = await fetch(streamUrl, { headers: { Accept: 'application/json' } });
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`);
                }
                const payload = await res.json();
                onInit({ data: payload, timestamp: new Date().toISOString() });
                setStatus('connected', 'Polling');
            } catch (e) {
                logError('Polling failed', e);
                setStatus('error', 'Polling failed');
            } finally {
                state.reconnectTimer = setTimeout(tick, intervalMs);
            }
        }

        tick();
    }

    function logDebug(...args) {
        if (window && window.console && window.console.debug) {
            window.console.debug('[realtime-dashboard]', ...args);
        }
    }

    function logError(...args) {
        if (window && window.console && window.console.error) {
            window.console.error('[realtime-dashboard]', ...args);
        }
    }

    window.addEventListener('beforeunload', () => {
        if (state.source) {
            state.source.close();
        }
    });

    connect();
})();
