import { requireArray, requireFields } from '../core/validate.js';

const API = 'estacionar';

/**
 * Wrapper for `backend/api/apis/estacionar.php` — public parking-probability scoring.
 */
export class EstacionarApi {
  /** @param {import('../urbanaut.js').UrbanautClient} client */
  constructor(client) {
    this.client = client;
  }

  /**
   * GET /estacionar/probabilidad
   *
   * NOTE: `Main::run()` reads parameters from `php://input` for every HTTP method,
   * so this GET is issued with a JSON body — that is required, not a mistake.
   *
   * @param {{ cuadras_id: number[], dia_semana: string, hora: number }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<Array<{ cuadra_id: number, puntaje: number, confianza: number }>>}
   */
  async probabilidad({ cuadras_id, dia_semana, hora } = {}, options = {}) {
    const context = { api: API, endpoint: 'probabilidad' };

    requireFields({ cuadras_id, dia_semana, hora }, ['cuadras_id', 'dia_semana', 'hora'], context);
    requireArray(cuadras_id, 'cuadras_id', context);

    const body = await this.client.http.request(API, 'probabilidad', {
      method: 'GET',
      auth: false,
      data: { cuadras_id, dia_semana, hora },
      signal: options.signal
    });

    return Array.isArray(body) ? body : [];
  }
}
