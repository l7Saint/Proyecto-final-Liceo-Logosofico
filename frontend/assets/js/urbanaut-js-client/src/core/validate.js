import { ValidationError } from './errors.js';

/**
 * Mirrors `Api::checkParameters()` from the PHP backend: a parameter is considered
 * present when it is neither `undefined` nor `null` (matching PHP's `isset()`).
 *
 * Failing here avoids a pointless round-trip that would end in a 400 `bad_request`.
 *
 * @param {object|null|undefined} data
 * @param {string[]} fields
 * @param {{ api?: string, endpoint?: string }} [context]
 * @returns {object} the original data object
 * @throws {ValidationError}
 */
export function requireFields(data, fields, context = {}) {
  const source = data ?? {};
  const missing = fields.filter(
    (field) => source[field] === undefined || source[field] === null
  );

  if (missing.length > 0) {
    const plural = missing.length > 1 ? 's' : '';
    throw new ValidationError(
      `Missing required parameter${plural}: ${missing.join(', ')}.`,
      { ...context, details: { missing } }
    );
  }

  return source;
}

/**
 * Ensures a value is an array (used for `cuadras_id`).
 * @param {any} value
 * @param {string} field
 * @param {{ api?: string, endpoint?: string }} [context]
 * @returns {any[]}
 * @throws {ValidationError}
 */
export function requireArray(value, field, context = {}) {
  if (!Array.isArray(value)) {
    throw new ValidationError(`Parameter "${field}" must be an array.`, {
      ...context,
      details: { field, received: typeof value }
    });
  }
  return value;
}
