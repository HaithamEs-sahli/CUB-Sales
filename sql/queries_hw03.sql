-- CUB Sales — HW3 Queries (Person A)
-- (Q3, Q4, Q9)

USE cubsales;

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
