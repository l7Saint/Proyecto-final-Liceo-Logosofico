<?php
$backend = rtrim(getenv('BACKEND_HOST') ?: '', '/');
$port    = getenv('BACKEND_PORT') ?: '';
$url     = 'http://' . $backend . ':' . $port;
?>
function api_obtenerTodos(){
	try {
		const response = await fetch('/api.admin/obtener', {
			method: 'GET',
			headers: {
				'Accept': 'application/json'
			},
			credentials: 'include'
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

function api_eliminarUsuario(id){
	try {
		const response = await fetch(
		<?= "\"".$url.'/api/usuario.php/signin'."\"" ?>,
		{
			method: 'DELETE',
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json'
			},
			credentials: 'include',
			body: JSON.stringify({ id })
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

function borrarUsuario(boton) {
	const fila = boton.closest("tr");
	const nombre = fila.querySelector(".nombre").textContent.trim();
	const confirmar = confirm("¿Querés borrar al usuario " + nombre + "?");
	if (confirmar) {
		fila.remove();
		actualizarCantidad();
	}
}

function actualizarCantidad() {
	const filas = document.querySelectorAll("#tablaUsuarios tr");
	document.getElementById("cantidadUsuarios").textContent = filas.length;
}

function cambiarEventoEspecial(switchEvento) {
	const estado = document.getElementById("estadoEvento");
	if (switchEvento.checked) {
		estado.textContent = "Activado";
		estado.style.color = "#67ed9d";
		alert(
"Evento especial activado.\n\n" +
"Los usuarios que participen " +
"podrán formar parte de las " +
"estadísticas del sábado.",
		);
	} else {
		estado.textContent = "Desactivado";
		estado.style.color = "#779488";
		alert("Evento especial desactivado.");
	}
}
