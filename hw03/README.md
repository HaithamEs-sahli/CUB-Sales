# CUB Sales — Databases Project (HW3)

Repo for schema, queries, and documentation.

---

## Repo Layout
- sql/ → schema, sample data, and queries  
- docs/ → query documentation (hw03_queries.md)  
- mapping/ → ER diagram and mapping report  
- logs/ → MySQL execution logs (`hw03_session.log`)

---

## How to Run HW3

Run the following steps in MySQL (for example, on CLAMV):

```bash
mysql -u <netid> -p
tee logs/hw03_session.log

DROP DATABASE IF EXISTS cubsales;
CREATE DATABASE cubsales;
USE cubsales;

SOURCE sql/schema.sql;
SOURCE sql/indexes_hw03.sql;
SOURCE sql/sample_data_hw03.sql;
SOURCE sql/queries_hw03.sql;

notee
```

---

## Output Validation
- Verify that all queries in `sql/queries_hw03.sql` execute successfully.  
- Ensure non-empty, meaningful results for:
  - Average price per category  
  - Keyword search results  
  - Weekly category favorites  

The log file `logs/hw03_session.log` will be used for submission and validation.
