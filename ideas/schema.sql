--
-- Table structure for table `ideas`
--

DROP TABLE IF EXISTS `ideas`;
CREATE TABLE `ideas` (
  `IdeaID` int(10) NOT NULL AUTO_INCREMENT,
  `IdeaReplyCount` int(10) NOT NULL,
  `IdeaTimeUpdated` int(10) NOT NULL,
  `IdeaTimePosted` int(10) NOT NULL,
  `IdeaIP` varchar(15) NOT NULL,
  `IdeaName` varchar(30) NOT NULL,
  `IdeaSummary` varchar(100) NOT NULL,
  `IdeaTags` varchar(100) NOT NULL,
  `IdeaBody` text NOT NULL,
  PRIMARY KEY (`IdeaID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `idea_comments`
--

DROP TABLE IF EXISTS `idea_comments`;
CREATE TABLE `idea_comments` (
  `CommentID` int(10) NOT NULL AUTO_INCREMENT,
  `ParentIdeaID` int(10) NOT NULL,
  `CommentTimePosted` int(10) NOT NULL,
  `CommentIP` varchar(15) NOT NULL,
  `CommentName` varchar(30) NOT NULL,
  `CommentBody` text NOT NULL,
  PRIMARY KEY (`CommentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

