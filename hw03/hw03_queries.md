# CUB Sales — HW3 Queries

This document includes three SQL queries designed to analyze and search data in the CUB Sales database.  
They demonstrate aggregation, filtering, and join operations using realistic marketplace data.

---

## Q2 — Average Selling Price per Category
**Goal:**  
Display the average price of listings in each category.  
Only categories with an average price greater than 30 EUR are included.

**SQL Query:**
```sql
SELECT 
    c.name AS category_name,
    ROUND(AVG(l.price_decimal), 2) AS avg_price
FROM category c
JOIN listing_category lc ON c.category_id = lc.category_id
JOIN listing l ON lc.listing_id = l.listing_id
GROUP BY c.category_id, c.name
HAVING AVG(l.price_decimal) > 30
ORDER BY avg_price DESC;
```
---

## Q6 — Keyword Search with Categories and Poster
**Goal:**  
Find listings whose title or description contains a specific keyword (for example, “desk”),  
and display the listing’s category and the poster’s display name.

**SQL Query:**
```sql
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
```
*(If a FULLTEXT index is added on title/description, replace the WHERE clause with MATCH … AGAINST for improved performance.)*
---

## Q8 — Hottest Categories This Week
**Goal:**  
Identify which categories received the most favorites in the last 7 days.

**SQL Query:**
```sql
SELECT 
    c.name AS category_name,
    COUNT(f.listing_id) AS favorites_this_week
FROM favorite f
JOIN listing_category lc ON f.listing_id = lc.listing_id
JOIN category c ON lc.category_id = c.category_id
WHERE f.created_at >= NOW() - INTERVAL 7 DAY
GROUP BY c.category_id, c.name
ORDER BY favorites_this_week DESC;
```
These queries help analyze user engagement and listing trends within the platform.
