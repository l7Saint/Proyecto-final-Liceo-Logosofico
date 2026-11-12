const API = 'estadisticas';

/**
 * Wrapper for `backend/api/apis/estadisticas.php`.
 *
 * Endpoints:
 *   - `resumen` (GET, user) — aggregates of the authenticated user's parking
 *     history: total parkings, distinct blocks, parkings this month and the
 *     ranking of the blocks where the user parked the most.
 *
 * The backend takes the user from the Bearer token, never from the payload, so
 * this wrapper sends no user identifier and each caller only sees its own data.
 * The API-level `prerun` rejects requests without a valid session with `401`.
 */
export class EstadisticasApi {
  /** @param {import('../urbanaut.js').UrbanautClient} client */
  constructor(client) {
    this.client = client;
  }

  /**
   * GET /estadisticas/resumen
   *
   * NOTE: the request carries no body on purpose. `Main::run()` reads
   * parameters from `php://input`, but `fetch()` rejects a body on GET/HEAD
   * requests, so this endpoint accepts no parameters — the ranking is capped
   * server-side at 10 blocks.
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{
   *   total: number,
   *   cuadras_distintas: number,
   *   este_mes: number,
   *   primera_fecha: string|null,
   *   ultima_fecha: string|null,
   *   cuadras: Array<{
   *     id_cuadra: number,
   *     calle: string,
   *     puerta_inicial: number|null,
   *     puerta_final: number|null,
   *     total: number,
   *     porcentaje: number,
   *     ultima_vez: string|null
   *   }>
   * }>}
   */
  async resumen(options = {}) {
    const body = await this.client.http.request(API, 'resumen', {
      method: 'GET',
      signal: options.signal
    });

    const resumen = body?.resumen ?? {};
    const cuadras = Array.isArray(resumen.cuadras) ? resumen.cuadras : [];

    return {
      total: Number(resumen.total ?? 0),
      cuadras_distintas: Number(resumen.cuadras_distintas ?? 0),
      este_mes: Number(resumen.este_mes ?? 0),
      primera_fecha: resumen.primera_fecha ?? null,
      ultima_fecha: resumen.ultima_fecha ?? null,
      cuadras: cuadras.map((cuadra) => ({
        id_cuadra: Number(cuadra.id_cuadra ?? 0),
        calle: String(cuadra.calle ?? ''),
        puerta_inicial: cuadra.puerta_inicial ?? null,
        puerta_final: cuadra.puerta_final ?? null,
        total: Number(cuadra.total ?? 0),
        porcentaje: Number(cuadra.porcentaje ?? 0),
        ultima_vez: cuadra.ultima_vez ?? null
      }))
    };
  }
}
