<?php
/**
 * Logika za resurs Izvestaji
 * Obrađuju se GET, POST, PUT, DELETE zahtevi za entitet Izvestaj.
 */

require_once __DIR__ . '/../../klase/Izvestaj.php';

$izvestaj = new Izvestaj();

// Proverava se HTTP metoda zahteva
switch ($metoda) {
    case 'GET':
        if ($id) {
            // Dohvata se specifičan izveštaj i sve povezane intervencije
            $rezultat = $izvestaj->dohvatiSaIntervencijama($id);
            
            if ($rezultat) {
                echo json_encode($rezultat, JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(['greska' => 'Izvestaj nije pronadjen']);
            }
        } else {
            // Dohvataju se svi izveštaji iz baze
            $rezultat = $izvestaj->dohvatiSve();
            echo json_encode($rezultat, JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case 'POST':
        $json_tekst = file_get_contents('php://input');
        
        $podaci = json_decode($json_tekst, true);
        
        $intervencije = $podaci['intervencije'] ?? [];
        
        // Odvajaju se intervencije od osnovnih podataka izveštaja
        unset($podaci['intervencije']);
        
        // Izvršava se unos u okviru transakcije
        $noviId = $izvestaj->unesiSaIntervencijama($podaci, $intervencije);
        
        if ($noviId) {
            http_response_code(201);
            echo json_encode(['poruka' => 'Izvestaj uspesno kreiran', 'id' => $noviId]);
        } else {
            http_response_code(500);
            echo json_encode(['greska' => 'Greska pri kreiranju izvestaja']);
        }
        break;
    
    case 'PUT':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['greska' => 'ID je obavezan prilikom izmene']);
            break;
        }
        
        $podaci = json_decode(file_get_contents('php://input'), true);
        $intervencije = $podaci['intervencije'] ?? [];
        unset($podaci['intervencije']);
        
        $uspeh = $izvestaj->izmeniSaIntervencijama($id, $podaci, $intervencije);
        
        if ($uspeh) {
            echo json_encode(['poruka' => 'Izvestaj uspesno izmenjen']);
        } else {
            http_response_code(500);
            echo json_encode(['greska' => 'Greska pri izmeni izvestaja']);
        }
        break;
    
    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['greska' => 'ID je obavezan kod brisanja resursa']);
            break;
        }
        
        $uspeh = $izvestaj->obrisi($id);
        if ($uspeh) {
            echo json_encode(['poruka' => 'Izvestaj uspesno obrisan']);
        } else {
            http_response_code(500);
            echo json_encode(['greska' => 'Doslo je do greske prilikom brisanja izvestaja']);
        }
        break;
}
