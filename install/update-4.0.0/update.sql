START TRANSACTION;

ALTER TABLE tblDocumentLinks ADD CONSTRAINT `tblDocumentLinks_target` FOREIGN KEY (`target`) REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE;

ALTER TABLE tblMandatoryReviewers ADD CONSTRAINT `tblMandatoryReviewers_user` FOREIGN KEY (`userID`) REFERENCES `tblUsers` (`id`) ON DELETE CASCADE;

ALTER TABLE tblMandatoryApprovers ADD CONSTRAINT `tblMandatoryApprovers_user` FOREIGN KEY (`userID`) REFERENCES `tblUsers` (`id`) ON DELETE CASCADE;

ALTER TABLE tblDocumentContent ADD COLUMN `fileSize` bigint;

ALTER TABLE tblDocumentContent ADD COLUMN `checksum` char(32);

ALTER TABLE tblUsers ADD COLUMN `quota` bigint;

ALTER TABLE tblSessions ADD COLUMN `clipboard` text DEFAULT '';

CREATE TABLE tblWorkflowStates (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `visibility` smallint(5) DEFAULT 0,
	`maxtime` int(11) DEFAULT 0,
  `precondfunc` text DEFAULT NULL,
	`documentstatus` smallint(5) DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflowActions (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflows (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `initstate` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `tblWorkflow_initstate` FOREIGN KEY (`initstate`) REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflowTransitions (
  `id` int(11) NOT NULL auto_increment,
  `workflow` int(11) default NULL,
  `state` int(11) default NULL,
  `action` int(11) default NULL,
  `nextstate` int(11) default NULL,
	`maxtime` int(11) DEFAULT 0,
  PRIMARY KEY  (`id`),
  CONSTRAINT `tblWorkflowTransitions_workflow` FOREIGN KEY (`workflow`) REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowTransitions_state` FOREIGN KEY (`state`) REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowTransitions_action` FOREIGN KEY (`action`) REFERENCES `tblWorkflowActions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowTransitions_nextstate` FOREIGN KEY (`nextstate`) REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflowTransitionUsers (
  `id` int(11) NOT NULL auto_increment,
  `transition` int(11) default NULL,
  `userid` int(11) default NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `tblWorkflowTransitionUsers_transition` FOREIGN KEY (`transition`) REFERENCES `tblWorkflowTransitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflowTransitionGroups (
  `id` int(11) NOT NULL auto_increment,
  `transition` int(11) default NULL,
  `groupid` int(11) default NULL,
  `minusers` int(11) default NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT `tblWorkflowTransitionGroups_transition` FOREIGN KEY (`transition`) REFERENCES `tblWorkflowTransitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflowLog (
  `id` int(11) NOT NULL auto_increment,
  `document` int(11) default NULL,
  `version` smallint(5) default NULL,
  `workflow` int(11) default NULL,
  `userid` int(11) default NULL,
  `transition` int(11) default NULL,
  `nextstate` int(11) default NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `comment` text,
  PRIMARY KEY  (`id`),
  CONSTRAINT `tblWorkflowLog_document` FOREIGN KEY (`document`) REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowLog_workflow` FOREIGN KEY (`workflow`) REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowLog_userid` FOREIGN KEY (`userid`) REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowLog_transition` FOREIGN KEY (`transition`) REFERENCES `tblWorkflowTransitions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflowDocumentContent (
  `parentworkflow` int(11) DEFAULT 0,
  `workflow` int(11) DEFAULT NULL,
  `document` int(11) DEFAULT NULL,
  `version` smallint(5) DEFAULT NULL,
  `state` int(11) DEFAULT NULL,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  CONSTRAINT `tblWorkflowDocument_document` FOREIGN KEY (`document`) REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowDocument_workflow` FOREIGN KEY (`workflow`) REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowDocument_state` FOREIGN KEY (`state`) REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tblWorkflowMandatoryWorkflow (
  `userid` int(11) default NULL,
  `workflow` int(11) default NULL,
	UNIQUE(userid, workflow),
  CONSTRAINT `tblWorkflowMandatoryWorkflow_workflow` FOREIGN KEY (`workflow`) REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tblWorkflowMandatoryWorkflow_userid` FOREIGN KEY (`userid`) REFERENCES `tblUsers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE tblVersion set date=NOW(), major=4, minor=0, subminor=0;

COMMIT;

