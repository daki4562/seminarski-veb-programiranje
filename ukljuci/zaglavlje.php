<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evidencija popravki kvarova</title>
    <link rel="stylesheet" href="../css/stil.css">
</head>
<body>
    <!-- Navigacioni meni se prikazuje isključivo prijavljenim korisnicima -->
    <?php if (isset($_SESSION['korisnik_id'])): ?>
    <nav class="navigacija">
        <div class="nav-kontejner">
            <div class="nav-logo">Evidencija Popravki</div>
            <ul class="nav-linkovi">
                <li><a href="pocetna.php">Početna</a></li>
                <li><a href="izvestaj_lista.php">Izveštaji</a></li>
                <li><a href="izvestaj_unos.php">Novi izveštaj</a></li>
            </ul>
            <div class="nav-korisnik">
                Dobrodošli, <strong><?php echo htmlspecialchars($_SESSION['korisnicko_ime'] ?? 'Korisnik'); ?></strong>
                <a href="odjava.php" class="btn btn-odjava">Odjava</a>
            </div>
        </div>
    </nav>
    <?php endif; ?>
    
    <main class="glavni-kontejner">
