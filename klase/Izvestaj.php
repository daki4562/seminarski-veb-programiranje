<?php
require_once 'BaznaKlasa.php';
require_once 'Majstor.php';
require_once 'Intervencija.php';

/**
 * Glavna klasa za rad sa izveštajima o popravkama.
 * Oslikava kompoziciju sa klasom Intervencija i asocijaciju sa klasom Majstor.
 */
class Izvestaj extends BaznaKlasa {
    
    protected $tabela = 'izvestaj';
    
    public $intervencije = [];
    public $majstor = null;

    /**
     * Dohvataju se svi izveštaji iz baze podataka.
     */
    public function dohvatiSve() {
        $sql = "SELECT * FROM {$this->tabela} ORDER BY datum_intervencije DESC";
        $rezultat = $this->konekcija->query($sql);
        return $rezultat->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Dohvata se izveštaj na osnovu ID-a.
     */
    public function dohvatiPoId($id) {
        $sql = "SELECT * FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        $rezultat = $stmt->get_result();
        return $rezultat->fetch_assoc();
    }
    
    /**
     * Dohvata se izveštaj sa povezanim intervencijama i majstorom.
     */
    public function dohvatiSaIntervencijama($id) {
        $podaci_izvestaja = $this->dohvatiPoId($id);
        if (!$podaci_izvestaja) return false;
        
        $objIntervencija = new Intervencija();
        $this->intervencije = $objIntervencija->dohvatiPoIzvestaju($id);
        
        $objMajstor = new Majstor();
        $this->majstor = $objMajstor->dohvatiPoId($podaci_izvestaja['majstor_id']);
        
        if ($this->majstor) {
            $podaci_izvestaja['majstor_ime'] = $this->majstor['ime'];
            $podaci_izvestaja['majstor_prezime'] = $this->majstor['prezime'];
        }
        
        return [
            'izvestaj' => $podaci_izvestaja,
            'intervencije' => $this->intervencije,
            'majstor' => $this->majstor
        ];
    }
    
    public function unesi($podaci) {
        $sql = "INSERT INTO {$this->tabela} (naziv_preduzeca, adresa_preduzeca, telefon_preduzeca, email_preduzeca, pib, broj_izvestaja, datum_radnog_naloga, broj_radnog_naloga, datum_intervencije, adresa_stana, opis_kvara, majstor_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $parametri = [
            $podaci['naziv_preduzeca'], $podaci['adresa_preduzeca'], $podaci['telefon_preduzeca'], 
            $podaci['email_preduzeca'], $podaci['pib'], $podaci['broj_izvestaja'], 
            $podaci['datum_radnog_naloga'], $podaci['broj_radnog_naloga'], 
            $podaci['datum_intervencije'], $podaci['adresa_stana'], $podaci['opis_kvara'], 
            $podaci['majstor_id']
        ];
        
        $stmt = $this->izvrsiUpit($sql, $parametri, 'sssssssssssi');
        return $stmt->insert_id;
    }
    
