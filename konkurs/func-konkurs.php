<?php

function czy_kraj_istnieje($conn, $nazwa_kraju)
{
    $sql_kraj_id = "SELECT id_kraju FROM Kraj WHERE nazwa_kraju = $1";
    $result = pg_query_params($conn, $sql_kraj_id, array($nazwa_kraju));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        return true;
    } else {
        return false;
    }
}

function get_id_kraju($conn, $nazwa_kraju) {
    $sql_kraj_id = "SELECT id_kraju FROM Kraj WHERE nazwa_kraju = $1";
    $result = pg_query_params($conn, $sql_kraj_id, array($nazwa_kraju));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['id_kraju'];
    } else {
        return false;
    }
}

function get_id_konkursu($conn, $nazwa_konkursu) {
    $sql_konkurs_id = "SELECT id_konkursu FROM Konkurs WHERE nazwa_konkursu = $1";
    $result = pg_query_params($conn, $sql_konkurs_id, array($nazwa_konkursu));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['id_konkursu'];
    } else {
        return false;
    }
}

function czy_konkurs_istnieje($conn, $nazwa_konkursu)
{
    $sql_konkurs_id = "SELECT id_konkursu FROM Konkurs WHERE nazwa_konkursu = $1";
    $result = pg_query_params($conn, $sql_konkurs_id, array($nazwa_konkursu));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        return true;
    } else {
        return false;
    }
}

function ilu_zawodnikow_z_kraju($conn, $nazwa_konkursu, $nazwa_kraju) {
    $sql_ilu_zawodnikow_z_kraju = "SELECT id_zawodnika FROM zawodnik WHERE id_konkursu = $1 AND id_kraju = $2";
    $result = pg_query_params($conn, $sql_ilu_zawodnikow_z_kraju, array(get_id_konkursu($conn, $nazwa_konkursu), get_id_kraju($conn, $nazwa_kraju)));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['count'];
    } else {
        return 0;
    }
}

function czy_zawodnik_istnieje($conn, $imie, $nazwisko, $kraj) {
    $sql_czy_zawodnik_istnieje = "SELECT id_zawodnika FROM zawodnik WHERE imie = $1 AND nazwisko = $2 AND id_kraju = $3";
    $result = pg_query_params($conn, $sql_czy_zawodnik_istnieje, array($imie, $nazwisko, get_id_kraju($conn, $kraj)));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck == 0) {
        return false;
    } else {
        return true;
    }
}

function get_id_zawodnika($conn, $imie, $nazwisko, $kraj) {
    $sql_id_zawodnika = "SELECT id_zawodnika FROM zawodnik WHERE imie = $1 AND nazwisko = $2 AND id_kraju = $3";
    $result = pg_query_params($conn, $sql_id_zawodnika, array($imie, $nazwisko, get_id_kraju($conn, $kraj)));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['id_zawodnika'];
    } else {
        return false;
    }
}

function get_id_zgloszenia($conn, $nazwa_konkursu, $imie, $nazwisko, $kraj_zawodnika) {
    $sql_id_zgloszenia = "SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = $1 AND id_zawodnika = $2";
    $result = pg_query_params($conn, $sql_id_zgloszenia, array(get_id_konkursu($conn, $nazwa_konkursu), get_id_zawodnika($conn, $imie, $nazwisko, $kraj_zawodnika)));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['id_zgloszenia'];
    } else {
        return false;
    }
}

function get_id_skoku($conn, $id_zgloszenia, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga) {
    $sql_id_skoku = "SELECT id_skoku FROM skok WHERE id_zgloszenia = $1 AND czy_seria_kwalfikacyjna = $2 AND czy_seria_pierwsza = $3 AND czy_seria_druga = $4";
    $result = pg_query_params($conn, $sql_id_skoku, array($id_zgloszenia));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['id_skoku'];
    } else {
        return false;
    }
}

