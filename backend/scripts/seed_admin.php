<?php
require_once '/var/www/html/config/conexion.php';
require_once '/var/www/html/handlers/UsuarioHandler.php';
require_once __DIR__ . '/../models/Usuario.php';

$email    = getenv('ADMIN_EMAIL')    ?: 'admin@example.com';
$password = getenv('ADMIN_PASSWORD') ?: 'changeme';
$nombre   = getenv('ADMIN_NOMBRE')   ?: 'Admin';
$apellido = getenv('ADMIN_APELLIDO') ?: 'Admin';

$hnd = new UsuarioHandler($conexion);

if ($hnd->obtenerPorEmail($email) !== false) {
	fwrite(STDERR, "Admin {$email} already exists, skipping seed.\n");
	exit(0);
}

$admin = new Usuario(
	null,
	$nombre,
	$apellido,
	$email,
	password_hash($password, PASSWORD_DEFAULT),
	0,     // inactivo
	1,     // es_admin
null
);

$id = $hnd->crearUsuario($admin);
if (!$id) {
	fwrite(STDERR, "Failed to create admin user.\n");
	exit(1);
}

error_log("====================================================");
error_log("Default admin created: {$email}");
error_log("Password: {$password}");
error_log("CHANGE THIS PASSWORD IMMEDIATELY.");
error_log("====================================================");
