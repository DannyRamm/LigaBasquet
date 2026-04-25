
create database basquet;

use basquet;


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
    corUsu VARCHAR(100) UNIQUE NOT NULL, -- correo
    pasUsu VARCHAR(255) NOT NULL, -- contraseña/password
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