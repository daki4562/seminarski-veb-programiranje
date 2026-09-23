-- Baza podataka: evidencija_popravki
-- Charset: utf8mb4

CREATE DATABASE IF NOT EXISTS `evidencija_popravki` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `evidencija_popravki`;

-- --------------------------------------------------------
-- Tabela: korisnik (Nezavisna tabela)
-- Cuva podatke o korisnicima sistema (administratorima i zaposlenima)
-- --------------------------------------------------------
CREATE TABLE `korisnik` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `korisnicko_ime` VARCHAR(50) UNIQUE NOT NULL,
  `lozinka` VARCHAR(255) NOT NULL, -- Cuva bcrypt hes lozinke
  `ime` VARCHAR(50) NOT NULL,
  `prezime` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lozinke su heširane pomoću PHP password_hash() funkcije (PASSWORD_BCRYPT).
-- admin123 -> $2y$10$8tGlCEz7UVdt.5rkSaCGxupZg0pNMKqJ8kJd3.VRwMEs4VD2b0VXu
-- korisnik123 -> $2y$10$LgfYWkF8m3pKJM.NUY/y8OHpGpGxJdAnJl7d.MjYFxjBnLqW3qIGO
INSERT INTO `korisnik` (`korisnicko_ime`, `lozinka`, `ime`, `prezime`, `email`) VALUES
('admin', '$2y$10$8tGlCEz7UVdt.5rkSaCGxupZg0pNMKqJ8kJd3.VRwMEs4VD2b0VXu', 'Petar', 'Petrovic', 'admin@example.com'),
('korisnik', '$2y$10$LgfYWkF8m3pKJM.NUY/y8OHpGpGxJdAnJl7d.MjYFxjBnLqW3qIGO', 'Marko', 'Markovic', 'korisnik@example.com');

