const API_BASE = 'http://127.0.0.1:30000/api/admin.php';

let usuariosCache = [];

/* ============================
   LLAMADAS A LA API
   ============================ */

async function api_obtenerTodos() {
  try {
    const response = await fetch(`${API_BASE}/obtener`, {
      method: 'GET',
      headers: { Accept: 'application/json' },
      credentials: 'include',
    });
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || `HTTP ${response.status}`);
    }
    return data.usuarios;
  } catch (error) {
    console.error('Error obteniendo usuarios:', error);
    throw error;
  }
}

async function api_crearUsuario(usuario) {
  try {
    const response = await fetch(`${API_BASE}/crear`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify(usuario),
    });
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || `HTTP ${response.status}`);
    }
    return data;
  } catch (error) {
    console.error('Error creando usuario:', error);
    throw error;
  }
}

async function api_modificarUsuario(id, cambios) {
  try {
    const response = await fetch(`${API_BASE}/modificar`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify({ id, ...cambios }),
    });
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || `HTTP ${response.status}`);
    }
    return data;
  } catch (error) {
    console.error('Error modificando usuario:', error);
    throw error;
  }
}

async function api_eliminarUsuario(id) {
  try {
    const response = await fetch(`${API_BASE}/eliminar`, {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify({ id }),
    });
    const data = await response.json();
    if (!response.ok) {
      throw new Error(data.error || `HTTP ${response.status}`);
    }
    return data.success;
  } catch (error) {
    console.error('Error eliminando usuario:', error);
    throw error;
  }
}

/* ============================
   RENDERIZADO
   ============================ */

function renderUsuarios(usuarios) {
  const tbody = document.getElementById('tablaUsuarios');
  tbody.innerHTML = '';

  usuarios.forEach((u) => {
    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td class="id-usuario">${u.id}</td>
      <td class="nombre">${u.nombre}</td>
      <td class="dato">${u.apellido}</td>
      <td class="dato">${u.email}</td>
      <td class="dato">${u.es_admin == 1 ? 'Sí' : 'No'}</td>
      <td class="dato">${u.inactivo == 1 ? 'Sí' : 'No'}</td>
      <td class="dato">${u.fecha_registro || '-'}</td>
      <td>
        <button class="btn-editar" onclick="editarUsuario(${u.id})">
          <i class="bi bi-pencil"></i> Editar
        </button>
        <button class="btn-borrar" onclick="borrarUsuario(${u.id})">
          <i class="bi bi-trash"></i> Borrar
        </button>
      </td>
    `;

    tbody.appendChild(tr);
  });

  actualizarCantidad();
}

function actualizarCantidad() {
  const filas = document.querySelectorAll('#tablaUsuarios tr');
  document.getElementById('cantidadUsuarios').textContent = filas.length;
}

/* ============================
   CARGA INICIAL
   ============================ */

async function cargarUsuarios() {
  try {
    const usuarios = await api_obtenerTodos();
    usuariosCache = usuarios;
    renderUsuarios(usuarios);
  } catch (error) {
    alert('No se pudieron cargar los usuarios: ' + error.message);
  }
}

/* ============================
   CREAR / EDITAR
   ============================ */

document.getElementById('usuarioForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const id = document.getElementById('usuarioId').value;
  const contrasena = document.getElementById('contrasena').value;

  const usuario = {
    nombre: document.getElementById('nombre').value.trim(),
    apellido: document.getElementById('apellido').value.trim(),
    email: document.getElementById('email').value.trim(),
    es_admin: document.getElementById('es_admin').checked ? 1 : 0,
    inactivo: document.getElementById('inactivo').checked ? 1 : 0,
  };

  if (contrasena) {
    usuario.contrasena = contrasena;
  }

  try {
    if (id) {
      // Modificar
      await api_modificarUsuario(id, usuario);
    } else {
      // Crear
      if (!contrasena) {
        throw new Error('La contraseña es obligatoria para un usuario nuevo.');
      }
      await api_crearUsuario(usuario);
    }

    cancelarEdicion();
    await cargarUsuarios();
  } catch (error) {
    alert('Error: ' + error.message);
  }
});

function editarUsuario(id) {
  const u = usuariosCache.find((u) => u.id == id);
  if (!u) return;

  document.getElementById('usuarioId').value = u.id;
  document.getElementById('nombre').value = u.nombre;
  document.getElementById('apellido').value = u.apellido;
  document.getElementById('email').value = u.email;
  document.getElementById('contrasena').value = '';
  document.getElementById('es_admin').checked = u.es_admin == 1;
  document.getElementById('inactivo').checked = u.inactivo == 1;

  document.getElementById('formTitulo').textContent = 'Editar usuario #' + u.id;
}

function cancelarEdicion() {
  document.getElementById('usuarioForm').reset();
  document.getElementById('usuarioId').value = '';
  document.getElementById('formTitulo').textContent = 'Nuevo usuario';
}

/* ============================
   ELIMINAR
   ============================ */

async function borrarUsuario(id) {
  const u = usuariosCache.find((u) => u.id == id);
  const nombre = u ? `${u.nombre} ${u.apellido}` : `ID ${id}`;

  if (!confirm(`¿Querés borrar al usuario ${nombre}?`)) return;

  try {
    await api_eliminarUsuario(id);
    await cargarUsuarios();
  } catch (error) {
    alert('Error al borrar: ' + error.message);
  }
}

/* ============================
   EVENTO ESPECIAL
   ============================ */

function cambiarEventoEspecial(switchEvento) {
  const estado = document.getElementById('estadoEvento');
  if (switchEvento.checked) {
    estado.textContent = 'Activado';
    estado.style.color = '#67ed9d';
    alert(
      'Evento especial activado.\n\n' +
        'Los usuarios que participen podrán formar parte de las estadísticas del sábado.'
    );
  } else {
    estado.textContent = 'Desactivado';
    estado.style.color = '#779488';
    alert('Evento especial desactivado.');
  }
}

/* ============================
   INICIO
   ============================ */

document.addEventListener('DOMContentLoaded', cargarUsuarios);
