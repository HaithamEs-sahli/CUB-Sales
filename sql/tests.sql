SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE email_contact;
TRUNCATE TABLE phone_contact;
TRUNCATE TABLE contact;
TRUNCATE TABLE favorite;
TRUNCATE TABLE listing_category;
TRUNCATE TABLE sales_listing;
TRUNCATE TABLE housing_listing;
TRUNCATE TABLE listing;
TRUNCATE TABLE category;
TRUNCATE TABLE admin_user;
TRUNCATE TABLE student_user;
TRUNCATE TABLE user;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO user (netid, email, display_name)
VALUES
  ('fatya',  'ffares@constructor.university',   'Fatya Fares'),
  ('yassin', 'yassin@constructor.university',   'Yassin Soliman'),
  ('admin',  'admin@constructor.university',    'Admin');

INSERT INTO student_user (user_id, class_year, major)
SELECT user_id, 2026, 'CS' FROM user WHERE netid = 'fatya';

INSERT INTO admin_user (user_id, role_note)
SELECT user_id, 'Global moderator' FROM user WHERE netid = 'admin';

INSERT INTO category (name, description)
VALUES
  ('Books', 'Course books and novels'),
  ('Furniture', 'Desks, chairs, shelves'),
  ('Electronics', 'Laptops, phones, accessories'),
  ('Apartment', 'Whole flats'),
  ('SharedFlat', 'Rooms in shared flats'),
  ('Sublet', 'Short-term sublets');

INSERT INTO listing (poster_id, title, description, price_decimal, location, status)
SELECT user_id, 'Intro to CS (used)', 'Clean copy', 15.00, 'Mercator', 'active'
FROM user WHERE netid = 'yassin';

INSERT INTO listing (poster_id, title, description, price_decimal, location, status)
SELECT user_id, 'IKEA Desk', '120x60 cm', 40.00, 'Off-campus', 'active'
FROM user WHERE netid = 'yassin';

INSERT INTO listing (poster_id, title, description, price_decimal, location, status)
SELECT user_id, '2-room flat', 'Near tram', 650.00, 'Bremen-Nord', 'active'
FROM user WHERE netid = 'yassin';

SET @sale_book_id = (SELECT listing_id FROM listing WHERE title='Intro to CS (used)');
SET @sale_desk_id = (SELECT listing_id FROM listing WHERE title='IKEA Desk');
SET @housing_id   = (SELECT listing_id FROM listing WHERE title='2-room flat');

INSERT INTO sales_listing (listing_id, condition_note, warranty_info)
VALUES
  (@sale_book_id, 'good', NULL),
  (@sale_desk_id, 'used', 'no warranty');

INSERT INTO housing_listing (listing_id, rooms, monthly_rent, warm, available_from, lease_term_months)
VALUES
  (@housing_id, 2.0, 650.00, TRUE, CURRENT_DATE + INTERVAL 14 DAY, 12);

INSERT INTO listing_category (listing_id, category_id)
VALUES
  (@sale_book_id, (SELECT category_id FROM category WHERE name='Books')),
  (@sale_desk_id, (SELECT category_id FROM category WHERE name='Furniture')),
  (@housing_id,   (SELECT category_id FROM category WHERE name='Apartment'));

INSERT INTO favorite (user_id, listing_id)
SELECT u.user_id, @sale_book_id FROM user u WHERE u.netid = 'fatya';
INSERT INTO favorite (user_id, listing_id)
SELECT u.user_id, @housing_id FROM user u WHERE u.netid = 'fatya';

INSERT INTO contact (listing_id, from_user_id, message, method)
VALUES
  (@sale_book_id,
   (SELECT user_id FROM user WHERE netid='fatya'),
   'Hi! Is the book still available?',
   'email');

SET @contact_email_id = LAST_INSERT_ID();

INSERT INTO email_contact (contact_id, email)
VALUES (@contact_email_id, 'ffares@constructor.university');

INSERT INTO contact (listing_id, from_user_id, message, method)
VALUES
  (@sale_desk_id,
   (SELECT user_id FROM user WHERE netid='fatya'),
   'Can I see the desk tomorrow?',
   'phone');

SET @contact_phone_id = LAST_INSERT_ID();

INSERT INTO phone_contact (contact_id, phone)
VALUES (@contact_phone_id, '+49-170-1234567');

INSERT INTO sales_listing (listing_id, condition_note, warranty_info)
VALUES (999999, 'new', 'store warranty');

DELETE FROM user
WHERE netid = 'yassin';

INSERT INTO listing (poster_id, title, description, price_decimal, location, status)
SELECT user_id, 'Bad Status', 'invalid enum', 1.00, 'Nowhere', 'archived'
FROM user WHERE netid='yassin';

INSERT INTO user (netid, email, display_name)
VALUES ('fatya2', 'ffares@constructor.university', 'Fatya Clone');
