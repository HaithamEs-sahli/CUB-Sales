DROP TABLE IF EXISTS user;

CREATE TABLE user (
  user_id       BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  netid         VARCHAR(64)  NOT NULL UNIQUE,     -- campus login
  email         VARCHAR(255) NOT NULL UNIQUE,
  display_name  VARCHAR(100) NOT NULL,
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS admin_user;
DROP TABLE IF EXISTS student_user;

CREATE TABLE admin_user (
  user_id   BIGINT UNSIGNED PRIMARY KEY,
  role_note VARCHAR(255) NULL,
  CONSTRAINT fk_admin_user_user
    FOREIGN KEY (user_id) REFERENCES user(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE student_user (
  user_id     BIGINT UNSIGNED PRIMARY KEY,
  class_year  SMALLINT      NULL,   -- e.g. 2026
  major       VARCHAR(80)   NULL,
  CONSTRAINT fk_student_user_user
    FOREIGN KEY (user_id) REFERENCES user(user_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS listing;

CREATE TABLE listing (
  listing_id     BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  poster_id      BIGINT UNSIGNED NOT NULL,  -- FK → user
  title          VARCHAR(120) NOT NULL,
  description    TEXT         NOT NULL,
  price_decimal  DECIMAL(10,2) NULL,        -- general price (used differently in subclasses)
  location       VARCHAR(120) NOT NULL,
  status         ENUM('active','reserved','sold','removed') NOT NULL DEFAULT 'active',
  created_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_listing_user
    FOREIGN KEY (poster_id) REFERENCES user(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS sales_listing;
DROP TABLE IF EXISTS housing_listing;

CREATE TABLE sales_listing (
  listing_id     BIGINT UNSIGNED PRIMARY KEY,  -- 1:1 with listing
  condition_note ENUM('new','like_new','good','used','for_parts') NOT NULL DEFAULT 'used',
  warranty_info  VARCHAR(255) NULL,
  CONSTRAINT fk_sales_listing_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE housing_listing (
  listing_id        BIGINT UNSIGNED PRIMARY KEY,  -- 1:1 with listing
  rooms             DECIMAL(3,1)  NOT NULL,       -- e.g. 1.0, 2.5
  monthly_rent      DECIMAL(10,2) NOT NULL,
  warm              BOOLEAN       NOT NULL DEFAULT TRUE, -- warm/cold rent
  available_from    DATE          NULL,
  lease_term_months SMALLINT      NULL,
  CONSTRAINT fk_housing_listing_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS category;

CREATE TABLE category (
  category_id  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(80)  NOT NULL UNIQUE,
  description  VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS listing_category;

CREATE TABLE listing_category (
  listing_id  BIGINT UNSIGNED NOT NULL,
  category_id BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (listing_id, category_id),
  CONSTRAINT fk_lc_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_lc_category
    FOREIGN KEY (category_id) REFERENCES category(category_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



DROP TABLE IF EXISTS favorite;

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
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS contact;

CREATE TABLE contact (
  contact_id   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  listing_id   BIGINT UNSIGNED NOT NULL,
  from_user_id BIGINT UNSIGNED NOT NULL,   -- who is contacting
  message      TEXT NULL,
  method       ENUM('email','phone') NOT NULL, -- contact type
  created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_contact_listing
    FOREIGN KEY (listing_id) REFERENCES listing(listing_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_contact_user
    FOREIGN KEY (from_user_id) REFERENCES user(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS email_contact;
DROP TABLE IF EXISTS phone_contact;

CREATE TABLE email_contact (
  contact_id BIGINT UNSIGNED PRIMARY KEY,
  email      VARCHAR(255) NOT NULL,
  CONSTRAINT uq_email_contact UNIQUE (email, contact_id),
  CONSTRAINT fk_email_contact_contact
    FOREIGN KEY (contact_id) REFERENCES contact(contact_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE phone_contact (
  contact_id BIGINT UNSIGNED PRIMARY KEY,
  phone      VARCHAR(32) NOT NULL,
  CONSTRAINT uq_phone_contact UNIQUE (phone, contact_id),
  CONSTRAINT fk_phone_contact_contact
    FOREIGN KEY (contact_id) REFERENCES contact(contact_id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
