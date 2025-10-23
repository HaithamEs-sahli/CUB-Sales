# Person C bundle – Relationship Input & Reference Lists

## Files
- `_db.php` – mysqli connection (edit credentials as needed)
- `ping_db.php` – quick MySQL connectivity check (shows `DB_OK` if fine)
- `listing_category_new.php` + `insert_listing_category.php` – Listing ↔ Category (M:N)
- `favorite_new.php` + `insert_favorite.php` – User ↔ Listing favorites (M:N)
- `contact_new.php` + `insert_contact.php` – Contact flow (User → Listing) incl. method (email/phone)

## Run locally (PHP built-in server)
```bash
php -S 127.0.0.1:8000 -t public_html
```
Then open:
- http://127.0.0.1:8000/ping_db.php
- http://127.0.0.1:8000/listing_category_new.php
- http://127.0.0.1:8000/favorite_new.php
- http://127.0.0.1:8000/contact_new.php
```

Ensure your MySQL is running and contains your schema; credentials in `_db.php` must match.
