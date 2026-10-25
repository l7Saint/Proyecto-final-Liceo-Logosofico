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
	FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
);

CREATE TABLE IF NOT EXISTS Horario (
	numero_hora INT,
	dia_semana ENUM('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
	horario VARCHAR(15) NOT NULL,
	PRIMARY KEY (numero_hora, dia_semana)
);

CREATE TABLE IF NOT EXISTS Cuadra (
	id INT AUTO_INCREMENT PRIMARY KEY,
	numero_puerta_inicial INT,
	numero_puerta_final INT,
	calle VARCHAR(100) NOT NULL,
	latitud DECIMAL(10,7),
	longitud DECIMAL(10,7),
	CHECK (latitud BETWEEN -90 AND 90),
	CHECK (longitud BETWEEN -180 AND 180)
);

CREATE TABLE IF NOT EXISTS Reporte (
	id_cuadra INT,
	fecha DATE,
	hora TIME,
	PRIMARY KEY (id_cuadra, fecha, hora),
	FOREIGN KEY (id_cuadra) REFERENCES Cuadra(id)
);

CREATE TABLE IF NOT EXISTS Tiene (
	id_usuario INT NOT NULL,
	numero_hora INT NOT NULL,
	dia_semana ENUM('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
	PRIMARY KEY (id_usuario, numero_hora, dia_semana),
	FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
	FOREIGN KEY (numero_hora, dia_semana) REFERENCES Horario(numero_hora, dia_semana)
);

CREATE TABLE IF NOT EXISTS Posee (
	id_usuario INT NOT NULL,
	id_vehiculo INT NOT NULL,
	PRIMARY KEY (id_usuario, id_vehiculo),
	FOREIGN KEY (id_usuario) REFERENCES Usuario(id),
	FOREIGN KEY (id_vehiculo) REFERENCES Vehiculo(id)
);

CREATE TABLE IF NOT EXISTS Estaciona_en (
	id INT AUTO_INCREMENT PRIMARY KEY,
	id_usuario INT NOT NULL,
	id_vehiculo INT NOT NULL,
	id_cuadra INT NOT NULL,
	numero_hora INT NOT NULL,
	dia_semana ENUM('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
	fecha DATE NOT NULL,
	hora_inicio TIME NOT NULL DEFAULT (CURRENT_TIME),
	hora_fin TIME,
	FOREIGN KEY (id_usuario, id_vehiculo) REFERENCES Posee(id_usuario, id_vehiculo),
	FOREIGN KEY (id_cuadra) REFERENCES Cuadra(id),
	FOREIGN KEY (numero_hora, dia_semana) REFERENCES Horario(numero_hora, dia_semana),
	CHECK (hora_fin IS NULL OR hora_fin > hora_inicio)
);

CREATE TABLE IF NOT EXISTS Sesion (
	token VARCHAR(64) PRIMARY KEY,
	id_usuario INT NOT NULL,
	fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY (id_usuario) REFERENCES Usuario(id) ON DELETE CASCADE
);




/* Importar Conteo Vehicular de la IM */
CREATE TABLE IF NOT EXISTS conteo_dataset (
	id INT AUTO_INCREMENT PRIMARY KEY,
	fecha DATE NOT NULL,
	hora_time TIME NOT NULL,
	latitud DECIMAL(10,7) NOT NULL,
	longitud DECIMAL(10,7) NOT NULL,
	vehiculos_hora_carril INT NOT NULL
);
LOAD DATA INFILE '/var/lib/mysql/conteoFiltrado.csv'
INTO TABLE conteo_dataset
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(fecha, hora_time, latitud, longitud, vehiculos_hora_carril);

CREATE TABLE IF NOT EXISTS Conteo (
	latitud DECIMAL(10,7) NOT NULL,
	longitud DECIMAL(10,7) NOT NULL,
	dia_semana ENUM('lunes','martes','miercoles','jueves','viernes','sabado','domingo') NOT NULL,
	hora INT NOT NULL,
	cantidad_muestras INT NOT NULL,
	promedio_vehiculos_carril REAL NOT NULL,
	PRIMARY KEY(latitud, longitud, dia_semana, hora)
);

INSERT INTO Conteo (latitud, longitud, dia_semana, hora, cantidad_muestras, promedio_vehiculos_carril)
SELECT
	latitud,
	longitud,
	ELT(WEEKDAY(fecha) + 1,'lunes','martes','miercoles','jueves','viernes','sabado','domingo') AS dia_semana,
	HOUR(hora_time) AS hora,
	COUNT(*) AS cantidad_muestras,
	AVG(vehiculos_hora_carril) AS promedio_vehiculos_carril
FROM conteo_dataset
GROUP BY latitud, longitud, dia_semana, hora;




/* Importar Cuadras */
LOAD DATA INFILE '/var/lib/mysql/cuadras.csv'
INTO TABLE Cuadra
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS
(numero_puerta_inicial,numero_puerta_final,calle,latitud,longitud);
