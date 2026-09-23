<?php
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Izvestaj.php';

$izvestajModel = new Izvestaj();
$greska = '';

// Brisanje se izvršava pri POST zahtevu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        $rezultat = $izvestajModel->obrisiSaIntervencijama($id);
        if ($rezultat) {
            header('Location: izvestaj_lista.php?poruka=obrisano');
            exit;
        } else {
            $greska = "Došlo je do greške prilikom brisanja.";
        }
    }
}

// Dohvataju se podaci za prikaz pre brisanja pri GET zahtevu
$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: izvestaj_lista.php');
    exit;
}

$podaci = $izvestajModel->dohvatiSaIntervencijama($id);
if (!$podaci || !$podaci['izvestaj']) {
    header('Location: izvestaj_lista.php');
    exit;
}

$izvestaj = $podaci['izvestaj'];

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<div style="max-width: 600px; margin: 0 auto; margin-top: 50px;">
    
    <?php if (!empty($greska)): ?>
        <div class="poruka poruka-greska"><?php echo htmlspecialchars($greska); ?></div>
    <?php endif; ?>

    <div class="poruka poruka-greska" style="background-color: #fff; border: 1px solid #d32f2f; border-left: 5px solid #d32f2f;">
        <h3 style="color: #d32f2f; margin-bottom: 15px;">Potvrda brisanja</h3>
        <p style="margin-bottom: 10px;">Da li ste sigurni da želite da obrišete sledeći izveštaj?</p>
        
        <div style="background-color: #f9f9f9; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <p><strong>Broj izveštaja:</strong> <?php echo htmlspecialchars($izvestaj['broj_izvestaja']); ?></p>
            <p><strong>Datum:</strong> <?php echo htmlspecialchars(date('d.m.Y.', strtotime($izvestaj['datum_intervencije']))); ?></p>
            <p><strong>Adresa stana:</strong> <?php echo htmlspecialchars($izvestaj['adresa_stana']); ?></p>
            <p><strong>Majstor:</strong> <?php echo htmlspecialchars($izvestaj['majstor_ime'] . ' ' . $izvestaj['majstor_prezime']); ?></p>
        </div>
        
        <p style="color: #d32f2f; font-size: 0.9em; margin-bottom: 20px;"><em>Napomena: Brisanje izveštaja će obrisati i sve pripadajuće intervencije. Ova akcija je nepovratna.</em></p>
        
        <form method="POST" action="izvestaj_brisanje.php" style="display: flex; gap: 10px;">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($izvestaj['id']); ?>">
            <button type="submit" class="btn btn-crveno">Da, obriši izveštaj</button>
            <a href="izvestaj_lista.php" class="btn btn-sivo">Ne, odustani</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../ukljuci/podnozje.php'; ?>
