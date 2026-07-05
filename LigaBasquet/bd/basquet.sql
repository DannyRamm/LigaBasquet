-- ==================================================
-- BASE DE DATOS
-- ==================================================
CREATE DATABASE basquet;
USE basquet;

-- ==================================================
-- ESTRUCTURA: TABLAS
-- ==================================================

-- =========================
-- ROL
-- =========================
CREATE TABLE Rol (
    codRol INT AUTO_INCREMENT PRIMARY KEY,
    nomRol VARCHAR(50) NOT NULL
);

-- =========================
-- USUARIO
-- =========================
CREATE TABLE Usuario (
    codUsu INT AUTO_INCREMENT PRIMARY KEY,
    nomUsu VARCHAR(100),
    apeUsu VARCHAR(100),
    corUsu VARCHAR(100) UNIQUE NOT NULL, -- correo
    pasUsu VARCHAR(255) NOT NULL, -- contraseña/password
    mesNacUsu TINYINT,
    anioNacUsu SMALLINT,
    paisUsu VARCHAR(80) DEFAULT 'Perú',
    aceptaTerminosUsu TINYINT(1) NOT NULL DEFAULT 0,
    aceptaMarketingUsu TINYINT(1) NOT NULL DEFAULT 0,
    fecRegUsu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    codRol INT,
    FOREIGN KEY (codRol) REFERENCES Rol(codRol)
);

-- =========================
-- LIGA
-- =========================
CREATE TABLE Liga (
    codLig INT AUTO_INCREMENT PRIMARY KEY,
    nomLig VARCHAR(100)
);

-- =========================
-- TEMPORADA
-- =========================
CREATE TABLE Temporada (
    codTem INT AUTO_INCREMENT PRIMARY KEY,
    nomTem VARCHAR(50),
    feciniTem DATE, -- fecha inicio
    fecfinTem DATE, -- fecha fin
    codLig INT,
    FOREIGN KEY (codLig) REFERENCES Liga(codLig)
);

-- =========================
-- EQUIPO
-- =========================
CREATE TABLE Equipo (
    codEqui INT AUTO_INCREMENT PRIMARY KEY,
    nomEqui VARCHAR(100),
    ciuEqui VARCHAR(100) -- ciudad
);

-- =========================
-- PARTICIPACION
-- =========================
CREATE TABLE Participacion (
    codPar INT AUTO_INCREMENT PRIMARY KEY,
    codEqui INT,
    codTem INT,
    puntotPar INT DEFAULT 0, -- puntos totales
    posfinPar INT, -- posicion final
    FOREIGN KEY (codEqui) REFERENCES Equipo(codEqui),
    FOREIGN KEY (codTem) REFERENCES Temporada(codTem),
    UNIQUE (codEqui, codTem) -- evita duplicados
);

-- =========================
-- USUARIO_EQUIPO
-- =========================
CREATE TABLE Usuario_Equipo (
    codUsu INT,
    codEqui INT,
    rolequiUsu VARCHAR(50), -- rol equipo
    PRIMARY KEY (codUsu, codEqui),
    FOREIGN KEY (codUsu) REFERENCES Usuario(codUsu),
    FOREIGN KEY (codEqui) REFERENCES Equipo(codEqui)
);

-- =========================
-- JUGADOR
-- =========================
CREATE TABLE Jugador (
    codJug INT AUTO_INCREMENT PRIMARY KEY,
    nomJug VARCHAR(100),
    edaJug INT,
    altJug DECIMAL(4,2) -- altura
);

-- =========================
-- JUGADOR_EQUIPO
-- =========================
CREATE TABLE Jugador_Equipo (
    codJugEqui INT AUTO_INCREMENT PRIMARY KEY,
    codJug INT,
    codEqui INT,
    feciniJugEqui DATE, -- fecha inicio
    fecfinJugEqui DATE, -- fecha fin
    FOREIGN KEY (codJug) REFERENCES Jugador(codJug),
    FOREIGN KEY (codEqui) REFERENCES Equipo(codEqui)
);

-- =========================
-- CANCHA
-- =========================
CREATE TABLE Cancha (
    codCan INT AUTO_INCREMENT PRIMARY KEY,
    nomCan VARCHAR(100),
    ubiCan VARCHAR(150) -- ubicacion
);

-- =========================
-- ARBITRO
-- =========================
CREATE TABLE Arbitro (
    codArb INT AUTO_INCREMENT PRIMARY KEY,
    nomArb VARCHAR(100)
);