// Jeśli i > liczba zawodnikow w danej turze danego konkursu to zwracamy ostatniego zawodnika
// Ten ostatni zawodnik musi być niezdyskfalikowany jeśli przechodzimy od kwalfikacji do pierwszej serii
function poznaj_ity_wynik_w_turze($conn, $i, $id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $czy_zdyskwalifikowany) {
    $sql_liczba_zawodnikow_w_danej_serii = "SELECT id_skoku FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia
                                            WHERE z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 
                                            AND s.czy_seria_druga = $4 AND s.czy_zdyskwalifikowany = $5"; // ok

    $result = pg_query_params($conn, $sql_liczba_zawodnikow_w_danej_serii, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $czy_zdyskwalifikowany));
    $liczba_zawodnikow = pg_num_rows($result);
    if ($liczba_zawodnikow < $i) {
        $i = $liczba_zawodnikow;
    }

    if ($czy_seria_kwalfikacyjna == 1) {
        // Zwracamy punkty i-tego najlepszego niezdyskwalifikowanego zawodnika w kwalfikacji
        $sql = "SELECT s.punkty FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
                z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
                s.czy_seria_druga = $4 AND s.czy_zdyskwalifikowany = $5 ORDER BY s.punkty, s.id_zgloszenia DESC LIMIT 1 OFFSET $6";
                $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $czy_zdyskwalifikowany, $i));
    } else if ($czy_seria_pierwsza == 1) {
        // Zwracamy punkty i-tego najlepszego zawodnika w kwalfikacyjnej serii
        $sql = "SELECT s.punkty FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4 ORDER BY s.punkty, s.id_zgloszenia DESC LIMIT 1 OFFSET $5";
            $result = pg_query_params($conn, $sql, array($id_konkursu, 't', 'f', 'f', $i));
    } else if ($czy_seria_druga == 1) {
        // Zwracamy punkty i-tego najgorszego zawodnika w pierwszej serii
        $sql = "SELECT s.punkty FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4 ORDER BY s.punkty, s.id_zgloszenia ASC LIMIT 1 OFFSET $5";
            $result = pg_query_params($conn, $sql, array($id_konkursu, 'f', 't', 'f', $i));
    }
    
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['punkty'];
    } else {
        return false;
    }
}

function emptyInput($input) {
    $result = true;
    if (empty($input)) {
        $result = true;
    } else {
        $result = false;
    }
    return $result;
}

// Zwraca liczbę zawodników w danej serii konkursu, którzy oddali skok i nie zostali zdyskwalifikowani
function ile_skokow_w_danej_turze($conn, $id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga) {
    $sql = "SELECT id_skoku FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4";
    $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga));
    $number = pg_num_rows($result);
    return $number;
}

function ile_skokow_niezdyskwalfikowanych_w_danej_turze($conn, $id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga) {
    $sql = "SELECT id_skoku FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4 AND s.czy_zdyskwalifikowany = 'f'";
    $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga));
    $number = pg_num_rows($result);
    return $number;
}

function ile_zawodnikow_ma_taki_sam_wynik($conn, $id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $punkty) {
    $sql = "SELECT id_skoku FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4 AND s.punkty = $5";
    $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $punkty));
    $resultCheck = pg_num_rows($result);
    return $resultCheck;
}

function sprawdz_czy_zawodnik_skoczyl_w_danej_serii($conn, $id_zgloszenia_zawodnika) {
    $sql_czy_skoczyl_w_serii = "SELECT s1.id_zgloszenia FROM skok s1, skok s2 WHERE 
                            s1.id_zgloszenia = $1 AND
                            s1.czy_seria_kwalfikacyjna = s2.czy_seria_kwalfikacyjna 
                            AND s1.czy_seria_pierwsza = s2.czy_seria_pierwsza AND 
                            s1.czy_seria_druga = s2.czy_seria_druga AND
                            s1.id_zgloszenia = s2.id_zgloszenia GROUP BY 
                            s1.id_zgloszenia HAVING COUNT(*) >= 1";
    $result_czy_skoczyl_w_serii = pg_query_params($conn, $sql_czy_skoczyl_w_serii, array($id_zgloszenia_zawodnika));
    $rows = pg_num_rows($result_czy_skoczyl_w_serii);
    return $rows;
}

