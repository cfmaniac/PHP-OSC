-- 
-- Table structure for table `admin_projects`
-- 

CREATE TABLE `admin_projects` (
  `admin_projects_id` int(11) NOT NULL auto_increment,
  `admin_projects_name` varchar(64) default NULL,
  `admin_projects_customer` TINYINT( 11 ) NULL DEFAULT '0',
  `admin_projects_type` char(1) NOT NULL default 'D',
  PRIMARY KEY  (`admin_projects_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `admin_projects`
-- 

INSERT INTO `admin_projects` VALUES (1, 'Test Project', 'D');


-- --------------------------------------------------------

-- 
-- Table structure for table `admin_tasks`
-- 

CREATE TABLE `admin_tasks` (
  `admin_task_id` int(11) NOT NULL auto_increment,
  `admin_id` int(11) NOT NULL default '0',
  `admin_projects_id` int(11) default NULL,
  `admin_task_priority` tinyint(11) NOT NULL default '0',
  `admin_task_status` tinyint(11) NOT NULL default '0',
  `admin_task_name` varchar(32) NOT NULL default '',
  `admin_task_description` text,
  `admin_task_created` datetime default NULL,
  `admin_task_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `admin_task_due` DATETIME NULL,
  `admin_overdue_mail_sent` int(11) NOT NULL default '0',
  PRIMARY KEY  (`admin_task_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `admin_tasks`
-- 
INSERT INTO `admin_tasks` VALUES (1, 1, 1, 1, 2, 'Test Task', 'This is a Test Task To Ensure that your install went smoothly', '2006-04-04 15:59:10', '2006-04-04 16:58:17, 2006-04-04 16:58:17');

-- --------------------------------------------------------

-- 
-- Table structure for table `admin_tasks_priorities`
-- 

CREATE TABLE `admin_tasks_priorities` (
  `admin_priority_id` int(11) NOT NULL auto_increment,
  `admin_priority_name` varchar(64) default NULL,
  PRIMARY KEY  (`admin_priority_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `admin_tasks_priorities`
-- 

INSERT INTO `admin_tasks_priorities` VALUES (1, 'Critical');
INSERT INTO `admin_tasks_priorities` VALUES (2, 'High');
INSERT INTO `admin_tasks_priorities` VALUES (3, 'Medium-High');
INSERT INTO `admin_tasks_priorities` VALUES (4, 'Medium');
INSERT INTO `admin_tasks_priorities` VALUES (5, 'Medium-Low');
INSERT INTO `admin_tasks_priorities` VALUES (6, 'Low');

-- --------------------------------------------------------

-- 
-- Table structure for table `admin_tasks_statuses`
-- 

CREATE TABLE `admin_tasks_statuses` (
  `admin_status_id` int(11) NOT NULL auto_increment,
  `admin_status_name` varchar(64) default NULL,
  PRIMARY KEY  (`admin_status_id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `admin_tasks_statuses`
-- 

INSERT INTO `admin_tasks_statuses` VALUES (1, '100% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (2, '90% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (3, '80% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (4, '70% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (5, '60% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (6, '50% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (7, '40% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (8, '30% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (9, '20% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (10, '10% Complete');
INSERT INTO `admin_tasks_statuses` VALUES (11, '0% -Not Started');
INSERT INTO `admin_tasks_statuses` VALUES (12, 'Daily Recurring Task');
INSERT INTO `admin_tasks_statuses` VALUES (13, 'Weekly Recurring Task');
INSERT INTO `admin_tasks_statuses` VALUES (14, 'Monthly Recurring Task');
