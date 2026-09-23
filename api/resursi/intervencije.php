<?php
/**
 * Logika za resurs Intervencije
 * Obrađuju se zahtevi za popravke i stavke u okviru izveštaja.
 */

require_once __DIR__ . '/../../klase/Intervencija.php';

$intervencija = new Intervencija();

switch ($metoda) {
    case 'GET':
        if ($id) {
            // Dohvata se specifična intervencija na osnovu ID-a
            $rezultat = $intervencija->dohvatiPoId($id);
            
            if ($rezultat) {
                echo json_encode($rezultat, JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(['greska' => 'Intervencija sa tim ID brojem nije pronadjena']);
            }
        } else {
            // Filtriraju se intervencije ukoliko je prosleđen ID izveštaja
            if (isset($_GET['izvestaj_id'])) {
                $izvestajId = $_GET['izvestaj_id'];
                $rezultat = $intervencija->dohvatiPoIzvestaju($izvestajId);
            } else {
                // Dohvataju se sve intervencije ukoliko nema filtera
                $rezultat = $intervencija->dohvatiSve();
            }
            
            echo json_encode($rezultat, JSON_UNESCAPED_UNICODE);
        }
        break;
        
    case 'POST':
        $podaci = json_decode(file_get_contents('php://input'), true);
        $noviId = $intervencija->unesi($podaci);
        
        if ($noviId) {
            http_response_code(201);
            echo json_encode(['poruka' => 'Intervencija zabelezena', 'id' => $noviId]);
        } else {
            http_response_code(500);
            echo json_encode(['greska' => 'Nije moguce uneti intervenciju']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['greska' => 'Trazena HTTP operacija nije dozvoljena na ovom resursu']);
        break;
}
