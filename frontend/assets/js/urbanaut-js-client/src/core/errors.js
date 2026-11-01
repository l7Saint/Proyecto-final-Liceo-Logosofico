/**
 * Error hierarchy for the Urbanaut client.
 *
 *   UrbanautError
 *     ├── ValidationError   (client-side, request never sent)
 *     ├── NetworkError      (fetch rejected: offline, CORS, timeout)
 *     └── HttpError         (response status outside 200-299)
 */

export class UrbanautError extends Error {
  /**
   * @param {string} message
   * @param {object} [options]
   * @param {number} [options.status]    HTTP status (0 when no response was received)
   * @param {string|null} [options.code] Backend `error` discriminator
   * @param {any} [options.body]         Parsed response body
   * @param {string|null} [options.api]  API name, e.g. 'vehiculo'
   * @param {string|null} [options.endpoint] Endpoint name, e.g. 'crear'
   * @param {any} [options.details]      Extra structured context
   * @param {any} [options.cause]        Underlying error
   */
  constructor(message, options = {}) {
    super(message, options.cause !== undefined ? { cause: options.cause } : undefined);
    this.name = new.target.name;
    this.status = options.status ?? 0;
    this.code = options.code ?? null;
    this.body = options.body ?? null;
    this.api = options.api ?? null;
    this.endpoint = options.endpoint ?? null;
    this.details = options.details ?? null;
  }
}

export class ValidationError extends UrbanautError {}

export class NetworkError extends UrbanautError {}

export class HttpError extends UrbanautError {}
