DROP DATABASE IF EXISTS projektzbaz;
CREATE DATABASE projektzbaz;
ALTER DATABASE projektzbaz DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE projektzbaz;

CREATE TABLE uzytkownicy (
uzytk_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nick VARCHAR(32) UNIQUE,
imie VARCHAR(32) NOT NULL,
nazwisko VARCHAR(32) NOT NULL,
haslo VARCHAR(64) NOT NULL,
email VARCHAR(32) UNIQUE,
telefon INT UNSIGNED,
data_ur DATE,
data_rejestracji TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
typ_konta INT DEFAULT 1);

CREATE TABLE stanowiska (
stanowisko_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nazwa VARCHAR(32) );

CREATE TABLE wojewodztwa (
wojewodztwo_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nazwa VARCHAR(32) );

CREATE TABLE miejscowosci (
miejscowosc_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nazwa VARCHAR(32),
wojewodztwo_id INT UNSIGNED);
ALTER TABLE miejscowosci ADD FOREIGN KEY (wojewodztwo_id) REFERENCES wojewodztwa(wojewodztwo_id) ON DELETE SET NULL;

CREATE TABLE uslugi (
usluga_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
nazwa VARCHAR(32),
opis VARCHAR(64) );

CREATE TABLE obiekty (
obiekt_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
miejscowosc_id INT UNSIGNED,
nazwa VARCHAR(32),
adres VARCHAR(32),
kod_pocztowy INT,
czynny_od TIME,
czynny_do TIME,
czynny_dni INT DEFAULT 127);
ALTER TABLE obiekty ADD FOREIGN KEY (miejscowosc_id) REFERENCES miejscowosci(miejscowosc_id) ON DELETE SET NULL;

CREATE TABLE pracownicy (
prac_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
uzytk_id INT UNSIGNED,
obiekt_id INT UNSIGNED,
pesel INT,
stanowisko_id INT UNSIGNED,
szef_id INT UNSIGNED,
pensja INT,
umowa_do DATE);
ALTER TABLE pracownicy ADD FOREIGN KEY (uzytk_id) REFERENCES uzytkownicy(uzytk_id) ON DELETE SET NULL;
ALTER TABLE pracownicy ADD FOREIGN KEY (stanowisko_id) REFERENCES stanowiska(stanowisko_id) ON DELETE SET NULL;
ALTER TABLE pracownicy ADD FOREIGN KEY (obiekt_id) REFERENCES obiekty(obiekt_id) ON DELETE SET NULL;

CREATE TABLE sale (
sala_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
obiekt_id INT UNSIGNED,
nazwa VARCHAR(32),
miejsca INT,
cena INT DEFAULT 50 );
ALTER TABLE sale ADD FOREIGN KEY (obiekt_id) REFERENCES obiekty(obiekt_id) ON DELETE SET NULL;

CREATE TABLE dost_uslugi (
usluga_id INT UNSIGNED,
sala_id INT UNSIGNED,
max_ilosc INT UNSIGNED,
cena FLOAT NOT NULL,
marza FLOAT NOT NULL
);
ALTER TABLE dost_uslugi ADD FOREIGN KEY (usluga_id) REFERENCES uslugi(usluga_id) ON DELETE SET NULL;
ALTER TABLE dost_uslugi ADD FOREIGN KEY (sala_id) REFERENCES sale(sala_id) ON DELETE SET NULL;

CREATE TABLE rezerwacje (
rez_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
uzytk_id INT UNSIGNED,
sala_id INT UNSIGNED,
kiedy DATE,
czas_od TIME,
czas_do TIME,
oplacona BOOLEAN default FALSE );
ALTER TABLE rezerwacje ADD FOREIGN KEY (uzytk_id) REFERENCES uzytkownicy(uzytk_id) ON DELETE SET NULL;
ALTER TABLE rezerwacje ADD FOREIGN KEY (sala_id) REFERENCES sale(sala_id) ON DELETE SET NULL;

CREATE TABLE zam_uslugi (
usluga_id INT UNSIGNED,
rez_id INT UNSIGNED,
ilosc INT UNSIGNED);
ALTER TABLE zam_uslugi ADD FOREIGN KEY (usluga_id) REFERENCES uslugi(usluga_id) ON DELETE SET NULL;
ALTER TABLE zam_uslugi ADD FOREIGN KEY (rez_id) REFERENCES rezerwacje(rez_id) ON DELETE SET NULL;

CREATE TABLE dodatkowe_oplaty (
oplata_id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
obiekt_id INT UNSIGNED,
nazwa VARCHAR(32),
kwota INT,
kiedy DATE);
ALTER TABLE dodatkowe_oplaty ADD FOREIGN KEY (obiekt_id) REFERENCES obiekty(obiekt_id) ON DELETE SET NULL;
