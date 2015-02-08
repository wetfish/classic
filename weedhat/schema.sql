CREATE TABLE `Accounts` (
  `AccountID` int(10) NOT NULL auto_increment,
  `AccountCreationTime` int(10) NOT NULL,
  `AccountLoginTime` int(10) NOT NULL,
  `AccountName` varchar(32) NOT NULL,
  `AccountPassword` varchar(32) NOT NULL,
  `AccountEmail` varchar(255) NOT NULL,
  `AccountAuthCode` varchar(32) NOT NULL,
  `AccountStatus` varchar(32) NOT NULL,
  `AccountHats` longtext NOT NULL,
  PRIMARY KEY  (`AccountID`)
);


CREATE TABLE `Hats` (
  `HatID` int(10) NOT NULL auto_increment,
  `HatCreationTime` int(10) NOT NULL,
  `HatURL` varchar(40) NOT NULL,
  `HatName` varchar(32) NOT NULL,
  `HatCreator` varchar(32) NOT NULL,
  `HatCreatorIP` varchar(15) NOT NULL,
  `HatCategory` varchar(32) NOT NULL,
  `HatRaters` longtext NOT NULL,
  `HatRating` int(10) NOT NULL,
  `HatReplyTo` int(10) NOT NULL,
  `HatReplies` longtext NOT NULL,
  `HatTags` longtext NOT NULL,
  PRIMARY KEY  (`HatID`)
);

CREATE TABLE `Tags` (
  `TagID` int(10) NOT NULL auto_increment,
  `TagName` varchar(32) NOT NULL,
  `TagHats` longtext NOT NULL,
  `TagOccurrence` int(10) NOT NULL,
  PRIMARY KEY  (`TagID`)
);