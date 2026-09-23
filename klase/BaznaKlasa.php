<?php
/**
 * Apstraktna bazna klasa za rad sa bazom podataka.
 * Obezbeđuje konekciju na MySQL bazu i osnovne metode za izvršavanje upita, uskladištenih procedura i pogleda.
 */
abstract class BaznaKlasa {
    protected $konekcija;
    protected $tabela;
    
    /**
     * Uspostavlja se konekcija sa bazom podataka.
     */
    public function __construct() {
        // Učitava se konfiguracija, u suprotnom se koriste podrazumevane vrednosti.
        $config_putanja = __DIR__ . '/../ukljuci/konfiguracija.php';
        if (file_exists($config_putanja)) {
            $config = require $config_putanja;
        } else {
            $config = [
                'db_host' => 'localhost',
                'db_korisnik' => 'root',
                'db_lozinka' => '',
                'db_naziv' => 'evidencija_popravki'
            ];
        }
        
        $this->konekcija = new mysqli(
            $config['db_host'],
            $config['db_korisnik'],
            $config['db_lozinka'],
            $config['db_naziv']
        );
        
        if ($this->konekcija->connect_error) {
            die('Greška pri povezivanju: ' . $this->konekcija->connect_error);
        }
        
        $this->konekcija->set_charset('utf8mb4');
        $this->konekcija->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        $this->konekcija->query("SET collation_connection = 'utf8mb4_unicode_ci'");
    }
    
    /**
     * Zatvara se konekcija sa bazom podataka.
     */
    public function __destruct() {
        if ($this->konekcija) {
            $this->konekcija->close();
        }
    }
    
    /**
     * Vraća se MySQLi objekat konekcije.
     */
    public function getKonekcija() {
        return $this->konekcija;
    }
    
    /**
     * Izvršava se pripremljeni SQL upit sa parametrima.
     * Koriste se pripremljeni iskazi radi zaštite od SQL injection napada.
     */
    protected function izvrsiUpit($sql, $parametri = [], $tipovi = '') {
        $stmt = $this->konekcija->prepare($sql);
        if (!$stmt) {
            die('Greška u pripremi upita: ' . $this->konekcija->error);
        }
        if (!empty($parametri)) {
            $stmt->bind_param($tipovi, ...$parametri);
        }
        $stmt->execute();
        return $stmt;
    }
    
    /**
     * Izvršava se uskladištena procedura.
     */
    protected function izvrsiProceduru($naziv, $parametri = [], $tipovi = '') {
        $placeholders = implode(',', array_fill(0, count($parametri), '?'));
        $sql = "CALL {$naziv}({$placeholders})";
        return $this->izvrsiUpit($sql, $parametri, $tipovi);
    }
    
    /**
     * Izvršava se pogled (VIEW) i dohvataju se svi podaci.
     */
    protected function izvrsiPogled($naziv) {
        $rezultat = $this->konekcija->query("SELECT * FROM {$naziv}");
        if (!$rezultat) {
            die('Greška pri izvršavanju pogleda: ' . $this->konekcija->error);
        }
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }
    
    abstract public function dohvatiSve();
    abstract public function dohvatiPoId($id);
    abstract public function unesi($podaci);
    abstract public function izmeni($id, $podaci);
    abstract public function obrisi($id);
}
?>
