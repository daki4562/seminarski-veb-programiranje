<?php
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Izvestaj.php';

$izvestajModel = new Izvestaj();

$filter_datum_od = $_GET['datum_od'] ?? '';
$filter_datum_do = $_GET['datum_do'] ?? '';
$filter_majstor_id = $_GET['majstor_id'] ?? '';
$filter_adresa = $_GET['adresa_stana'] ?? '';

$izvestaji = [];
$izvestaji = $izvestajModel->pretrazi($filter_datum_od, $filter_datum_do, $filter_majstor_id, $filter_adresa);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Štampa liste izveštaja</title>
    <link rel="stylesheet" href="../css/stil.css">
    <style>
        body { background-color: #fff; padding: 20px; }
        .stampa-akcija { margin-bottom: 20px; text-align: right; }
        @media print {
            .stampa-akcija { display: none; }
        }
    </style>
</head>
<body>

    <div class="stampa-akcija">
        <button onclick="window.print()" class="btn btn-plavo">Štampaj</button>
    </div>

    <div class="stampa-zaglavlje">
        <h2>Evidencija popravki kvarova</h2>
        <h3>Lista izveštaja</h3>
        <p>Datum štampe: <?php echo date('d.m.Y. H:i'); ?></p>
        <p>Korisnik: <?php echo htmlspecialchars($_SESSION['korisnicko_ime'] ?? 'Nepoznat'); ?></p>
    </div>

    <table class="tabela">
        <thead>
            <tr>
                <th>Broj izveštaja</th>
                <th>Datum</th>
                <th>Adresa stana</th>
                <th>Majstor</th>
                <th>Opis kvara</th>
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
                        <td><?php echo htmlspecialchars($izvestaj['opis_kvara']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Nema pronađenih izveštaja za dati kriterijum pretrage.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>
