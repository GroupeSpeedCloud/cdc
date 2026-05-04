-- Migration 002 : suppression des colonnes Dolibarr
-- À exécuter une seule fois après la mise en prod du refactoring

ALTER TABLE tiers    DROP COLUMN IF EXISTS dolibarr_id;
ALTER TABLE invoices DROP COLUMN IF EXISTS dolibarr_id;
ALTER TABLE payments DROP COLUMN IF EXISTS dolibarr_id;
ALTER TABLE products DROP COLUMN IF EXISTS dolibarr_id;
DROP TABLE  IF EXISTS sync_logs;
