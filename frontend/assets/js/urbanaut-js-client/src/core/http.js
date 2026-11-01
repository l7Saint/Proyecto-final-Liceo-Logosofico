import { HttpError, NetworkError } from './errors.js';
import { DEFAULT_BASE_URL, DEFAULT_TIMEOUT } from '../constants.js';

/**
 * Parses a response body that may not be valid JSON.
 *
 * The PHP backend has several endpoints that `echo` their own JSON payload and then
 * fall through, so `Main::run()` appends a second `json_encode(null)`. The wire body
 * therefore looks like `{"success":true,"id":1}null`. We try a strict parse first and
 * then fall back to extracting the first balanced JSON value.
 *
 * @param {string} rawText
 * @returns {any}
 */
export function parseBody(rawText) {
  if (typeof rawText !== 'string') return null;

  const text = rawText.trim();
  if (text === '') return null;

  try {
    return JSON.parse(text);
  } catch {
    /* not strict JSON — try the lenient path below */
  }

  const start = text.search(/[\[{]/);
  if (start === -1) return text;

  const source = text.slice(start);
  let depth = 0;
  let inString = false;
  let escaped = false;

  for (let i = 0; i < source.length; i += 1) {
    const char = source[i];

    if (inString) {
      if (escaped) escaped = false;
      else if (char === '\\') escaped = true;
      else if (char === '"') inString = false;
      continue;
    }

    if (char === '"') {
      inString = true;
    } else if (char === '{' || char === '[') {
      depth += 1;
    } else if (char === '}' || char === ']') {
      depth -= 1;
      if (depth === 0) {
        try {
          return JSON.parse(source.slice(0, i + 1));
        } catch {
          return text;
        }
      }
    }
  }

  return text;
}

/**
 * Thin, framework-agnostic HTTP transport for the Urbanaut backend.
 *
 * Routing is `<baseUrl>/<api>/<endpoint>`; all payloads are JSON, including on GET,
 * because the PHP router only reads `php://input`.
 */
export class HttpClient {
  /**
   * @param {object} [options]
   * @param {string} [options.baseUrl]
   * @param {typeof fetch|null} [options.fetchImpl]
   * @param {() => (string|null)} [options.tokenProvider]
   * @param {number} [options.timeout]
   * @param {RequestCredentials} [options.credentials]
   * @param {(error: import('./errors.js').UrbanautError) => void} [options.onError]
   */
  constructor({
    baseUrl = DEFAULT_BASE_URL,
    fetchImpl = null,
    tokenProvider = () => null,
    timeout = DEFAULT_TIMEOUT,
    credentials = 'omit',
    onError = null
  } = {}) {
    this.baseUrl = String(baseUrl).replace(/\/+$/, '');
    this.fetchImpl = fetchImpl ?? ((...args) => globalThis.fetch(...args));
    this.tokenProvider = tokenProvider;
    this.timeout = timeout;
    this.credentials = credentials;
    this.onError = onError;
  }

  /**
   * Builds the absolute path for an API endpoint.
   * @param {string} api
   * @param {string} endpoint
   * @returns {string}
   */
  url(api, endpoint) {
    return `${this.baseUrl}/${api}/${endpoint}`;
  }

  /**
   * Performs a request and returns the parsed body of a successful response.
   *
   * @param {string} api
   * @param {string} endpoint
   * @param {object} [options]
   * @param {string} [options.method]
   * @param {object|null} [options.data]   Serialised as JSON. Sent for GET too.
   * @param {boolean} [options.auth]       Attach the Bearer token (default true)
   * @param {object} [options.headers]
   * @param {AbortSignal|null} [options.signal]
   * @returns {Promise<any>}
   */
  async request(api, endpoint, options = {}) {
    const {
      method = 'GET',
      data = null,
      auth = true,
      headers = {},
      signal = null
    } = options;

    const url = this.url(api, endpoint);
    const finalHeaders = { Accept: 'application/json', ...headers };
    const init = {
      method,
      headers: finalHeaders,
      credentials: this.credentials
    };

    if (data !== null && data !== undefined) {
      finalHeaders['Content-Type'] = 'application/json';
      init.body = JSON.stringify(data);
    }

    if (auth) {
      const token = this.tokenProvider();
      if (token) finalHeaders.Authorization = `Bearer ${token}`;
    }

    const controller = new AbortController();
    const timer = this.timeout > 0
      ? setTimeout(() => controller.abort(new Error('Timeout')), this.timeout)
      : null;

    let detach = null;
    if (signal) {
      if (signal.aborted) {
        controller.abort(signal.reason);
      } else {
        detach = () => controller.abort(signal.reason);
        signal.addEventListener('abort', detach, { once: true });
      }
    }
    init.signal = controller.signal;

    let response;
    try {
      response = await this.fetchImpl(url, init);
    } catch (cause) {
      if (signal?.aborted) {
        throw cause instanceof Error ? cause : new Error('Request aborted.');
      }
      const error = controller.signal.aborted
        ? new NetworkError(`Request to ${url} timed out after ${this.timeout} ms.`, {
            api, endpoint, cause
          })
        : new NetworkError(`Network request to ${url} failed.`, { api, endpoint, cause });
      this.onError?.(error);
      throw error;
    } finally {
      if (timer) clearTimeout(timer);
      if (detach) signal.removeEventListener('abort', detach);
    }

    let rawText = '';
    try {
      rawText = await response.text();
    } catch (cause) {
      const error = new NetworkError(`Could not read the response body from ${url}.`, {
        api, endpoint, status: response.status, cause
      });
      this.onError?.(error);
      throw error;
    }

    const body = parseBody(rawText);

    if (!response.ok) {
      const isObject = body !== null && typeof body === 'object' && !Array.isArray(body);
      const code = isObject && typeof body.error === 'string' ? body.error : null;
      const message =
        isObject && typeof body.message === 'string' && body.message.length > 0
          ? body.message
          : `Request to ${url} failed with HTTP ${response.status}.`;

      const error = new HttpError(message, {
        status: response.status,
        code,
        body,
        api,
        endpoint
      });
      this.onError?.(error);
      throw error;
    }

    return body;
  }
}
