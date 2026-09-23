<?php
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Izvestaj.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: izvestaj_lista.php');
    exit;
}

$izvestajModel = new Izvestaj();
$podaci = null;

if ($id) {
    $podaci = $izvestajModel->dohvatiSaIntervencijama($id);
}

if (!$podaci || !$podaci['izvestaj']) {
    // Vrši se preusmeravanje na listu ukoliko izveštaj ne postoji
    header('Location: izvestaj_lista.php');
    exit;
}

$izvestaj = $podaci['izvestaj'];
$intervencije = $podaci['intervencije'];

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Pregled izveštaja o popravci</h2>
    <div>
        <a href="izvestaj_izmena.php?id=<?php echo $izvestaj['id']; ?>" class="btn btn-plavo">Izmeni</a>
        <a href="stampa_izvestaj.php?id=<?php echo $izvestaj['id']; ?>" target="_blank" class="btn btn-sivo" style="background-color: #424242;">Štampaj</a>
        <a href="izvestaj_lista.php" class="btn btn-sivo">Nazad na listu</a>
    </div>
</div>

<div class="master-forma" style="background-color: #fff; border: 1px solid #e0e0e0;">
    <div style="grid-column: span 2;">
        <h4 style="color: #1a237e; border-bottom: 1px solid #e0e0e0; padding-bottom: 5px; margin-bottom: 15px;">Podaci o preduzeću</h4>
    </div>
    
    <div>
        <strong>Naziv preduzeća:</strong>
        <p><?php echo htmlspecialchars($izvestaj['naziv_preduzeca']); ?></p>
    </div>
    <div>
        <strong>Adresa preduzeća:</strong>
        <p><?php echo htmlspecialchars($izvestaj['adresa_preduzeca']); ?></p>
    </div>
    <div>
        <strong>Telefon:</strong>
        <p><?php echo htmlspecialchars($izvestaj['telefon_preduzeca']); ?></p>
    </div>
    <div>
        <strong>Email adresa:</strong>
        <p><?php echo htmlspecialchars($izvestaj['email_preduzeca']); ?></p>
    </div>
    <div>
        <strong>PIB:</strong>
        <p><?php echo htmlspecialchars($izvestaj['pib']); ?></p>
    </div>

    <div style="grid-column: span 2; margin-top: 20px;">
        <h4 style="color: #1a237e; border-bottom: 1px solid #e0e0e0; padding-bottom: 5px; margin-bottom: 15px;">Podaci o popravci</h4>
    </div>

    <div>
        <strong>Broj izveštaja:</strong>
        <p><?php echo htmlspecialchars($izvestaj['broj_izvestaja']); ?></p>
    </div>
    <div>
        <strong>Datum izveštaja:</strong>
        <p><?php echo htmlspecialchars(date('d.m.Y.', strtotime($izvestaj['datum_intervencije']))); ?></p>
    </div>
    <div>
        <strong>Broj radnog naloga:</strong>
        <p><?php echo htmlspecialchars($izvestaj['broj_radnog_naloga']); ?></p>
    </div>
    <div>
        <strong>Datum radnog naloga:</strong>
        <p><?php echo htmlspecialchars(date('d.m.Y.', strtotime($izvestaj['datum_radnog_naloga']))); ?></p>
    </div>
    <div style="grid-column: span 2;">
        <strong>Adresa stana:</strong>
        <p><?php echo htmlspecialchars($izvestaj['adresa_stana']); ?></p>
    </div>
    <div style="grid-column: span 2;">
        <strong>Opis kvara:</strong>
        <p style="white-space: pre-wrap;"><?php echo htmlspecialchars($izvestaj['opis_kvara']); ?></p>
    </div>
    <div style="grid-column: span 2;">
        <strong>Majstor:</strong>
        <p><?php echo htmlspecialchars($izvestaj['majstor_ime'] . ' ' . $izvestaj['majstor_prezime']); ?></p>
    </div>
</div>

<h3 class="sekcija-naslov">Obavljene intervencije</h3>
<div style="background-color: #fff; padding: 15px; border: 1px solid #e0e0e0; border-radius: 6px;">
    <table class="tabela">
        <thead>
            <tr>
                <th style="width: 50px;">Rb</th>
                <th>Radna operacija</th>
                <th>Potrošeni materijal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($intervencije) > 0): ?>
                <?php foreach ($intervencije as $index => $int): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($int['radna_operacija']); ?></td>
                        <td><?php echo htmlspecialchars($int['potroseni_materijal']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center;">Nema unetih intervencija.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../ukljuci/podnozje.php'; ?>
