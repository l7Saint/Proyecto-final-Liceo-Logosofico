/**
 * Urbanaut admin panel — now backed by the Urbanaut JS client.
 *
 * Replaces the hand-rolled `authFetch` + `/api/admin.php/*` calls with the
 * `admin` namespace of the client. Endpoint mapping:
 *
 *   cargarUsuarios      → api.admin.obtener()
 *   guardarUsuario      → api.admin.crear() / api.admin.modificar()
 *   eliminarUsuario     → api.admin.eliminar()
 *   logout              → api.usuario.logout() + api.clearSession()
 *
 * The HTML uses inline `onclick="cancelarEdicion()"`, `onchange="cambiarEventoEspecial(this)"`
 * and `onclick="logout()"`. Since this file is loaded as an ES module, those
 * functions are attached to `window` at the bottom of the file.
 */

import UrbanautClient from '/assets/js/urbanaut-js-client/src/urbanaut.js';

const BACKEND_URL = 'http://127.0.0.1:30000';
const LEGACY_TOKEN_KEY = 'urbanaut_token';

/* -------------------------------------------------------------------------
	 Token migration — users who logged in before the client migration still have
	 their token under `urbanaut_token` and no `urbanaut.token`. Copy it over so
	 the client picks it up automatically.
	 ------------------------------------------------------------------------- */
(function migrateLegacyToken() {
	try {
		const legacy = localStorage.getItem(LEGACY_TOKEN_KEY);
		const current = localStorage.getItem('urbanaut.token');
		if (legacy && !current) localStorage.setItem('urbanaut.token', legacy);
	} catch { /* storage unavailable */ }
})();

const api = new UrbanautClient({ baseUrl: BACKEND_URL + '/api' });

/* -------------------------------------------------------------------------
	 DOM references
	 ------------------------------------------------------------------------- */
const tbody = document.getElementById('tablaUsuarios');
const form = document.getElementById('usuarioForm');
const formTitulo = document.getElementById('formTitulo');
const usuarioIdInput = document.getElementById('usuarioId');
const nombreInput = document.getElementById('nombre');
const apellidoInput = document.getElementById('apellido');
const emailInput = document.getElementById('email');
const contrasenaInput = document.getElementById('contrasena');
const esAdminInput = document.getElementById('es_admin');
const inactivoInput = document.getElementById('inactivo');
const cantidadUsuariosEl = document.getElementById('cantidadUsuarios');

/** @type {object[]} */
let usuariosCache = [];

/* -------------------------------------------------------------------------
	 Auth guard — redirects to the login page when the session is missing or the
	 user is not an admin. Falls back to /usuario/user if the client has a token
	 but no cached profile (e.g. legacy session restored from storage).
	 ------------------------------------------------------------------------- */
async function requireAdmin() {
	if (!api.isAuthenticated) {
		window.location.href = '../login/login.php';
		return false;
	}

	let user = api.currentUser;
	if (!user) {
		try {
			user = await api.usuario.getUser();
		} catch (err) {
			console.warn('No se pudo obtener el usuario:', err);
			user = null;
		}
	}

	if (!user || Number(user.es_admin) !== 1) {
		alert('Acceso denegado. Se requiere una cuenta de administrador.');
		api.clearSession();
		localStorage.removeItem(LEGACY_TOKEN_KEY);
		window.location.href = '../login/login.php';
		return false;
	}

	return true;
}

/* -------------------------------------------------------------------------
	 Rendering helpers
	 ------------------------------------------------------------------------- */
function escapeHtml(value) {
	return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
		'&': '&amp;',
		'<': '&lt;',
		'>': '&gt;',
		'"': '&quot;',
		"'": '&#39;'
	})[ch]);
}

function renderUsuarios(usuarios) {
	tbody.innerHTML = '';

	for (const u of usuarios) {
		const tr = document.createElement('tr');
		tr.innerHTML = `
			<td>${escapeHtml(u.id)}</td>
			<td>${escapeHtml(u.nombre)}</td>
			<td>${escapeHtml(u.apellido)}</td>
			<td>${escapeHtml(u.email)}</td>
			<td>${Number(u.es_admin) === 1 ? '✓' : '—'}</td>
			<td>${Number(u.inactivo) === 1 ? '✓' : '—'}</td>
			<td>${escapeHtml(u.fecha_registro ?? '')}</td>
			<td>
				<button type="button" class="btn-borrar" data-action="edit" data-id="${escapeHtml(u.id)}" title="Editar">
					<i class="bi bi-pencil"></i>
				</button>
				<button type="button" class="btn-borrar" data-action="delete" data-id="${escapeHtml(u.id)}" title="Eliminar">
					<i class="bi bi-trash"></i>
				</button>
			</td>
		`;
		tbody.appendChild(tr);
	}

	tbody.querySelectorAll('[data-action="edit"]').forEach((btn) => {
		btn.addEventListener('click', () => editarUsuario(Number(btn.dataset.id)));
	});
	tbody.querySelectorAll('[data-action="delete"]').forEach((btn) => {
		btn.addEventListener('click', () => eliminarUsuario(Number(btn.dataset.id)));
	});
}

