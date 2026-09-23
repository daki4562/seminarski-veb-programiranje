<?php
require_once 'BaznaKlasa.php';

/**
 * Klasa za upravljanje detaljima (stavkama) izveštaja o popravci.
 */
class Intervencija extends BaznaKlasa {
    
    protected $tabela = 'intervencija';
    
    /**
     * Dohvataju se sve intervencije.
     */
    public function dohvatiSve() {
        $sql = "SELECT * FROM {$this->tabela} ORDER BY izvestaj_id, redni_broj";
        $rezultat = $this->konekcija->query($sql);
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Dohvata se jedna intervencija na osnovu ID-a.
     */
    public function dohvatiPoId($id) {
        $sql = "SELECT * FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        $rezultat = $stmt->get_result();
        return $rezultat->fetch_assoc();
    }
    
    /**
     * Dohvataju se sve intervencije koje pripadaju određenom izveštaju.
     */
    public function dohvatiPoIzvestaju($izvestaj_id) {
        $sql = "SELECT * FROM {$this->tabela} WHERE izvestaj_id = ? ORDER BY redni_broj";
        $stmt = $this->izvrsiUpit($sql, [$izvestaj_id], 'i');
        $rezultat = $stmt->get_result();
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }
    
    public function unesi($podaci) {
        $sql = "INSERT INTO {$this->tabela} (izvestaj_id, redni_broj, radna_operacija, potroseni_materijal) VALUES (?, ?, ?, ?)";
        $parametri = [
            $podaci['izvestaj_id'],
            $podaci['redni_broj'],
            $podaci['radna_operacija'],
            $podaci['potroseni_materijal']
        ];
        $stmt = $this->izvrsiUpit($sql, $parametri, 'iiss');
        return $stmt->insert_id;
    }
    
    public function izmeni($id, $podaci) {
        $sql = "UPDATE {$this->tabela} SET izvestaj_id = ?, redni_broj = ?, radna_operacija = ?, potroseni_materijal = ? WHERE id = ?";
        $parametri = [
            $podaci['izvestaj_id'],
            $podaci['redni_broj'],
            $podaci['radna_operacija'],
            $podaci['potroseni_materijal'],
            $id
        ];
        $stmt = $this->izvrsiUpit($sql, $parametri, 'iissi');
        return $stmt->affected_rows > 0;
    }
    
    public function obrisi($id) {
        $sql = "DELETE FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        return $stmt->affected_rows > 0;
    }
    
    /**
     * Brišu se sve intervencije vezane za zadati izveštaj.
     */
    public function obrisiPoIzvestaju($izvestaj_id) {
        $sql = "DELETE FROM {$this->tabela} WHERE izvestaj_id = ?";
        $stmt = $this->izvrsiUpit($sql, [$izvestaj_id], 'i');
        return $stmt->affected_rows > 0;
    }
}
?>
