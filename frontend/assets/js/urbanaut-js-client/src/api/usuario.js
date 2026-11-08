import { requireFields } from '../core/validate.js';

const API = 'usuario';

/**
 * Wrapper for `backend/api/apis/usuario.php`.
 *
 * Endpoints: login (POST), user (GET), check (GET), logout (GET), signin (POST).
 */
export class UsuarioApi {
  /** @param {import('../urbanaut.js').UrbanautClient} client */
  constructor(client) {
    this.client = client;
  }

  /**
   * POST /usuario/login — exchanges credentials for a Bearer token.
   * The token and the user profile are stored on the client automatically.
   *
   * @param {{ email: string, contrasena: string }} credentials
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean, token: string, usuario: object }>}
   */
  async login({ email, contrasena } = {}, options = {}) {
    requireFields({ email, contrasena }, ['email', 'contrasena'], {
      api: API,
      endpoint: 'login'
    });

    const body = await this.client.http.request(API, 'login', {
      method: 'POST',
      auth: false,
      data: { email, contrasena },
      signal: options.signal
    });

    this.client._applyLogin(body);
    return body;
  }

  /**
   * GET /usuario/user — returns the profile of the authenticated user.
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<object>} the `usuario` object
   */
  async getUser(options = {}) {
    const body = await this.client.http.request(API, 'user', {
      method: 'GET',
      signal: options.signal
    });

    this.client.session.setUser(body?.usuario ?? null);
    return body?.usuario ?? null;
  }

  /**
   * GET /usuario/check — liveness probe for the current token.
   * Resolves to `false` on 401 instead of throwing.
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<boolean>}
   */
  async check(options = {}) {
    try {
      await this.client.http.request(API, 'check', {
        method: 'GET',
        signal: options.signal
      });
      return true;
    } catch (error) {
      if (error?.status === 401) return false;
      throw error;
    }
  }

  /**
   * GET /usuario/logout — destroys the server-side session row.
   * The local token is cleared even if the request fails.
   *
   * @param {{ signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean }>}
   */
  async logout(options = {}) {
    try {
      return await this.client.http.request(API, 'logout', {
        method: 'GET',
        signal: options.signal
      });
    } finally {
      this.client._applyLogout();
    }
  }

  /**
   * POST /usuario/signin — public self-registration.
   *
   * NOTE: the backend creates a session but never returns its token, so the caller
   * is left unauthenticated. Pass `{ autoLogin: true }` to log in right after and
   * receive the session in the `session` field.
   *
   * @param {{ nombre: string, apellido: string, email: string, contrasena: string }} payload
   * @param {{ autoLogin?: boolean, signal?: AbortSignal }} [options]
   * @returns {Promise<{ success: boolean, session: object|null }>}
   */
  async signin({ nombre, apellido, email, contrasena } = {}, options = {}) {
    requireFields({ nombre, apellido, email, contrasena },
      ['nombre', 'apellido', 'email', 'contrasena'],
      { api: API, endpoint: 'signin' });

    const body = await this.client.http.request(API, 'signin', {
      method: 'POST',
      auth: false,
      data: { nombre, apellido, email, contrasena },
      signal: options.signal
    });

    let session = null;
    if (options.autoLogin) {
      session = await this.login({ email, contrasena }, { signal: options.signal });
    }

    return { success: Boolean(body?.success), session };
  }

  async modificar({ nombre, apellido, email } = {}, options = {}) {
    requireFields({ nombre, apellido, email }, ['nombre', 'apellido', 'email'], {
      api: API,
      endpoint: 'modificar'
    });

    return this.client.http.request(API, 'modificar', {
      method: 'PUT',
      data: { nombre, apellido, email },
      signal: options.signal
    });
  }
}
