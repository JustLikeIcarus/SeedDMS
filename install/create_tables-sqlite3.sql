-- 
-- Table structure for table `tblACLs`
-- 

CREATE TABLE `tblACLs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `target` INTEGER NOT NULL default '0',
  `targetType` INTEGER NOT NULL default '0',
  `userID` INTEGER NOT NULL default '-1',
  `groupID` INTEGER NOT NULL default '-1',
  `mode` INTEGER NOT NULL default '0'
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblCategory`
-- 

CREATE TABLE `tblCategory` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` text NOT NULL
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblAttributeDefinitions`
-- 

CREATE TABLE `tblAttributeDefinitions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(100) default NULL,
  `objtype` INTEGER NOT NULL default '0',
  `type` INTEGER NOT NULL default '0',
  `multiple` INTEGER NOT NULL default '0',
  `minvalues` INTEGER NOT NULL default '0',
  `maxvalues` INTEGER NOT NULL default '0',
  `valueset` TEXT default NULL,
  `regex` TEXT DEFAULT '',
  UNIQUE(`name`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblUsers`
-- 

CREATE TABLE `tblUsers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `login` varchar(50) default NULL,
  `pwd` varchar(50) default NULL,
  `fullName` varchar(100) default NULL,
  `email` varchar(70) default NULL,
  `language` varchar(32) NOT NULL,
  `theme` varchar(32) NOT NULL,
  `comment` text NOT NULL,
  `role` INTEGER NOT NULL default '0',
  `hidden` INTEGER NOT NULL default '0',
  `pwdExpiration` TEXT NOT NULL default '0000-00-00 00:00:00',
  `loginfailures` INTEGER NOT NULL default '0',
  `disabled` INTEGER NOT NULL default '0',
  `quota` INTEGER,
  UNIQUE (`login`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `tblUserPasswordRequest`
-- 

CREATE TABLE `tblUserPasswordRequest` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `hash` varchar(50) default NULL,
  `date` TEXT NOT NULL default '0000-00-00 00:00:00'
);

-- --------------------------------------------------------

-- 
-- Table structure for table `tblUserPasswordHistory`
-- 

CREATE TABLE `tblUserPasswordHistory` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `pwd` varchar(50) default NULL,
  `date` TEXT NOT NULL default '0000-00-00 00:00:00'
);

-- --------------------------------------------------------

-- 
-- Table structure for table `tblUserImages`
-- 

CREATE TABLE `tblUserImages` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `image` blob NOT NULL,
  `mimeType` varchar(10) NOT NULL default ''
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblFolders`
-- 

CREATE TABLE `tblFolders` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(70) default NULL,
  `parent` INTEGER default NULL,
  `folderList` text NOT NULL,
  `comment` text,
  `date` INTEGER default NULL,
  `owner` INTEGER default NULL REFERENCES `tblUsers` (`id`),
  `inheritAccess` INTEGER NOT NULL default '1',
  `defaultAccess` INTEGER NOT NULL default '0',
  `sequence` double NOT NULL default '0'
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblFolderAttributes`
-- 

CREATE TABLE `tblFolderAttributes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `folder` INTEGER default NULL REFERENCES `tblFolders` (`id`) ON DELETE CASCADE,
  `attrdef` INTEGER default NULL REFERENCES `tblAttributeDefinitions` (`id`),
  `value` text default NULL,
  UNIQUE (folder, attrdef)
) ;
 
-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocuments`
-- 

CREATE TABLE `tblDocuments` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(150) default NULL,
  `comment` text,
  `date` INTEGER default NULL,
  `expires` INTEGER default NULL,
  `owner` INTEGER default NULL REFERENCES `tblUsers` (`id`),
  `folder` INTEGER default NULL REFERENCES `tblFolders` (`id`),
  `folderList` text NOT NULL,
  `inheritAccess` INTEGER NOT NULL default '1',
  `defaultAccess` INTEGER NOT NULL default '0',
  `locked` INTEGER NOT NULL default '-1',
  `keywords` text NOT NULL,
  `sequence` double NOT NULL default '0'
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentAttributes`
-- 

