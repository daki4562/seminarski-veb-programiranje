<?php
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Izvestaj.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID izveštaja nije prosleđen.");
}

$izvestajModel = new Izvestaj();
$podaci = $izvestajModel->dohvatiSaIntervencijama($id);

if (!$podaci || !$podaci['izvestaj']) {
    die("Izveštaj nije pronađen.");
}

$izvestaj = $podaci['izvestaj'];
$intervencije = $podaci['intervencije'];
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Štampa - Izveštaj <?php echo htmlspecialchars($izvestaj['broj_izvestaja']); ?></title>
    <link rel="stylesheet" href="../css/stil.css">
    <style>
        body { background-color: #fff; display: flex; justify-content: center; }
        .stampa-dokument {
            width: 800px;
            margin: 20px auto;
            border: 2px solid #000;
            padding: 40px;
            font-family: 'Times New Roman', Times, serif;
        }
        .stampa-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .stampa-header h2 { margin: 0 0 5px 0; font-size: 1.5rem; }
        .stampa-header p { margin: 2px 0; font-size: 1rem; }
        .stampa-naslov {
            text-align: center;
            margin: 30px 0;
            font-weight: bold;
            font-size: 1.3rem;
            text-transform: uppercase;
        }
        .stampa-podaci {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        .stampa-red { margin-bottom: 10px; }
        .stampa-red strong { display: inline-block; width: 150px; }
        .stampa-podnaslov {
            text-align: center;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            margin: 20px 0;
            font-weight: bold;
        }
        .stampa-potpis {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .potpis-linija {
            border-bottom: 1px solid #000;
            width: 250px;
            display: inline-block;
            margin-bottom: 5px;
        }
        .stampa-akcija {
            text-align: center;
            margin-bottom: 20px;
            width: 100%;
        }
        @media print {
            .stampa-akcija { display: none; }
            body { margin: 0; padding: 0; }
            .stampa-dokument { border: none; width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body>

    <div style="width: 100%; display: flex; flex-direction: column; align-items: center;">
        <div class="stampa-akcija">
            <button onclick="window.print()" class="btn btn-plavo" style="font-size: 1.2rem; padding: 10px 30px;">Odštampaj izveštaj</button>
            <a href="izvestaj_prikaz.php?id=<?php echo htmlspecialchars($izvestaj['id']); ?>" class="btn btn-sivo">Nazad na prikaz</a>
        </div>

        <div class="stampa-dokument">
            <div class="stampa-header">
                <h2><?php echo htmlspecialchars($izvestaj['naziv_preduzeca']); ?></h2>
                <p><?php echo htmlspecialchars($izvestaj['adresa_preduzeca']); ?> | Tel: <?php echo htmlspecialchars($izvestaj['telefon_preduzeca']); ?> | Email: <?php echo htmlspecialchars($izvestaj['email_preduzeca']); ?></p>
                <p>PIB: <?php echo htmlspecialchars($izvestaj['pib']); ?></p>
            </div>

            <div class="stampa-naslov">
                DNEVNI IZVEŠTAJ O REALIZACIJI<br>POPRAVKE U STANU
            </div>

            <div class="stampa-podaci">
                <div>
                    <div class="stampa-red"><strong>Broj izveštaja:</strong> <?php echo htmlspecialchars($izvestaj['broj_izvestaja']); ?></div>
                    <div class="stampa-red"><strong>Radni nalog br:</strong> <?php echo htmlspecialchars($izvestaj['broj_radnog_naloga']); ?></div>
                </div>
                <div>
                    <div class="stampa-red"><strong>Datum:</strong> <?php echo htmlspecialchars(date('d.m.Y.', strtotime($izvestaj['datum_intervencije']))); ?></div>
                    <div class="stampa-red"><strong>Datum RN:</strong> <?php echo htmlspecialchars(date('d.m.Y.', strtotime($izvestaj['datum_radnog_naloga']))); ?></div>
                </div>
            </div>

            <div class="stampa-red" style="margin-top: 20px;">
                <strong>Adresa stana:</strong> <?php echo htmlspecialchars($izvestaj['adresa_stana']); ?>
            </div>
            <div class="stampa-red" style="margin-top: 10px;">
                <strong>Opis kvara:</strong><br>
                <div style="border: 1px solid #ccc; padding: 10px; min-height: 50px; margin-top: 5px;">
                    <?php echo nl2br(htmlspecialchars($izvestaj['opis_kvara'])); ?>
                </div>
            </div>
            
            <div class="stampa-red" style="margin-top: 20px;">
                <strong>Majstor:</strong> <?php echo htmlspecialchars($izvestaj['majstor_prezime'] . ' ' . $izvestaj['majstor_ime']); ?>
            </div>

            <div class="stampa-podnaslov">
                OBAVLJENE INTERVENCIJE
            </div>

            <table class="tabela" style="border: 1px solid #000;">
                <thead>
                    <tr>
                        <th style="border: 1px solid #000; background-color: #f0f0f0; color: #000; width: 50px;">Rb</th>
                        <th style="border: 1px solid #000; background-color: #f0f0f0; color: #000;">Radna operacija</th>
                        <th style="border: 1px solid #000; background-color: #f0f0f0; color: #000;">Materijal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($intervencije) > 0): ?>
                        <?php foreach ($intervencije as $index => $int): ?>
                            <tr>
                                <td style="border: 1px solid #000; text-align: center;"><?php echo $index + 1; ?></td>
                                <td style="border: 1px solid #000;"><?php echo htmlspecialchars($int['radna_operacija']); ?></td>
                                <td style="border: 1px solid #000;"><?php echo htmlspecialchars($int['potroseni_materijal']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="border: 1px solid #000; text-align: center; height: 30px;"></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="stampa-potpis">
                <div>
                    Datum štampe:<br>
                    <strong><?php echo date('d.m.Y.'); ?></strong>
                </div>
                <div style="text-align: center;">
                    Potpis majstora:<br><br>
                    <span class="potpis-linija"></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
