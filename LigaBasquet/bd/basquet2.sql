-- 1. Crear Base de Datos
DROP DATABASE IF EXISTS basquet;
CREATE DATABASE basquet;
USE basquet;

-- 2. Estructura de Tablas (Sin cambios, solo ordenadas)
CREATE TABLE Rol (
    codRol INT AUTO_INCREMENT PRIMARY KEY,
    nomRol VARCHAR(50) NOT NULL
);

CREATE TABLE Usuario (
    codUsu INT AUTO_INCREMENT PRIMARY KEY,
    nomUsu VARCHAR(100),
    apeUsu VARCHAR(100),
    corUsu VARCHAR(100) UNIQUE NOT NULL,
    pasUsu VARCHAR(255) NOT NULL,
    mesNacUsu TINYINT,
    anioNacUsu SMALLINT,
    paisUsu VARCHAR(80) DEFAULT 'Perú',
    aceptaTerminosUsu TINYINT(1) NOT NULL DEFAULT 0,
    aceptaMarketingUsu TINYINT(1) NOT NULL DEFAULT 0,
    fecRegUsu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    codRol INT,
    FOREIGN KEY (codRol) REFERENCES Rol(codRol)
);

CREATE TABLE Liga (
    codLig INT AUTO_INCREMENT PRIMARY KEY,
    nomLig VARCHAR(100)
);

CREATE TABLE Temporada (
    codTem INT AUTO_INCREMENT PRIMARY KEY,
    nomTem VARCHAR(50),
    feciniTem DATE,
    fecfinTem DATE,
    codLig INT,
    FOREIGN KEY (codLig) REFERENCES Liga(codLig)
);

CREATE TABLE Equipo (
    codEqui INT AUTO_INCREMENT PRIMARY KEY,
    nomEqui VARCHAR(100),
    ciuEqui VARCHAR(100)
);

CREATE TABLE Participacion (
    codPar INT AUTO_INCREMENT PRIMARY KEY,
    codEqui INT,
    codTem INT,
    puntotPar INT DEFAULT 0,
    posfinPar INT,
    FOREIGN KEY (codEqui) REFERENCES Equipo(codEqui),
    FOREIGN KEY (codTem) REFERENCES Temporada(codTem),
    UNIQUE (codEqui, codTem)
);

CREATE TABLE Usuario_Equipo (
    codUsu INT,
    codEqui INT,
    rolequiUsu VARCHAR(50),
    PRIMARY KEY (codUsu, codEqui),
    FOREIGN KEY (codUsu) REFERENCES Usuario(codUsu),
    FOREIGN KEY (codEqui) REFERENCES Equipo(codEqui)
);

CREATE TABLE Jugador (
    codJug INT AUTO_INCREMENT PRIMARY KEY,
    nomJug VARCHAR(100),
    edaJug INT,
    altJug DECIMAL(4,2)
);

CREATE TABLE Jugador_Equipo (
    codJugEqui INT AUTO_INCREMENT PRIMARY KEY,
    codJug INT,
    codEqui INT,
    feciniJugEqui DATE,
    fecfinJugEqui DATE,
    tipoContrato VARCHAR(20) DEFAULT 'estandar', -- Columna agregada aquí
    FOREIGN KEY (codJug) REFERENCES Jugador(codJug),
    FOREIGN KEY (codEqui) REFERENCES Equipo(codEqui)
);

CREATE TABLE Cancha (
    codCan INT AUTO_INCREMENT PRIMARY KEY,
    nomCan VARCHAR(100),
    ubiCan VARCHAR(150)
);

CREATE TABLE Arbitro (
    codArb INT AUTO_INCREMENT PRIMARY KEY,
    nomArb VARCHAR(100)
);

CREATE TABLE Partido (
    codMat INT AUTO_INCREMENT PRIMARY KEY,
    codTem INT,
    codequilocMat INT,
    codequivisMat INT,
    codCan INT,
    fecMat DATETIME,
    estMat ENUM('pendiente','jugado','cancelado'),
    punlocMat INT,
    punvisMat INT,
    FOREIGN KEY (codTem) REFERENCES Temporada(codTem),
    FOREIGN KEY (codequilocMat) REFERENCES Equipo(codEqui),
    FOREIGN KEY (codequivisMat) REFERENCES Equipo(codEqui),
    FOREIGN KEY (codCan) REFERENCES Cancha(codCan),
    CHECK (codequilocMat <> codequivisMat)
);

