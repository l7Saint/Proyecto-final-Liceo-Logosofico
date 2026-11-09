# Urbanaut JS Client

A zero-dependency, vanilla-browser JavaScript wrapper around the **Urbanaut** PHP backend
(`/api/<api>/<endpoint>`), reverse-engineered from `backend_workspace.txt`.

It ships as plain **ES modules** — no bundler, no transpiler, no `npm install`.
Drop the folder on any static server and `import` it.

---

## 1. Directory structure

```

urbanaut-js-client/
├── README.md
├── src/
│   ├── urbanaut.js            ← entry point (UrbanautClient + all re-exports)
│   ├── constants.js           ← DIAS_SEMANA, DEFAULT_BASE_URL
│   ├── core/
│   │   ├── errors.js          ← error hierarchy
│   │   ├── http.js            ← HttpClient + lenient JSON parsing
│   │   ├── session.js         ← SessionStore (token + user profile)
│   │   └── validate.js        ← requireFields() client-side guard
│   └── api/
│       ├── usuario.js         ← UsuarioApi      (login / user / check / logout / signin)
│       ├── admin.js           ← AdminApi        (crear / obtener / modificar / eliminar)
│       ├── horario.js         ← HorarioApi      (crear / obtener / eliminar)
│       ├── tiene.js           ← TieneApi        (asignar / obtener)
│       ├── vehiculo.js        ← VehiculoApi     (crear / obtener / modificar / eliminar)
│       └── estacionar.js      ← EstacionarApi   (probabilidad / estacionar / liberar)
└── examples/
├── demo.html
└── demo.js

```

### Layer responsibilities

| Layer | Files | Responsibility |
|---|---|---|
| **Client** | `src/urbanaut.js` | Owns state (token, cached user), exposes one namespace per backend API, emits events. |
| **API wrappers** | `src/api/*.js` | One class per backend API file. Each public method maps **1:1** to a backend endpoint, validates required fields, and returns a typed, unwrapped payload. |
| **Transport** | `src/core/http.js` | Builds the URL, injects `Authorization: Bearer`, serialises the body, handles timeouts/aborts, parses the response. |
| **Support** | `src/core/errors.js`, `session.js`, `validate.js` | Error classes, token persistence, required-field checks. |

Naming convention: **one namespace per backend API name**, **one method per backend endpoint name**.

```

const api = new UrbanautClient();
await api.usuario.login({ email, contrasena });   // → /api/usuario/login
await api.vehiculo.obtener();                     // → /api/vehiculo/obtener
await api.estacionar.probabilidad({ ... });       // → /api/estacionar/probabilidad

```

---

## 2. Quick start

```
<script type="module">
  import UrbanautClient from './urbanaut-js-client/src/urbanaut.js';

  const api = new UrbanautClient({ baseUrl: 'http://localhost:8000/api' });

  await api.usuario.login({ email: 'admin@example.com', contrasena: 'changeme' });
  console.log(api.isAuthenticated, api.currentUser);

  const vehiculos = await api.vehiculo.obtener();
  console.log(vehiculos);
</script>
```

Serve the folder over HTTP (ES modules do not work from `file://`):

```

npx serve urbanaut-js-client

# or

python3 -m http.server --directory urbanaut-js-client

```

---

## 3. Client configuration

```

new UrbanautClient({
baseUrl: '/api',        // prefix for every request (no trailing slash needed)
storage: null,          // Storage-like object; defaults to localStorage (memory fallback)
persistUser: true,      // also persist the user profile object
fetchImpl: null,        // inject a custom fetch (tests, polyfills)
timeout: 20000,         // ms; 0 disables the timeout
credentials: 'omit'     // cookies are not used — auth is Bearer-token only
});

```

### Instance surface

| Member | Type | Description |
|---|---|---|
| `api.usuario` | `UsuarioApi` | `/api/usuario/*` |
| `api.admin` | `AdminApi` | `/api/admin/*` |
| `api.horario` | `HorarioApi` | `/api/horario/*` |
| `api.tiene` | `TieneApi` | `/api/tiene/*` |
| `api.vehiculo` | `VehiculoApi` | `/api/vehiculo/*` |
| `api.estacionar` | `EstacionarApi` | `/api/estacionar/*` |
| `api.token` | `string | null` | Current Bearer token |
| `api.currentUser` | `object | null` | Cached user profile |
| `api.isAuthenticated` | `boolean` | `token != null` |
| `api.setToken(t)` / `api.clearSession()` | method | Manual session control |
| `api.on(event, fn)` | method | Subscribe, returns an unsubscribe function |
| `api.http.request(api, endp, opts)` | method | Escape hatch for un-wrapped calls |

### Events

| Event ↕▾ | `detail` ↕▾ | When ↕▾ |
|---|---|---|
| −`login` | `{ token, usuario }` | `usuario.login()` succeeded |
| `logout` | `{}` | `usuario.logout()` or `clearSession()` |
| `tokenchange` | `{ token }` | `setToken()` |
| `unauthorized` | `{ error }` | A request returned `401` (token cleared automatically) |
⚙

