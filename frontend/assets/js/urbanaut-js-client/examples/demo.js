import UrbanautClient, { DIAS_SEMANA } from '../src/urbanaut.js';

const $ = (id) => document.getElementById(id);
const out = $('out');
const status = $('status');

let api = new UrbanautClient({ baseUrl: $('baseUrl').value });

function log(label, value) {
  const stamp = new Date().toLocaleTimeString();
  const text = typeof value === 'string' ? value : JSON.stringify(value, null, 2);
  out.textContent = `[${stamp}] ${label}\n${text}`;
}

function refreshStatus() {
  status.textContent = api.isAuthenticated
    ? `Authenticated as ${api.currentUser?.email ?? '(unknown)'} — token ${api.token.slice(0, 12)}…`
    : 'Not authenticated.';
}

api.on('login', () => { refreshStatus(); });
api.on('logout', () => { refreshStatus(); });
api.on('unauthorized', () => { refreshStatus(); log('Session expired', 'Token cleared.'); });

async function guarded(label, fn) {
  try {
    await fn();
  } catch (error) {
    log(`${label} failed`, {
      name: error.name,
      status: error.status,
      code: error.code,
      message: error.message,
      details: error.details
    });
  }
}

$('btnSave').addEventListener('click', () => {
  api = new UrbanautClient({ baseUrl: $('baseUrl').value });
  refreshStatus();
  log('Client rebuilt', { baseUrl: api.baseUrl });
});

$('btnLogin').addEventListener('click', () => guarded('login', async () => {
  const result = await api.usuario.login({
    email: $('email').value,
    contrasena: $('contrasena').value
  });
  log('login', { token: `${result.token.slice(0, 16)}…`, usuario: result.usuario });
}));

$('btnCheck').addEventListener('click', () => guarded('check', async () => {
  log('check', { valid: await api.usuario.check() });
}));

$('btnMe').addEventListener('click', () => guarded('getUser', async () => {
  log('getUser', await api.usuario.getUser());
}));

$('btnLogout').addEventListener('click', () => guarded('logout', async () => {
  await api.usuario.logout();
  log('logout', 'Session destroyed.');
}));

$('btnVehCreate').addEventListener('click', () => guarded('vehiculo.crear', async () => {
  const created = await api.vehiculo.crear({
    marca: $('marca').value,
    modelo: $('modelo').value,
    color: $('color').value,
    tamano: $('tamano').value
  });
  log('vehiculo.crear', created);
}));

$('btnVehList').addEventListener('click', () => guarded('vehiculo.obtener', async () => {
  log('vehiculo.obtener', await api.vehiculo.obtener());
}));

$('btnHorList').addEventListener('click', () => guarded('horario.obtener', async () => {
  log('horario.obtener', await api.horario.obtener());
}));

$('btnTieneList').addEventListener('click', () => guarded('tiene.obtener', async () => {
  log('tiene.obtener', await api.tiene.obtener());
}));

$('btnProb').addEventListener('click', () => guarded('estacionar.probabilidad', async () => {
  const result = await api.estacionar.probabilidad({
    cuadras_id: [1],
    dia_semana: DIAS_SEMANA[0],
    hora: 9
  });
  log('estacionar.probabilidad', result);
}));

$('btnEstacionar').addEventListener('click', () => guarded('estacionar.estacionar', async () => {
  const result = await api.estacionar.estacionar({
    latitud: Number($('latitud').value),
    longitud: Number($('longitud').value),
    id_vehiculo: Number($('idVehiculo').value)
  });
  log('estacionar.estacionar', result);
}));

$('btnLiberar').addEventListener('click', () => guarded('estacionar.liberar', async () => {
  const result = await api.estacionar.liberar({ id_est: Number($('idEst').value) });
  log('estacionar.liberar', result);
}));

refreshStatus();
