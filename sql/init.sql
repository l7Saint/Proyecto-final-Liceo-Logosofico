CREATE DATABASE IF NOT EXISTS urbanaut;
USE urbanaut;

CREATE TABLE IF NOT EXISTS Usuario (
	id INT AUTO_INCREMENT PRIMARY KEY,
	nombre VARCHAR(40) NOT NULL,
	apellido VARCHAR(40) NOT NULL,
	email VARCHAR(150) NOT NULL,
	contrasena VARCHAR(255) NOT NULL,
	fechaRegistro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS Auto (
	id INT AUTO_INCREMENT PRIMARY KEY,
	matricula VARCHAR(8) NOT NULL,
	color VARCHAR(15) NOT NULL,
	tamano VARCHAR(15) NOT NULL
);

CREATE TABLE IF NOT EXISTS J_Usuario_Auto (
	FK_id_Usuario INT,
	FK_id_Auto INT,

	FOREIGN KEY (FK_id_Usuario) 
		REFERENCES Usuario(id),
	FOREIGN KEY (FK_id_Auto) 
		REFERENCES Auto(id),

	PRIMARY KEY(FK_id_Usuario, FK_id_Auto)
);

CREATE TABLE IF NOT EXISTS Ubicacion (
	id INT AUTO_INCREMENT PRIMARY KEY,
	numeroPuerta INT,
	calle VARCHAR(100) NOT NULL,
	esquina VARCHAR(100),
	latitud REAL,
	longitud REAL
);

CREATE TABLE IF NOT EXISTS Estaciona (
	FK_id_Usuario INT,
	FK_id_Auto INT,
	FK_id_Ubicacion INT,
	horaInicio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	horaFin TIMESTAMP,

	FOREIGN KEY (FK_id_Usuario, FK_id_Auto) 
		REFERENCES J_Usuario_Auto(FK_id_Usuario, FK_id_Auto),
	FOREIGN KEY (FK_id_Ubicacion) 
		REFERENCES Ubicacion(id),

	PRIMARY KEY(FK_id_Usuario, FK_id_Auto, horaInicio, horaFin)
);
