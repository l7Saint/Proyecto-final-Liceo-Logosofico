CREATE DATABASE IF NOT EXISTS urbanaut;
USE urbanaut;

CREATE TABLE IF NOT EXISTS Usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(40) NOT NULL,
	apellido VARCHAR(40) NOT NULL,
	email VARCHAR(150) NOT NULL,
	contrasena VARCHAR(255) NOT NULL,
	fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Vehiculo (
	id INT AUTO_INCREMENT PRIMARY KEY,
	matricula VARCHAR(8) NOT NULL,
	color VARCHAR(15) NOT NULL,
	tamano VARCHAR(15) NOT NULL
);

CREATE TABLE IF NOT EXISTS Ubicacion (
	id INT AUTO_INCREMENT PRIMARY KEY,
	numero_puerta INT NOT NULL,
	//esquina capaz que no, depende, y tambien depende como se tendria que guardar.
	calle VARCHAR(40) NOT NULL,
	latitud DOUBLE NOT NULL,
	longitud DOUBLE NOT NULL
);
