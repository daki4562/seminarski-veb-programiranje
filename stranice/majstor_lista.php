<?php
// Proverava se pristup
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Majstor.php';

$majstorModel = new Majstor();

// Dohvataju se svi majstori iz baze
$majstori = $majstorModel->dohvatiSve();

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Zaposleni majstori</h2>
    <a href="pocetna.php" class="btn btn-sivo">Nazad na početnu</a>
</div>

<div style="overflow-x: auto;">
    <table class="tabela">
        <thead>
            <tr>
                <th>R.br.</th>
                <th>Ime</th>
                <th>Prezime</th>
                <th>Telefon</th>
                <th>Specijalnost</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($majstori) > 0): ?>
                <?php foreach ($majstori as $index => $majstor): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($majstor['ime']); ?></td>
                        <td><?php echo htmlspecialchars($majstor['prezime']); ?></td>
                        <td><?php echo htmlspecialchars($majstor['telefon'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($majstor['specijalnost'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Nema registrovanih majstora.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../ukljuci/podnozje.php'; ?>
