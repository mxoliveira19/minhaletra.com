-- Add cached joinha totals for fast public rendering.
-- The storage/joinhas.json file remains the append-only click log.

SET @column_exists := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'textos'
    AND COLUMN_NAME = 'joinhas_count'
);

SET @sql := IF(
  @column_exists = 0,
  'ALTER TABLE `textos` ADD COLUMN `joinhas_count` int(10) unsigned NOT NULL DEFAULT 0 AFTER `status`',
  'SELECT ''textos.joinhas_count already exists'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
