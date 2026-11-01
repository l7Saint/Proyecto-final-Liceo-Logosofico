/**
 * Persistent storage for the Bearer token and the cached user profile.
 *
 * Uses `localStorage` when available and silently degrades to an in-memory Map
 * (private browsing, sandboxed iframes, storage quota errors).
 */

function createMemoryStorage() {
  const map = new Map();
  return {
    getItem: (key) => (map.has(key) ? map.get(key) : null),
    setItem: (key, value) => { map.set(key, String(value)); },
    removeItem: (key) => { map.delete(key); }
  };
}

function defaultStorage() {
  try {
    const storage = globalThis.localStorage;
    const probe = '__urbanaut_probe__';
    storage.setItem(probe, '1');
    storage.removeItem(probe);
    return storage;
  } catch {
    return createMemoryStorage();
  }
}

export class SessionStore {
  /**
   * @param {object} [options]
   * @param {Storage|null} [options.storage]
   * @param {string} [options.tokenKey]
   * @param {string} [options.userKey]
   * @param {boolean} [options.persistUser]
   */
  constructor({
    storage = null,
    tokenKey = 'urbanaut.token',
    userKey = 'urbanaut.usuario',
    persistUser = true
  } = {}) {
    this.storage = storage ?? defaultStorage();
    this.tokenKey = tokenKey;
    this.userKey = userKey;
    this.persistUser = persistUser;
    this._user = null;
  }

  /** @returns {string|null} */
  getToken() {
    try {
      return this.storage.getItem(this.tokenKey) || null;
    } catch {
      return null;
    }
  }

  /** @param {string|null} token */
  setToken(token) {
    try {
      if (token) this.storage.setItem(this.tokenKey, token);
      else this.storage.removeItem(this.tokenKey);
    } catch {
      /* storage is full or unavailable — session lives in memory only */
    }
  }

  /** @returns {object|null} */
  getUser() {
    if (this._user) return this._user;
    if (!this.persistUser) return null;
    try {
      const raw = this.storage.getItem(this.userKey);
      this._user = raw ? JSON.parse(raw) : null;
    } catch {
      this._user = null;
    }
    return this._user;
  }

  /** @param {object|null} user */
  setUser(user) {
    this._user = user ?? null;
    if (!this.persistUser) return;
    try {
      if (user) this.storage.setItem(this.userKey, JSON.stringify(user));
      else this.storage.removeItem(this.userKey);
    } catch {
      /* ignore quota errors */
    }
  }

  clear() {
    this.setToken(null);
    this.setUser(null);
  }
}
