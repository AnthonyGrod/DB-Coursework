CREATE TABLE IF NOT EXISTS konkurs
(
    id_konkursu integer NOT NULL DEFAULT nextval('konkurs_id_konkursu_seq'::regclass),
    nazwa_konkursu character varying(64) NOT NULL,
    termin_zapisow date NOT NULL,
    czy_zgloszenia_otwarte boolean NOT NULL,
    czy_podczas_kwalfikacji boolean NOT NULL,
    czy_podczas_serii_pierwszej boolean NOT NULL,
    czy_podczas_serii_drugiej boolean NOT NULL,
    czy_konkurs_zakonczony boolean NOT NULL,
    kraj_organizatora character varying(64) NOT NULL,
    CONSTRAINT konkurs_pkey PRIMARY KEY (id_konkursu),
    CONSTRAINT konkurs_nazwa_konkursu_key UNIQUE (nazwa_konkursu)
)

CREATE TABLE IF NOT EXISTS kraj
(
    id_kraju integer NOT NULL DEFAULT nextval('kraj_id_kraju_seq'::regclass),
    nazwa_kraju character varying(64) NOT NULL,
    CONSTRAINT kraj_pkey PRIMARY KEY (id_kraju),
    CONSTRAINT kraj_nazwa_kraju_key UNIQUE (nazwa_kraju)
)

CREATE TABLE IF NOT EXISTS kwota
(
    id_kraju INTEGER NOT NULL REFERENCES Kraj(id_kraju),
    id_konkursu INTEGER NOT NULL REFERENCES Konkurs(id_konkursu),
    kwota INTEGER NOT NULL,
    CONSTRAINT PK_Kwota PRIMARY KEY (id_kraju, id_konkursu)
);

CREATE TABLE IF NOT EXISTS skok
(
    id_skoku integer NOT NULL DEFAULT nextval('skok_id_skoku_seq'::regclass),
    odleglosc numeric(16,0) NOT NULL,
    punkty numeric(16,0) NOT NULL,
    czy_seria_kwalfikacyjna boolean NOT NULL,
    czy_seria_pierwsza boolean NOT NULL,
    czy_seria_druga boolean NOT NULL,
    id_zgloszenia integer NOT NULL,
    czy_zdyskwalifikowany boolean NOT NULL,
    numer_startowy integer NOT NULL DEFAULT nextval('skok_numer_startowy_seq'::regclass),
    CONSTRAINT skok_pkey PRIMARY KEY (id_skoku),
    CONSTRAINT skok_id_zgloszenia_fkey FOREIGN KEY (id_zgloszenia)
        REFERENCES public.zgloszenie (id_zgloszenia) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

CREATE TABLE IF NOT EXISTS uzytkownicy
(
    nazwa character varying(64) NOT NULL,
    haslo character varying(64) NOT NULL,
    CONSTRAINT uzytkownicy_pkey PRIMARY KEY (nazwa)
)

CREATE TABLE IF NOT EXISTS zawodnik
(
    id_zawodnika integer NOT NULL DEFAULT nextval('zawodnik_id_zawodnika_seq'::regclass),
    imie character varying(64) NOT NULL,
    nazwisko character varying(64) NOT NULL,
    id_kraju integer NOT NULL,
    CONSTRAINT zawodnik_pkey PRIMARY KEY (id_zawodnika),
    CONSTRAINT zawodnik_id_kraju_fkey FOREIGN KEY (id_kraju)
        REFERENCES public.kraj (id_kraju) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)

CREATE TABLE IF NOT EXISTS zgloszenie
(
    id_zgloszenia integer NOT NULL DEFAULT nextval('zgloszenie_id_zgloszenia_seq'::regclass),
    id_konkursu integer NOT NULL,
    id_zawodnika integer NOT NULL,
    CONSTRAINT zgloszenie_pkey PRIMARY KEY (id_zgloszenia),
    CONSTRAINT zgloszenie_id_konkursu_fkey FOREIGN KEY (id_konkursu)
        REFERENCES public.konkurs (id_konkursu) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION,
    CONSTRAINT zgloszenie_id_zawodnika_fkey FOREIGN KEY (id_zawodnika)
        REFERENCES public.zawodnik (id_zawodnika) MATCH SIMPLE
        ON UPDATE NO ACTION
        ON DELETE NO ACTION
)