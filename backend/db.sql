CREATE DATABASE myuni;

USE myuni;

CREATE TABLE utenti (
    email varchar(255) primary key,
    nome varchar(255) not null,
    cognome varchar(255) not null,
    password varchar(255) not null,
    budget_mensile decimal(6,2) default 500,
    data_iscrizione DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorie (
    ID varchar(255) primary key,
    denominazione varchar(255) not null
);

INSERT INTO categorie (id, denominazione) values ("cibo", "Spesa e cibo"), ("casa", "Casa e utenze"), ("studio", "Università e studio"), ("trasporti", "Trasporti e viaggi"), ("svago", "Svago e personale"), ("extra", "Extra");

CREATE TABLE spese (
    ID int auto_increment primary key,
    email_utente varchar(255) not null,
    importo decimal(6,2) not null,
    descrizione varchar(255),
    id_categoria varchar(255) not null,
    data DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (email_utente) REFERENCES utenti (email),
    FOREIGN KEY (id_categoria) REFERENCES categorie (ID),
    ON DELETE CASCADE
    ON UPDATE CASCADE;
);

CREATE TABLE articoli (
    ID int auto_increment primary key,
    email_utente varchar(255) not null,
    descrizione varchar(255) not null,
    checked boolean default false,
    FOREIGN KEY (email_utente) REFERENCES utenti (email)
);