CREATE TABLE `tblDocumentAttributes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `document` INTEGER default NULL REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `attrdef` INTEGER default NULL REFERENCES `tblAttributeDefinitions` (`id`),
  `value` text default NULL,
  UNIQUE (document, attrdef)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentApprovers`
-- 

CREATE TABLE `tblDocumentApprovers` (
  `approveID` INTEGER PRIMARY KEY AUTOINCREMENT,
  `documentID` INTEGER NOT NULL default '0' REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `version` INTEGER unsigned NOT NULL default '0',
  `type` INTEGER NOT NULL default '0',
  `required` INTEGER NOT NULL default '0',
  UNIQUE (`documentID`,`version`,`type`,`required`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentApproveLog`
-- 

CREATE TABLE `tblDocumentApproveLog` (
  `approveLogID` INTEGER PRIMARY KEY AUTOINCREMENT,
  `approveID` INTEGER NOT NULL default '0' REFERENCES `tblDocumentApprovers` (`approveID`) ON DELETE CASCADE,
  `status` INTEGER NOT NULL default '0',
  `comment` TEXT NOT NULL,
  `date` TEXT NOT NULL default '0000-00-00 00:00:00',
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentContent`
-- 

CREATE TABLE `tblDocumentContent` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `document` INTEGER NOT NULL default '0' REFERENCES `tblDocuments` (`id`),
  `version` INTEGER unsigned NOT NULL,
  `comment` text,
  `date` INTEGER default NULL,
  `createdBy` INTEGER default NULL,
  `dir` varchar(255) NOT NULL default '',
  `orgFileName` varchar(150) NOT NULL default '',
  `fileType` varchar(10) NOT NULL default '',
  `mimeType` varchar(70) NOT NULL default '',
  `fileSize` INTEGER,
  `checksum` char(32),
  UNIQUE (`document`,`version`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentContentAttributes`
-- 

CREATE TABLE `tblDocumentContentAttributes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `content` INTEGER default NULL REFERENCES `tblDocumentContent` (`id`) ON DELETE CASCADE,
  `attrdef` INTEGER default NULL REFERENCES `tblAttributeDefinitions` (`id`),
  `value` text default NULL,
  UNIQUE (content, attrdef)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentLinks`
-- 

CREATE TABLE `tblDocumentLinks` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `document` INTEGER NOT NULL default 0 REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `target` INTEGER NOT NULL default 0 REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `userID` INTEGER NOT NULL default 0 REFERENCES `tblUsers` (`id`),
  `public` INTEGER NOT NULL default 0
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentFiles`
-- 

CREATE TABLE `tblDocumentFiles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `document` INTEGER NOT NULL default 0 REFERENCES `tblDocuments` (`id`),
  `userID` INTEGER NOT NULL default 0 REFERENCES `tblUsers` (`id`),
  `comment` text,
  `name` varchar(150) default NULL,
  `date` INTEGER default NULL,
  `dir` varchar(255) NOT NULL default '',
  `orgFileName` varchar(150) NOT NULL default '',
  `fileType` varchar(10) NOT NULL default '',
  `mimeType` varchar(70) NOT NULL default ''
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentLocks`
-- 

CREATE TABLE `tblDocumentLocks` (
  `document` INTEGER REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentReviewLog`
-- 

CREATE TABLE `tblDocumentReviewLog` (
  `reviewLogID` INTEGER PRIMARY KEY AUTOINCREMENT,
  `reviewID` INTEGER NOT NULL default 0 REFERENCES `tblDocumentReviewers` (`reviewID`) ON DELETE CASCADE,
  `status` INTEGER NOT NULL default 0,
  `comment` TEXT NOT NULL,
  `date` TEXT NOT NULL default '0000-00-00 00:00:00',
  `userID` INTEGER NOT NULL default 0 REFERENCES `tblUsers` (`id`) ON DELETE CASCADE
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentReviewers`
-- 

CREATE TABLE `tblDocumentReviewers` (
  `reviewID` INTEGER PRIMARY KEY AUTOINCREMENT,
  `documentID` INTEGER NOT NULL default '0' REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `version` INTEGER unsigned NOT NULL default '0',
  `type` INTEGER NOT NULL default '0',
  `required` INTEGER NOT NULL default '0',
  UNIQUE (`documentID`,`version`,`type`,`required`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentStatus`
-- 

CREATE TABLE `tblDocumentStatus` (
  `statusID` INTEGER PRIMARY KEY AUTOINCREMENT,
  `documentID` INTEGER NOT NULL default '0' REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `version` INTEGER unsigned NOT NULL default '0',
  UNIQUE (`documentID`,`version`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentStatusLog`
-- 

CREATE TABLE `tblDocumentStatusLog` (
  `statusLogID` INTEGER PRIMARY KEY AUTOINCREMENT,
  `statusID` INTEGER NOT NULL default '0' REFERENCES `tblDocumentStatus` (`statusID`) ON DELETE CASCADE,
  `status` INTEGER NOT NULL default '0',
  `comment` text NOT NULL,
  `date` TEXT NOT NULL default '0000-00-00 00:00:00',
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblGroups`
-- 

CREATE TABLE `tblGroups` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(50) default NULL,
  `comment` text NOT NULL
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblGroupMembers`
-- 

CREATE TABLE `tblGroupMembers` (
  `groupID` INTEGER NOT NULL default '0' REFERENCES `tblGroups` (`id`) ON DELETE CASCADE,
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `manager` INTEGER NOT NULL default '0',
  UNIQUE  (`groupID`,`userID`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblKeywordCategories`
-- 

CREATE TABLE `tblKeywordCategories` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL default '',
  `owner` INTEGER NOT NULL default '0'
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblKeywords`
-- 

CREATE TABLE `tblKeywords` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `category` INTEGER NOT NULL default '0' REFERENCES `tblKeywordCategories` (`id`) ON DELETE CASCADE,
  `keywords` text NOT NULL
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblDocumentCategory`
-- 

CREATE TABLE `tblDocumentCategory` (
  `categoryID` INTEGER NOT NULL default '0' REFERENCES `tblCategory` (`id`) ON DELETE CASCADE,
  `documentID` INTEGER NOT NULL default '0' REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblNotify`
-- 

CREATE TABLE `tblNotify` (
  `target` INTEGER NOT NULL default '0',
  `targetType` INTEGER NOT NULL default '0',
  `userID` INTEGER NOT NULL default '-1',
  `groupID` INTEGER NOT NULL default '-1',
  UNIQUE  (`target`,`targetType`,`userID`,`groupID`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `tblSessions`
-- 

CREATE TABLE `tblSessions` (
  `id` varchar(50) PRIMARY KEY,
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `lastAccess` INTEGER NOT NULL default '0',
  `theme` varchar(30) NOT NULL default '',
  `language` varchar(30) NOT NULL default '',
  `clipboard` text default '',
	`su` INTEGER DEFAULT NULL,
  `splashmsg` text default ''
) ;

-- --------------------------------------------------------

-- 
-- Table structure for mandatory reviewers
-- 

CREATE TABLE `tblMandatoryReviewers` (
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `reviewerUserID` INTEGER NOT NULL default '0',
  `reviewerGroupID` INTEGER NOT NULL default '0',
  UNIQUE (`userID`,`reviewerUserID`,`reviewerGroupID`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for mandatory approvers
-- 

CREATE TABLE `tblMandatoryApprovers` (
  `userID` INTEGER NOT NULL default '0' REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `approverUserID` INTEGER NOT NULL default '0',
  `approverGroupID` INTEGER NOT NULL default '0',
  UNIQUE (`userID`,`approverUserID`,`approverGroupID`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for events (calendar)
-- 

CREATE TABLE `tblEvents` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(150) default NULL,
  `comment` text,
  `start` INTEGER default NULL,
  `stop` INTEGER default NULL,
  `date` INTEGER default NULL,
  `userID` INTEGER NOT NULL default '0'
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflow states
-- 

CREATE TABLE tblWorkflowStates (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` text NOT NULL,
  `visibility` smallint(5) DEFAULT 0,
  `maxtime` INTEGER DEFAULT 0,
  `precondfunc` text DEFAULT NULL,
  `documentstatus` smallint(5) DEFAULT NULL
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflow actions
-- 

CREATE TABLE tblWorkflowActions (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` text NOT NULL
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflows
-- 

CREATE TABLE tblWorkflows (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` text NOT NULL,
  `initstate` INTEGER NOT NULL REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflow transitions
-- 

CREATE TABLE tblWorkflowTransitions (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `workflow` INTEGER default NULL REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  `state` INTEGER default NULL REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE,
  `action` INTEGER default NULL REFERENCES `tblWorkflowActions` (`id`) ON DELETE CASCADE,
  `nextstate` INTEGER default NULL REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE,
  `maxtime` INTEGER DEFAULT 0
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflow transition users
-- 

CREATE TABLE tblWorkflowTransitionUsers (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `transition` INTEGER default NULL REFERENCES `tblWorkflowTransitions` (`id`) ON DELETE CASCADE,
  `userid` INTEGER default NULL REFERENCES `tblUsers` (`id`) ON DELETE CASCADE
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflow transition groups
-- 

CREATE TABLE tblWorkflowTransitionGroups (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `transition` INTEGER default NULL REFERENCES `tblWorkflowTransitions` (`id`) ON DELETE CASCADE,
  `groupid` INTEGER default NULL REFERENCES `tblGroups` (`id`) ON DELETE CASCADE,
  `minusers` INTEGER default NULL
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflow log
-- 

CREATE TABLE tblWorkflowLog (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `document` INTEGER default NULL REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `version` smallint default NULL,
  `workflow` INTEGER default NULL REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  `userid` INTEGER default NULL REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `transition` INTEGER default NULL REFERENCES `tblWorkflowTransitions` (`id`) ON DELETE CASCADE,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `comment` text
) ;

-- --------------------------------------------------------

-- 
-- Table structure for workflow document relation
-- 

CREATE TABLE tblWorkflowDocumentContent (
  `parentworkflow` INTEGER DEFAULT 0,
  `workflow` INTEGER DEFAULT NULL REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  `document` INTEGER DEFAULT NULL REFERENCES `tblDocuments` (`id`) ON DELETE CASCADE,
  `version` smallint DEFAULT NULL,
  `state` INTEGER DEFAULT NULL REFERENCES `tblWorkflowStates` (`id`) ON DELETE CASCADE,
  `date` datetime NOT NULL default '0000-00-00 00:00:00'
) ;

-- --------------------------------------------------------

-- 
-- Table structure for mandatory workflows
-- 

CREATE TABLE tblWorkflowMandatoryWorkflow (
  `userid` INTEGER default NULL REFERENCES `tblUsers` (`id`) ON DELETE CASCADE,
  `workflow` INTEGER default NULL REFERENCES `tblWorkflows` (`id`) ON DELETE CASCADE,
  UNIQUE(userid, workflow)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for version
-- 

CREATE TABLE `tblVersion` (
  `date` TEXT NOT NULL default '0000-00-00 00:00:00',
  `major` smallint,
  `minor` smallint,
  `subminor` smallint
) ;

-- --------------------------------------------------------

--
-- Initial content for database
--

INSERT INTO tblUsers VALUES (1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Administrator', 'address@server.com', '', '', '', 1, 0, '', 0, 0, 0);
INSERT INTO tblUsers VALUES (2, 'guest', NULL, 'Guest User', NULL, '', '', '', 2, 0, '', 0, 0, 0);
INSERT INTO tblFolders VALUES (1, 'DMS', 0, '', 'DMS root', strftime('%s','now'), 1, 0, 2, 0);
INSERT INTO tblVersion VALUES (DATETIME(), 4, 3, 0);
INSERT INTO tblCategory VALUES (0, '');
