<?php
// Vrši se provera pristupa
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';

require_once __DIR__ . '/../klase/Izvestaj.php';
require_once __DIR__ . '/../klase/Majstor.php';

$izvestajModel = new Izvestaj();
$majstorModel = new Majstor();

// Dohvataju se majstori za padajuću listu filtera
$sviMajstori = [];
$sviMajstori = $majstorModel->dohvatiSve();

// Preuzimaju se vrednosti filtera
$filter_datum_od = $_GET['datum_od'] ?? '';
$filter_datum_do = $_GET['datum_do'] ?? '';
$filter_majstor_id = $_GET['majstor_id'] ?? '';
$filter_adresa = $_GET['adresa_stana'] ?? '';

// Dohvataju se filtrirani izveštaji
$izvestaji = [];
$izvestaji = $izvestajModel->pretrazi($filter_datum_od, $filter_datum_do, $filter_majstor_id, $filter_adresa);

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2>Lista izveštaja</h2>
    <a href="izvestaj_unos.php" class="btn btn-zeleno">Dodaj novi izveštaj</a>
</div>

<!-- Forma za filtriranje izveštaja -->
<form method="GET" action="izvestaj_lista.php" class="filter-forma">
    <div class="forma-grupa">
        <label for="datum_od">Datum od:</label>
        <input type="date" name="datum_od" id="datum_od" class="forma-kontrola" value="<?php echo htmlspecialchars($filter_datum_od); ?>">
    </div>
    
    <div class="forma-grupa">
        <label for="datum_do">Datum do:</label>
        <input type="date" name="datum_do" id="datum_do" class="forma-kontrola" value="<?php echo htmlspecialchars($filter_datum_do); ?>">
    </div>

    <div class="forma-grupa">
        <label for="majstor_id">Majstor:</label>
        <select name="majstor_id" id="majstor_id" class="forma-kontrola">
            <option value="">-- Svi majstori --</option>
            <?php foreach ($sviMajstori as $m): ?>
                <option value="<?php echo $m['id']; ?>" <?php if ($filter_majstor_id == $m['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($m['ime'] . ' ' . $m['prezime']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="forma-grupa">
        <label for="adresa_stana">Adresa stana:</label>
        <input type="text" name="adresa_stana" id="adresa_stana" class="forma-kontrola" value="<?php echo htmlspecialchars($filter_adresa); ?>">
    </div>

    <div class="forma-grupa" style="flex: 0 0 auto; display: flex; gap: 10px;">
        <button type="submit" class="btn btn-plavo">Pretraži</button>
        <a href="izvestaj_lista.php" class="btn btn-sivo">Poništi filter</a>
        
        <!-- Prosleđuju se parametri filtera formi za štampu -->
        <?php
        $print_params = http_build_query([
            'datum_od' => $filter_datum_od,
            'datum_do' => $filter_datum_do,
            'majstor_id' => $filter_majstor_id,
            'adresa_stana' => $filter_adresa
        ]);
        ?>
        <a href="stampa_lista.php?<?php echo $print_params; ?>" target="_blank" class="btn btn-sivo" style="background-color: #424242;">Štampaj listu</a>
    </div>
</form>

<?php
// Prikazuju se sistemske poruke
if (isset($_GET['poruka'])) {
    if ($_GET['poruka'] === 'obrisano') {
        echo '<div class="poruka poruka-uspeh">Izveštaj je uspešno obrisan.</div>';
    } elseif ($_GET['poruka'] === 'dodato') {
        echo '<div class="poruka poruka-uspeh">Izveštaj je uspešno dodat.</div>';
    } elseif ($_GET['poruka'] === 'izmenjeno') {
        echo '<div class="poruka poruka-uspeh">Izveštaj je uspešno izmenjen.</div>';
    }
}
?>

<div style="overflow-x: auto;">
    <table class="tabela">
        <thead>
            <tr>
                <th>Broj izveštaja</th>
                <th>Datum</th>
                <th>Adresa stana</th>
                <th>Majstor</th>
                <th>Opis kvara</th>
                <th class="akcije">Akcije</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($izvestaji) > 0): ?>
                <?php foreach ($izvestaji as $izvestaj): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($izvestaj['broj_izvestaja']); ?></td>
                        <td><?php echo htmlspecialchars(date('d.m.Y.', strtotime($izvestaj['datum_intervencije']))); ?></td>
                        <td><?php echo htmlspecialchars($izvestaj['adresa_stana']); ?></td>
                        <td><?php echo htmlspecialchars($izvestaj['majstor_ime'] . ' ' . $izvestaj['majstor_prezime']); ?></td>
                        <td>
                            <?php 
                            // Prikazuje se skraćeni opis kvara
                            $opis = $izvestaj['opis_kvara'];
                            echo htmlspecialchars(mb_strlen($opis) > 30 ? mb_substr($opis, 0, 30) . '...' : $opis); 
                            ?>
                        </td>
                        <td class="akcije">
                            <a href="izvestaj_prikaz.php?id=<?php echo $izvestaj['id']; ?>" class="btn btn-plavo" style="padding: 5px 10px; font-size: 0.8rem;">Prikaz</a>
                            <a href="izvestaj_izmena.php?id=<?php echo $izvestaj['id']; ?>" class="btn btn-sivo" style="padding: 5px 10px; font-size: 0.8rem;">Izmena</a>
                            <a href="izvestaj_brisanje.php?id=<?php echo $izvestaj['id']; ?>" class="btn btn-crveno" style="padding: 5px 10px; font-size: 0.8rem;">Brisanje</a>
                            <a href="stampa_izvestaj.php?id=<?php echo $izvestaj['id']; ?>" target="_blank" class="btn btn-sivo" style="background-color: #424242; padding: 5px 10px; font-size: 0.8rem;">Štampa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nema pronađenih izveštaja za dati kriterijum pretrage.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once __DIR__ . '/../ukljuci/podnozje.php';
?>
