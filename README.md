# CUB Sales — Databases Project

Repo for schema, ER diagrams, and docs.

## Repo Layout
- er/ → ER diagram(s)
- sql/ → SQL schema + test data
- docs/ → mapping notes and PDFs

## How to run on CLAMV (MySQL)
```sql
DROP DATABASE IF EXISTS cubsales;
CREATE DATABASE cubsales;
USE cubsales;

SOURCE sql/schema.sql;
SOURCE sql/tests.sql;

