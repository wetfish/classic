--
-- Table structure for table `things`
--

DROP TABLE IF EXISTS `things`;
CREATE TABLE `things` (
  `ThingID` int(10) NOT NULL AUTO_INCREMENT,
  `ThingReplyCount` int(10) NOT NULL,
  `ThingTimeUpdated` int(10) NOT NULL,
  `ThingTimePosted` int(10) NOT NULL,
  `ThingIP` varchar(15) NOT NULL,
  `ThingName` varchar(30) NOT NULL,
  `ThingSummary` varchar(100) NOT NULL,
  `ThingTags` varchar(100) NOT NULL,
  `ThingFile` varchar(100) NOT NULL,
  `ThingBody` text NOT NULL,
  PRIMARY KEY (`ThingID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `thing_comments`
--

DROP TABLE IF EXISTS `thing_comments`;
CREATE TABLE `thing_comments` (
  `CommentID` int(10) NOT NULL AUTO_INCREMENT,
  `ParentThingID` int(10) NOT NULL,
  `CommentTimePosted` int(10) NOT NULL,
  `CommentIP` varchar(15) NOT NULL,
  `CommentName` varchar(30) NOT NULL,
  `CommentBody` text NOT NULL,
  PRIMARY KEY (`CommentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
