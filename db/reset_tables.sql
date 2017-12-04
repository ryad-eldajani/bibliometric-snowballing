DELETE FROM `work_author`;
DELETE FROM `work_journal`;
DELETE FROM `work_project`;
DELETE FROM `author`;
DELETE FROM `miscellany`;
DELETE FROM `journal`;
DELETE FROM `quote`;
DELETE FROM `work`;
DELETE FROM `project`;

ALTER TABLE `author` AUTO_INCREMENT = 1;
ALTER TABLE `journal` AUTO_INCREMENT = 1;
ALTER TABLE `project` AUTO_INCREMENT = 1;
ALTER TABLE `work` AUTO_INCREMENT = 1;
