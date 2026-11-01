import { requireFields } from '../core/validate.js';

const API = 'horario';

/**
 * Wrapper for `backend/api/apis/horario.php` — administrative time-slot catalogue.
 *
 * `dia_semana` must be one of `DIAS_SEMANA` (`lunes` … `sabado`); `Horario` throws
 * otherwise and the backend surfaces it as a 500.
 */
export class HorarioApi {
  /** @param {import('../urbanaut.js').UrbanautClient} client */
  constructor(client) {
    this.client = client;
  }

  /**
   * POST /horario/crear
   *
   * @param {{ numero_hora: number, dia_semana: string, horario: string }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean }>}
   */
  async crear({ numero_hora, dia_semana, horario } = {}, options = {}) {
    requireFields({ numero_hora, dia_semana, horario },
      ['numero_hora', 'dia_semana', 'horario'],
      { api: API, endpoint: 'crear' });

    return this.client.http.request(API, 'crear', {
      method: 'POST',
      data: { numero_hora, dia_semana, horario },
      signal: options.signal
    });
  }

  /**
   * GET /horario/obtener
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<object[]>} array of `horario` records
   */
  async obtener(options = {}) {
    const body = await this.client.http.request(API, 'obtener', {
      method: 'GET',
      signal: options.signal
    });
    return body?.horarios ?? [];
  }

  /**
   * DELETE /horario/eliminar — identifies the row by the composite key.
   *
   * @param {{ numero_hora: number, dia_semana: string }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean }>}
   */
  async eliminar({ numero_hora, dia_semana } = {}, options = {}) {
    requireFields({ numero_hora, dia_semana }, ['numero_hora', 'dia_semana'], {
      api: API,
      endpoint: 'eliminar'
    });

    return this.client.http.request(API, 'eliminar', {
      method: 'DELETE',
      data: { numero_hora, dia_semana },
      signal: options.signal
    });
  }
}
