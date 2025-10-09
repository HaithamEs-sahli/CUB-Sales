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

INSERT INTO user (netid, email, display_name) VALUES
  ('fatya','ffares@constructor.university','Fatya Fares'),
  ('yassin','yassin@constructor.university','Yassin Soliman'),
  ('alice','alice@constructor.university','Alice'),
  ('bob','bob@constructor.university','Bob'),
  ('lina','lina@constructor.university','Lina'),
  ('mark','mark@constructor.university','Mark'),
  ('sara','sara@constructor.university','Sara'),
  ('admin','admin@constructor.university','Admin');

INSERT INTO student_user (user_id, class_year, major)
SELECT user_id, 2026, 'CS' FROM user WHERE netid IN ('fatya','yassin','alice','lina','mark','sara');

INSERT INTO admin_user (user_id, role_note)
SELECT user_id, 'Global moderator' FROM user WHERE netid='admin';

INSERT INTO category (name, description) VALUES
  ('Books','Books and textbooks'),
  ('Furniture','Desks, chairs, shelves'),
  ('Electronics','Laptops, phones, accessories'),
  ('Apartment','Whole flats'),
  ('SharedFlat','Rooms in shared flats'),
  ('Sublet','Short-term sublets'),
  ('Bike','Bicycles'),
  ('Computer','PC parts and accessories');

INSERT INTO listing (poster_id, title, description, price_decimal, location, status, created_at)
SELECT u.user_id, t.title, t.description, t.price, t.location, 'active', t.created_at
FROM user u
JOIN (
  SELECT 'yassin' AS netid,'IKEA Desk 120x60' AS title,'Sturdy desk' AS description,40.00 AS price,'Mercator' AS location,NOW() - INTERVAL 3 DAY AS created_at
  UNION ALL SELECT 'yassin','Ikea desk 120x60cm','Same model, slight wear',42.00,'Mercator',DATE_ADD(NOW() - INTERVAL 3 DAY, INTERVAL 1 HOUR)
  UNION ALL SELECT 'yassin','2-room flat','Near tram',650.00,'Bremen-Nord',NOW() - INTERVAL 15 DAY
  UNION ALL SELECT 'yassin','Monitor 24"','1080p',65.00,'Campus',NOW() - INTERVAL 20 DAY
  UNION ALL SELECT 'yassin','Headphones Sony','Noise cancelling',55.00,'Campus',NOW() - INTERVAL 12 DAY
  UNION ALL SELECT 'fatya','Calculus Book','Calculus: Early Transcendentals',15.00,'Campus North',NOW() - INTERVAL 10 DAY
  UNION ALL SELECT 'fatya','Lamp','Desk lamp',12.00,'Campus North',NOW() - INTERVAL 8 DAY
  UNION ALL SELECT 'fatya','Room in shared flat','Utilities included',420.00,'Vegesack',NOW() - INTERVAL 5 DAY
  UNION ALL SELECT 'alice','Bike Trek 2018','Good condition',150.00,'Campus',NOW() - INTERVAL 28 DAY
  UNION ALL SELECT 'alice','Arduino Kit','Starter kit',30.00,'Campus',NOW() - INTERVAL 9 DAY
  UNION ALL SELECT 'alice','Sublet 3 months','Close to campus',500.00,'Bremen-Nord',NOW() - INTERVAL 18 DAY
  UNION ALL SELECT 'bob','Bookshelf','Tall 5-shelf',25.00,'Off-campus',NOW() - INTERVAL 23 DAY
  UNION ALL SELECT 'bob','Chair','Ergonomic',35.00,'Off-campus',NOW() - INTERVAL 6 DAY
  UNION ALL SELECT 'bob','Apartment near tram','1-room, furnished',700.00,'Bremen',NOW() - INTERVAL 19 DAY
  UNION ALL SELECT 'lina','MacBook Air 2019','8GB/256GB',350.00,'Campus',NOW() - INTERVAL 4 DAY
  UNION ALL SELECT 'lina','Printer HP','LaserJet',60.00,'Campus',NOW() - INTERVAL 14 DAY
  UNION ALL SELECT 'lina','Studio near campus','Compact studio',560.00,'Campus',NOW() - INTERVAL 26 DAY
  UNION ALL SELECT 'mark','External HDD 1TB','USB 3.0',35.00,'Campus',NOW() - INTERVAL 2 DAY
  UNION ALL SELECT 'mark','Graphics Card GTX 1060','6GB',120.00,'Campus',NOW() - INTERVAL 40 DAY
  UNION ALL SELECT 'mark','SharedFlat Room Sunny','Great roommates',400.00,'Bremen',NOW() - INTERVAL 3 DAY
  UNION ALL SELECT 'sara','Keyboard Mechanical','Blue switches',45.00,'Campus',NOW() - INTERVAL 7 DAY
  UNION ALL SELECT 'sara','Apartment 1-room','Central',720.00,'Bremen Mitte',NOW() - INTERVAL 11 DAY
  UNION ALL SELECT 'sara','Coffee Maker','Drip',18.00,'Campus',NOW() - INTERVAL 1 DAY
) AS t ON t.netid=u.netid;