function id_zgloszenia_nastepnego_skoczka($conn, $ilu_skoczylo_w_tej_serii, $ilu_jest_w_tej_serii, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $czy_zmiana_serii) {
    // Mamy dane jaka jest TERAZ seria (wiemy, że musi być odrazu=tak skoro wchodzimy w te funkcje)
    if ($czy_zmiana_serii == 1) {
        
    }
}

function numer_startowy_z_danej_serii($conn, $id_zgloszenia, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $id_konkursu) {
    $sql = "SELECT numer_startowy FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4 AND s.id_zgloszenia = $5";
    $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $id_zgloszenia));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['numer_startowy'];
    } else {
        return false;
    }
}

function poznaj_itego_zawodnika_w_danej_turze($conn, $i, $id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $czy_zdyskfalfikowany) {
    if ($czy_seria_kwalfikacyjna == 1) {
        // Zwracamy id_zgloszenia i-tego najlepszego niezdyskwalifikowanego zawodnika w kwalfikacji
        $sql = "SELECT s.id_zgloszenia FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
                z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
                s.czy_seria_druga = $4 AND s.czy_zdyskwalifikowany = $5 ORDER BY s.punkty, s.id_zgloszenia DESC LIMIT 1 OFFSET $6";
                $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $czy_zdyskfalfikowany, $i));
    } else if ($czy_seria_pierwsza == 1) {
        // Zwracamy punkty i-tego najlepszego zawodnika w pierwszej serii
        $sql = "SELECT s.id_zgloszenia FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4 ORDER BY s.punkty, s.id_zgloszenia DESC LIMIT 1 OFFSET $5";
            $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $i));
    } else if ($czy_seria_druga == 1) {
        // Zwracamy punkty i-tego najgorszego zawodnika w pierwszej serii
        $sql = "SELECT s.id_zgloszenia FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE 
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = $2 AND s.czy_seria_pierwsza = $3 AND 
            s.czy_seria_druga = $4 ORDER BY s.punkty, s.id_zgloszenia ASC LIMIT 1 OFFSET $5";
            $result = pg_query_params($conn, $sql, array($id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga, $i));
    }
    
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        $row = pg_fetch_assoc($result);
        return $row['id_zgloszenia'];
    } else {
        return false;
    }
}

function ile_zawodnikow_w_konkursie($conn, $id_konkursu) {
    $sql = "SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = $1";
    $result = pg_query_params($conn, $sql, array($id_konkursu));
    $resultCheck = pg_num_rows($result);
    return $resultCheck;
}

function jaka_seria($conn, $id_konkursu) {
    $sql_zgloszenia_otwarte = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_zgloszenia_otwarte = $2";
    $sql_kwalfikacyjna = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_podczas_kwalfikacji = $2";
    $sql_pierwsza = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_podczas_serii_pierwszej = $2";
    $sql_druga = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_podczas_serii_drugiej = $2";
    $sql_zakoczone = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_konkurs_zakonczony = $2";
    $result_zgloszenia_otwarte = pg_query_params($conn, $sql_zgloszenia_otwarte, array($id_konkursu, 1));
    if ($result_zgloszenia_otwarte != false) {
        $resultCheck_zgloszenia_otwarte = pg_num_rows($result_zgloszenia_otwarte);
    } else {
        $resultCheck_zgloszenia_otwarte = 0;
    }
    $result_kwalfikacyjna = pg_query_params($conn, $sql_kwalfikacyjna, array($id_konkursu, 1));
    if ($result_kwalfikacyjna != false) {
        $resultCheck_kwalfikacyjna = pg_num_rows($result_kwalfikacyjna);
    } else {
        $resultCheck_kwalfikacyjna = 0;
    }
    $result_pierwsza = pg_query_params($conn, $sql_pierwsza, array($id_konkursu, 1));
    if ($result_pierwsza != false) {
        $resultCheck_pierwsza = pg_num_rows($result_pierwsza);
    } else {
        $resultCheck_pierwsza = 0;
    }
    $result_druga = pg_query_params($conn, $sql_druga, array($id_konkursu, 1));
    if ($result_druga != false) {
        $resultCheck_druga = pg_num_rows($result_druga);
    } else {
        $resultCheck_druga = 0;
    }
    $result_zakoczone = pg_query_params($conn, $sql_zakoczone, array($id_konkursu, 1));
    if ($result_zakoczone != false) {
        $resultCheck_zakonczone = pg_num_rows($result_zakoczone);
    } else {
        $resultCheck_zakonczone = 0;
    }
    if ($resultCheck_zgloszenia_otwarte > 0) {
        return "ZGLOSZENIA";
    } else if ($resultCheck_kwalfikacyjna > 0) {
        return "KWALFIKACYJNA";
    } else if ($resultCheck_pierwsza > 0) {
        return "PIERWSZA";
    } else if ($resultCheck_druga > 0) {
        return "DRUGA";
    }
    return "ZAKONCZONE";
}