-- =========================
-- PARTIDO
-- =========================
CREATE TABLE Partido (
    codMat INT AUTO_INCREMENT PRIMARY KEY,
    codTem INT,
    codequilocMat INT, -- cod equipo local
    codequivisMat INT, -- cod equipo visitante
    codCan INT,
    fecMat DATETIME, -- fecha
    estMat ENUM('pendiente','jugado','cancelado'), -- estado
    punlocMat INT, -- puntaje local
    punvisMat INT, -- puntaje visitante
    FOREIGN KEY (codTem) REFERENCES Temporada(codTem),
    FOREIGN KEY (codequilocMat) REFERENCES Equipo(codEqui),
    FOREIGN KEY (codequivisMat) REFERENCES Equipo(codEqui),
    FOREIGN KEY (codCan) REFERENCES Cancha(codCan),
    CHECK (codequilocMat <> codequivisMat) -- evita mismo equipo
);

-- =========================
-- ARBITRO_PARTIDO
-- =========================
CREATE TABLE Arbitro_Partido (
    codArb INT,
    codMat INT,
    PRIMARY KEY (codArb, codMat),
    FOREIGN KEY (codArb) REFERENCES Arbitro(codArb),
    FOREIGN KEY (codMat) REFERENCES Partido(codMat)
);

