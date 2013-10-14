-- mysql -uyouruser -pyourpassword yourdb < update.sql
-- this script must be executed when updating form a version < 1.9

-- --------------------------------------------------------

-- 
-- New field for hidden users
-- 

ALTER TABLE `tblUsers` ADD `hidden` smallint(1) NOT NULL default '0' ;

-- 
-- New field for group manager permission
-- 

ALTER TABLE `tblGroupMembers` ADD `manager` smallint(1) NOT NULL default '0' ;

-- 
-- Table structure for mandatory reviewers
-- 

CREATE TABLE `tblMandatoryReviewers` (
  `userID` int(11) NOT NULL default '0',
  `reviewerUserID` int(11) NOT NULL default '0',
  `reviewerGroupID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`userID`,`reviewerUserID`,`reviewerGroupID`)
) ;

-- 
-- Table structure for mandatory approvers
-- 

CREATE TABLE `tblMandatoryApprovers` (
  `userID` int(11) NOT NULL default '0',
  `approverUserID` int(11) NOT NULL default '0',
  `approverGroupID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`userID`,`approverUserID`,`approverGroupID`)
) ;
