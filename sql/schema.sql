-- CUB Sales — Assignment 3 schema
-- Re-runnable on CLAMV

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop in reverse dependency order
DROP TABLE IF EXISTS email_contact;
DROP TABLE IF EXISTS phone_contact;
DROP TABLE IF EXISTS contact;
DROP TABLE IF EXISTS favorite;
DROP TABLE IF EXISTS listing_category;
DROP TABLE IF EXISTS sales_listing;
DROP TABLE IF EXISTS housing_listing;
DROP TABLE IF EXISTS listing;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS admin_user;
DROP TABLE IF EXISTS student_user;
DROP TABLE IF EXISTS user;

SET FOREIGN_KEY_CHECKS = 1;


CREATE TABLE user (
  user_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  netid         VARCHAR(64)  NOT NULL UNIQUE,     -- campus login
  email         VARCHAR(255) NOT NULL UNIQUE,
  display_name  VARCHAR(100) NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admin_user (
  user_id   BIGINT UNSIGNED PRIMARY KEY,
  role_note VARCHAR(255) NULL,
  CONSTRAINT fk_admin_user_user
    FOREIGN KEY (user_id) REFERENCES user(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE student_user (
  user_id     BIGINT UNSIGNED PRIMARY KEY,
  class_year  SMALLINT      NULL,
  major       VARCHAR(80)   NULL,
  CONSTRAINT ck_student_year CHECK (class_year IS NULL OR class_year BETWEEN 2000 AND 2100),
  CONSTRAINT fk_student_user_user
    FOREIGN KEY (user_id) REFERENCES user(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE listing (
  listing_id     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  poster_id      BIGINT UNSIGNED NOT NULL,  -- FK → user
  title          VARCHAR(120) NOT NULL,
  description    TEXT         NOT NULL,
  price_decimal  DECIMAL(10,2) NULL,
  location       VARCHAR(120) NOT NULL,
  status         ENUM('active','reserved','sold','removed') NOT NULL DEFAULT 'active',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT ck_price_nonneg CHECK (price_decimal IS NULL OR price_decimal >= 0),
  CONSTRAINT fk_listing_user
    FOREIGN KEY (poster_id) REFERENCES user(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  KEY idx_listing_poster (poster_id),
  KEY idx_listing_status_created (status, created_at),
  KEY idx_listing_price (price_decimal),
  KEY idx_listing_location (location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE sales_listing (
  listing_id     BIGINT UNSIGNED PRIMARY KEY,  
  condition_note ENUM('new','like_new','good','used','for_parts') NOT NULL DEFAULT 'used',
  warranty_info  VARCHAR(255) NULL,
  CONSTRAINT fk_sales_listing_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE housing_listing (
  listing_id        BIGINT UNSIGNED PRIMARY KEY,  
  rooms             DECIMAL(3,1)  NOT NULL,      
  monthly_rent      DECIMAL(10,2) NOT NULL,
  warm              BOOLEAN       NOT NULL DEFAULT TRUE, 
  available_from    DATE          NULL,
  lease_term_months SMALLINT      NULL,
  CONSTRAINT ck_rooms_nonneg CHECK (rooms >= 0),
  CONSTRAINT ck_rent_pos   CHECK (monthly_rent > 0),
  CONSTRAINT fk_housing_listing_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_housing_rent (monthly_rent)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE category (
  category_id  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(80)  NOT NULL UNIQUE,
  description  VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE listing_category (
  listing_id  BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (listing_id, category_id),
  CONSTRAINT fk_lc_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_lc_category
    FOREIGN KEY (category_id) REFERENCES category(category_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  KEY idx_lc_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE favorite (
  user_id    BIGINT UNSIGNED NOT NULL,
  listing_id BIGINT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, listing_id),
  CONSTRAINT fk_fav_user
    FOREIGN KEY (user_id) REFERENCES user(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_fav_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  KEY idx_fav_listing (listing_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE contact (
  contact_id   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  listing_id   BIGINT UNSIGNED NOT NULL,
  from_user_id BIGINT UNSIGNED NOT NULL,   
  message      TEXT NULL,
  method       ENUM('email','phone') NOT NULL,
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_contact_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_contact_user
    FOREIGN KEY (from_user_id) REFERENCES user(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  KEY idx_contact_listing (listing_id),
  KEY idx_contact_from_user (from_user_id),
  KEY idx_contact_method (method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE email_contact (
  contact_id BIGINT UNSIGNED PRIMARY KEY,
  email      VARCHAR(255) NOT NULL,
  CONSTRAINT fk_email_contact_contact
    FOREIGN KEY (contact_id) REFERENCES contact(contact_id)
    ON DELETE CASCADE ON UPDATE CASCADE
 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE phone_contact (
  contact_id BIGINT UNSIGNED PRIMARY KEY,
  phone      VARCHAR(32) NOT NULL,
  CONSTRAINT fk_phone_contact_contact
    FOREIGN KEY (contact_id) REFERENCES contact(contact_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;