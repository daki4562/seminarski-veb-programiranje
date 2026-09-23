<?php
session_start();

// Vrši se preusmeravanje ukoliko je korisnik već prijavljen
if (isset($_SESSION['korisnik_id'])) {
    header('Location: pocetna.php');
    exit;
}

require_once __DIR__ . '/../klase/Korisnik.php';

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnickoIme = trim($_POST['korisnicko_ime'] ?? '');
    $lozinka = trim($_POST['lozinka'] ?? '');

    if (empty($korisnickoIme) || empty($lozinka)) {
        $greska = 'Unesite korisničko ime i lozinku.';
    } else {
        $korisnik = new Korisnik();
        $uspesno = $korisnik->prijava($korisnickoIme, $lozinka);

        if ($uspesno) {
            // Upisuju se podaci o korisniku u sesiju
            $_SESSION['korisnik_id'] = $uspesno['id'];
            $_SESSION['korisnicko_ime'] = $uspesno['korisnicko_ime'];
            $_SESSION['ime'] = $uspesno['ime'];
            $_SESSION['prezime'] = $uspesno['prezime'];
            
            header('Location: pocetna.php');
            exit;
        } else {
            $greska = 'Pogrešno korisničko ime ili lozinka.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prijava - Evidencija popravki</title>
    <link rel="stylesheet" href="../css/stil.css">
</head>
<body style="background-color: #e3f2fd; justify-content: center; align-items: center; display: flex; height: 100vh; margin: 0;">

    <div class="login-kontejner">
        <h2 class="login-naslov">Prijava na sistem</h2>
        
        <?php if (!empty($greska)): ?>
            <div class="poruka poruka-greska"><?php echo htmlspecialchars($greska); ?></div>
        <?php endif; ?>

        <form method="POST" action="prijava.php" onsubmit="return validacijaPrijave();">
            <div class="forma-grupa">
                <label for="korisnicko_ime" class="obavezno">Korisničko ime</label>
                <input type="text" id="korisnicko_ime" name="korisnicko_ime" class="forma-kontrola" value="<?php echo htmlspecialchars($_POST['korisnicko_ime'] ?? ''); ?>">
            </div>
            
            <div class="forma-grupa">
                <label for="lozinka" class="obavezno">Lozinka</label>
                <input type="password" id="lozinka" name="lozinka" class="forma-kontrola">
            </div>
            
            <button type="submit" class="btn btn-plavo" style="width: 100%; margin-top: 10px;">Prijavi se</button>
        </form>
    </div>

    <script src="../js/validacija.js"></script>
</body>
</html>