INSERT INTO sales_listing (listing_id, condition_note, warranty_info)
SELECT listing_id, 'used', NULL FROM listing
WHERE title IN ('IKEA Desk 120x60','Ikea desk 120x60cm','Monitor 24"','Headphones Sony','Calculus Book','Lamp',
                'Bike Trek 2018','Arduino Kit','Bookshelf','Chair','MacBook Air 2019','Printer HP',
                'External HDD 1TB','Graphics Card GTX 1060','Keyboard Mechanical','Coffee Maker');

INSERT INTO housing_listing (listing_id, rooms, monthly_rent, warm, available_from, lease_term_months)
SELECT listing_id,
       CASE title
         WHEN '2-room flat' THEN 2.0
         WHEN 'Room in shared flat' THEN 1.0
         WHEN 'Sublet 3 months' THEN 1.0
         WHEN 'Apartment near tram' THEN 1.0
         WHEN 'Studio near campus' THEN 1.0
         WHEN 'SharedFlat Room Sunny' THEN 1.0
         WHEN 'Apartment 1-room' THEN 1.0
       END AS rooms,
       price_decimal AS monthly_rent, TRUE, CURRENT_DATE + INTERVAL 14 DAY,
       CASE title WHEN 'Sublet 3 months' THEN 3 ELSE 12 END AS lease_term_months
FROM listing
WHERE title IN ('2-room flat','Room in shared flat','Sublet 3 months','Apartment near tram',
                'Studio near campus','SharedFlat Room Sunny','Apartment 1-room');

INSERT INTO listing_category (listing_id, category_id)
SELECT l.listing_id, c.category_id
FROM listing l
JOIN category c
  ON ( (l.title IN ('IKEA Desk 120x60','Ikea desk 120x60cm','Bookshelf','Chair','Lamp') AND c.name='Furniture')
    OR (l.title IN ('Monitor 24"','Headphones Sony','MacBook Air 2019','Printer HP','External HDD 1TB',
                    'Graphics Card GTX 1060','Keyboard Mechanical','Arduino Kit') AND c.name='Electronics')
    OR (l.title IN ('Calculus Book') AND c.name='Books')
    OR (l.title IN ('Bike Trek 2018') AND c.name='Bike')
    OR (l.title IN ('2-room flat') AND c.name='Apartment')
    OR (l.title IN ('Apartment near tram','Apartment 1-room') AND c.name='Apartment')
    OR (l.title IN ('Room in shared flat','SharedFlat Room Sunny') AND c.name='SharedFlat')
    OR (l.title IN ('Sublet 3 months','Studio near campus') AND c.name='Sublet') );

INSERT INTO favorite (user_id, listing_id, created_at)
SELECT u.user_id, l.listing_id,
       CASE l.title
         WHEN 'IKEA Desk 120x60' THEN NOW() - INTERVAL 2 DAY
         WHEN 'Ikea desk 120x60cm' THEN NOW() - INTERVAL 2 DAY
         WHEN '2-room flat' THEN NOW() - INTERVAL 6 DAY
         WHEN 'Room in shared flat' THEN NOW() - INTERVAL 3 DAY
         WHEN 'MacBook Air 2019' THEN NOW() - INTERVAL 1 DAY
         WHEN 'Monitor 24"' THEN NOW() - INTERVAL 20 DAY
         WHEN 'Headphones Sony' THEN NOW() - INTERVAL 29 DAY
         WHEN 'Bike Trek 2018' THEN NOW() - INTERVAL 35 DAY
         WHEN 'Sublet 3 months' THEN NOW() - INTERVAL 10 DAY
         WHEN 'Apartment 1-room' THEN NOW() - INTERVAL 9 DAY
         WHEN 'Bookshelf' THEN NOW() - INTERVAL 13 DAY
         WHEN 'Chair' THEN NOW() - INTERVAL 4 DAY
         WHEN 'External HDD 1TB' THEN NOW() - INTERVAL 2 DAY
         WHEN 'Keyboard Mechanical' THEN NOW() - INTERVAL 7 DAY
         WHEN 'Coffee Maker' THEN NOW() - INTERVAL 1 DAY
       END AS fav_time
FROM user u
JOIN listing l ON l.title IN ('IKEA Desk 120x60','Ikea desk 120x60cm','2-room flat','Room in shared flat',
                              'MacBook Air 2019','Monitor 24"','Headphones Sony','Bike Trek 2018',
                              'Sublet 3 months','Apartment 1-room','Bookshelf','Chair','External HDD 1TB',
                              'Keyboard Mechanical','Coffee Maker')
WHERE u.netid IN ('fatya','alice','bob','lina','mark','sara');

INSERT INTO favorite (user_id, listing_id, created_at)
SELECT (SELECT user_id FROM user WHERE netid='fatya'),
       (SELECT listing_id FROM listing WHERE title='MacBook Air 2019' LIMIT 1),
       NOW() - INTERVAL 2 DAY;
