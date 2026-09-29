<?php
require_once __DIR__ . '/../ukljuci/provera_pristupa.php';
require_once __DIR__ . '/../klase/Korisnik.php';

$greska = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $korisnicko_ime = trim($_POST['korisnicko_ime'] ?? '');
    $lozinka = $_POST['lozinka'] ?? '';
    $ime = trim($_POST['ime'] ?? '');
    $prezime = trim($_POST['prezime'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Serverska validacija
    if (empty($korisnicko_ime) || empty($lozinka) || empty($ime) || empty($prezime) || empty($email)) {
        $greska = 'Sva polja su obavezna!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $greska = 'Email adresa nije u validnom formatu!';
    } elseif (strlen($lozinka) < 4) {
        $greska = 'Lozinka mora imati najmanje 4 karaktera.';
    } else {
        $korisnikModel = new Korisnik();
        
        // Podesavanje niza za metodu unesi
        $podaci = [
            'korisnicko_ime' => $korisnicko_ime,
            'lozinka' => $lozinka,
            'ime' => $ime,
            'prezime' => $prezime,
            'email' => $email
        ];
        
        $novi_id = $korisnikModel->unesi($podaci);
        
        if ($novi_id) {
            // Ako je uspesno, prebaci ga nazad na listu
            header('Location: korisnik_lista.php');
            exit;
        } else {
            // Ako je metoda unesi() vratila false (znaci da korisnicko ime vec postoji)
            $greska = 'Korisničko ime je već zauzeto! Molimo izaberite drugo.';
        }
    }
}

require_once __DIR__ . '/../ukljuci/zaglavlje.php';
?>

<h2>Dodavanje novog korisnika</h2>

<?php if ($greska): ?>
    <div class="poruka poruka-greska"><?php echo htmlspecialchars($greska); ?></div>
<?php endif; ?>

<div class="forma-kontejner">
    <form method="POST" action="korisnik_unos.php">
        
        <div class="form-grupa">
            <label for="korisnicko_ime">Korisničko ime:</label>
            <input type="text" id="korisnicko_ime" name="korisnicko_ime" value="<?php echo htmlspecialchars($_POST['korisnicko_ime'] ?? ''); ?>" required>
        </div>
        
        <div class="form-grupa">
            <label for="lozinka">Lozinka:</label>
            <input type="password" id="lozinka" name="lozinka" required>
        </div>
        
        <div class="form-grupa">
            <label for="ime">Ime:</label>
            <input type="text" id="ime" name="ime" value="<?php echo htmlspecialchars($_POST['ime'] ?? ''); ?>" required>
        </div>
        
        <div class="form-grupa">
            <label for="prezime">Prezime:</label>
            <input type="text" id="prezime" name="prezime" value="<?php echo htmlspecialchars($_POST['prezime'] ?? ''); ?>" required>
        </div>
        
        <div class="form-grupa">
            <label for="email">Email adresa:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
        </div>
        
        <div class="form-akcije">
            <button type="submit" class="btn btn-zeleno">Sačuvaj korisnika</button>
            <a href="korisnik_lista.php" class="btn btn-sivo">Odustani</a>
        </div>
        
    </form>
</div>

<?php
require_once __DIR__ . '/../ukljuci/podnozje.php';
?>
