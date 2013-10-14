-- This file will be read by the letodms update script
-- place empty lines in between sql commands you would like to
-- be executed separately
-- put -- in front of a line if it is a comment like this line
DROP PROCEDURE IF EXISTS DROPFK;

-- uncomment the following if run in mysql client
-- DELIMITER $$ ;

CREATE PROCEDURE DROPFK (
IN parm_table_name VARCHAR(100),
IN parm_key_name VARCHAR(100)
)
BEGIN
	SET @table_name = parm_table_name;
	SET @key_name = parm_key_name;
	SET @sql_text = concat('ALTER TABLE ',@table_name,' DROP FOREIGN KEY ',@key_name);
	IF EXISTS (SELECT NULL FROM information_schema.TABLE_CONSTRAINTS
		WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = parm_key_name) THEN
		PREPARE stmt FROM @sql_text;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	END IF;
END

-- place a $$ after the last END if run the file in mysql client
-- uncomment the following if run in mysql client
-- DELIMITER ; $$

START TRANSACTION;

ALTER TABLE tblACLs ENGINE=InnoDB;

ALTER TABLE tblCategory ENGINE=InnoDB;

ALTER TABLE tblUsers ENGINE=InnoDB;

ALTER TABLE tblUserPasswordRequest ENGINE=InnoDB;

ALTER TABLE tblUserImages ENGINE=InnoDB;

ALTER TABLE tblFolders ENGINE=InnoDB;

ALTER TABLE tblDocuments ENGINE=InnoDB;

ALTER TABLE tblDocumentApprovers ENGINE=InnoDB;

ALTER TABLE tblDocumentApproveLog ENGINE=InnoDB;

ALTER TABLE tblDocumentContent ENGINE=InnoDB;

ALTER TABLE tblDocumentLinks ENGINE=InnoDB;

ALTER TABLE tblDocumentFiles ENGINE=InnoDB;

ALTER TABLE tblDocumentLocks ENGINE=InnoDB;

ALTER TABLE tblDocumentReviewers ENGINE=InnoDB;

ALTER TABLE tblDocumentReviewLog ENGINE=InnoDB;

ALTER TABLE tblDocumentStatus ENGINE=InnoDB;

ALTER TABLE tblDocumentStatusLog ENGINE=InnoDB;

ALTER TABLE tblGroups ENGINE=InnoDB;

ALTER TABLE tblGroupMembers ENGINE=InnoDB;

ALTER TABLE tblKeywordCategories ENGINE=InnoDB;

ALTER TABLE tblKeywords ENGINE=InnoDB;

ALTER TABLE tblDocumentCategory ENGINE=InnoDB;

ALTER TABLE tblNotify ENGINE=InnoDB;

ALTER TABLE tblSessions ENGINE=InnoDB;

ALTER TABLE tblMandatoryReviewers ENGINE=InnoDB;

ALTER TABLE tblMandatoryApprovers ENGINE=InnoDB;

ALTER TABLE tblEvents ENGINE=InnoDB;

ALTER TABLE tblVersion ENGINE=InnoDB;

CALL DROPFK('tblFolders', 'tblFolders_owner');

ALTER TABLE tblFolders ADD CONSTRAINT `tblFolders_owner` FOREIGN KEY (`owner`) REFERENCES `tblUsers` (`id`);

CALL DROPFK('tblDocuments', 'tblDocuments_owner');

ALTER TABLE tblDocuments ADD CONSTRAINT `tblDocuments_owner` FOREIGN KEY (`owner`) REFERENCES `tblUsers` (`id`);

CALL DROPFK('tblDocuments', 'tblDocuments_folder');

ALTER TABLE tblDocuments ADD CONSTRAINT `tblDocuments_folder` FOREIGN KEY (`folder`) REFERENCES `tblFolders` (`id`);

CALL DROPFK('tblDocumentContent', 'tblDocumentDocument_document');

ALTER TABLE tblDocumentContent ADD CONSTRAINT `tblDocumentContent_document` FOREIGN KEY (`document`) REFERENCES `tblDocuments` (`id`);

