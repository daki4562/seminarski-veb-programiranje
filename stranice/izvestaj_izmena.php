<?php
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Izvestaj.php';
require_once __DIR__ . '/../klase/Majstor.php';

$id = $_GET['id'] ?? null;
if (!$id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: izvestaj_lista.php');
    exit;
}

$greske = [];
$izvestajModel = new Izvestaj();
$majstorModel = new Majstor();
$sviMajstori = $majstorModel->dohvatiSve();

// Obrada izmena se vrši pri POST zahtevu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    
    $podaci = [
        'id' => $id,
        'naziv_preduzeca' => trim($_POST['naziv_preduzeca'] ?? ''),
        'adresa_preduzeca' => trim($_POST['adresa_preduzeca'] ?? ''),
        'telefon_preduzeca' => trim($_POST['telefon'] ?? ''),
        'email_preduzeca' => trim($_POST['email'] ?? ''),
        'pib' => trim($_POST['pib'] ?? ''),
        'broj_izvestaja' => trim($_POST['broj_izvestaja'] ?? ''),
        'datum_radnog_naloga' => trim($_POST['datum_radnog_naloga'] ?? ''),
        'broj_radnog_naloga' => trim($_POST['broj_radnog_naloga'] ?? ''),
        'datum_intervencije' => trim($_POST['datum_izvestaja'] ?? ''),
        'adresa_stana' => trim($_POST['adresa_stana'] ?? ''),
        'opis_kvara' => trim($_POST['opis_kvara'] ?? ''),
        'majstor_id' => trim($_POST['majstor_id'] ?? '')
    ];
    
    $radneOperacije = $_POST['radna_operacija'] ?? [];
    $potroseniMaterijali = $_POST['potroseni_materijal'] ?? [];
    
    $intervencije = [];
    for ($i = 0; $i < count($radneOperacije); $i++) {
        if (!empty(trim($radneOperacije[$i]))) {
            $intervencije[] = [
                'redni_broj' => $i + 1,
                'radna_operacija' => trim($radneOperacije[$i]),
                'potroseni_materijal' => trim($potroseniMaterijali[$i] ?? '')
            ];
        }
    }

    // Vrši se validacija podataka
    if (empty($podaci['naziv_preduzeca']) || strlen($podaci['naziv_preduzeca']) < 3) {
        $greske[] = "Naziv preduzeća je obavezan i mora imati najmanje 3 karaktera.";
    }
    if (empty($podaci['adresa_preduzeca'])) {
        $greske[] = "Adresa preduzeća je obavezna.";
    }
    if (empty($podaci['telefon_preduzeca']) || !preg_match('/^[0-9+\-\/\s]+$/', $podaci['telefon_preduzeca'])) {
        $greske[] = "Telefon je obavezan i mora biti u ispravnom formatu.";
    }
    if (empty($podaci['email_preduzeca']) || strpos($podaci['email_preduzeca'], '@') === false) {
        $greske[] = "Email adresa je obavezna i mora sadržati znak @.";
    }
    if (empty($podaci['pib']) || !preg_match('/^\d{9}$/', $podaci['pib'])) {
        $greske[] = "PIB je obavezan i mora sadržati tačno 9 cifara.";
    }
    if (empty($podaci['broj_izvestaja'])) {
        $greske[] = "Broj izveštaja je obavezan.";
    } elseif ($izvestajModel->proveriBrojIzvestaja($podaci['broj_izvestaja'], $id)) {
        $greske[] = "Broj izveštaja već postoji u bazi.";
    }
    if (empty($podaci['datum_radnog_naloga'])) {
        $greske[] = "Datum radnog naloga je obavezan.";
    }
    if (empty($podaci['broj_radnog_naloga'])) {
        $greske[] = "Broj radnog naloga je obavezan.";
    }
    if (empty($podaci['datum_intervencije'])) {
        $greske[] = "Datum izveštaja je obavezan.";
    }
    if (empty($podaci['adresa_stana'])) {
        $greske[] = "Adresa stana je obavezna.";
    }
    if (empty($podaci['opis_kvara']) || strlen($podaci['opis_kvara']) < 10) {
        $greske[] = "Opis kvara je obavezan i mora imati najmanje 10 karaktera.";
    }
    if (empty($podaci['majstor_id'])) {
        $greske[] = "Morate izabrati majstora.";
    }
    if (count($intervencije) === 0) {
        $greske[] = "Morate uneti bar jednu intervenciju sa popunjenom radnom operacijom.";
    }

    if (!empty($greske)) {
        $izvestaj = $podaci; // Podaci se zadržavaju za ponovni prikaz
    } else {
        // Izmene se čuvaju u bazi podataka
        $rezultat = $izvestajModel->izmeniSaIntervencijama($id, $podaci, $intervencije);
        if ($rezultat) {
            header('Location: izvestaj_lista.php?poruka=izmenjeno');
            exit;
        } else {
            $greske[] = "Došlo je do greške prilikom čuvanja izmena.";
            $izvestaj = $podaci;
        }
    }
} else {
    // Forma se popunjava postojećim podacima pri GET zahtevu
    $podaciSaIntervencijama = $izvestajModel->dohvatiSaIntervencijama($id);
    
    if (!$podaciSaIntervencijama || !$podaciSaIntervencijama['izvestaj']) {
        header('Location: izvestaj_lista.php');
        exit;
    }
    
    $izvestaj = $podaciSaIntervencijama['izvestaj'];
    $intervencije = $podaciSaIntervencijama['intervencije'];
}

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<h2>Izmena izveštaja o popravci</h2>

