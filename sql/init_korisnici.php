<?php
/**
 * Skripta za inicijalizaciju korisnika sa pravilno hesiranim lozinkama.
 * 
 * Pokreni ovu skriptu JEDNOM nakon importa baza.sql:
 *   php sql/init_korisnici.php
 * 
 * Ova skripta azurira lozinke korisnika u bazi sa pravilnim bcrypt hesovima.
 */

require_once __DIR__ . '/../ukljuci/konfiguracija.php';

$config = require __DIR__ . '/../ukljuci/konfiguracija.php';

$konekcija = new mysqli(
    $config['db_host'],
    $config['db_korisnik'],
    $config['db_lozinka'],
    $config['db_naziv']
);

if ($konekcija->connect_error) {
    die("Greska pri povezivanju: " . $konekcija->connect_error . "\n");
}

$konekcija->set_charset('utf8mb4');

// Definisemo korisnike i njihove lozinke
$korisnici = [
    ['korisnicko_ime' => 'admin', 'lozinka' => 'admin123'],
    ['korisnicko_ime' => 'korisnik', 'lozinka' => 'korisnik123']
];

foreach ($korisnici as $k) {
    // Generisemo bcrypt hes lozinke
    $hes = password_hash($k['lozinka'], PASSWORD_BCRYPT);
    
    // Azuriramo lozinku u bazi
    $stmt = $konekcija->prepare("UPDATE korisnik SET lozinka = ? WHERE korisnicko_ime = ?");
    $stmt->bind_param('ss', $hes, $k['korisnicko_ime']);
    $stmt->execute();
    
    echo "Korisnik '{$k['korisnicko_ime']}' - lozinka azurirana (hes: {$hes})\n";
}

echo "\nSvi korisnici su azurirani!\n";
echo "Podaci za prijavu:\n";
echo "  admin / admin123\n";
echo "  korisnik / korisnik123\n";

$konekcija->close();
