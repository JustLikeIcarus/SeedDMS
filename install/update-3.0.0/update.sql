START TRANSACTION;

ALTER TABLE tblUsers ADD COLUMN `role` smallint(1) NOT NULL default '0' AFTER `isAdmin`;

UPDATE tblUsers SET `role` = 1 WHERE `isAdmin` = 1;

UPDATE tblUsers SET `role` = 2 WHERE `id` = 2;

ALTER TABLE tblUsers DROP COLUMN isAdmin;

ALTER TABLE tblFolders ADD COLUMN `date` int(12) default NULL AFTER `comment`;

CREATE TABLE `tblVersion` (
	`date` datetime,
	`major` smallint,
	`minor` smallint,
	`subminor` smallint
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO tblVersion VALUES (NOW(), 3, 0, 0);

COMMIT;