function czy_pierwsza_odrazu($conn, $id_konkursu) {
    $sql = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_podczas_serii_pierwszej = 't'
            AND (SELECT COUNT(*) FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE
            z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = 't') = 0";
    $result = pg_query_params($conn, $sql, array($id_konkursu));
    $resultCheck = pg_num_rows($result);
    if ($resultCheck > 0) {
        return true;
    } else {
        return false;
    }
}

// Do tej funckji wejdziemy po sprawdzeniu, że jeszcze nie wszyscy skoczyli w danej turze skoczyli
// Funkcja zwraca -1, gdy wszystkie skoki w serii zostały przeprowadzone
function id_zgloszenia_zawodnika_do_skoku($conn, $id_konkursu, $czy_seria_kwalfikacyjna, $czy_seria_pierwsza, $czy_seria_druga)
{
    if ($czy_seria_pierwsza == 1 && czy_pierwsza_odrazu($conn, $id_konkursu) == false) {
        // Załóżmy, że w serii kwalfikacyjnej (czyli poprzedniej) skoczyło k zawodników. Ponadto w bieżącej
        // serii pierwszej skoczyło już m zawodników. Zatem zawodnik skaczący teraz będzie m+1-wszy o ile
        // m+1 <=k.
        $m = ile_skokow_w_danej_turze($conn, $id_konkursu, 0, 1, 0);
        $k = ile_skokow_niezdyskwalfikowanych_w_danej_turze($conn, $id_konkursu, 1, 0, 0);
        if (!($m + 1 <= $k)) {
            return -1;
        }
        $sql = "SELECT id_zgloszenia FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE
                z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = 't' AND s.czy_seria_pierwsza = 'f' AND s.czy_seria_druga = 'f'
                ORDER BY s.punkty, s.id_zgloszenia DESC LIMIT 1 OFFSET $2";
        $result = pg_query_params($conn, $sql, array($id_konkursu, $m));
        $row = pg_fetch_assoc($result);
        return $row['id_zgloszenia'];
    } else if (($czy_seria_pierwsza == 1 && czy_pierwsza_odrazu($conn, $id_konkursu) == true) || $czy_seria_kwalfikacyjna == 1) {
        // Załóżmy, że w bieżącej serii pierwszej skoczyło już k zawodników. Teraz jest zatem kolej
        // k+1-wszego zawodnika według id_zgloszenia
        if ($czy_seria_kwalfikacyjna == 1) {
            $k = ile_skokow_w_danej_turze($conn, $id_konkursu, 1, 0, 0);
        } else {
            $k = ile_skokow_w_danej_turze($conn, $id_konkursu, 0, 1, 0);
        }
        // Sprawdzamy, czy przypadkiem wszyscy już nie skoczyli
        $liczba_zawodnikow = ile_zawodnikow_w_konkursie($conn, $id_konkursu);
        if ($k + 1 > $liczba_zawodnikow) {
            return -1;
        }
        $sql = "SELECT id_zgloszenia FROM zgloszenie WHERE id_konkursu = $1 ORDER BY id_zgloszenia DESC LIMIT 1 OFFSET $2";
        $result = pg_query_params($conn, $sql, array($id_konkursu, $k));
        $row = pg_fetch_assoc($result);
        return $row['id_zgloszenia'];
    } else if ($czy_seria_druga == 1) {
        // W tej serii będzie skakać k zawodników, którzy skoczyli i nie zostali zdyskwalfikowani w serii pierwszej (czyli poprzedniej).
        // k może być równy maksymalnie 30 + liczba zawodników, którzy mają tyle samo punktów co zawodnik 30.

        // Znajdźmy k
        $k = ile_skokow_niezdyskwalfikowanych_w_danej_turze($conn, $id_konkursu, 0, 1, 0);
        if ($k > 30) {
            // Patrzymy, ile zdobył zawodnik nr 30.
            $sql = "SELECT punkty FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE
                    z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = 'f' AND s.czy_seria_pierwsza = 't' AND s.czy_seria_druga = 'f'
                    ORDER BY s.punkty, s.id_zgloszenia ASC LIMIT 1 OFFSET 29";
            $result = pg_query_params($conn, $sql, array($id_konkursu));
            $row = pg_fetch_assoc($result);
            $punkty_zawodnika_30 = $row['punkty'];
            // Patrzymy, czy istnieją zawodnicy mający tyle samo punktów co zawodnik 30.
            $sql = "SELECT id_skoku FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE
                    z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = 'f' AND s.czy_seria_pierwsza = 't' AND s.czy_seria_druga = 'f'
                    AND s.punkty = $2";
            $result = pg_query_params($conn, $sql, array($id_konkursu, $punkty_zawodnika_30));
            $zawodnicy_tyle_samo_co_30 = pg_num_rows($result);
            $k = 30 + $zawodnicy_tyle_samo_co_30 - 1;
            // Czyli k jest liczbą zawodników, którzy skaczą w trzeciej serii. Teraz sprawdźmy, ilu już skoczyło w serii drugiej
            $m = ile_skokow_w_danej_turze($conn, $id_konkursu, 0, 0, 1);
            if ($m + 1 <= $k) { // Jeśli m + 1 jest mniejsze równe k, to znaczy, że nie wszyscy już skoczyli
                // Znajdźmy id_zgloszenia zawodnika, który skacze teraz
                $sql = "SELECT s.id_zgloszenia FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE
                        z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = 'f' AND s.czy_seria_pierwsza = 't' AND s.czy_seria_druga = 'f'
                        ORDER BY s.punkty, s.id_zgloszenia ASC LIMIT 1 OFFSET $2";
                $result = pg_query_params($conn, $sql, array($id_konkursu, $m));
                $row = pg_fetch_assoc($result);
                return $row['id_zgloszenia'];
            } else {
                return -1;
            }
        } else {
            // Czyli k jest liczbą zawodników, którzy skaczą w trzeciej serii. Teraz sprawdźmy, ilu już skoczyło w serii drugiej
            $m = ile_skokow_niezdyskwalfikowanych_w_danej_turze($conn, $id_konkursu, 0, 0, 1);
            if ($m + 1 <= $k) { // Jeśli m + 1 jest mniejsze równe k, to znaczy, że nie wszyscy już skoczyli
                // Znajdźmy id_zgloszenia zawodnika, który skacze teraz
                $sql = "SELECT s.id_zgloszenia FROM skok s INNER JOIN zgloszenie z ON s.id_zgloszenia = z.id_zgloszenia WHERE
                        z.id_konkursu = $1 AND s.czy_seria_kwalfikacyjna = 'f' AND s.czy_seria_pierwsza = 't' AND s.czy_seria_druga = 'f'
                        AND s.czy_zdyskwalifikowany = 'f' ORDER BY s.punkty, s.id_zgloszenia ASC LIMIT 1 OFFSET $2";
                $result = pg_query_params($conn, $sql, array($id_konkursu, $m));
                $row = pg_fetch_assoc($result);
                return $row['id_zgloszenia'];
            } else {
                return -1;
            }
        }
    }
}

