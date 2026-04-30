# Raktárkezelő rendszer

## Leírás

PHP alapú raktárkezelő webalkalmazás, amely lehetővé teszi termékek, készletek, raktárak és felhasználók kezelését.

## Fő funkciók

* Termékek kezelése (létrehozás, szerkesztés, törlés)
* Készletkezelés raktáranként és tárolóhelyenként
* Leltározás (nyitás, szerkesztés, lezárás)
* Felhasználó- és jogosultságkezelés
* Beszállítók kezelése
* Képfeltöltés termékekhez
* Naplózás (log rendszer)

## Technológiák

* PHP (PDO)
* MySQL
* HTML / CSS / JavaScript

## Telepítés

1. XAMPP indítása (Apache + MySQL)
2. Projekt másolása:

   ```
   C:\xampp\htdocs\VizsgaPHP\raktarprogram
   ```
3. Adatbázis létrehozása phpMyAdminban
4. SQL import:

   * válaszd ki az aktuális dump fájlt (pl. vrdb-*.sql)
5. `includes/db.php` beállítása:

   ```
   host: localhost
   adatbázis neve
   felhasználó: root
   jelszó: (üres alapból)
   ```

## Bejelentkezés
Mostani admin bejelentkezée:
email: admin@gmail.com
jelszó: 123456

Mostani olvasó bejelentkezés:
email: gabor@gmail.com
jelszó: 123456

Mostani raktáros bejelentkezés:
email: ferenc@gmail.com
jelszó: 123456

Ha nem jó, futtasd SQL-ben:

```
UPDATE felhasznalo
SET jelszo_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.ogU5Q9WcW9W4Hj7K'
WHERE email = 'admin@gmail.com';
```

Belépés:

```
email: admin@gmail.com
jelszó: test123
```

## Fájlszerkezet

```
includes/
  auth.php
  db.php
  helpers.php
  product_images.php
  logger.php

actions/
  create_product.php
  update_product.php
  delete_product.php
  login_action.php

assets/
  images/
  css/
  js/
```

## Megjegyzések

* A képek fájlrendszerben kerülnek tárolásra
* Az adatbázis csak a hivatkozást tárolja
* A törlés POST alapon történik (biztonságos)
* A rendszer jogosultság alapú