CALL DROPFK('tblDocumentLinks', 'tblDocumentLinks_user');

ALTER TABLE tblDocumentLinks ADD CONSTRAINT `tblDocumentLinks_user` FOREIGN KEY (`userID`) REFERENCES `tblUsers` (`id`);

CALL DROPFK('tblDocumentFiles', 'tblDocumentFiles_user');

ALTER TABLE tblDocumentFiles ADD CONSTRAINT `tblDocumentFiles_user` FOREIGN KEY (`userID`) REFERENCES `tblUsers` (`id`);

ALTER TABLE tblGroupMembers DROP PRIMARY KEY;

ALTER TABLE tblGroupMembers ADD UNIQUE(`groupID`,`userID`);

ALTER TABLE tblGroupMembers ADD CONSTRAINT `tblGroupMembers_user` FOREIGN KEY (`userID`) REFERENCES `tblUsers` (`id`) ON DELETE CASCADE;

ALTER TABLE tblGroupMembers ADD CONSTRAINT `tblGroupMembers_group` FOREIGN KEY (`groupID`) REFERENCES `tblGroups` (`id`) ON DELETE CASCADE;

CREATE TABLE `tblAttributeDefinitions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) default NULL,
  `objtype` tinyint(4) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `multiple` tinyint(4) NOT NULL default '0',
  `minvalues` int(11) NOT NULL default '0',
  `maxvalues` int(11) NOT NULL default '0',
  `valueset` text default NULL,
	UNIQUE(`name`),
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tblFolderAttributes` (
  `id` int(11) NOT NULL auto_increment,
  `folder` int(11) default NULL,
  `attrdef` int(11) default NULL,
  `value` text default NULL,
  PRIMARY KEY  (`id`),
	UNIQUE (folder, attrdef),
  CONSTRAINT `tblFolderAttributes_folder` FOREIGN KEY (`folder`) REFERENCES `tblFolders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblFolderAttributes_attrdef` FOREIGN KEY (`attrdef`) REFERENCES `tblAttributeDefinitions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tblDocumentAttributes` (
  `id` int(11) NOT NULL auto_increment,
  `document` int(11) default NULL,
  `attrdef` int(11) default NULL,
  `value` text default NULL,
  PRIMARY KEY  (`id`),
	UNIQUE (document, attrdef),
  CONSTRAINT `tblDocumentAttributes_document` FOREIGN KEY (`document`) REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblDocumentAttributes_attrdef` FOREIGN KEY (`attrdef`) REFERENCES `tblAttributeDefinitions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tblDocumentContent ADD COLUMN `id` int(11) NOT NULL auto_increment PRIMARY KEY FIRST;

CREATE TABLE `tblDocumentContentAttributes` (
  `id` int(11) NOT NULL auto_increment,
  `content` int(11) default NULL,
  `attrdef` int(11) default NULL,
  `value` text default NULL,
  PRIMARY KEY  (`id`),
	UNIQUE (content, attrdef),
  CONSTRAINT `tblDocumentContentAttributes_document` FOREIGN KEY (`content`) REFERENCES `tblDocumentContent` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblDocumentContentAttributes_attrdef` FOREIGN KEY (`attrdef`) REFERENCES `tblAttributeDefinitions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tblUserPasswordHistory` (
  `id` int(11) NOT NULL auto_increment,
  `userID` int(11) NOT NULL default '0',
  `pwd` varchar(50) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  CONSTRAINT `tblUserPasswordHistory_user` FOREIGN KEY (`userID`) REFERENCES `tblUsers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE tblUsers ADD COLUMN `pwdExpiration` datetime NOT NULL default '0000-00-00 00:00:00';

ALTER TABLE tblUsers ADD COLUMN `loginfailures` tinyint(4) NOT NULL default '0';

ALTER TABLE tblUsers ADD COLUMN `disabled` smallint(4) NOT NULL default '0';

ALTER TABLE tblUsers ADD UNIQUE(`login`);

UPDATE tblVersion set date=NOW(), major=3, minor=4, subminor=0;

COMMIT;

DROP PROCEDURE IF EXISTS DROPFK;