```

const off = api.on('unauthorized', () => location.assign('/login'));

```

---

## 4. Authentication model

The backend issues an opaque 64-hex-character token (`Sesion.token`) from
`POST /api/usuario/login`. There are **no cookies** — the token must be replayed as
`Authorization: Bearer <token>`.

The client handles this for you:

1. `usuario.login()` stores the returned token in `SessionStore`.
2. Every subsequent request injects the header automatically.
3. A `401` response clears the stored token and emits `unauthorized`.
4. `usuario.logout()` deletes the server-side session row *and* the local token.

---

## 5. Endpoint reference

`Auth` column: `—` public, `user` any valid Bearer token, `admin` Bearer token whose user has `es_admin = 1`.

### `usuario` — `api.usuario`

| Wrapper method ↕▾ | HTTP ↕▾ | Path ↕▾ | Auth ↕▾ | Request body ↕▾ | Resolves to ↕▾ |
|---|---|---|---|---|---|
| −`login({ email, contrasena })` | POST | `/usuario/login` | — | `email`, `contrasena` | `{ success, token, usuario }` |
| `getUser()` | GET | `/usuario/user` | user | — | `usuario` |
| `check()` | GET | `/usuario/check` | user | — | `boolean` (never throws on 401) |
| `logout()` | GET | `/usuario/logout` | user | — | `{ success }` |
| `signin({ nombre, apellido, email, contrasena })` | POST | `/usuario/signin` | — | 4 fields | `{ success, session }` |
⚙

> **`signin()` does not return a token.** The backend creates a session server-side but
> discards the token before responding. Pass `{ autoLogin: true }` to have the wrapper
> call `login()` immediately afterwards and return the session in `session`.

### `admin` — `api.admin` (admin only)

| Wrapper method ↕▾ | HTTP ↕▾ | Path ↕▾ | Request body ↕▾ | Resolves to ↕▾ |
|---|---|---|---|---|
| −`crear({ nombre, apellido, email, contrasena, inactivo?, es_admin? })` | POST | `/admin/crear` | 4 required + 2 optional | `{ success, id }` |
| `obtener()` | GET | `/admin/obtener` | — | `usuario[]` |
| `modificar({ id, ...campos })` | PUT | `/admin/modificar` | `id` + any of the fields | `{ success }` |
| `eliminar({ id })` | DELETE | `/admin/eliminar` | `id` | `{ success }` |
⚙