function imie_zawodnika($conn, $id_zgloszenia_do_skoku) {
    $sql = "SELECT imie FROM zawodnik WHERE id_zawodnika = (SELECT id_zawodnika FROM zgloszenie WHERE id_zgloszenia = $1)";
    $result = pg_query_params($conn, $sql, array($id_zgloszenia_do_skoku));
    $row = pg_fetch_assoc($result);
    return $row['imie'];
}

function nazwisko_zawodnika($conn, $id_zgloszenia_do_skoku) {
    $sql = "SELECT nazwisko FROM zawodnik WHERE id_zawodnika = (SELECT id_zawodnika FROM zgloszenie WHERE id_zgloszenia = $1)";
    $result = pg_query_params($conn, $sql, array($id_zgloszenia_do_skoku));
    $row = pg_fetch_assoc($result);
    return $row['nazwisko'];
}

function kraj_zawodnika($conn, $id_zgloszenia_do_skoku) {
    $sql_nazwa_kraju = "SELECT nazwa_kraju FROM kraj WHERE id_kraju = (SELECT id_kraju FROM zawodnik WHERE id_zawodnika = (SELECT id_zawodnika FROM zgloszenie WHERE id_zgloszenia = $1));";
    $result = pg_query_params($conn, $sql_nazwa_kraju, array($id_zgloszenia_do_skoku));
    $row = pg_fetch_assoc($result);
    return $row['nazwa_kraju'];
}

