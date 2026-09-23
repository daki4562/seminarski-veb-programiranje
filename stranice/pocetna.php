<?php
// Proverava se status prijave korisnika
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';

require_once __DIR__ . '/../klase/Izvestaj.php';
require_once __DIR__ . '/../klase/Majstor.php';

$izvestajModel = new Izvestaj();
$majstorModel = new Majstor();

// Preuzima se statistika
$sviIzvestaji = $izvestajModel->dohvatiSve();
$ukupanBrojIzvestaja = count($sviIzvestaji);

$sviMajstori = $majstorModel->dohvatiSve();
$brojMajstora = count($sviMajstori);

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<h2>Nadzorna ploča</h2>

<p>Dobrodošli na sistem za evidenciju popravki kvarova u stanovima.</p>

<div class="kartice-kontejner">
    <div class="kartica">
        <h3>Ukupno izveštaja</h3>
        <p><?php echo $ukupanBrojIzvestaja; ?></p>
        <a href="izvestaj_lista.php" class="btn btn-plavo">Pregledaj izveštaje</a>
    </div>
    
    <div class="kartica">
        <h3>Zaposleni majstori</h3>
        <p><?php echo $brojMajstora; ?></p>
        <a href="#" class="btn btn-sivo">Pregled majstora</a>
    </div>

    <div class="kartica">
        <h3>Novi unos</h3>
        <p>+</p>
        <a href="izvestaj_unos.php" class="btn btn-zeleno">Dodaj novi izveštaj</a>
    </div>
</div>



<?php
require_once __DIR__ . '/../ukljuci/podnozje.php';
?>
