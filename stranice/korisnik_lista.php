<?php
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Korisnik.php';

$korisnikModel = new Korisnik();
$korisnici = $korisnikModel->dohvatiSve();

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<h2>Korisnici sistema</h2>
<p>Pregled svih administratora koji imaju pristup sistemu.</p>

<div class="akcije-kontejner">
    <a href="korisnik_unos.php" class="btn btn-zeleno">+ Dodaj novog korisnika</a>
</div>

<div class="tabela-kontejner">
    <table class="tabela">
        <thead>
            <tr>
                <th>ID</th>
                <th>Korisničko ime</th>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($korisnici) > 0): ?>
                <?php foreach ($korisnici as $kor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($kor['id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($kor['korisnicko_ime']); ?></strong></td>
                        <td><?php echo htmlspecialchars($kor['ime']); ?></td>
                        <td><?php echo htmlspecialchars($kor['prezime']); ?></td>
                        <td><?php echo htmlspecialchars($kor['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Nema unetih korisnika u sistemu.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once __DIR__ . '/../ukljuci/podnozje.php';
?>