    /**
     * Unosi se izveštaj i pripadajuće intervencije uz upotrebu transakcije.
     */
    public function unesiSaIntervencijama($podaci, $listaIntervencija) {
        try {
            $this->konekcija->begin_transaction();
            
            $this->konekcija->query("SET @novi_id = 0");
            
            $parametri = [
                $podaci['naziv_preduzeca'], $podaci['adresa_preduzeca'], $podaci['telefon_preduzeca'], 
                $podaci['email_preduzeca'], $podaci['pib'], $podaci['broj_izvestaja'], 
                $podaci['datum_radnog_naloga'], $podaci['broj_radnog_naloga'], 
                $podaci['datum_intervencije'], $podaci['adresa_stana'], $podaci['opis_kvara'], 
                $podaci['majstor_id']
            ];
            
            $sql = "CALL sp_unos_izvestaja(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @novi_id)";
            $this->izvrsiUpit($sql, $parametri, 'sssssssssssi');
            
            $rezultat = $this->konekcija->query("SELECT @novi_id AS id");
            $row = $rezultat->fetch_assoc();
            $izvestaj_id = $row['id'];
            
            if (!$izvestaj_id) {
                throw new Exception("ID izveštaja nije vraćen.");
            }
            
            foreach ($listaIntervencija as $index => $intervencija) {
                $sql_int = "INSERT INTO intervencija (izvestaj_id, redni_broj, radna_operacija, potroseni_materijal) VALUES (?, ?, ?, ?)";
                $stmt_int = $this->konekcija->prepare($sql_int);
                $rb = $index + 1;
                $stmt_int->bind_param('iiss', $izvestaj_id, $rb, $intervencija['radna_operacija'], $intervencija['potroseni_materijal']);
                if (!$stmt_int->execute()) {
                    throw new Exception("Greška pri unosu intervencije.");
                }
            }
            
            $this->konekcija->commit();
            return $izvestaj_id;
            
        } catch (Exception $e) {
            $this->konekcija->rollback();
            error_log("Greška u transakciji: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vrši se izmena izveštaja i prepisivanje njegovih intervencija u okviru transakcije.
     */
    public function izmeniSaIntervencijama($id, $podaci, $listaIntervencija) {
        try {
            $this->konekcija->begin_transaction();
            
            $this->izmeni($id, $podaci);
            
            $sql_del = "DELETE FROM intervencija WHERE izvestaj_id = ?";
            $stmt_del = $this->konekcija->prepare($sql_del);
            $stmt_del->bind_param('i', $id);
            $stmt_del->execute();
            
            foreach ($listaIntervencija as $index => $intervencija) {
                $sql_int = "INSERT INTO intervencija (izvestaj_id, redni_broj, radna_operacija, potroseni_materijal) VALUES (?, ?, ?, ?)";
                $stmt_int = $this->konekcija->prepare($sql_int);
                $rb = $index + 1;
                $stmt_int->bind_param('iiss', $id, $rb, $intervencija['radna_operacija'], $intervencija['potroseni_materijal']);
                if (!$stmt_int->execute()) {
                    throw new Exception("Greška pri unosu novih intervencija.");
                }
            }
            
            $this->konekcija->commit();
            return true;
            
        } catch (Exception $e) {
            $this->konekcija->rollback();
            return false;
        }
    }
    
    public function izmeni($id, $podaci) {
        $sql = "UPDATE {$this->tabela} SET naziv_preduzeca = ?, adresa_preduzeca = ?, telefon_preduzeca = ?, email_preduzeca = ?, pib = ?, broj_izvestaja = ?, datum_radnog_naloga = ?, broj_radnog_naloga = ?, datum_intervencije = ?, adresa_stana = ?, opis_kvara = ?, majstor_id = ? WHERE id = ?";
        
        $parametri = [
            $podaci['naziv_preduzeca'], $podaci['adresa_preduzeca'], $podaci['telefon_preduzeca'], 
            $podaci['email_preduzeca'], $podaci['pib'], $podaci['broj_izvestaja'], 
            $podaci['datum_radnog_naloga'], $podaci['broj_radnog_naloga'], 
            $podaci['datum_intervencije'], $podaci['adresa_stana'], $podaci['opis_kvara'], 
            $podaci['majstor_id'],
            $id
        ];
        
        $stmt = $this->izvrsiUpit($sql, $parametri, 'sssssssssssii');
        return $stmt->affected_rows >= 0;
    }
    
    /**
     * Vrši se brisanje izveštaja i svih njegovih intervencija u okviru transakcije.
     */
    public function obrisiSaIntervencijama($id) {
        try {
            $this->konekcija->begin_transaction();
            $this->obrisi($id);
            $this->konekcija->commit();
            return true;
        } catch (Exception $e) {
            $this->konekcija->rollback();
            return false;
        }
    }
    
    public function obrisi($id) {
        $sql = "DELETE FROM {$this->tabela} WHERE id = ?";
        $stmt = $this->izvrsiUpit($sql, [$id], 'i');
        return $stmt->affected_rows > 0;
    }
    
    /**
     * Vrši se pretraga izveštaja pozivom uskladištene procedure.
     */
    public function pretrazi($datumOd, $datumDo, $majstorId, $adresaStana) {
        $dOd = empty($datumOd) ? null : $datumOd;
        $dDo = empty($datumDo) ? null : $datumDo;
        $mId = empty($majstorId) ? null : (int)$majstorId;
        $adresa = empty($adresaStana) ? null : $adresaStana;
        
        $stmt = $this->konekcija->prepare("CALL sp_pretraga_izvestaja(?, ?, ?, ?)");
        $stmt->bind_param("ssis", $dOd, $dDo, $mId, $adresa);
        $stmt->execute();
        
        $rezultat = $stmt->get_result();
        
        while ($this->konekcija->more_results() && $this->konekcija->next_result()) {;}
        
        if ($rezultat) {
            return $rezultat->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
    
    /**
     * Dohvataju se izveštaji sa podacima o majstorima pomoću pogleda.
     */
    public function dohvatiIzPogled() {
        return $this->izvrsiPogled('v_izvestaji_sa_majstorima');
    }
    
    /**
     * Dohvata se statistika o majstorima pomoću pogleda.
     */
    public function dohvatiStatistiku() {
        return $this->izvrsiPogled('v_statistika_majstora');
    }

    /**
     * Vrši se provera da li zadati broj izveštaja već postoji u bazi.
     * @param string $brojIzvestaja Broj izveštaja za proveru
     * @param int|null $izuzmiId Opcioni ID koji se izuzima iz provere
     * @return bool
     */
    public function proveriBrojIzvestaja($brojIzvestaja, $izuzmiId = null) {
        $sql = "SELECT COUNT(*) as broj FROM {$this->tabela} WHERE broj_izvestaja = ?";
        $parametri = [$brojIzvestaja];
        $tipovi = 's';
        
        if ($izuzmiId !== null) {
            $sql .= " AND id != ?";
            $parametri[] = $izuzmiId;
            $tipovi .= 'i';
        }
        
        $stmt = $this->izvrsiUpit($sql, $parametri, $tipovi);
        $rezultat = $stmt->get_result();
        $red = $rezultat->fetch_assoc();
        
        return $red['broj'] > 0;
    }
}
?>
