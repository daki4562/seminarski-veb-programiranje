<?php
// Pokreće se sesija ukoliko već nije aktivna
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proverava se da li je korisnik prijavljen
if (!isset($_SESSION['korisnik_id'])) {
    // Vrši se preusmeravanje na stranicu za prijavu ukoliko korisnik nema pristup
    header('Location: prijava.php');
    exit;
}
