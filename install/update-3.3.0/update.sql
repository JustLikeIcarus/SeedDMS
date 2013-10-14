START TRANSACTION;

CREATE TABLE `tblUserPasswordRequest` (
  `id` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `hash` varchar(50) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE tblVersion set major=3, minor=3, subminor=0;

ALTER TABLE tblDocumentContent MODIFY mimeType varchar(100);

ALTER TABLE tblDocumentFiles MODIFY mimeType varchar(100);

ALTER TABLE tblFolders ADD COLUMN `folderList` text NOT NULL;

COMMIT;
