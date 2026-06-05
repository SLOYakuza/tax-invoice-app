# Aplikacija za Izdaju Davčnih Računa

Laravel aplikacija za upravljanje i izdaju davčnih računa sa MySQL bazom podataka.

## Funkcionalnosti

- 👤 Upravljanje korisnicima i klijentima
- 📄 Kreiranje i upravljanje računima
- 📊 Pregled i arhiv računa
- 📥 Izvoz u PDF i Excel formate
- 💰 Automatske PDV kalkulacije
- 🔐 Autentifikacija i autorizacija
- 📱 Responzivno korisničko sučelje

## Instalacija

### Preduslovi
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js (za frontend)

### Koraci

1. **Kloniraj repozitorij:**
```bash
git clone https://github.com/SLOYakuza/tax-invoice-app.git
cd tax-invoice-app
```

2. **Instaliraj zavisnosti:**
```bash
composer install
npm install
```

3. **Kreiraj .env datoteku:**
```bash
cp .env.example .env
```

4. **Generiraj aplikacijski ključ:**
```bash
php artisan key:generate
```

5. **Kreiraj MySQL bazu:**
```bash
mysql -u root -p
CREATE DATABASE tax_invoices;
```

6. **Pokreni migracije:**
```bash
php artisan migrate
php artisan db:seed
```

7. **Pokreni aplikaciju:**
```bash
php artisan serve
```

Aplikacija je dostupna na `http://localhost:8000`

## Struktura Projekta

```
tax-invoice-app/
├── app/
│   ├── Models/              # Baza podataka modela
│   ├── Http/
│   │   ├── Controllers/     # Kontroleri
│   │   └── Requests/        # Form zahtjevi
│   └── Services/            # Poslovne logike
├── database/
│   ├── migrations/          # Migracije baze
│   └── seeders/             # Seed datoteke
├── resources/
│   ├── views/               # Blade šabloni
│   └── css/                 # CSS datoteke
├── routes/
│   └── web.php              # Web rute
└── storage/                 # Privremene datoteke i logovi
```

## Korišćenje

### Autentifikacija
- Registruj se ili prijavi sa demo nalogom
- Demo: `demo@example.com` / `password`

### Kreiranje Računa
1. Idi na "Novi Račun"
2. Izaberi klijenta
3. Dodaj stavke sa cijenama
4. PDV se automatski izračunava
5. Generiraj PDF ili preuzmi Excel

## Tehnologije

- **Backend**: Laravel 10
- **Baza**: MySQL
- **Frontend**: Blade, Bootstrap 5
- **PDF**: DomPDF
- **Excel**: Maatwebsite Excel

## Razvojni Timovi

Autor: SLOYakuza

## Licenca

MIT License
