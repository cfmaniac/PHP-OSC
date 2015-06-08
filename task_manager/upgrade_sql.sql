ALTER TABLE `admin_tasks` ADD `admin_task_due` DATETIME NULL AFTER `admin_task_modified` ;

ALTER TABLE `admin_projects` ADD `admin_projects_customer` TINYINT( 11 ) NULL DEFAULT '0' AFTER `admin_projects_name` ;