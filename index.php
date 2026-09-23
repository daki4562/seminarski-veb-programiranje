<?php
// Pokretanje sesije za proveru prijave
session_start();

// Ako je korisnik prijavljen, preusmeri ga na početnu stranicu
if (isset($_SESSION['korisnik_id'])) {
    header('Location: stranice/pocetna.php');
    exit;
} else {
    // Ako nije prijavljen, preusmeri na stranicu za prijavu
    header('Location: stranice/prijava.php');
    exit;
}
