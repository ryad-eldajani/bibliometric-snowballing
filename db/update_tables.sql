# This SQL file updates the Bibliometric Snowballing relations when created
# before 2017-Dec-27.

ALTER TABLE `work_project` ADD `created_at` DATETIME DEFAULT NOW();

DELETE FROM `quote`;
ALTER TABLE `quote` DROP FOREIGN KEY `fk_quote_id_work_quoted`;
ALTER TABLE `quote` DROP FOREIGN KEY `fk_quote_id_work`;
ALTER TABLE `quote` DROP PRIMARY KEY;
ALTER TABLE `quote` ADD `doi_work` VARCHAR(255) NOT NULL;
ALTER TABLE `quote` ADD `doi_work_quoted` VARCHAR(255) NOT NULL;
ALTER TABLE `quote` DROP COLUMN `id_work`;
ALTER TABLE `quote` DROP COLUMN `id_work_quoted`;
ALTER TABLE `quote` ADD PRIMARY KEY (`doi_work`, `doi_work_quoted`);