<?php if (!empty($greske)): ?>
    <div class="poruka poruka-greska">
        <ul>
            <?php foreach ($greske as $g): ?>
                <li><?php echo htmlspecialchars($g); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="izvestaj_izmena.php" onsubmit="return validacijaIzvestaja();">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($izvestaj['id']); ?>">
    
    <!-- Glavna sekcija -->
    <h3 class="sekcija-naslov">Podaci o izveštaju</h3>
    <div class="master-forma">
        <div class="forma-grupa">
            <label for="naziv_preduzeca" class="obavezno">Naziv preduzeća</label>
            <input type="text" id="naziv_preduzeca" name="naziv_preduzeca" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['naziv_preduzeca'] ?? ''); ?>">
        </div>
        
        <div class="forma-grupa">
            <label for="adresa_preduzeca" class="obavezno">Adresa preduzeća</label>
            <input type="text" id="adresa_preduzeca" name="adresa_preduzeca" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['adresa_preduzeca'] ?? ''); ?>">
        </div>
        
        <div class="forma-grupa">
            <label for="telefon" class="obavezno">Telefon</label>
            <input type="text" id="telefon" name="telefon" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['telefon_preduzeca'] ?? ''); ?>">
        </div>
        <div class="forma-grupa">
            <label for="email" class="obavezno">Email adresa</label>
            <input type="email" id="email" name="email" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['email_preduzeca'] ?? ''); ?>">
        </div>
        <div class="forma-grupa">
            <label for="pib" class="obavezno">PIB</label>
            <input type="text" id="pib" name="pib" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['pib'] ?? ''); ?>" maxlength="9">
        </div>
        <div class="forma-grupa">
            <label for="broj_izvestaja" class="obavezno">Broj izveštaja</label>
            <input type="text" id="broj_izvestaja" name="broj_izvestaja" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['broj_izvestaja'] ?? ''); ?>">
        </div>
        <div class="forma-grupa">
            <label for="datum_izvestaja" class="obavezno">Datum izveštaja</label>
            <input type="date" id="datum_izvestaja" name="datum_izvestaja" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['datum_intervencije'] ?? ''); ?>">
        </div>
        <div class="forma-grupa">
            <label for="broj_radnog_naloga" class="obavezno">Broj radnog naloga</label>
            <input type="text" id="broj_radnog_naloga" name="broj_radnog_naloga" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['broj_radnog_naloga'] ?? ''); ?>">
        </div>
        <div class="forma-grupa">
            <label for="datum_radnog_naloga" class="obavezno">Datum radnog naloga</label>
            <input type="date" id="datum_radnog_naloga" name="datum_radnog_naloga" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['datum_radnog_naloga'] ?? ''); ?>">
        </div>
        
        <div class="forma-grupa">
            <label for="majstor_id" class="obavezno">Majstor</label>
            <select id="majstor_id" name="majstor_id" class="forma-kontrola">
                <option value="">-- Izaberite majstora --</option>
                <?php foreach ($sviMajstori as $m): ?>
                    <option value="<?php echo $m['id']; ?>" <?php if (($izvestaj['majstor_id'] ?? '') == $m['id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($m['ime'] . ' ' . $m['prezime']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="forma-grupa" style="grid-column: span 2;">
            <label for="adresa_stana" class="obavezno">Adresa stana</label>
            <input type="text" id="adresa_stana" name="adresa_stana" class="forma-kontrola" value="<?php echo htmlspecialchars($izvestaj['adresa_stana'] ?? ''); ?>">
        </div>
        
        <div class="forma-grupa" style="grid-column: span 2;">
            <label for="opis_kvara" class="obavezno">Opis kvara</label>
            <textarea id="opis_kvara" name="opis_kvara" class="forma-kontrola" rows="3"><?php echo htmlspecialchars($izvestaj['opis_kvara'] ?? ''); ?></textarea>
        </div>
    </div>

    <!-- Sekcija za detalje -->
    <h3 class="sekcija-naslov">Obavljene intervencije</h3>
    <div class="detail-forma">
        <table class="tabela" id="intervencije_tabela">
            <thead>
                <tr>
                    <th style="width: 50px;">Rb</th>
                    <th>Radna operacija <span style="color:#d32f2f">*</span></th>
                    <th>Potrošeni materijal</th>
                    <th style="width: 100px;">Akcija</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($intervencije)): ?>
                    <?php foreach ($intervencije as $index => $int): ?>
                    <tr>
                        <td class="rb"><?php echo $index + 1; ?></td>
                        <td>
                            <input type="text" name="radna_operacija[]" class="forma-kontrola" placeholder="Unesite radnu operaciju" value="<?php echo htmlspecialchars($int['radna_operacija'] ?? ''); ?>">
                        </td>
                        <td>
                            <input type="text" name="potroseni_materijal[]" class="forma-kontrola" placeholder="Unesite potrošeni materijal" value="<?php echo htmlspecialchars($int['potroseni_materijal'] ?? ''); ?>">
                        </td>
                        <td>
                            <button type="button" class="btn btn-crveno" onclick="ukloniIntervenciju(this)">Ukloni</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Prikazuje se podrazumevani red ukoliko nema intervencija -->
                    <tr>
                        <td class="rb">1</td>
                        <td>
                            <input type="text" name="radna_operacija[]" class="forma-kontrola" placeholder="Unesite radnu operaciju">
                        </td>
                        <td>
                            <input type="text" name="potroseni_materijal[]" class="forma-kontrola" placeholder="Unesite potrošeni materijal">
                        </td>
                        <td>
                            <button type="button" class="btn btn-crveno" onclick="ukloniIntervenciju(this)">Ukloni</button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <button type="button" class="btn btn-zeleno" onclick="dodajIntervenciju()">+ Dodaj intervenciju</button>
    </div>

    <div style="margin-top: 30px; text-align: right; border-top: 1px solid #ccc; padding-top: 20px;">
        <a href="izvestaj_lista.php" class="btn btn-sivo">Otkaži</a>
        <button type="submit" class="btn btn-plavo" style="margin-left: 10px;">Sačuvaj izmene</button>
    </div>
</form>

<?php require_once __DIR__ . '/../ukljuci/podnozje.php'; ?>
