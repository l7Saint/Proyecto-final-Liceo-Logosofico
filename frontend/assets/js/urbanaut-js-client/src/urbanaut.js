import { HttpClient } from './core/http.js';
import { SessionStore } from './core/session.js';
import { UrbanautError, HttpError, NetworkError, ValidationError } from './core/errors.js';
import { DIAS_SEMANA, DEFAULT_BASE_URL, DEFAULT_TIMEOUT } from './constants.js';

import { UsuarioApi } from './api/usuario.js';
import { AdminApi } from './api/admin.js';
import { HorarioApi } from './api/horario.js';
import { TieneApi } from './api/tiene.js';
import { VehiculoApi } from './api/vehiculo.js';
import { EstacionarApi } from './api/estacionar.js';

/**
 * Central client for the Urbanaut backend.
 *
 * Owns the token, the cached user profile and the HTTP transport, and exposes one
 * namespace per backend API. Emits `login`, `logout`, `tokenchange` and `unauthorized`.
 *
 * @example
 * const api = new UrbanautClient({ baseUrl: 'http://localhost:8000/api' });
 * await api.usuario.login({ email: 'admin@example.com', contrasena: 'changeme' });
 * const vehiculos = await api.vehiculo.obtener();
 */
export class UrbanautClient extends EventTarget {
  /**
   * @param {object} [options]
   * @param {string} [options.baseUrl]          API prefix, default `/api`
   * @param {Storage|null} [options.storage]    Storage-like object, default `localStorage`
   * @param {boolean} [options.persistUser]     Persist the user profile, default `true`
   * @param {typeof fetch|null} [options.fetchImpl]
   * @param {number} [options.timeout]          Milliseconds, default 20000
   * @param {RequestCredentials} [options.credentials] Default `'omit'` (Bearer-only auth)
   */
  constructor({
    baseUrl = DEFAULT_BASE_URL,
    storage = null,
    persistUser = true,
    fetchImpl = null,
    timeout = DEFAULT_TIMEOUT,
    credentials = 'omit'
  } = {}) {
    super();

    this.session = new SessionStore({ storage, persistUser });

    this.http = new HttpClient({
      baseUrl,
      fetchImpl,
      timeout,
      credentials,
      tokenProvider: () => this.session.getToken(),
      onError: (error) => {
        const isLogin = error.api === 'usuario' && error.endpoint === 'login';
        if (error.status === 401 && !isLogin && this.session.getToken()) {
          this.session.clear();
          this._emit('unauthorized', { error });
        }
      }
    });

    /** @type {UsuarioApi} */
    this.usuario = new UsuarioApi(this);
    /** @type {AdminApi} */
    this.admin = new AdminApi(this);
    /** @type {HorarioApi} */
    this.horario = new HorarioApi(this);
    /** @type {TieneApi} */
    this.tiene = new TieneApi(this);
    /** @type {VehiculoApi} */
    this.vehiculo = new VehiculoApi(this);
    /** @type {EstacionarApi} */
    this.estacionar = new EstacionarApi(this);
  }

  /** @returns {string} the resolved API prefix */
  get baseUrl() {
    return this.http.baseUrl;
  }

  /** @returns {string|null} */
  get token() {
    return this.session.getToken();
  }

  /** @returns {object|null} */
  get currentUser() {
    return this.session.getUser();
  }

  /** @returns {boolean} */
  get isAuthenticated() {
    return Boolean(this.session.getToken());
  }

  /**
   * Restores a token obtained elsewhere (SSR hand-off, another tab, tests).
   * @param {string|null} token
   * @returns {this}
   */
  setToken(token) {
    this.session.setToken(token);
    this._emit('tokenchange', { token: token ?? null });
    return this;
  }

  /**
   * Drops the local token and cached profile without calling the backend.
   * @returns {this}
   */
  clearSession() {
    this._applyLogout();
    return this;
  }

  /**
   * Subscribes to a client event.
   * @param {'login'|'logout'|'tokenchange'|'unauthorized'} type
   * @param {(event: CustomEvent) => void} handler
   * @returns {() => void} unsubscribe
   */
  on(type, handler) {
    this.addEventListener(type, handler);
    return () => this.removeEventListener(type, handler);
  }

  /**
   * @private
   * @param {string} type
   * @param {object} detail
   */
  _emit(type, detail) {
    this.dispatchEvent(new CustomEvent(type, { detail }));
  }

  /**
   * @private
   * @param {{ token?: string, usuario?: object }|null} body
   */
  _applyLogin(body) {
    const token = typeof body?.token === 'string' ? body.token : null;
    const usuario = body?.usuario ?? null;

    this.session.setToken(token);
    this.session.setUser(usuario);
    this._emit('login', { token, usuario });
  }

  /** @private */
  _applyLogout() {
    this.session.clear();
    this._emit('logout', {});
  }
}

/**
 * Convenience factory.
 * @param {ConstructorParameters<typeof UrbanautClient>[0]} [options]
 * @returns {UrbanautClient}
 */
export function createClient(options = {}) {
  return new UrbanautClient(options);
}

export default UrbanautClient;

export {
  HttpClient,
  SessionStore,
  UsuarioApi,
  AdminApi,
  HorarioApi,
  TieneApi,
  VehiculoApi,
  EstacionarApi,
  UrbanautError,
  HttpError,
  NetworkError,
  ValidationError,
  DIAS_SEMANA,
  DEFAULT_BASE_URL,
  DEFAULT_TIMEOUT
};