CREATE TABLE Arbitro_Partido (
    codArb INT,
    codMat INT,
    PRIMARY KEY (codArb, codMat),
    FOREIGN KEY (codArb) REFERENCES Arbitro(codArb),
    FOREIGN KEY (codMat) REFERENCES Partido(codMat)
);

CREATE TABLE Estadistica (
    codEst INT AUTO_INCREMENT PRIMARY KEY,
    codMat INT,
    codJug INT,
    punEst INT,
    rebEst INT,
    asiEst INT,
    falEst INT,
    FOREIGN KEY (codMat) REFERENCES Partido(codMat),
    FOREIGN KEY (codJug) REFERENCES Jugador(codJug)
);

-- =========================
-- NOTICIA
-- =========================
CREATE TABLE Noticia (
    codNot INT AUTO_INCREMENT PRIMARY KEY,
    tituloNot VARCHAR(255) NOT NULL,
    contenidoNot TEXT,
    fechaNot DATE NOT NULL
);
CREATE TABLE Configuracion (
    codConf INT AUTO_INCREMENT PRIMARY KEY,
    claveConf VARCHAR(50) NOT NULL UNIQUE,
    valorConf TEXT,
    descripcionConf VARCHAR(255)
);

-- 3. Inserción de Datos (ORDEN CORREGIDO)

-- Roles base
INSERT INTO Rol (nomRol) VALUES ('Admin'), ('Entrenador'), ('Jugador');

-- Equipos (Los 29 que pasaste)
INSERT INTO Equipo (nomEqui, ciuEqui) VALUES
('Los Angeles Lakers', 'Los Angeles'), ('Boston Celtics', 'Boston'), 
('Golden State Warriors', 'San Francisco'), ('Miami Heat', 'Miami'),
('Dallas Mavericks', 'Dallas'), ('Phoenix Suns', 'Phoenix'),
('Denver Nuggets', 'Denver'), ('Milwaukee Bucks', 'Milwaukee'),
('Philadelphia 76ers', 'Philadelphia'), ('Los Angeles Clippers', 'Los Angeles'),
('Brooklyn Nets', 'Brooklyn'), ('Utah Jazz', 'Salt Lake City'),
('Chicago Bulls', 'Chicago'), ('San Antonio Spurs', 'San Antonio'),
('Portland Trail Blazers', 'Portland'), ('Sacramento Kings', 'Sacramento'),
('New Orleans Pelicans', 'New Orleans'), ('Toronto Raptors', 'Toronto'),
('Minnesota Timberwolves', 'Minneapolis'), ('Oklahoma City Thunder', 'Oklahoma City'),
('Washington Wizards', 'Washington'), ('Orlando Magic', 'Orlando'),
('Charlotte Hornets', 'Charlotte'), ('Detroit Pistons', 'Detroit'),
('Indiana Pacers', 'Indianapolis'), ('Atlanta Hawks', 'Atlanta'),
('Cleveland Cavaliers', 'Cleveland'), ('New York Knicks', 'New York'),
('Memphis Grizzlies', 'Memphis');

-- Datos necesarios para que Estadistica no falle (Ejemplos mínimos)
INSERT INTO Liga (nomLig) VALUES ('NBA');
INSERT INTO Temporada (nomTem, codLig) VALUES ('Temporada Regular 2026', 1);
INSERT INTO Jugador (nomJug, edaJug, altJug) VALUES ('LeBron James', 39, 2.06), ('Stephen Curry', 36, 1.88), ('Nikola Jokic', 29, 2.11);
INSERT INTO Cancha (nomCan, ubiCan) VALUES ('Crypto.com Arena', 'Los Angeles');
INSERT INTO Partido (codTem, codequilocMat, codequivisMat, codCan, estMat) VALUES (1, 1, 2, 1, 'jugado');

-- Estadísticas (Ahora sí hay Partido 1 y Jugadores del 1 al 3)
INSERT INTO Estadistica (codMat, codJug, punEst, rebEst, asiEst, falEst) VALUES
(1, 1, 25, 10, 5, 2),
(1, 2, 20, 8, 3, 1),
(1, 3, 15, 12, 7, 3);


