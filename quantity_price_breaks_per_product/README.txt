********************************************************************
*
* OSCommerce Patch
*
* Name:    Price Break
* 
* Author:  Andrew Baucom (abaucom@beezle.com)
*
* Purpose: 1) To allow quantity based pricing per product
*          2) To allow the ability to sell a product in
*             lots of # quantity only.
*
* Date:    August, 2003
*
* Revisions: 5/13/2003 - Initial 1.0 (awb_pb)
*            8/18/2003 - Rewrite 1.1
*            2/13/2004 - Step by step instructions
*                        compiled by Mike Sullivan
*            7/15/2004 - Step by step instruction for 8 breaks - german            
*                        edited by LuiGee
*
*            10 Nov 2004 - Fixed bugs in the step-by-step
*                          instructions, applied existing patch to
*                          PriceFormatter.php, and added support for
*                          price breaks to search results.
*                          -- Tim Cartwright, Berbee
*
*            23 Dec 2006 - v 1.2.0 by JanZ (for changes see further)
*            5 Jan 2007  - v 1.2.1 by JanZ (additional instructions for a price break that ignores attributes)
*            31 Jan 2007 - v 1.2.2 by JanZ (bugfix in PriceFormatterStore where the first item in the array with
*                          product_id's from the cart is an empty value in certain shops, resulting in an sql error)
*            19 Feb 2007 - v 1.2.3 (bugfix in admin/categories.php where sql for copying products was not
*                          complete: see http://forums.oscommerce.com/index.php?showtopic=246615)
*            8 Dec 2007 - v 1.2.4 by JanZ (no changes, added instructions and files for a
*                          modification: price break per discount category, updated included files
*                          to use osC 2.2 RC1 files)
*            16 Dec 2007 - v 1.2.5 by JanZ (two bugfixes in sql of PriceFormatter_pbpdc.php and
*                          PriceFormatterStore_pbpdc.php dc.products_id -> ptdc.products_id)
*            2 Jan 2008 - v 1.2.6 by JanZ (forgot to add discount_categories.php to the package)
*            3 Jan 2008 - v 1.2.7 by JanZ (a line was missing in the instructions for adding the 
*            link to the discount_categories.php file in the categories box in the admin: 
*            catalog/admin/includes/boxes/catalog.php - this was missing in
*            upgrade_to_price_break_per_discount_category.html) Kudos to John Price for catching
*            this error and the missing file discount_categories.php.
*            3 Feb 2008 - v 1.2.8 by JanZ (updated install.php and included files to RC2a)
*            1 March 2008 - v 1.2.9 by JanZ (added more info about the contribution itself)
*            3 April 2008 - v 1.3.0 by Pbor1234 (moved pricebreak data out of products table to seperate (new) table).
*            11 April 2008 - v 1.3.1 by Pbor1234 (integrated price break per discount category).
*            4 May 2008 - v 1.3.2(+a/b) by JanZ (bugfixes).
*            13 May 2008 - v 1.3.3 by JanZ (bugfixes in three files).
*            15 May 2008 - v 1.3.4 by JanZ (two fixes for bugs I introduced in 
*            includes/classes/shopping_cart.php, function calculate).
*            7 September 2008 - v 1.3.5 fixed an error in the described changes for admin/categories.php
*            where it said:
-------------------------------------------
 After (around line 384):

    } elseif (tep_not_null($HTTP_POST_VARS)) {
      $pInfo->objectInfo($HTTP_POST_VARS);

 Replace with: 
-------------------------------------------
*            The "Replace with:" should have been "Add:"
*
********************************************************************
Version 1.3.5 No changes, just a fix for a faulty description in the changes for admin/categories.php, see above.
********************************************************************
Version 1.3.4 Changes from previous version 1.3.3
- Two bugs in the function calculate that I introduced while mixing code from version 1.2.9 and 1.3.1.
In this part the condition that checks for discount_categories_id not being null was missing:

// BOF qpbpp
        $discount_category_quantity = array(); // calculates no of items per discount category in shopping basket
      foreach ($this->contents as $products_id => $contents_array) {
	      if(tep_not_null($contents_array['discount_categories_id'])) {
	        if (!isset($discount_category_quantity[$contents_array['discount_categories_id']])) {
		        $discount_category_quantity[$contents_array['discount_categories_id']] = $contents_array['qty'];
	        } else {
		        $discount_category_quantity[$contents_array['discount_categories_id']] += $contents_array['qty'];
	        }
	      }
      } // end foreach
      
Then a little further:

$products_price = $pf->computePrice($qty, $nof_items_in_cart_same_cat); 

should have been:

$products_price = $pf->computePrice($qty, $nof_other_items_in_cart_same_cat);
********************************************************************
Version 1.3.3 Changes from previous version 1.3.2b
- Bugfix in includes/classes/PriceFormatter where two variables needed conversion when information was retrieved from PriceFormatterStore (regarding qtyBlocks and special price)
Two more bugfixes in case there are no price breaks. sunrise99/David was right when he said $this->price_break can be empty. It *can* happen when the product is already in the cart in two places (one when dealing with special prices)
Upload the new file in case of upgrading from 1.3.2b
- Bugfix in admin/categories.php where a warning is echo'ed multiple times in the price break part because $price_breaks is not an array:
around line 715:

if(array_key_exists($count, $price_breaks_array)) {
is changed to
if(is_array($price_breaks_array) && array_key_exists($count, $price_breaks_array)) {

- Bugfix in includes/modules/product_listing.php where with MySQL4 you get an error on line 92 about product_id being ambiguous (doesn't happen in MySQL5). 

using(products_id) where products_id 
was changed to 
using(products_id) where p.products_id
********************************************************************
Version 1.3.2b Changes from previous version 1.3.2a
- Bugfix in includes/classes/shopping_cart where old name of variable was used on line 298
(thanks to Greg/mickeymouse for reporting that)
********************************************************************
Version 1.3.2a Changes from previous version 1.3.2
- Forgot to replace includes/modules/product_listing.php
********************************************************************
Version 1.3.2 Changes from previous version 1.3.1
- Changes in includes/classes/PriceFormatter.php, includes/classes/PriceFormatterStore.php, includes/modules/product_listing, and product_info.php (bugfixes mainly, doing some things differently).
- Updated installation instructions including adding back some missing code in the instructions for admin/categories.php (the file itself was not changed).
********************************************************************
Version 1.3.1 Changes from previous version 1.3.0
- Added price break per discount category (from 1.2.9)
- Added manual installation instructions
- Also a small bugfix where price-break data did not get copied when cloning a product in admin
********************************************************************
Version 1.3.0 Changes from previous version 1.2.9
- Moved pricebreak data out of products table to seperate (new) table.
- Added configuration item to set the nof price levels.
- Added improved formatting (from ashleylr 22 Mar 2007)).
- Added dutch language
- Solve preview of pricebreak data (not yet in the database)
- Removed pricebreak-per-category support, intention is to add it offcourse but i feel to first upload this basic price-break version
====================================================================
Version 1.2.9 Changes from previous version 1.2.8 (none)
- No changes in the files. Screenshots and some frequently asked questions (FAQ) were added to the install.html in the hope of avoiding unncessary questions in the forum about this contribution (and frustration on the part of the user). All screenshots where moved to a separate folder in the package in a further attempt to not confuse newbies.

Version 1.2.8 Changes from previous version 1.2.7

- Minor updates for RC2a, in admin/categories.php where a few things were added for MySQL5 strict compatibility and catalog/includes/functions/general.php were a function was slightly changed. The rest of the changes are mostly changes of the id tags (line 3 of all the files) in the included files.
====================================================================
Version 1.2.7 Changes from previous version 1.2.6

- No changes, line of text needed for the link to discount_categories.php in the admin was missing.
====================================================================
Version 1.2.6 Changes from previous version 1.2.5

- No changes, forgot to add admin/discount_categories.php to the package.
====================================================================
Version 1.2.5 Changes from previous version 1.2.4

- No changes, just bugfixes in the two classes for the price break per discount category mod. Two instances of p.products_id = dc.products_id had to be changed to p.products_id = ptdc.products_id.
====================================================================
Version 1.2.4 Changes from previous version 1.2.3

- No changes to the basic version. Left out the files and instructions to use the base products_id for calculating the price break (see changes in version 1.2.1 for more info).
- Added instructions and files to calculate a price break based on the number of products that are in the same discount category (see upgrade_to_price_break_per_discount_category.html for details and instructions).
====================================================================
Version 1.2.3 Changes from previous version 1.2.2

- Bugfix in admin/categories.php. As pointed out correctly in this thread:
http://forums.oscommerce.com/index.php?showtopic=246615 the code for copying products was not correct
Fix added to the admin/categories.php file and the install.html
====================================================================
Version 1.2.2 Changes from previous version 1.2.1

- Change in PriceFormatterStore. Some osC installations reported an sql error in this class. It appears that the first item in the shopping_cart can be an empty value when using: $product_id_list = $cart->get_product_id_list();
Added some code to check for that.
====================================================================
Version 1.2.1 Changes from previous version 1.2.0

- No changes in the code, just added instructions to change the QPBPP code to use the base products_id to calculate the discount in case you use attributes (see price_break_per_products_id_instr.txt for more information). The modified files for this mod of a mod are includes/classes/shopping_cart_pb_base_id.php and includes/classes/PriceFormatter_pb_base_id.php. They should be renamed to shopping_cart.php and PriceFormatter.php respectively if you want to use this modification.
====================================================================
Version 1.2.0 Changes from previous version (1.11.2 Jemlin1 May 6, 2005)

- added PriceFormatterStore.php as a class/object to store mysql results (for the shopping cart box 5 slow, identical queries per product were done, now 1 for all products in the cart)
(see post by Wayne Lynn/echo242372 http://forums.oscommerce.com/index.php?s=&showtopic=220794&view=findpost&p=986262 about the slow shopping_cart.php page when products get added to the cart)
- added the preview of price breaks [like in the catalog] to admin/categories.php (Com2, June 2, 2005)
- updated manual instructions (renamed to install.html) by Yakky (August 19, 2005; for the files and the install instructions the osC update of August 17, 2006 was used)
- changed PriceFormatter to take advantage of PriceFormatterStore (changes in function loadProduct, commented out functions encode and decode because they are not used anyway, added quotes around the $style in function getPriceString)
- added instructions to change the function tep_get_stock to take advantage of PriceFormatterStore (instead of using 1 query per product in catalog/shopping_cart.php)

Support forum: http://forums.oscommerce.com/index.php?showtopic=220794&view=getlastpost
====================================================================
I take no credit (good or bad) for the original Price Break
contribution.

This set of instructions was derived from the 1.11.1 version
submitted by Lui Gee on 15 Jul 2004.  It includes many bug fixes for
that release and also extends the functionality to product search
results.

I did not complete extensive testing, but I believe that this
version does implement up to eight price breaks correctly for common
use cases.

This version is NOT a patch to Lui Gee's version -- these
instructions assume that you are starting with a base osCommerce MS
2.2 distribution.

====================================================================
= As always, backup your files and database first. =
====================================================================

====================================================================
Use your favorite MySQL tool to add the following to the database (or use the included file price-break_v1_2_0.sql):
//Deutsch: Folgende Werte in euere Datenbak schreiben:

alter table products add column products_price1 decimal(15,4) not null default 0.0;
alter table products add column products_price2 decimal(15,4) not null default 0.0;
alter table products add column products_price3 decimal(15,4) not null default 0.0;
alter table products add column products_price4 decimal(15,4) not null default 0.0;
alter table products add column products_price5 decimal(15,4) not null default 0.0;
alter table products add column products_price6 decimal(15,4) not null default 0.0;
alter table products add column products_price7 decimal(15,4) not null default 0.0;
alter table products add column products_price8 decimal(15,4) not null default 0.0;
alter table products add column products_price1_qty int not null default 0;
alter table products add column products_price2_qty int not null default 0;
alter table products add column products_price3_qty int not null default 0;
alter table products add column products_price4_qty int not null default 0;
alter table products add column products_price5_qty int not null default 0;
alter table products add column products_price6_qty int not null default 0;
alter table products add column products_price7_qty int not null default 0;
alter table products add column products_price8_qty int not null default 0;
alter table products add column products_qty_blocks int not null default 1;


====================================================================
Upload the following file:
//Deutsch: Folgende Datei hochladen:

catalog/includes/classes/PriceFormatter.php
catalog/includes/classes/PriceFormatterStore.php

====================================================================
Use the included install.html to edit the following files (or use as supplied):
//Deutsch: Folgende Dateien anhand der install.html abändern

catalog/admin/categories.php
catalog/admin/includes/languages/english/categories.php
catalog/admin/includes/stylesheet.css
catalog/advanced_search_result.php
catalog/includes/application_top.php
catalog/includes/classes/shopping_cart.php
catalog/includes/languages/english/product_info.php
catalog/includes/modules/product_listing.php
catalog/index.php
catalog/product_info.php
catalog/stylesheet.css

====================================================================
After applying the patches, be sure to test both the storefront and
the administrative area (particularly, the product edit page).
