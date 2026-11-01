import { requireFields } from '../core/validate.js';

const API = 'vehiculo';

/**
 * Wrapper for `backend/api/apis/vehiculo.php` — vehicles owned by the authenticated user.
 * Ownership is enforced server-side (403 on foreign ids).
 */
export class VehiculoApi {
  /** @param {import('../urbanaut.js').UrbanautClient} client */
  constructor(client) {
    this.client = client;
  }

  /**
   * POST /vehiculo/crear
   *
   * @param {{ marca: string, modelo: string, color: string, tamano: string }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean, id: number }>}
   */
  async crear({ marca, modelo, color, tamano } = {}, options = {}) {
    requireFields({ marca, modelo, color, tamano }, ['marca', 'modelo', 'color', 'tamano'], {
      api: API,
      endpoint: 'crear'
    });

    return this.client.http.request(API, 'crear', {
      method: 'POST',
      data: { marca, modelo, color, tamano },
      signal: options.signal
    });
  }

  /**
   * GET /vehiculo/obtener — vehicles belonging to the authenticated user.
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<object[]>}
   */
  async obtener(options = {}) {
    const body = await this.client.http.request(API, 'obtener', {
      method: 'GET',
      signal: options.signal
    });
    return body?.vehiculos ?? [];
  }

  /**
   * PUT /vehiculo/modificar — partial update; only the provided fields are sent.
   *
   * @param {{ id: number, marca?: string, modelo?: string, color?: string, tamano?: string }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean }>}
   */
  async modificar(payload = {}, options = {}) {
    const { id, marca, modelo, color, tamano } = payload;
    requireFields({ id }, ['id'], { api: API, endpoint: 'modificar' });

    const data = { id };
    const optional = { marca, modelo, color, tamano };
    for (const [field, value] of Object.entries(optional)) {
      if (value !== undefined) data[field] = value;
    }

    return this.client.http.request(API, 'modificar', {
      method: 'PUT',
      data,
      signal: options.signal
    });
  }

  /**
   * DELETE /vehiculo/eliminar
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