-- Configuraciones
INSERT INTO Configuracion (claveConf, valorConf, descripcionConf) VALUES
('max_jugadores_estandar', '15', 'Máximo de jugadores con contrato estándar por equipo'),
('max_jugadores_two_way', '3', 'Máximo de jugadores con contrato two-way por equipo'),
('min_jugadores_estandar', '14', 'Mínimo de jugadores con contrato estándar por equipo');


-- Insertar equipos adicionales
INSERT INTO Equipo (nomEqui, ciuEqui) VALUES
('Los Angeles Lakers', 'Los Angeles'),
('Boston Celtics', 'Boston'),
('Golden State Warriors', 'San Francisco'),
('Miami Heat', 'Miami'),
('Dallas Mavericks', 'Dallas'),
('Phoenix Suns', 'Phoenix'),
('Denver Nuggets', 'Denver'),
('Milwaukee Bucks', 'Milwaukee'),
('Philadelphia 76ers', 'Philadelphia'),
('Los Angeles Clippers', 'Los Angeles'),
('Brooklyn Nets', 'Brooklyn'),
('Utah Jazz', 'Salt Lake City'),
('Chicago Bulls', 'Chicago'),
('San Antonio Spurs', 'San Antonio'),
('Portland Trail Blazers', 'Portland'),
('Sacramento Kings', 'Sacramento'),
('New Orleans Pelicans', 'New Orleans'),
('Toronto Raptors', 'Toronto'),
('Minnesota Timberwolves', 'Minneapolis'),
('Oklahoma City Thunder', 'Oklahoma City'),
('Washington Wizards', 'Washington'),
('Orlando Magic', 'Orlando'),
('Charlotte Hornets', 'Charlotte'),
('Detroit Pistons', 'Detroit'),
('Indiana Pacers', 'Indianapolis'),
('Atlanta Hawks', 'Atlanta'),
('Cleveland Cavaliers', 'Cleveland'),
('New York Knicks', 'New York'),
('Memphis Grizzlies', 'Memphis');


INSERT INTO Jugador (nomJug, edaJug, altJug) VALUES
('Juan Pérez', 25, 1.85),
('María López', 22, 1.78),
('Carlos García', 28, 1.92),
('Ana Martínez', 24, 1.75),
('Pedro Sánchez', 26, 1.88),
('Laura Rodríguez', 23, 1.80),
('Miguel Fernández', 27, 1.90),
('Carmen González', 21, 1.76),
('José Hernández', 29, 1.95),
('Isabel Jiménez', 25, 1.82),
('Antonio Ruiz', 24, 1.87),
('Pilar Díaz', 22, 1.79),
('Francisco Moreno', 26, 1.93),
('Rosa Muñoz', 23, 1.77),
('Luis Álvarez', 28, 1.89),
('Elena Romero', 24, 1.81),
('Javier Navarro', 27, 1.91),
('Teresa Torres', 22, 1.74),
('David Ramos', 25, 1.86),
('Cristina Gil', 26, 1.83),
('Manuel Serrano', 24, 1.88),
('Lucía Vega', 23, 1.78),
('Rafael Castro', 27, 1.94),
('Mónica Ortega', 21, 1.75),
('Sergio Delgado', 28, 1.90),
('Nuria Rubio', 25, 1.82),
('Alberto Guerrero', 26, 1.87),
('Silvia Morales', 24, 1.79),
('Fernando Vargas', 29, 1.96),
('Beatriz Flores', 22, 1.76),
('Pablo Herrera', 27, 1.89),
('Inés Medina', 23, 1.80),
('Rubén Castro', 25, 1.85),
('Alicia León', 24, 1.77),
('Iván Peña', 26, 1.92),
('Patricia Soto', 22, 1.81),
('Adrián Cruz', 28, 1.88),
('Sonia Aguilar', 25, 1.79),
('Hugo Reyes', 24, 1.91),
('Marta Blanco', 23, 1.74),
('Diego Mendoza', 27, 1.86),
('Teresa Vega', 26, 1.83),
('Álvaro Cortés', 25, 1.90),
('Irene Pardo', 24, 1.78),
('Óscar Molina', 28, 1.95),
('Eva Suárez', 22, 1.76),
('Raúl Iglesias', 26, 1.87),
('Natalia Cabrera', 23, 1.80),
('Víctor Fuentes', 27, 1.89),
('Clara Marín', 25, 1.82),
('Joaquín Silva', 24, 1.84),
('Lorena Campos', 26, 1.77),
('Eduardo Ponce', 28, 1.93),
('Pilar Vega', 22, 1.75);


