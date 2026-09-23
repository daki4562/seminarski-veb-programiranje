<?php
require_once 'BaznaKlasa.php';

/**
 * Klasa za upravljanje podacima o majstorima.
 */
class Majstor extends BaznaKlasa {
    
    protected $tabela = 'majstor';
    
    /**
     * Dohvataju se svi majstori iz baze podataka.
     */
    public function dohvatiSve() {
        $sql = "SELECT * FROM {$this->tabela} ORDER BY ime, prezime";
        $rezultat = $this->konekcija->query($sql);
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Dohvata se jedan majstor na osnovu ID-a.
     */
    public function dohvatiPoId($id) {
        $sql = "SELECT * FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        $rezultat = $stmt->get_result();
        return $rezultat->fetch_assoc();
    }
    
    public function unesi($podaci) {
        $sql = "INSERT INTO {$this->tabela} (ime, prezime, telefon, specijalnost) VALUES (?, ?, ?, ?)";
        $parametri = [
            $podaci['ime'],
            $podaci['prezime'],
            $podaci['telefon'],
            $podaci['specijalnost']
        ];
        $stmt = $this->izvrsiUpit($sql, $parametri, 'ssss');
        return $stmt->insert_id;
    }
    
    public function izmeni($id, $podaci) {
        $sql = "UPDATE {$this->tabela} SET ime = ?, prezime = ?, telefon = ?, specijalnost = ? WHERE id = ?";
        $parametri = [
            $podaci['ime'],
            $podaci['prezime'],
            $podaci['telefon'],
            $podaci['specijalnost'],
            $id
        ];
        $stmt = $this->izvrsiUpit($sql, $parametri, 'ssssi');
        return $stmt->affected_rows > 0;
    }
    
    public function obrisi($id) {
        $sql = "DELETE FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        return $stmt->affected_rows > 0;
    }
    
    /**
     * Dohvata se formatirana lista majstora prilagođena za HTML select element.
     */
    public function dohvatiZaSelect() {
        $sql = "SELECT id, CONCAT(ime, ' ', prezime, ' (', specijalnost, ')') AS naziv FROM {$this->tabela} ORDER BY ime";
        $rezultat = $this->konekcija->query($sql);
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }
}
?>
