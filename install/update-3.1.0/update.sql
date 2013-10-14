START TRANSACTION;

CREATE TABLE `tblCategory` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO tblCategory VALUES (0, '');

CREATE TABLE `tblDocumentCategory` (
  `categoryID` int(11) NOT NULL default 0,
  `documentID` int(11) NOT NULL default 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE tblVersion set major=3, minor=1, subminor=0;

COMMIT;
