# Person 2 (Server Logic & Feedback) — Search Results Bundle

Place these files in your repo under `public_html/`:

- `_db.php` — mysqli DB connection (edit creds as needed)
- `search_result_1.php` — Listings by a specific user (expects `?user_id=`)
- `search_result_2.php` — Counts listings per category
- `search_result_3.php` — Shows user favorites with listing titles

## How to test
1) Serve locally:
   ```bash
   php -S 127.0.0.1:8000 -t public_html
   ```
2) Open the existing forms and submit:
   - `search_form_1.php` → hits `search_result_1.php`
   - `search_form_2.php` → hits `search_result_2.php`
   - `search_form_3.php` → hits `search_result_3.php`

Make sure your database is running and populated; update `_db.php` to match your credentials.
