CREATE DATABASE myuni;

USE myuni;

CREATE TABLE utenti (
    email varchar(255) primary key,
    password varchar(255) not null,
    budget decimal(6,2) default 500
);