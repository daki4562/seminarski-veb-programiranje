<?php
/**
 * Logika za resurs Majstori
 * Obrađuju se HTTP zahtevi za entitet Majstor.
 */

require_once __DIR__ . '/../../klase/Majstor.php';

$majstor = new Majstor();

switch ($metoda) {
    case 'GET':
        if ($id) {
            // Dohvata se specifičan majstor na osnovu ID-a
            $rezultat = $majstor->dohvatiPoId($id);
            if ($rezultat) {
                echo json_encode($rezultat, JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(404);
                echo json_encode(['greska' => 'Majstor nije pronadjen']);
            }
        } else {
            // Dohvataju se svi majstori iz baze
            $rezultat = $majstor->dohvatiSve();
            echo json_encode($rezultat, JSON_UNESCAPED_UNICODE);
        }
        break;
        
    case 'POST':
        $podaci = json_decode(file_get_contents('php://input'), true);
        
        $noviId = $majstor->unesi($podaci);
        
        if ($noviId) {
            http_response_code(201);
            echo json_encode(['poruka' => 'Majstor uspesno dodat', 'id' => $noviId]);
        } else {
            http_response_code(500);
            echo json_encode(['greska' => 'Greska pri dodavanju majstora u bazu podataka']);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(['greska' => 'Ova metoda nije podrzana za rad sa majstorima']);
        break;
}
