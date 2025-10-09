-- CUB Sales — HW3 Queries (All 9)
-- Team: Haitham (A), Yassin (B), Fatima (C)

USE cubsales;

-- =========================================================
-- PERSON A QUERIES (Haitham)
-- =========================================================

-- =========================================================
-- Q3: Affordable apartments in Bremen (housing + join)
-- =========================================================
SET @max_rent := COALESCE(@max_rent, 600.00);

SELECT
  h.listing_id,
  l.title,
  l.location,
  h.rooms,
  h.monthly_rent,
  h.warm,
  l.created_at
FROM housing_listing AS h
JOIN listing AS l
  ON l.listing_id = h.listing_id
WHERE l.status = 'active'
  AND l.location LIKE '%Bremen%'
  AND h.monthly_rent <= @max_rent
ORDER BY h.monthly_rent ASC, l.created_at DESC;

-- =========================================================
-- Q4: Users with BOTH housing & sales posts
-- =========================================================
SELECT
  u.user_id,
  u.display_name,
  SUM(h.listing_id IS NOT NULL) AS housing_posts,
  SUM(s.listing_id IS NOT NULL) AS sales_posts
FROM user AS u
JOIN listing AS l
  ON l.poster_id = u.user_id
LEFT JOIN housing_listing AS h
  ON h.listing_id = l.listing_id
LEFT JOIN sales_listing AS s
  ON s.listing_id = l.listing_id
GROUP BY u.user_id, u.display_name
HAVING housing_posts > 0 AND sales_posts > 0
ORDER BY (housing_posts + sales_posts) DESC, u.display_name;

-- =========================================================
-- Q9: Favorites-per-user vs. posts (CTE / aggregation)
-- =========================================================
WITH favs AS (
  SELECT f.user_id, COUNT(*) AS fav_count
  FROM favorite AS f
  GROUP BY f.user_id
),
posts AS (
  SELECT l.poster_id AS user_id, COUNT(*) AS post_count
  FROM listing AS l
  GROUP BY l.poster_id
)
SELECT
  u.user_id,
  u.display_name,
  COALESCE(f.fav_count, 0)  AS favorites_made,
  COALESCE(p.post_count, 0) AS posts_created,
  ROUND(
    COALESCE(f.fav_count, 0) / NULLIF(COALESCE(p.post_count, 0), 0),
    2
  ) AS favs_per_post
FROM user AS u
LEFT JOIN favs AS f USING (user_id)
LEFT JOIN posts AS p USING (user_id)
ORDER BY favs_per_post DESC, favorites_made DESC, u.display_name;


-- =========================================================
-- PERSON B QUERIES (Yassin)
-- =========================================================

-- =========================================================
-- Q2: Average selling price per category (aggregation + HAVING)
-- =========================================================
SELECT 
    c.name AS category_name,
    ROUND(AVG(l.price_decimal), 2) AS avg_price
FROM category c
JOIN listing_category lc ON c.category_id = lc.category_id
JOIN listing l ON lc.listing_id = l.listing_id
GROUP BY c.category_id, c.name
HAVING AVG(l.price_decimal) > 30
ORDER BY avg_price DESC;

-- =========================================================
-- Q6: Keyword search with categories and poster
-- =========================================================
SELECT 
    l.listing_id,
    l.title,
    l.description,
    c.name AS category_name,
    u.display_name AS poster
FROM listing l
JOIN listing_category lc ON l.listing_id = lc.listing_id
JOIN category c ON lc.category_id = c.category_id
JOIN user u ON l.poster_id = u.user_id
WHERE l.title LIKE '%desk%' OR l.description LIKE '%desk%'
ORDER BY l.created_at DESC;

-- =========================================================
-- Q8: Hottest categories this week (aggregation + date window)
-- =========================================================
SELECT 
    c.name AS category_name,
    COUNT(f.listing_id) AS favorites_this_week
FROM favorite f
JOIN listing_category lc ON f.listing_id = lc.listing_id
JOIN category c ON lc.category_id = c.category_id
WHERE f.created_at >= NOW() - INTERVAL 7 DAY
GROUP BY c.category_id, c.name
ORDER BY favorites_this_week DESC;


-- =========================================================
-- PERSON C QUERIES (Fatima)
-- =========================================================

-- =========================================================
-- Q1: Top 5 most-favorited listings in the last 30 days
-- =========================================================
WITH recent_fav AS (
  SELECT listing_id
  FROM favorite
  WHERE created_at >= NOW() - INTERVAL 30 DAY
)
SELECT
  l.listing_id,
  l.title,
  u.display_name AS poster,
  COUNT(*) AS fav_count
FROM listing AS l
JOIN user AS u ON u.user_id = l.poster_id
JOIN recent_fav AS r ON r.listing_id = l.listing_id
GROUP BY l.listing_id, l.title, u.display_name
ORDER BY fav_count DESC, l.created_at DESC
LIMIT 5;

-- =========================================================
-- Q5: Per-user activity (count + latest post date + total favorites)
-- =========================================================
SELECT
  u.user_id,
  u.display_name,
  COUNT(l.listing_id) AS listings_count,
  MAX(l.created_at) AS latest_post_at,
  COALESCE(f.total_favs_30d, 0) AS total_favorites_last_30d
FROM user AS u
LEFT JOIN listing AS l ON l.poster_id = u.user_id
LEFT JOIN (
  SELECT l.poster_id, COUNT(*) AS total_favs_30d
  FROM favorite AS f
  JOIN listing AS l ON l.listing_id = f.listing_id
  WHERE f.created_at >= NOW() - INTERVAL 30 DAY
  GROUP BY l.poster_id
) AS f ON f.poster_id = u.user_id
GROUP BY u.user_id, u.display_name
ORDER BY listings_count DESC, latest_post_at DESC;

-- =========================================================
-- Q7: Potential duplicate listings within 24 hours
-- =========================================================
SELECT
  u.display_name AS poster,
  l1.listing_id AS listing_id_1,
  l2.listing_id AS listing_id_2,
  l1.title AS title_1,
  l2.title AS title_2,
  l1.price_decimal AS price_1,
  l2.price_decimal AS price_2,
  l1.created_at AS created_at_1,
  l2.created_at AS created_at_2
FROM listing AS l1
JOIN listing AS l2
  ON l1.poster_id = l2.poster_id
 AND l1.listing_id < l2.listing_id
JOIN user AS u ON u.user_id = l1.poster_id
WHERE REGEXP_REPLACE(LOWER(l1.title), '[^a-z0-9]+', '') =
      REGEXP_REPLACE(LOWER(l2.title), '[^a-z0-9]+', '')
  AND ABS(TIMESTAMPDIFF(HOUR, l1.created_at, l2.created_at)) <= 24
  AND (
       (l1.price_decimal IS NOT NULL AND l2.price_decimal IS NOT NULL
        AND ABS(l1.price_decimal - l2.price_decimal) <= 5.00)
       OR (l1.price_decimal IS NULL AND l2.price_decimal IS NULL)
  )
ORDER BY poster, created_at_1;
