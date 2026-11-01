import { requireFields } from '../core/validate.js';

const API = 'admin';

const MUTABLE_FIELDS = ['nombre', 'apellido', 'email', 'contrasena', 'inactivo', 'es_admin'];

/**
 * Wrapper for `backend/api/apis/admin.php` — every endpoint requires an admin Bearer token.
 */
export class AdminApi {
  /** @param {import('../urbanaut.js').UrbanautClient} client */
  constructor(client) {
    this.client = client;
  }

  /**
   * POST /admin/crear — creates a user with explicit flags.
   *
   * `inactivo` and `es_admin` are read by the backend without being declared in the
   * endpoint's `parameters` list, so they are always sent (defaulting to 0).
   *
   * @param {{ nombre: string, apellido: string, email: string, contrasena: string, inactivo?: 0|1|boolean, es_admin?: 0|1|boolean }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean, id: number }>}
   */
  async crear(payload = {}, options = {}) {
    const { nombre, apellido, email, contrasena, inactivo = 0, es_admin = 0 } = payload;

    requireFields({ nombre, apellido, email, contrasena },
      ['nombre', 'apellido', 'email', 'contrasena'],
      { api: API, endpoint: 'crear' });

    return this.client.http.request(API, 'crear', {
      method: 'POST',
      data: {
        nombre,
        apellido,
        email,
        contrasena,
        inactivo: inactivo ? 1 : 0,
        es_admin: es_admin ? 1 : 0
      },
      signal: options.signal
    });
  }

  /**
   * GET /admin/obtener — lists every registered user.
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<object[]>} array of `usuario` records
   */
  async obtener(options = {}) {
    const body = await this.client.http.request(API, 'obtener', {
      method: 'GET',
      signal: options.signal
    });
    return body?.usuarios ?? [];
  }

  /**
   * PUT /admin/modificar — partial update; only the provided fields are sent.
   *
   * @param {{ id: number, nombre?: string, apellido?: string, email?: string, contrasena?: string, inactivo?: 0|1|boolean, es_admin?: 0|1|boolean }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean }>}
   */
  async modificar(payload = {}, options = {}) {
    const { id } = payload;
    requireFields({ id }, ['id'], { api: API, endpoint: 'modificar' });

    const data = { id };
    for (const field of MUTABLE_FIELDS) {
      const value = payload[field];
      if (value === undefined) continue;
      data[field] = field === 'inactivo' || field === 'es_admin'
        ? (value ? 1 : 0)
        : value;
    }

    return this.client.http.request(API, 'modificar', {
      method: 'PUT',
      data,
      signal: options.signal
    });
  }

  /**
   * DELETE /admin/eliminar — removes a user. An admin cannot delete itself (400).
   *
   * @param {{ id: number }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean }>}
   */
  async eliminar({ id } = {}, options = {}) {
    requireFields({ id }, ['id'], { api: API, endpoint: 'eliminar' });

    return this.client.http.request(API, 'eliminar', {
      method: 'DELETE',
      data: { id },
      signal: options.signal
    });
  }
}
