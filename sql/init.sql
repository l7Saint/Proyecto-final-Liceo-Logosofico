CREATE DATABASE IF NOT EXISTS urbanaut;
USE urbanaut;

CREATE TABLE IF NOT EXISTS Usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(40) NOT NULL,
	apellido VARCHAR(40) NOT NULL,
	email VARCHAR(150) NOT NULL UNIQUE,
	contrasena_hash VARCHAR(255) NOT NULL,
	inactivo BOOLEAN NOT NULL DEFAULT FALSE,
	es_admin BOOLEAN NOT NULL DEFAULT FALSE,
	fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Vehiculo (
	id INT AUTO_INCREMENT PRIMARY KEY,
	id_usuario INT NOT NULL,
	marca VARCHAR(40) NOT NULL, 
	modelo VARCHAR(100) NOT NULL,
	color VARCHAR(15) NOT NULL,
	tamano VARCHAR(15) NOT NULL,
	FOREIGN KEY(id_usuario) REFERENCES Usuario(id)
);

CREATE TABLE IF NOT EXISTS Horario (
	numero_hora INT,
	dia_semana ENUM('lunes','martes','miercoles','jueves','viernes','sabado') NOT NULL,
	horario VARCHAR(15) NOT NULL,
	PRIMARY KEY(numero_hora, dia_semana)
);

CREATE TABLE IF NOT EXISTS Ubicacion (
	id INT AUTO_INCREMENT PRIMARY KEY,
	numero_puerta INT,
	calle VARCHAR(100) NOT NULL,
	esquina VARCHAR(100),
	latitud DECIMAL(10,7),
	longitud DECIMAL(10,7),
	CHECK (latitud BETWEEN -90 AND 90),
	CHECK (longitud BETWEEN -180 AND 180)
);

CREATE TABLE IF NOT EXISTS Tiene (
	id_usuario INT NOT NULL,
	numero_hora INT NOT NULL,
	dia_semana INT NOT NULL,
	FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
	FOREIGN KEY (numero_hora) REFERENCES Horario(numero_hora),
	FOREIGN KEY (dia_semana) REFERENCES Horario(dia_semana),
	PRIMARY KEY(id_usuario, numero_hora, dia_semana)
);

CREATE TABLE IF NOT EXISTS Estaciona_en (
	id INT AUTO_INCREMENT PRIMARY KEY,
	id_usuario INT NOT NULL,
	id_vehiculo INT NOT NULL,
	id_ubicacion INT NOT NULL,
	numero_hora INT NOT NULL,
	fecha DATE NOT NULL,
	hora_inicio TIME NOT NULL DEFAULT CURRENT_TIME,
	hora_fin TIME,
	FOREIGN KEY (id_usuario, id_vehiculo) REFERENCES Posee(id_usuario, id_vehiculo),
	FOREIGN KEY (id_ubicacion) REFERENCES Ubicacion(id),
	FOREIGN KEY (numero_hora) REFERENCES Horario(numero_hora),
	CHECK (hora_fin IS NULL OR hora_fin > hora_inicio)
);
