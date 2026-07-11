-- Compatibility migration for databases created before the status column existed.
-- Run through scripts/migrate.php only after review/backup.

SET @column_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'textos'
    AND COLUMN_NAME = 'status'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `textos` ADD COLUMN `status` varchar(20) DEFAULT ''publicado''',
  'SELECT ''textos.status already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
