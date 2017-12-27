SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `author` (
  `id_author` INT NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `journal` (
  `id_journal` INT NOT NULL AUTO_INCREMENT,
  `journal_name` varchar(45) DEFAULT NULL,
  `issn` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_journal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `miscellany` (
  `id_work` INT NOT NULL,
  `id_work_part_of` INT NOT NULL,
  PRIMARY KEY (`id_work`, `id_work_part_of`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `project` (
  `id_project` INT NOT NULL AUTO_INCREMENT,
  `id_user` INT NOT NULL,
  `project_name` varchar(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT NOW(),
  PRIMARY KEY (`id_project`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `work` (
  `id_work` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) DEFAULT NULL,
  `subtitle` VARCHAR(255) DEFAULT NULL,
  `work_year` INT DEFAULT NULL,
  `doi` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_work`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `quote` (
  `id_work` INT NOT NULL,
  `doi_work_quoted` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id_work`, `doi_work_quoted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `work_journal` (
  `id_work` INT NOT NULL,
  `id_journal` INT NOT NULL,
  `publish_volume` INT DEFAULT NULL,
  `publish_number` INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `work_author` (
  `id_author` INT NOT NULL,
  `id_work` INT NOT NULL,
  PRIMARY KEY (`id_author`,`id_work`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `work_project` (
  `id_project` INT NOT NULL,
  `id_work` INT NOT NULL,
  PRIMARY KEY (`id_project`,`id_work`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `miscellany`
  ADD CONSTRAINT `fk_miscellany_id_work_part_of` FOREIGN KEY (`id_work_part_of`) REFERENCES `work` (`id_work`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_miscellany_id_work` FOREIGN KEY (`id_work`) REFERENCES `work` (`id_work`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `work_journal`
  ADD CONSTRAINT `fk_work_journal_id_journal` FOREIGN KEY (`id_journal`) REFERENCES `journal` (`id_journal`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_work_journal_id_work` FOREIGN KEY (`id_work`) REFERENCES `work` (`id_work`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `quote`
  ADD CONSTRAINT `fk_quote_id_work_quoted` FOREIGN KEY (`id_work_quoted`) REFERENCES `work` (`id_work`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_quote_id_work` FOREIGN KEY (`id_work`) REFERENCES `work` (`id_work`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `work_author`
  ADD CONSTRAINT `fk_work_author_id_author` FOREIGN KEY (`id_author`) REFERENCES `author` (`id_author`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_work_author_id_work` FOREIGN KEY (`id_work`) REFERENCES `work` (`id_work`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `work_project`
  ADD CONSTRAINT `fk_work_project_id_project` FOREIGN KEY (`id_project`) REFERENCES `project` (`id_project`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_work_project_id_work` FOREIGN KEY (`id_work`) REFERENCES `work` (`id_work`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;