-- Asignar jugadores a equipos (ejemplo: asignar algunos a equipos 1-5, con contratos estándar y two-way)
INSERT INTO Jugador_Equipo (codJug, codEqui, feciniJugEqui, tipoContrato) VALUES
(1, 1, CURDATE(), 'estandar'),
(2, 1, CURDATE(), 'estandar'),
(3, 1, CURDATE(), 'estandar'),
(4, 1, CURDATE(), 'estandar'),
(5, 1, CURDATE(), 'estandar'),
(6, 1, CURDATE(), 'estandar'),
(7, 1, CURDATE(), 'estandar'),
(8, 1, CURDATE(), 'estandar'),
(9, 1, CURDATE(), 'estandar'),
(10, 1, CURDATE(), 'estandar'),
(11, 1, CURDATE(), 'estandar'),
(12, 1, CURDATE(), 'estandar'),
(13, 1, CURDATE(), 'estandar'),
(14, 1, CURDATE(), 'estandar'),
(15, 1, CURDATE(), 'estandar'),
(16, 1, CURDATE(), 'two-way'),
(17, 1, CURDATE(), 'two-way'),
(18, 1, CURDATE(), 'two-way'),
(19, 2, CURDATE(), 'estandar'),
(20, 2, CURDATE(), 'estandar'),
(21, 2, CURDATE(), 'estandar'),
(22, 2, CURDATE(), 'estandar'),
(23, 2, CURDATE(), 'estandar'),
(24, 2, CURDATE(), 'estandar'),
(25, 2, CURDATE(), 'estandar'),
(26, 2, CURDATE(), 'estandar'),
(27, 2, CURDATE(), 'estandar'),
(28, 2, CURDATE(), 'estandar'),
(29, 2, CURDATE(), 'estandar'),
(30, 2, CURDATE(), 'estandar'),
(31, 2, CURDATE(), 'estandar'),
(32, 2, CURDATE(), 'estandar'),
(33, 2, CURDATE(), 'estandar'),
(34, 2, CURDATE(), 'two-way'),
(35, 2, CURDATE(), 'two-way'),
(36, 2, CURDATE(), 'two-way'),
(37, 3, CURDATE(), 'estandar'),
(38, 3, CURDATE(), 'estandar'),
(39, 3, CURDATE(), 'estandar'),
(40, 3, CURDATE(), 'estandar'),
(41, 3, CURDATE(), 'estandar'),
(42, 3, CURDATE(), 'estandar'),
(43, 3, CURDATE(), 'estandar'),
(44, 3, CURDATE(), 'estandar'),
(45, 3, CURDATE(), 'estandar'),
(46, 3, CURDATE(), 'estandar'),
(47, 3, CURDATE(), 'estandar'),
(48, 3, CURDATE(), 'estandar'),
(49, 3, CURDATE(), 'estandar'),
(50, 3, CURDATE(), 'estandar'),
(51, 3, CURDATE(), 'estandar'),
(52, 3, CURDATE(), 'two-way'),
(53, 3, CURDATE(), 'two-way'),
(54, 3, CURDATE(), 'two-way');

-- Insertar noticias de ejemplo
INSERT INTO Noticia (tituloNot, contenidoNot, fechaNot) VALUES
('Inicio de Temporada 2024', 'La nueva temporada de LeagueDan comienza con grandes expectativas. Los equipos se preparan para una competencia intensa llena de emoción y talento.', '2024-10-01'),
('Nuevo Jugador en Lakers', 'Los Lakers anuncian la incorporación de un nuevo talento prometedor que fortalecerá su roster para la temporada.', '2024-09-15'),
('Cambio de Reglas', 'Se implementan nuevas reglas para mejorar el espectáculo y la seguridad en los partidos de la liga.', '2024-08-20');
select * from usuario;
