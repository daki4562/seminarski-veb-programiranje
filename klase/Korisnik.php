<?php
require_once 'BaznaKlasa.php';

/**
 * Klasa za upravljanje korisničkim nalozima u sistemu.
 */
class Korisnik extends BaznaKlasa {
    
    protected $tabela = 'korisnik';
    
    /**
     * Dohvataju se svi korisnici iz baze podataka.
     */
    public function dohvatiSve() {
        $sql = "SELECT id, korisnicko_ime, ime, prezime, email FROM {$this->tabela}";
        $rezultat = $this->konekcija->query($sql);
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Dohvata se jedan korisnik na osnovu ID-a.
     */
    public function dohvatiPoId($id) {
        $sql = "SELECT id, korisnicko_ime, ime, prezime, email FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        $rezultat = $stmt->get_result();
        return $rezultat->fetch_assoc();
    }
    
    /**
     * Unosi se novi korisnik u bazu podataka.
     */
    public function unesi($podaci) {
        if ($this->proveraKorisnickogImena($podaci['korisnicko_ime'])) {
            return false;
        }
        
        $lozinkaHash = password_hash($podaci['lozinka'], PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO {$this->tabela} (korisnicko_ime, lozinka, ime, prezime, email) VALUES (?, ?, ?, ?, ?)";
        $parametri = [
            $podaci['korisnicko_ime'], 
            $lozinkaHash, 
            $podaci['ime'], 
            $podaci['prezime'], 
            $podaci['email']
        ];
        
        $stmt = $this->izvrsiUpit($sql, $parametri, 'sssss');
        return $stmt->insert_id;
    }
    
    public function izmeni($id, $podaci) {
        if (!empty($podaci['lozinka'])) {
            $lozinkaHash = password_hash($podaci['lozinka'], PASSWORD_BCRYPT);
            $sql = "UPDATE {$this->tabela} SET korisnicko_ime = ?, lozinka = ?, ime = ?, prezime = ?, email = ? WHERE id = ?";
            $parametri = [
                $podaci['korisnicko_ime'], 
                $lozinkaHash, 
                $podaci['ime'], 
                $podaci['prezime'], 
                $podaci['email'],
                $id
            ];
            $stmt = $this->izvrsiUpit($sql, $parametri, 'sssssi');
        } else {
            $sql = "UPDATE {$this->tabela} SET korisnicko_ime = ?, ime = ?, prezime = ?, email = ? WHERE id = ?";
            $parametri = [
                $podaci['korisnicko_ime'], 
                $podaci['ime'], 
                $podaci['prezime'], 
                $podaci['email'],
                $id
            ];
            $stmt = $this->izvrsiUpit($sql, $parametri, 'ssssi');
        }
        
        return $stmt->affected_rows > 0;
    }
    
    public function obrisi($id) {
        $sql = "DELETE FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        return $stmt->affected_rows > 0;
    }
    
    /**
     * Vrši se prijava korisnika proverom prosleđenih parametara.
     * @return array|false Vraća se niz sa podacima o korisniku u slučaju uspešne prijave, inače se vraća false.
     */
    public function prijava($korisnicko_ime, $lozinka) {
        $sql = "SELECT * FROM {$this->tabela} WHERE korisnicko_ime = ?";
        $stmt = $this->izvrsiUpit($sql, [$korisnicko_ime], 's');
        $rezultat = $stmt->get_result();
        
        if ($rezultat->num_rows === 1) {
            $korisnik = $rezultat->fetch_assoc();
            if (password_verify($lozinka, $korisnik['lozinka'])) {
                unset($korisnik['lozinka']);
                return $korisnik;
            }
        }
        return false;
    }
    
    /**
     * Vrši se provera zauzetosti korisničkog imena u bazi.
     * @return bool
     */
    public function proveraKorisnickogImena($korisnicko_ime) {
        $sql = "SELECT id FROM {$this->tabela} WHERE korisnicko_ime = ?";
        $stmt = $this->izvrsiUpit($sql, [$korisnicko_ime], 's');
        $rezultat = $stmt->get_result();
        return $rezultat->num_rows > 0;
    }
}
?>