/* -------------------------------------------------------------------------
	 API operations — via the JS client
	 ------------------------------------------------------------------------- */
async function cargarUsuarios() {
	try {
		const usuarios = await api.admin.obtener();
		usuariosCache = Array.isArray(usuarios) ? usuarios : [];
		renderUsuarios(usuariosCache);
		cantidadUsuariosEl.textContent = String(usuariosCache.length);
	} catch (err) {
		handleApiError(err, 'cargar usuarios');
	}
}

function editarUsuario(id) {
	const u = usuariosCache.find((x) => Number(x.id) === id);
	if (!u) return;

	usuarioIdInput.value = u.id;
	nombreInput.value = u.nombre ?? '';
	apellidoInput.value = u.apellido ?? '';
	emailInput.value = u.email ?? '';
	contrasenaInput.value = '';
	esAdminInput.checked = Number(u.es_admin) === 1;
	inactivoInput.checked = Number(u.inactivo) === 1;

	formTitulo.textContent = 'Editar usuario';
}

function cancelarEdicion() {
	form.reset();
	usuarioIdInput.value = '';
	formTitulo.textContent = 'Nuevo usuario';
}

async function guardarUsuario(event) {
	event.preventDefault();

	const idRaw = usuarioIdInput.value;
	const id = idRaw ? Number(idRaw) : null;

	const nombre = nombreInput.value.trim();
	const apellido = apellidoInput.value.trim();
	const email = emailInput.value.trim();
	const contrasena = contrasenaInput.value.trim();
	const es_admin = esAdminInput.checked ? 1 : 0;
	const inactivo = inactivoInput.checked ? 1 : 0;

	if (!nombre || !apellido || !email) {
		alert('Completá nombre, apellido y email.');
		return;
	}
	if (!id && !contrasena) {
		alert('La contraseña es obligatoria para un usuario nuevo.');
		return;
	}

	try {
		if (id) {
			const payload = { id, nombre, apellido, email, es_admin, inactivo };
			if (contrasena) payload.contrasena = contrasena;
			await api.admin.modificar(payload);
		} else {
			await api.admin.crear({ nombre, apellido, email, contrasena, es_admin, inactivo });
		}

		cancelarEdicion();
		await cargarUsuarios();
	} catch (err) {
		handleApiError(err, id ? 'modificar usuario' : 'crear usuario');
	}
}

async function eliminarUsuario(id) {
	if (!confirm(`¿Eliminar el usuario #${id}?`)) return;

	try {
		await api.admin.eliminar({ id });
		await cargarUsuarios();
	} catch (err) {
		handleApiError(err, 'eliminar usuario');
	}
}

/* -------------------------------------------------------------------------
	 Error handling — maps client errors to user-facing alerts
	 ------------------------------------------------------------------------- */
function handleApiError(err, contexto) {
	console.error(`Error al ${contexto}:`, err);

	if (err?.name === 'ValidationError') {
		const missing = err?.details?.missing?.join(', ') ?? '';
		alert(`Datos incompletos${missing ? ': ' + missing : '.'}`);
		return;
	}

	if (err?.name === 'NetworkError') {
		alert('No se pudo conectar con el servidor.');
		return;
	}

	switch (err?.status) {
		case 400:
			alert('Solicitud inválida. Revisá los datos ingresados.');
			break;
		case 401:
			alert('Tu sesión expiró. Iniciá sesión de nuevo.');
			window.location.href = '../login/login.php';
			break;
		case 403:
			alert('No tenés permiso para realizar esta acción.');
			break;
		case 404:
			alert('El recurso solicitado no existe.');
			break;
		case 409:
			alert('Ya existe un usuario con ese email.');
			break;
		case 500:
			alert('Error interno del servidor. Revisá los logs del backend.');
			break;
		default:
			alert('Ocurrió un error inesperado.');
	}
}

/* -------------------------------------------------------------------------
	 Misc — non-API helpers referenced from the HTML
	 ------------------------------------------------------------------------- */
function cambiarEventoEspecial(checkbox) {
	const estado = document.getElementById('estadoEvento');
	if (checkbox.checked) {
		estado.textContent = 'Activado';
		alert('Evento especial activado (funcionalidad reservada, aún no persistida).');
	} else {
		estado.textContent = 'Desactivado';
		alert('Evento especial desactivado.');
	}
}

function logout() {
	// Fire-and-forget server-side logout; navigation is handled by the anchor href.
	if (api.isAuthenticated) {
		api.usuario.logout().catch(() => { /* ignore */ });
	}
	api.clearSession();
	localStorage.removeItem(LEGACY_TOKEN_KEY);
}

/* -------------------------------------------------------------------------
	 Wiring
	 ------------------------------------------------------------------------- */
form.addEventListener('submit', guardarUsuario);

// Expose for inline handlers in index.html
window.cancelarEdicion = cancelarEdicion;
window.cambiarEventoEspecial = cambiarEventoEspecial;
window.logout = logout;

// Initial load
(async () => {
	if (await requireAdmin()) {
		await cargarUsuarios();
	}
})();