`inactivo` and `es_admin` default to `0` in `crear()` because the backend reads them
unconditionally (they are *not* declared in the endpoint's `parameters` list).

### `horario` — `api.horario` (admin only)

| Wrapper method ↕▾ | HTTP ↕▾ | Path ↕▾ | Request body ↕▾ | Resolves to ↕▾ |
|---|---|---|---|---|
| −`crear({ numero_hora, dia_semana, horario })` | POST | `/horario/crear` | 3 fields | `{ success }` |
| `obtener()` | GET | `/horario/obtener` | — | `horario[]` |
| `eliminar({ numero_hora, dia_semana })` | DELETE | `/horario/eliminar` | 2 fields | `{ success }` |
⚙

`dia_semana` must be one of `lunes … sabado` (no `domingo`), see `DIAS_SEMANA`.

### `tiene` — `api.tiene` (user)

| Wrapper method ↕▾ | HTTP ↕▾ | Path ↕▾ | Request body ↕▾ | Resolves to ↕▾ |
|---|---|---|---|---|
| −`asignar({ numero_hora, dia_semana })` | POST | `/tiene/asignar` | 2 fields | `{ success }` |
| `obtener()` | GET | `/tiene/obtener` | — | `tiene[]` |
⚙

The user id is derived server-side from the Bearer token — never send it.

### `vehiculo` — `api.vehiculo` (user)

| Wrapper method ↕▾ | HTTP ↕▾ | Path ↕▾ | Request body ↕▾ | Resolves to ↕▾ |
|---|---|---|---|---|
| −`crear({ marca, modelo, color, tamano })` | POST | `/vehiculo/crear` | 4 fields | `{ success, id }` |
| `obtener()` | GET | `/vehiculo/obtener` | — | `vehiculo[]` |
| `modificar({ id, marca?, modelo?, color?, tamano? })` | PUT | `/vehiculo/modificar` | `id` + any field | `{ success }` |
| `eliminar({ id })` | DELETE | `/vehiculo/eliminar` | `id` | `{ success }` |
⚙

Ownership (`vehiculo.id_usuario === session.id_usuario`) is enforced server-side → `403`.

### `estacionar` — `api.estacionar`

| Wrapper method | HTTP | Path | Auth | Request body | Resolves to |
|---|---|---|---|---|---|
| `probabilidad({ cuadras_id, dia_semana, hora })` | GET | `/estacionar/probabilidad` | — | `cuadras_id: number[]`, `dia_semana`, `hora` | `[{ cuadra_id, puntaje, confianza }]` |
| `estacionar({ latitud, longitud, id_vehiculo })` | POST | `/estacionar/estacionar` | user ² | `latitud`, `longitud`, `id_vehiculo` | `{ success, id_est }` |
| `liberar({ id_est })` | POST | `/estacionar/liberar` | — ¹ | `id_est` | `null` |

> ¹ **`liberar()` is currently unauthenticated.** The endpoint's API-level `prerun`
> session check is commented out in `backend/api/apis/estacionar.php`, so it accepts
> requests without a Bearer token and performs no ownership check. The wrapper still
> forwards the token when available, so it keeps working if the backend re-enables
> the check.
>
> ² **`estacionar()` requires a valid session, but the backend reports a missing one
> as `500` (not `401`).** The controller calls `checkSession(...)->id_usuario`; with
> no session, `checkSession` returns `false` and the property access fatals, which the
> endpoint catches and re-throws as `GenericRuntimeException(500)`. Call it after a
> successful `usuario.login()` and this is a non-issue.

---

## 6. Error handling

```

import { HttpError, NetworkError, ValidationError } from './urbanaut-js-client/src/urbanaut.js';

try {
await api.vehiculo.eliminar({ id: 42 });
} catch (err) {
if (err instanceof ValidationError)  { /* missing field, nothing was sent */ }
else if (err instanceof HttpError)   { console.warn(err.status, err.code, err.message); }
else if (err instanceof NetworkError){ /* offline / timeout / DNS */ }
}

```

| Class | Thrown when | Useful fields |
|---|---|---|
| `UrbanautError` | Base class of everything | `status`, `code`, `body`, `api`, `endpoint`, `details`, `cause` |
| `ValidationError` | A required parameter is `undefined`/`null` (checked *before* the request) | `details` = `{ missing: string[] }` |
| `NetworkError` | `fetch` rejected: offline, DNS, CORS preflight failure, timeout | `cause` |
| `HttpError` | Response status not in `200–299` | `status`, `code` (`body.error`), `body` |

Backend `error` codes surfaced on `HttpError.code`: `not_found`, `bad_request`, `bad_method`.
Business errors (`401`, `409`, `403`, `404` raised by endpoints) usually have a **`null`** body —
rely on `HttpError.status`, not on `body.message`.

---

## 7. Backend quirks the wrappers compensate for

These are real properties of the PHP code, documented here so nobody wastes an afternoon on them.

1. **GET requests carry a JSON body.**
`Main::run()` reads `json_decode(file_get_contents('php://input'))` for *every* method, and
parameters are validated against that decoded array. Query strings are never read.
⇒ `estacionar.probabilidad()` sends a `GET` **with** a JSON body. That is intentional and required.
2. **Double-echo responses.**
`vehiculo.*`, `estacionar.probabilidad`, `estacionar.estacionar` and `admin.eliminar` echo their
own JSON and *also* `return` (or fall through), so `Main` appends a second `json_encode(null)`.
The wire body looks like `{"success":true,"id":1}null`.
⇒ `parseBody()` falls back to extracting the first balanced JSON value.
3. **`signin` swallows the token.** See §5.
4. **`admin.crear` reads undeclared keys.** It accesses `$data['inactivo']` and `$data['es_admin']`
although they are not in `parameters`. The wrapper always sends both, defaulted to `0`.
5. **`horario`'s `prerun` references `$handlers['sesion']`, which that API never registers**,
so the admin guard silently fails. The wrapper still sends the Bearer token.
6. **`admin.*` closures use `handlers['usuario']` instead of `$handlers['usuario']`** —
a fatal `Error` (HTTP 500) on those endpoints until fixed server-side.
7. **`dia_semana` vocabulary** is `lunes … sabado`; `Horario` throws on anything else,
which surfaces as a `500`.
8. **`estacionar.liberar` has no auth and no ownership check.** Its `prerun` session
guard is commented out and the controller never calls `checkSession`, so any client
can close any event by `id_est`. The wrapper still forwards the Bearer token when
present so it remains compatible if the backend re-enables the check.

---

## 8. CORS / deployment

`backend/api/index.php` already answers preflight `OPTIONS` with `204` and echoes the origin,
allowing `Authorization`, `Content-Type` and `X-Requested-With`, with `Access-Control-Max-Age: 86400`.
`Access-Control-Allow-Credentials: true` is set but unused — the client uses `credentials: 'omit'`.

The PHP `getBearerToken()` helper reads the header from `HTTP_AUTHORIZATION`,
`REDIRECT_HTTP_AUTHORIZATION`, `getallheaders()` and `apache_request_headers()` in order,
so it works under both `mod_php` and `php-fpm` behind Apache.

---

## 9. Extending

Adding a new endpoint is a three-line change:

```

// src/api/horario.js
async actualizar({ numero_hora, dia_semana, horario }, options = {}) {
requireFields({ numero_hora, dia_semana, horario }, ['numero_hora', 'dia_semana', 'horario'],
{ api: 'horario', endpoint: 'actualizar' });
return this.client.http.request('horario', 'actualizar', {
method: 'PUT',
data: { numero_hora, dia_semana, horario },
signal: options.signal
});
}

```

Every method accepts an optional trailing `options` object supporting `{ signal }` for
`AbortController` cancellation and request timeouts.
