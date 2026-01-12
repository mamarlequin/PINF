CREATE TABLE Utilisateur (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    promotion INT NULL,
    motDePasse VARCHAR(255) NOT NULL,
    role VARCHAR(255) NOT NULL,
    adresseMail VARCHAR(255) NOT NULL,
    dateFinRole DATE NULL
);

CREATE TABLE Equipement (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    type VARCHAR(255) NOT NULL,
    disponible BOOLEAN NOT NULL,
    description VARCHAR(255) NOT NULL,
    risque VARCHAR(255) NOT NULL
);

CREATE TABLE Creneau (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    heureDebut DATETIME NOT NULL,
    heureFin DATETIME NOT NULL,
    idEquipement INT UNSIGNED NOT NULL,
    idUser INT UNSIGNED NOT NULL
);

CREATE TABLE Commentaire (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    idEquipement INT UNSIGNED NOT NULL,
    idUser INT UNSIGNED NOT NULL,
    idCreneau INT UNSIGNED NULL,
    resolu BOOLEAN NOT NULL
);

CREATE TABLE Emprunt (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    idUser INT UNSIGNED NOT NULL,
    dateDebut DATE NOT NULL,
    idEquipement INT UNSIGNED NOT NULL,
    dateRenduTheorique DATE NULL,
    dateRenduReel DATE NULL
);

-- 🔗 CLÉS ÉTRANGÈRES

ALTER TABLE Creneau
    ADD CONSTRAINT fk_creneau_equipement
    FOREIGN KEY (idEquipement) REFERENCES Equipement(id),
    ADD CONSTRAINT fk_creneau_user
    FOREIGN KEY (idUser) REFERENCES Utilisateur(id);

ALTER TABLE Commentaire
    ADD CONSTRAINT fk_commentaire_equipement
    FOREIGN KEY (idEquipement) REFERENCES Equipement(id),
    ADD CONSTRAINT fk_commentaire_user
    FOREIGN KEY (idUser) REFERENCES Utilisateur(id),
    ADD CONSTRAINT fk_commentaire_creneau
    FOREIGN KEY (idCreneau) REFERENCES Creneau(id);

ALTER TABLE Emprunt
    ADD CONSTRAINT fk_emprunt_user
    FOREIGN KEY (idUser) REFERENCES Utilisateur(id),
    ADD CONSTRAINT fk_emprunt_equipement
    FOREIGN KEY (idEquipement) REFERENCES Equipement(id);