-- =========================
-- ESTADISTICA
-- =========================
CREATE TABLE Estadistica (
    codEst INT AUTO_INCREMENT PRIMARY KEY,
    codMat INT,
    codJug INT,
    punEst INT,	-- puntos
    rebEst INT, -- rebotes
    asiEst INT, -- asistencias
    falEst INT, -- faltas
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

ALTER TABLE Noticia ADD COLUMN categoriaNot VARCHAR(50) DEFAULT 'General';
ALTER TABLE Noticia ADD COLUMN imagenNot VARCHAR(255) DEFAULT 'default-news.jpg';
-- =========================
-- CONFIGURACION
-- =========================
CREATE TABLE Configuracion (
    codConf INT AUTO_INCREMENT PRIMARY KEY,
    claveConf VARCHAR(50) NOT NULL UNIQUE,
    valorConf TEXT,
    descripcionConf VARCHAR(255)
);

-- ==================================================
-- ESTRUCTURA: ALTERACIONES
-- ==================================================

-- Agregar columna tipoContrato a Jugador_Equipo
ALTER TABLE Jugador_Equipo ADD COLUMN tipoContrato VARCHAR(20) DEFAULT 'estandar';

-- ==================================================
-- DATOS: INSERTS
-- ==================================================

-- =========================
-- ROL
-- ARREGLADO: esta tabla nunca se llenaba, y Usuario inserta
-- codRol 1, 2, 3, 4 más abajo, lo que rompía la FK. Se agregan
-- los 4 roles pedidos: Admin, Entrenador, Jugador, Usuario.
-- =========================
INSERT INTO Rol (nomRol) VALUES
('Admin'),
('Entrenador'),
('Jugador'),
('Usuario');

-- =========================
-- LIGA
-- ARREGLADO: no existía ningún registro; Temporada y, por
-- cadena, Partido dependen de ella. Se agrega una liga base.
-- =========================
INSERT INTO Liga (nomLig) VALUES
('LeagueDan');

-- =========================
-- TEMPORADA
-- ARREGLADO: no existía ningún registro; Partido depende de
-- codTem. Se agrega la temporada 2024 referenciando la Liga
-- insertada arriba (codLig = 1).
-- =========================
INSERT INTO Temporada (nomTem, feciniTem, fecfinTem, codLig) VALUES
('Temporada 2024', '2024-10-01', '2025-04-30', 1);

-- =========================
-- EQUIPO
-- (ya estaba bien, sin cambios)
-- =========================
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

-- =========================
-- CANCHA
-- ARREGLADO: no existía ningún registro; Partido depende de
-- codCan. Se agrega una cancha base.
-- =========================
INSERT INTO Cancha (nomCan, ubiCan) VALUES
('Coliseo Central', 'Sede Principal LeagueDan');

-- =========================
-- PARTIDO
-- ARREGLADO: no existía ningún registro, pero Estadistica
-- inserta filas con codMat 1 y 2 más abajo, lo que rompía esa
-- FK. Se agregan 2 partidos (codTem = 1, codCan = 1) usando
-- equipos ya insertados arriba (Lakers=1, Celtics=2, Warriors=3).
-- =========================
INSERT INTO Partido (codTem, codequilocMat, codequivisMat, codCan, fecMat, estMat, punlocMat, punvisMat) VALUES
(1, 1, 2, 1, '2024-10-05 19:00:00', 'jugado', 102, 98),
(1, 3, 1, 1, '2024-10-08 19:30:00', 'jugado', 110, 105);

-- =========================
-- JUGADOR
-- (ya estaba bien, sin cambios)
-- =========================
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

-- =========================
-- JUGADOR_EQUIPO
-- (ya estaba bien, sin cambios)
-- =========================
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

-- =========================
-- CONFIGURACION
-- (ya estaba bien, sin cambios)
-- =========================
INSERT INTO Configuracion (claveConf, valorConf, descripcionConf) VALUES
('max_jugadores_estandar', '15', 'Máximo de jugadores con contrato estándar por equipo'),
('max_jugadores_two_way', '3', 'Máximo de jugadores con contrato two-way por equipo'),
('min_jugadores_estandar', '14', 'Mínimo de jugadores con contrato estándar por equipo');

-- =========================
-- ESTADISTICA (ejemplo)
-- ARREGLADO: referenciaba codMat 1 y 2, que no existían en
-- Partido y rompían la FK. Ahora sí existen (ver sección PARTIDO).
-- =========================
INSERT INTO Estadistica (codMat, codJug, punEst, rebEst, asiEst, falEst) VALUES
(1, 1, 25, 10, 5, 2),
(1, 2, 20, 8, 3, 1),
(2, 3, 15, 12, 7, 3);

-- =========================
-- NOTICIA
-- (ya estaba bien, sin cambios)
-- =========================
INSERT INTO Noticia (tituloNot, contenidoNot, fechaNot) VALUES
('Inicio de Temporada 2024', 'La nueva temporada de LeagueDan comienza con grandes expectativas. Los equipos se preparan para una competencia intensa llena de emoción y talento.', '2024-10-01'),
('Nuevo Jugador en Lakers', 'Los Lakers anuncian la incorporación de un nuevo talento prometedor que fortalecerá su roster para la temporada.', '2024-09-15'),
('Cambio de Reglas', 'Se implementan nuevas reglas para mejorar el espectáculo y la seguridad en los partidos de la liga.', '2024-08-20');
INSERT INTO Noticia (tituloNot, contenidoNot, fechaNot, categoriaNot, imagenNot) VALUES
('LeagueDan confirma horarios y sedes para la próxima fecha del torneo', 'La organización del torneo LeagueDan hizo oficial el calendario completo para la jornada que se disputará este fin de semana. Tras reuniones con los delegados de los clubes, se confirmaron los horarios de alta competencia y las sedes principales que contarán con arbitraje profesionalizado. El partido de fondo promete chispas entre los dos líderes del campeonato.', '2026-07-05', 'Breaking', 'programacion.jpg'),
('Imparables: Halcones mantiene el primer lugar con una racha histórica', 'Con una defensa zonal impecable y una efectividad del 45% en tiros de tres puntos, el conjunto de Halcones logró derrotar a su clásico rival en un cierre de partido no apto para cardíacos. Con este resultado, el líder absoluto de la competencia asegura su ventaja mínima de cara a la fase eliminatoria del torneo regional.', '2026-07-04', 'Equipos', 'halcones.jpg'),
('¡Poder ofensivo! El base de Titanes es elegido el MVP tras anotar 31 puntos', 'Una actuación histórica se vivió en la última jornada. El armador estrella de Titanes se echó el equipo al hombro completando una planilla espectacular: 31 puntos, 8 asistencias y 4 robos de balón. Su capacidad para romper la defensa en transición dejó sin opciones al rival y lo posiciona como el máximo anotador de la semana.', '2026-07-03', 'Jugadores', 'mvp.jpg'),
('Alerta de fichaje: Condores refuerza su pintura con un nuevo pivot', 'La directiva de Condores sorprendió al mercado de pases tras anunciar la incorporación de un nuevo centro de 2.03 metros de altura. El jugador llega con el objetivo claro de potenciar el juego interior del roster, corregir las falencias en los rebotes defensivos y brindar mayor rotación para la etapa definitoria del campeonato.', '2026-07-02', 'Fichajes', 'fichaje.jpg'),
('Estrategia pura: Los cambios tácticos que están revolucionando la liga', 'Varios entrenadores de la liga están implementando sistemas de juego rápido o "Run and Gun", priorizando las posesiones cortas y los tiros perimetrales. Analizamos cómo este cambio en las pizarras ha aumentado el promedio de anotación por partido, convirtiendo a LeagueDan en uno de los espectáculos más dinámicos de la temporada.', '2026-07-01', 'Táctica', 'tactica.jpg');
-- =========================
-- USUARIO
-- (ya estaba bien, sin cambios; ahora sí funciona porque
-- Rol tiene los 4 registros necesarios)
-- =========================
INSERT INTO Usuario (nomUsu, corUsu, pasUsu, codRol, fecRegUsu) VALUES
('Admin User', 'admin@leaguedan.com', 'admin123', 1, NOW()),
('Staff User', 'staff@leaguedan.com', 'staff123', 2, NOW()),
('Player User', 'player@leaguedan.com', 'player123', 3, NOW()),
('Normal User', 'user@leaguedan.com', 'user123', 4, NOW());