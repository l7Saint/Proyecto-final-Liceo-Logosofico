/**
 * Shared constants mirroring the PHP domain models.
 */

/** Valid `dia_semana` values — see `backend/models/Horario.php`. Note: no 'domingo'. */
export const DIAS_SEMANA = Object.freeze([
  'lunes',
  'martes',
  'miercoles',
  'jueves',
  'viernes',
  'sabado'
]);

/** Default API prefix, matching `backend/api/index.php` routing. */
export const DEFAULT_BASE_URL = '/api';

/** Default request timeout in milliseconds. */
export const DEFAULT_TIMEOUT = 20000;
