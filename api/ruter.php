<?php
/**
 * REST Ruter
 * 
 * Prima sve API zahteve i usmerava ih ka odgovarajucem resursu.
 * Format URL-a: /api/{resurs}/{id}
 */

// Postavljaju se zaglavlja - odgovor je u JSON formatu
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Ako je OPTIONS zahtev, vraca se 200 i prekida se izvrsavanje
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$metoda = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Uklanja se bazni deo putanje kako bi se dobio pravilan resurs
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$basePath = dirname($scriptName);

if ($basePath === '\\' || $basePath === '/') {
    $basePath = '';
}

if (strpos($requestUri, $basePath) === 0) {
    $putanja = substr($requestUri, strlen($basePath));
} else {
    $putanja = $requestUri;
}

// Putanja se deli po kosim crtama da bi se izvukli resurs i ID
$delovi = explode('/', trim($putanja, '/'));
$apiIndex = array_search('api', $delovi);

$resurs = '';
$id = null;

if ($apiIndex !== false) {
    $resurs = isset($delovi[$apiIndex + 1]) ? $delovi[$apiIndex + 1] : '';
    $id = isset($delovi[$apiIndex + 2]) ? $delovi[$apiIndex + 2] : null;
}

// Usmerava se ka odgovarajucem fajlu na osnovu resursa
switch ($resurs) {
    case 'izvestaji':
        require_once __DIR__ . '/resursi/izvestaji.php';
        break;
    case 'majstori':
        require_once __DIR__ . '/resursi/majstori.php';
        break;
    case 'intervencije':
        require_once __DIR__ . '/resursi/intervencije.php';
        break;
    default:
        http_response_code(404);
        echo json_encode(['greska' => 'Resurs nije pronadjen']);
        break;
}