function przekieruj($conn, $id_konkursu) {
    $sql_zgloszenia_otwarte = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_zgloszenia_otwarte = $2";
    $sql_kwalfikacyjna = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_podczas_kwalfikacji = $2";
    $sql_pierwsza = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_podczas_serii_pierwszej = $2";
    $sql_druga = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_podczas_serii_drugiej = $2";
    $sql_zakoczone = "SELECT id_konkursu FROM konkurs WHERE id_konkursu = $1 AND czy_konkurs_zakonczony = $2";
    $result_zgloszenia_otwarte = pg_query_params($conn, $sql_zgloszenia_otwarte, array($id_konkursu, 1));
    if ($result_zgloszenia_otwarte != false) {
        $resultCheck_zgloszenia_otwarte = pg_num_rows($result_zgloszenia_otwarte);
    } else {
        $resultCheck_zgloszenia_otwarte = 0;
    }
    $result_kwalfikacyjna = pg_query_params($conn, $sql_kwalfikacyjna, array($id_konkursu, 1));
    if ($result_kwalfikacyjna != false) {
        $resultCheck_kwalfikacyjna = pg_num_rows($result_kwalfikacyjna);
    } else {
        $resultCheck_kwalfikacyjna = 0;
    }
    $result_pierwsza = pg_query_params($conn, $sql_pierwsza, array($id_konkursu, 1));
    if ($result_pierwsza != false) {
        $resultCheck_pierwsza = pg_num_rows($result_pierwsza);
    } else {
        $resultCheck_pierwsza = 0;
    }
    $result_druga = pg_query_params($conn, $sql_druga, array($id_konkursu, 1));
    if ($result_druga != false) {
        $resultCheck_druga = pg_num_rows($result_druga);
    } else {
        $resultCheck_druga = 0;
    }
    $result_zakoczone = pg_query_params($conn, $sql_zakoczone, array($id_konkursu, 1));
    if ($result_zakoczone != false) {
        $resultCheck_zakonczone = pg_num_rows($result_zakoczone);
    } else {
        $resultCheck_zakonczone = 0;
    }
    if ($resultCheck_kwalfikacyjna > 0) {
        header("location: ./wprowadz_skok.php");
    } else if ($resultCheck_pierwsza > 0) {
        header("location: ./wprowadz_skok.php");
    } else if ($resultCheck_druga > 0) {
        header("location: ./wprowadz_skok.php");
    } else if ($resultCheck_zakonczone > 0) {
        header("location: ./../widz.php");
    }
}

?>