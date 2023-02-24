

CREATE TABLE uzytkownicy (
    nazwa VARCHAR(64) PRIMARY KEY,
    haslo VARCHAR(64) NOT NULL
);

CREATE TABLE Kraj (
    id_kraju SERIAL PRIMARY KEY,
    nazwa_kraju VARCHAR(64) NOT NULL UNIQUE
);

CREATE TABLE Konkurs
(
    id_konkursu SERIAL PRIMARY KEY,
    nazwa_konkursu VARCHAR(64) NOT NULL UNIQUE,
    termin_zapisow DATE NOT NULL,
    czy_zgloszenia_otwarte BOOLEAN NOT NULL,
    czy_podczas_kwalfikacji BOOLEAN NOT NULL,
    czy_podczas_serii_pierwszej BOOLEAN NOT NULL,
    czy_podczas_serii_drugiej BOOLEAN NOT NULL,
    czy_konkurs_zakonczony BOOLEAN NOT NULL,
    kraj_organizatora VARCHAR(64) NOT NULL REFERENCES Kraj(nazwa_kraju)
);

CREATE TABLE Kwota
(
    id_kraju INTEGER NOT NULL REFERENCES Kraj(id_kraju),
    id_konkursu INTEGER NOT NULL REFERENCES Konkurs(id_konkursu),
    kwota INTEGER NOT NULL,
    CONSTRAINT PK_Kwota PRIMARY KEY (id_kraju, id_konkursu)
);

CREATE TABLE Zawodnik (
    id_zawodnika SERIAL PRIMARY KEY,
    imie VARCHAR(64) NOT NULL,
    nazwisko VARCHAR(64) NOT NULL,
    id_kraju INTEGER NOT NULL REFERENCES Kraj
);

CREATE TABLE Zgloszenie (
    id_zgloszenia SERIAL PRIMARY KEY,
    id_konkursu INTEGER NOT NULL REFERENCES Konkurs,
    id_zawodnika INTEGER NOT NULL REFERENCES Zawodnik
);

CREATE TABLE Skok (
    id_skoku SERIAL PRIMARY KEY,
    odleglosc NUMERIC(16) NOT NULL,
    punkty NUMERIC(16) NOT NULL,
    czy_seria_kwalfikacyjna BOOLEAN NOT NULL,
    czy_seria_pierwsza BOOLEAN NOT NULL,
    czy_seria_druga BOOLEAN NOT NULL,
    id_zgloszenia INTEGER NOT NULL REFERENCES Zgloszenie,
    czy_zdyskwalifikowany BOOLEAN NOT NULL,
    numer_startowy SERIAL NOT NULL
);

CREATE OR REPLACE FUNCTION czy_konkurs_istnieje(podana_nazwa VARCHAR(64)) RETURNS BOOLEAN AS $$
DECLARE count INTEGER;
BEGIN
SELECT count(*) INTO count
FROM konkurs
WHERE nazwa_konkursu = podana_nazwa;
IF count > 0 THEN RETURN true;
ELSE RETURN false;
END IF;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION czy_nazwa_konkursu_ok() RETURNS TRIGGER AS $$
BEGIN
   IF NEW.nazwa_konkursu ~ '^[a-zA-Z]*$' THEN 
     return new;
   ELSE
     RAISE EXCEPTION 'Only characters from A to Z allowed!' USING ERRCODE='20808';
   END IF;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER czy_nazwa_konkursu_ok_trigger BEFORE INSERT OR UPDATE 
    ON konkurs
    for each ROW 
    EXECUTE PROCEDURE czy_nazwa_konkursu_ok();