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
        <a href="majstor_lista.php" class="btn btn-sivo">Pregled majstora</a>
    </div>

    <div class="kartica">
        <h3>Novi unos</h3>
        <p>+</p>
        <a href="izvestaj_unos.php" class="btn btn-zeleno">Dodaj novi izveštaj</a>
    </div>
</div>

<h3 style="margin-top: 40px; margin-bottom: 15px;">Statistika učinka majstora (Podaci iz VIEW)</h3>
<div style="overflow-x: auto; max-width: 800px;">
    <table class="tabela">
        <thead>
            <tr>
                <th>Majstor (Ime i prezime)</th>
                <th>Ukupno evidentiranih izveštaja</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Direktno koriscenje pogleda
            $statistika = $izvestajModel->dohvatiStatistikuMajstora();
            if (count($statistika) > 0): 
                foreach ($statistika as $stat): 
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($stat['ime'] . ' ' . $stat['prezime']); ?></td>
                    <td><strong style="color: #2e7d32;"><?php echo htmlspecialchars($stat['broj_izvestaja']); ?></strong></td>
                </tr>
            <?php 
                endforeach; 
            else: 
            ?>
                <tr>
                    <td colspan="2" style="text-align: center;">Nema dovoljno podataka za statistiku.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once __DIR__ . '/../ukljuci/podnozje.php';
?>
