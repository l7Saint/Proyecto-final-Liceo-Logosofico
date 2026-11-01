import { requireFields } from '../core/validate.js';

const API = 'tiene';

/**
 * Wrapper for `backend/api/apis/tiene.php` — the N:M relation between users and
 * time slots. The user id always comes from the Bearer token, never from the body.
 */
export class TieneApi {
  /** @param {import('../urbanaut.js').UrbanautClient} client */
  constructor(client) {
    this.client = client;
  }

  /**
   * POST /tiene/asignar — claims a time slot for the authenticated user.
   *
   * @param {{ numero_hora: number, dia_semana: string }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean }>}
   */
  async asignar({ numero_hora, dia_semana } = {}, options = {}) {
    requireFields({ numero_hora, dia_semana }, ['numero_hora', 'dia_semana'], {
      api: API,
      endpoint: 'asignar'
    });

    return this.client.http.request(API, 'asignar', {
      method: 'POST',
      data: { numero_hora, dia_semana },
      signal: options.signal
    });
  }

  /**
   * GET /tiene/obtener — lists the slots claimed by the authenticated user.
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<object[]>} array of `{ id_usuario, numero_hora, dia_semana }`
   */
  async obtener(options = {}) {
    const body = await this.client.http.request(API, 'obtener', {
      method: 'GET',
      signal: options.signal
    });
    return body?.horarios ?? [];
  }

  /**
   * GET /tiene/obtenertodos — returns the full horario catalogue (user-level,
   * so non-admin signup users can read it; `horario.obtener` stays admin-only).
   *
   * Response: `{ success: true, horarios: [{ numero_hora, dia_semana, horario }] }`
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<Array<{ numero_hora: number, dia_semana: string, horario: string }>>}
   */
  async obtenertodos(options = {}) {
    const body = await this.client.http.request(API, 'obtenertodos', {
      method: 'GET',
      signal: options.signal
    });
    return body?.horarios ?? [];
  }
}
