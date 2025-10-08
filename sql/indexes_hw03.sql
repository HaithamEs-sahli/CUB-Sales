-- CUB Sales — HW3 Indexes (Person A)
-- Safe to re-run: drops first, then (re)creates.

USE cubsales;

-- ======================
-- Listing / Subclasses
-- ======================

DROP INDEX IF EXISTS idx_listing_poster_status_created ON listing;
CREATE INDEX idx_listing_poster_status_created
  ON listing (poster_id, status, created_at);

DROP INDEX IF EXISTS idx_listing_location_created ON listing;
CREATE INDEX idx_listing_location_created
  ON listing (location, created_at);

DROP INDEX IF EXISTS ft_listing_title_desc ON listing;
CREATE FULLTEXT INDEX ft_listing_title_desc
  ON listing (title, description);

DROP INDEX IF EXISTS idx_housing_rent_rooms ON housing_listing;
CREATE INDEX idx_housing_rent_rooms
  ON housing_listing (monthly_rent, rooms);

-- ======================
-- Categories / Junctions
-- ======================

DROP INDEX IF EXISTS idx_lc_category_listing ON listing_category;
CREATE INDEX idx_lc_category_listing
  ON listing_category (category_id, listing_id);

DROP INDEX IF EXISTS idx_category_name ON category;
CREATE INDEX idx_category_name
  ON category (name);

-- ======================
-- Favorites / Contacts
-- ======================

DROP INDEX IF EXISTS idx_fav_user_time ON favorite;
CREATE INDEX idx_fav_user_time
  ON favorite (user_id, created_at);

DROP INDEX IF EXISTS idx_fav_listing_time ON favorite;
CREATE INDEX idx_fav_listing_time
  ON favorite (listing_id, created_at);

DROP INDEX IF EXISTS idx_contact_listing_time ON contact;
CREATE INDEX idx_contact_listing_time
  ON contact (listing_id, created_at);

DROP INDEX IF EXISTS idx_contact_from_user_time ON contact;
CREATE INDEX idx_contact_from_user_time
  ON contact (from_user_id, created_at);
