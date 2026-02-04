CREATE TABLE Utilisateur (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    promotion INT NULL,
    motDePasse VARCHAR(255) NOT NULL,
    role INT NOT NULL,
    adresseMail VARCHAR(255) NOT NULL UNIQUE,
    dateFinRole DATE NULL
);

CREATE TABLE Equipement (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    enMaintenance TINYINT(1) NOT NULL DEFAULT 0,
    description VARCHAR(255) NOT NULL,
    risque VARCHAR(255) NOT NULL
);

CREATE TABLE Reservation (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dateDebut DATETIME NOT NULL,
    dateFin DATETIME NOT NULL,
    idEquipement INT UNSIGNED NOT NULL,
    idUser INT UNSIGNED NOT NULL,
    CONSTRAINT reservation_idequipement_fk
        FOREIGN KEY (idEquipement) REFERENCES Equipement(id)
        ON DELETE CASCADE,
    CONSTRAINT reservation_iduser_fk
        FOREIGN KEY (idUser) REFERENCES Utilisateur(id)
        ON DELETE CASCADE
);

CREATE TABLE Emprunt (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idUser INT UNSIGNED NOT NULL,
    idEquipement INT UNSIGNED NOT NULL,
    dateDebut DATE NOT NULL,
    dateRenduTheorique DATE NOT NULL,
    dateRenduReel DATE NULL,
    CONSTRAINT emprunt_iduser_fk
        FOREIGN KEY (idUser) REFERENCES Utilisateur(id)
        ON DELETE CASCADE,
    CONSTRAINT emprunt_idequipement_fk
        FOREIGN KEY (idEquipement) REFERENCES Equipement(id)
        ON DELETE CASCADE
);

CREATE TABLE Commentaire (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idEquipement INT UNSIGNED NOT NULL,
    idUser INT UNSIGNED NOT NULL,
    idReservation INT UNSIGNED NULL,
    resolu TINYINT(1) NOT NULL DEFAULT 0,
    CONSTRAINT commentaire_idequipement_fk
        FOREIGN KEY (idEquipement) REFERENCES Equipement(id)
        ON DELETE CASCADE,
    CONSTRAINT commentaire_iduser_fk
        FOREIGN KEY (idUser) REFERENCES Utilisateur(id)
        ON DELETE CASCADE,
    CONSTRAINT commentaire_idreservation_fk
        FOREIGN KEY (idReservation) REFERENCES Reservation(id)
        ON DELETE SET NULL
);

CREATE TABLE Creneau (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idAdmin INT UNSIGNED NOT NULL,
    dateDebut DATETIME NOT NULL,
    dateFin DATETIME NOT NULL,
    CONSTRAINT creneau_idadmin_fk
        FOREIGN KEY (idAdmin) REFERENCES Utilisateur(id)
        ON DELETE CASCADE
);
