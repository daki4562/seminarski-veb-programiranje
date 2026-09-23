/**
 * Validacija forme za prijavu
 */
function validacijaPrijave() {
    ukloniGreske();
    let validno = true;
    
    const korIme = document.getElementById('korisnicko_ime');
    const lozinka = document.getElementById('lozinka');
    
    if (korIme.value.trim() === '') {
        prikaziGresku(korIme, 'Korisničko ime je obavezno polje.');
        validno = false;
    }
    
    if (lozinka.value.trim() === '') {
        prikaziGresku(lozinka, 'Lozinka je obavezna.');
        validno = false;
    }
    
    return validno;
}

/**
 * Validacija master-detail forme za unos i izmenu izveštaja
 */
function validacijaIzvestaja() {
    ukloniGreske();
    let validno = true;
    
    // Master sekcija
    const nazivPreduzeca = document.getElementById('naziv_preduzeca');
    if (nazivPreduzeca && (nazivPreduzeca.value.trim() === '' || nazivPreduzeca.value.trim().length < 3)) {
        prikaziGresku(nazivPreduzeca, 'Naziv preduzeća je obavezan i mora imati bar 3 karaktera.');
        validno = false;
    }

    const adresaPreduzeca = document.getElementById('adresa_preduzeca');
    if (adresaPreduzeca && adresaPreduzeca.value.trim() === '') {
        prikaziGresku(adresaPreduzeca, 'Adresa preduzeća je obavezna.');
        validno = false;
    }

    const telefon = document.getElementById('telefon');
    const telefonRegex = /^[0-9+\-\s]+$/;
    if (telefon && (telefon.value.trim() === '' || !telefonRegex.test(telefon.value))) {
        prikaziGresku(telefon, 'Telefon je obavezan (dozvoljene su cifre, +, - i razmak).');
        validno = false;
    }

    const email = document.getElementById('email');
    if (email && (email.value.trim() === '' || !email.value.includes('@'))) {
        prikaziGresku(email, 'Unesite ispravnu email adresu (mora sadržati @).');
        validno = false;
    }

    const pib = document.getElementById('pib');
    const pibRegex = /^\d{9}$/;
    if (pib && (pib.value.trim() === '' || !pibRegex.test(pib.value))) {
        prikaziGresku(pib, 'PIB je obavezan i mora sadržati tačno 9 cifara.');
        validno = false;
    }

    const brojIzvestaja = document.getElementById('broj_izvestaja');
    if (brojIzvestaja && brojIzvestaja.value.trim() === '') {
        prikaziGresku(brojIzvestaja, 'Broj izveštaja je obavezan.');
        validno = false;
    }

    const datumIzvestaja = document.getElementById('datum_izvestaja');
    if (datumIzvestaja && datumIzvestaja.value.trim() === '') {
        prikaziGresku(datumIzvestaja, 'Datum izveštaja je obavezan.');
        validno = false;
    }

    const brojRadnogNaloga = document.getElementById('broj_radnog_naloga');
    if (brojRadnogNaloga && brojRadnogNaloga.value.trim() === '') {
        prikaziGresku(brojRadnogNaloga, 'Broj radnog naloga je obavezan.');
        validno = false;
    }
    
    const datumRadnogNaloga = document.getElementById('datum_radnog_naloga');
    if (datumRadnogNaloga && datumRadnogNaloga.value.trim() === '') {
        prikaziGresku(datumRadnogNaloga, 'Datum radnog naloga je obavezan.');
        validno = false;
    }

    const adresaStana = document.getElementById('adresa_stana');
    if (adresaStana && adresaStana.value.trim() === '') {
        prikaziGresku(adresaStana, 'Adresa stana je obavezna.');
        validno = false;
    }

    const opisKvara = document.getElementById('opis_kvara');
    if (opisKvara && (opisKvara.value.trim() === '' || opisKvara.value.trim().length < 10)) {
        prikaziGresku(opisKvara, 'Opis kvara je obavezan i mora imati bar 10 karaktera.');
        validno = false;
    }

    const majstorId = document.getElementById('majstor_id');
    if (majstorId && majstorId.value.trim() === '') {
        prikaziGresku(majstorId, 'Morate izabrati majstora.');
        validno = false;
    }

    // Detail sekcija - Intervencije
    const intervencijeRedovi = document.querySelectorAll('#intervencije_tabela tbody tr');
    if (intervencijeRedovi.length === 0) {
        // Dodavanje globalne greske za tabelu
        const tabela = document.getElementById('intervencije_tabela');
        const greskaSpan = document.createElement('span');
        greskaSpan.className = 'greska';
        greskaSpan.innerText = 'Morate uneti bar jednu intervenciju.';
        tabela.parentNode.insertBefore(greskaSpan, tabela.nextSibling);
        validno = false;
    } else {
        // Provera svake radne operacije
        const radneOperacije = document.getElementsByName('radna_operacija[]');
        for (let i = 0; i < radneOperacije.length; i++) {
            if (radneOperacije[i].value.trim() === '') {
                prikaziGresku(radneOperacije[i], 'Radna operacija je obavezna.');
                validno = false;
            }
        }
    }
    
    return validno;
}

/**
 * Dinamičko dodavanje reda za intervenciju
 */
function dodajIntervenciju() {
    const tbody = document.querySelector('#intervencije_tabela tbody');
    if (!tbody) return;
    
    const brojRedova = tbody.children.length;
    const noviRb = brojRedova + 1;
    
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td class="rb">${noviRb}</td>
        <td>
            <input type="text" name="radna_operacija[]" class="forma-kontrola" placeholder="Unesite radnu operaciju">
        </td>
        <td>
            <input type="text" name="potroseni_materijal[]" class="forma-kontrola" placeholder="Unesite potrošeni materijal (opciono)">
        </td>
        <td>
            <button type="button" class="btn btn-crveno" onclick="ukloniIntervenciju(this)">Ukloni</button>
        </td>
    `;
    
    tbody.appendChild(tr);
    azurirajRedneBrojeve();
}

/**
 * Uklanjanje reda za intervenciju
 */
function ukloniIntervenciju(dugme) {
    const tr = dugme.closest('tr');
    tr.remove();
    azurirajRedneBrojeve();
}

/**
 * Ažuriranje rednih brojeva nakon dodavanja/brisanja
 */
function azurirajRedneBrojeve() {
    const celijeRb = document.querySelectorAll('#intervencije_tabela tbody .rb');
    celijeRb.forEach((celija, index) => {
        celija.innerText = index + 1;
    });
}

/**
 * Pomoćna funkcija za prikaz greške ispod polja
 */
function prikaziGresku(element, poruka) {
    element.classList.add('polje-greska');
    
    const greskaSpan = document.createElement('span');
    greskaSpan.className = 'greska';
    greskaSpan.innerText = poruka;
    
    // Dodavanje poruke nakon input polja
    element.parentNode.insertBefore(greskaSpan, element.nextSibling);
}

/**
 * Pomoćna funkcija za uklanjanje svih poruka o greškama
 */
function ukloniGreske() {
    // Uklanjanje klasa sa polja
    const poljaSaGreskom = document.querySelectorAll('.polje-greska');
    poljaSaGreskom.forEach(polje => polje.classList.remove('polje-greska'));
    
    // Uklanjanje span elemenata sa porukama
    const greskeSpans = document.querySelectorAll('span.greska');
    greskeSpans.forEach(span => span.remove());
}
