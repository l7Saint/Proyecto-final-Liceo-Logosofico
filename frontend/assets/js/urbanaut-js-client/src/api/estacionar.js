import { requireArray, requireFields } from '../core/validate.js';

const API = 'estacionar';

/**
 * Wrapper for `backend/api/apis/estacionar.php`.
 *
 * Endpoints:
 *   - `probabilidad` (GET, public) — heuristic parking score for one or more blocks
 *   - `estacionar`   (POST, user)  — registers a parking event; returns its id
 *   - `liberar`      (POST)        — marks a parking event as released (sets hora_fin)
 *
 * NOTE: The backend `liberar` endpoint does not currently enforce authentication
 * (its API-level `prerun` session check is commented out), so the client treats it
 * as effectively public. The Bearer token is still attached when available so the
 * wrapper keeps working if the backend re-enables the check.
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

  /**
   * POST /estacionar/estacionar — registers a parking event for the authenticated
   * user at the block whose door is closest to the given coordinates.
   *
   * The backend reverse-geocodes `(latitud, longitud)` through Nominatim, looks up
   * the matching `Cuadra`, and inserts a row in `Estaciona_en`. If the location
   * falls outside the supported area it responds `404` ("That block is not in the
   * area"); if Nominatim is unreachable it responds `500`.
   *
   * A valid session is required, but the backend reports a missing one as `500`
   * (not `401`) because `checkSession(...)` returns `false` and the subsequent
   * `->id_usuario` property access fatals; call it after a successful login.
   *
   * Because the endpoint echoes its own JSON and then falls through, the wire body
   * is `{"success":true,"id_est":N}null`; `parseBody()` extracts the first object.
   *
   * @param {{ latitud: number|string, longitud: number|string, id_vehiculo: number }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean, id_est: number|null }>}
   */
  async estacionar({ latitud, longitud, id_vehiculo } = {}, options = {}) {
    const context = { api: API, endpoint: 'estacionar' };

    requireFields({ latitud, longitud, id_vehiculo },
      ['latitud', 'longitud', 'id_vehiculo'], context);

    const body = await this.client.http.request(API, 'estacionar', {
      method: 'POST',
      data: { latitud, longitud, id_vehiculo },
      signal: options.signal
    });

    return {
      success: Boolean(body?.success),
      id_est: body?.id_est ?? null
    };
  }

  /**
   * POST /estacionar/liberar — closes an open parking event by setting `hora_fin`
   * to the server's current time.
   *
   * The endpoint responds with an empty body (`null`). It does not verify that
   * the calling user owns the event, because its `prerun` auth check is currently
   * commented out in the backend — anyone who knows an `id_est` can close it.
   * The wrapper still forwards the Bearer token when available, so it remains
   * compatible if the backend re-enables the check.
   *
   * @param {{ id_est: number }} payload
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<null>}
   */
  async liberar({ id_est } = {}, options = {}) {
    requireFields({ id_est }, ['id_est'], { api: API, endpoint: 'liberar' });

    return this.client.http.request(API, 'liberar', {
      method: 'POST',
      data: { id_est },
      signal: options.signal
    });
  }
}
