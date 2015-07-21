alter table products 
add column products_qty_blocks int not null default 1;

DROP TABLE IF EXISTS `products_price_break`;
CREATE TABLE `products_price_break` (
   `products_price_break_id` int NOT NULL auto_increment,
   `products_id` int(11) NOT NULL,
   `products_price` decimal(15,4) NOT NULL default 0.0,
   `products_qty` int(11) NOT NULL default 0,
   PRIMARY KEY (products_price_break_id)
);

DROP TABLE IF EXISTS `discount_categories`;
CREATE TABLE `discount_categories` (
   `discount_categories_id` int NOT NULL auto_increment,
   `discount_categories_name` varchar(255) NOT NULL,
   PRIMARY KEY (discount_categories_id)
);

DROP TABLE IF EXISTS `products_to_discount_categories`;
CREATE TABLE `products_to_discount_categories` (
  `products_id` int NOT NULL,
  `discount_categories_id` int NOT NULL,
  PRIMARY KEY (products_id)
);

INSERT INTO configuration_group (configuration_group_id, configuration_group_title, configuration_group_description, sort_order, visible)
VALUES ('73', 'Price breaks', 'Configuration options for price breaks', 73, 1);
INSERT into configuration (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, last_modified, date_added, use_function, set_function)
VALUES ('Maximum number of price break levels', 'PRICE_BREAK_NOF_LEVELS', '10', 'Configures the number of price break levels that can be entered on admin side. Levels that are left empty will not be shown to the customer', '73', '1', now(), now(), NULL, NULL);