-- --------------------------------------------------------
-- Tabela: majstor (Šifarnik)
-- Cuva podatke o majstorima koji obavljaju intervencije
-- --------------------------------------------------------
CREATE TABLE `majstor` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ime` VARCHAR(50) NOT NULL,
  `prezime` VARCHAR(50) NOT NULL,
  `telefon` VARCHAR(20),
  `specijalnost` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `majstor` (`ime`, `prezime`, `telefon`, `specijalnost`) VALUES
('Jovan', 'Jovanovic', '061/111-2222', 'Vodoinstalater'),
('Nikola', 'Nikolic', '062/333-4444', 'Elektricar'),
('Milan', 'Milanovic', '063/555-6666', 'Stolar');

-- --------------------------------------------------------
-- Tabela: izvestaj (Celina/Master)
-- Cuva zaglavlje izvestaja o popravci
-- --------------------------------------------------------
CREATE TABLE `izvestaj` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `naziv_preduzeca` VARCHAR(100) NOT NULL,
  `adresa_preduzeca` VARCHAR(200) NOT NULL,
  `telefon_preduzeca` VARCHAR(20) NOT NULL,
  `email_preduzeca` VARCHAR(100),
  `pib` VARCHAR(9) NOT NULL,
  `broj_izvestaja` VARCHAR(20) UNIQUE NOT NULL,
  `datum_radnog_naloga` DATE NOT NULL,
  `broj_radnog_naloga` VARCHAR(20) NOT NULL,
  `datum_intervencije` DATE NOT NULL,
  `adresa_stana` VARCHAR(200) NOT NULL,
  `opis_kvara` TEXT NOT NULL,
  `majstor_id` INT NOT NULL,
  FOREIGN KEY (`majstor_id`) REFERENCES `majstor`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `izvestaj` (`naziv_preduzeca`, `adresa_preduzeca`, `telefon_preduzeca`, `email_preduzeca`, `pib`, `broj_izvestaja`, `datum_radnog_naloga`, `broj_radnog_naloga`, `datum_intervencije`, `adresa_stana`, `opis_kvara`, `majstor_id`) VALUES
('Stambeno doo', 'Bulevar Oslobodjenja 1', '011/123-456', 'office@stambeno.rs', '101112223', 'IZV-2023-01', '2023-10-01', 'RN-001', '2023-10-02', 'Dunavska 15', 'Curenje vode u kupatilu', 1),
('Gradsko Zelenilo', 'Maksima Gorkog 2', '021/987-654', 'info@zelenilo.rs', '102223334', 'IZV-2023-02', '2023-10-05', 'RN-002', '2023-10-06', 'Futoski put 50', 'Ne rade uticnice u kuhinji', 2),
('Odrzavanje doo', 'Cara Dusana 3', '011/555-111', 'kontakt@odrzavanje.rs', '103334445', 'IZV-2023-03', '2023-10-10', 'RN-003', '2023-10-11', 'Lomana 10', 'Slomljena vrata na kuhinjskom elementu', 3);

-- --------------------------------------------------------
-- Tabela: intervencija (Deo/Detail)
-- Cuva stavke (radne operacije) za svaki izvestaj
-- --------------------------------------------------------
CREATE TABLE `intervencija` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `izvestaj_id` INT NOT NULL,
  `redni_broj` INT NOT NULL,
  `radna_operacija` VARCHAR(255) NOT NULL,
  `potroseni_materijal` VARCHAR(255),
  FOREIGN KEY (`izvestaj_id`) REFERENCES `izvestaj`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `intervencija` (`izvestaj_id`, `redni_broj`, `radna_operacija`, `potroseni_materijal`) VALUES
(1, 1, 'Zatvaranje glavnog ventila i demontaza cevi', 'Kudelja, teflon traka'),
(1, 2, 'Montaza novog ventila i cevi', 'Ventil 1/2 cola, cev 1m'),
(2, 1, 'Provera osiguraca', 'Nema'),
(2, 2, 'Zamena zica u zidu', 'Zica 3x1.5 2m'),
(2, 3, 'Postavljanje nove uticnice', 'Dupla uticnica'),
(3, 1, 'Skidanje starih sarki', 'Nema'),
(3, 2, 'Postavljanje novih sarki i podesavanje vrata', 'Sarke 2 komada, srafovi');

-- --------------------------------------------------------
-- Stored Procedures
-- --------------------------------------------------------

DELIMITER //

-- Procedura za unos novog izvestaja. Vraca ID novog zapisa kroz OUT parametar.
CREATE PROCEDURE sp_unos_izvestaja(
  IN p_naziv_preduzeca VARCHAR(100),
  IN p_adresa_preduzeca VARCHAR(200),
  IN p_telefon_preduzeca VARCHAR(20),
  IN p_email_preduzeca VARCHAR(100),
  IN p_pib VARCHAR(9),
  IN p_broj_izvestaja VARCHAR(20),
  IN p_datum_radnog_naloga DATE,
  IN p_broj_radnog_naloga VARCHAR(20),
  IN p_datum_intervencije DATE,
  IN p_adresa_stana VARCHAR(200),
  IN p_opis_kvara TEXT,
  IN p_majstor_id INT,
  OUT p_novi_id INT
)
BEGIN
  INSERT INTO `izvestaj` (
    `naziv_preduzeca`, `adresa_preduzeca`, `telefon_preduzeca`, `email_preduzeca`,
    `pib`, `broj_izvestaja`, `datum_radnog_naloga`, `broj_radnog_naloga`,
    `datum_intervencije`, `adresa_stana`, `opis_kvara`, `majstor_id`
  ) VALUES (
    p_naziv_preduzeca, p_adresa_preduzeca, p_telefon_preduzeca, p_email_preduzeca,
    p_pib, p_broj_izvestaja, p_datum_radnog_naloga, p_broj_radnog_naloga,
    p_datum_intervencije, p_adresa_stana, p_opis_kvara, p_majstor_id
  );
  
  -- Dodela poslednjeg unetog ID-a OUT parametru
  SET p_novi_id = LAST_INSERT_ID();
END //

-- Procedura za pretragu izvestaja po razlicitim kriterijumima
-- Parametri su opcioni, ako se prosledi NULL kriterijum se ignorise
CREATE PROCEDURE sp_pretraga_izvestaja(
  IN p_datum_od DATE,
  IN p_datum_do DATE,
  IN p_majstor_id INT,
  IN p_adresa_stana VARCHAR(200)
)
BEGIN
  SELECT i.*, m.ime AS majstor_ime, m.prezime AS majstor_prezime 
  FROM `izvestaj` i
  JOIN `majstor` m ON i.majstor_id = m.id
  WHERE (p_datum_od IS NULL OR i.datum_intervencije >= p_datum_od)
    AND (p_datum_do IS NULL OR i.datum_intervencije <= p_datum_do)
    AND (p_majstor_id IS NULL OR i.majstor_id = p_majstor_id)
    AND (p_adresa_stana IS NULL OR i.adresa_stana LIKE CONCAT('%', p_adresa_stana, '%'))
  ORDER BY i.datum_intervencije DESC;
END //

DELIMITER ;

-- --------------------------------------------------------
-- Views (Pogledi)
-- --------------------------------------------------------

-- Pogled koji spaja izvestaj i majstora, prikazujuci pune podatke
CREATE VIEW v_izvestaji_sa_majstorima AS
SELECT 
  i.*, 
  m.ime AS majstor_ime, 
  m.prezime AS majstor_prezime,
  m.specijalnost,
  m.telefon AS majstor_telefon
FROM 
  `izvestaj` i
JOIN 
  `majstor` m ON i.majstor_id = m.id;

-- Pogled koji prikazuje statistiku po majstorima
CREATE VIEW v_statistika_majstora AS
SELECT 
  m.id,
  m.ime, 
  m.prezime, 
  COUNT(i.id) AS broj_izvestaja, 
  MAX(i.datum_intervencije) AS poslednji_datum
FROM 
  `majstor` m
LEFT JOIN 
  `izvestaj` i ON m.id = i.majstor_id
GROUP BY 
  m.id, m.ime, m.prezime;
