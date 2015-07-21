<?php
/*
  $Id: categories.php,v 1.26 2003/07/11 14:40:28 hpdl Exp $

  DUTCH TRANSLATION
  - V2.2 ms1: Author: Joost Billiet   Date: 06/18/2003   Mail: joost@jbpc.be
  - V2.2 ms2: Update: Martijn Loots   Date: 08/01/2003   Mail: oscommerce@cosix.com

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Categorie&euml;n / Artikelen');
define('HEADING_TITLE_SEARCH', 'Zoeken:');
define('HEADING_TITLE_GOTO', 'Ga naar:');

define('TABLE_HEADING_ID', 'ID');
define('TABLE_HEADING_CATEGORIES_PRODUCTS', 'Categorie&euml;n / Artikelen');
define('TABLE_HEADING_ACTION', 'Actie');
define('TABLE_HEADING_STATUS', 'Status');

define('TEXT_NEW_PRODUCT', 'Nieuw artikel\'%s\'');
define('TEXT_CATEGORIES', 'Categorie&euml;n:');
define('TEXT_SUBCATEGORIES', 'Subcategorie&euml;n:');
define('TEXT_PRODUCTS', 'Producten:');
define('TEXT_PRODUCTS_PRICE_INFO', 'Prijs:');
define('TEXT_PRODUCTS_TAX_CLASS', 'BTW tariefgroep:');
define('TEXT_PRODUCTS_AVERAGE_RATING', 'Gemiddelde waarde:');
define('TEXT_PRODUCTS_QUANTITY_INFO', 'Hoeveelheid:');
define('TEXT_DATE_ADDED', 'Datum toegevoegd:');
define('TEXT_DATE_AVAILABLE', 'Wordt verwacht:');
define('TEXT_LAST_MODIFIED', 'Laatst gewijzigd:');
define('TEXT_IMAGE_NONEXISTENT', 'PLAATJE BESTAAT NIET');
define('TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS', 'Gelieve een nieuw artikel of nieuwe categorie toe te voegen in<br>&nbsp;<br><b>%s</b>');
define('TEXT_PRODUCT_MORE_INFORMATION', 'Voor meer informatie, ga naar dit artikel <a href="http://%s" target="blank"><u>webpagina</u></a>.');
define('TEXT_PRODUCT_DATE_ADDED', 'Dit artikel is toegevoegd aan onze catalogus %s.');
define('TEXT_PRODUCT_DATE_AVAILABLE', 'Dit artikel zal op %s in voorraad zijn.');

define('TEXT_EDIT_INTRO', 'Gelieve de nodige aanpassingen door te voeren');
define('TEXT_EDIT_CATEGORIES_ID', 'Categorie ID:');
define('TEXT_EDIT_CATEGORIES_NAME', 'Naam van categorie:');
define('TEXT_EDIT_CATEGORIES_IMAGE', 'Categorie plaatje:');
define('TEXT_EDIT_SORT_ORDER', 'Sorteervolgorde:');

define('TEXT_INFO_COPY_TO_INTRO', 'Kies een categorie waar u het product naartoe wil kopie&euml;ren');
define('TEXT_INFO_CURRENT_CATEGORIES', 'Huidige categorie&euml;n:');

define('TEXT_INFO_HEADING_NEW_CATEGORY', 'Nieuwe categorie');
define('TEXT_INFO_HEADING_EDIT_CATEGORY', 'Wijzig categorie');
define('TEXT_INFO_HEADING_DELETE_CATEGORY', 'Verwijder categorie');
define('TEXT_INFO_HEADING_MOVE_CATEGORY', 'Verplaats categorie');
define('TEXT_INFO_HEADING_DELETE_PRODUCT', 'Verwijder artikel');
define('TEXT_INFO_HEADING_MOVE_PRODUCT', 'Verplaats artikel');
define('TEXT_INFO_HEADING_COPY_TO', 'Kopie&euml;r naar');

define('TEXT_DELETE_CATEGORY_INTRO', 'Weet u zeker dat je deze categorie wil verwijderen?');
define('TEXT_DELETE_PRODUCT_INTRO', 'Weet u zeker dat je dit product wil verwijderen?');

define('TEXT_DELETE_WARNING_CHILDS', '<b>WAARSCHUWING:</b> Er zijn %s (afgeleide ?) categorie&euml;n gelinked aan deze categorie!');
define('TEXT_DELETE_WARNING_PRODUCTS', '<b>WAARSCHUWING:</b> Er zijn %s producten gelinked aan deze categorie!');

define('TEXT_MOVE_PRODUCTS_INTRO', 'Gelieve de categorie te selecteren waar u <b>%s</b> naar wil verplaatsen');
define('TEXT_MOVE_CATEGORIES_INTRO', 'Gelieve de categorie te selecteren waar u <b>%s</b> naar wil verplaatsen');
define('TEXT_MOVE', 'Verplaats <b>%s</b> Naar:');

define('TEXT_NEW_CATEGORY_INTRO', 'Gelieve de volgende informatie in te vullen voor de nieuwe categorie');
define('TEXT_CATEGORIES_NAME', 'Naam van categorie:');
define('TEXT_CATEGORIES_IMAGE', 'Categorie plaatje');
define('TEXT_SORT_ORDER', 'Sorteervolgorde:');

define('TEXT_PRODUCTS_STATUS', 'Product status:');
define('TEXT_PRODUCTS_DATE_AVAILABLE', 'Verwacht op:');
define('TEXT_PRODUCT_AVAILABLE', 'Op voorraad');
define('TEXT_PRODUCT_NOT_AVAILABLE', 'Niet op voorraad');
define('TEXT_PRODUCTS_MANUFACTURER', 'Fabrikant:');
define('TEXT_PRODUCTS_NAME', 'Artikel naam:');
define('TEXT_PRODUCTS_DESCRIPTION', 'Artikel omschrijving:');
define('TEXT_PRODUCTS_QUANTITY', 'Artikel hoeveelheid:');
define('TEXT_PRODUCTS_MODEL', 'Artikel model:');
define('TEXT_PRODUCTS_URL', 'Artikel URL:');
define('TEXT_PRODUCTS_URL_WITHOUT_HTTP', '<small>(zonder http://)</small>');
define('TEXT_PRODUCTS_PRICE_NET', 'Prijs van artikel (Netto):');
define('TEXT_PRODUCTS_PRICE_GROSS', 'Prijs van artikel (Bruto):');
define('TEXT_PRODUCTS_WEIGHT', 'Gewicht van artikel (gram):');

define('EMPTY_CATEGORY', 'Lege categorie');

define('TEXT_HOW_TO_COPY', 'Kopie&euml;r methode:');
define('TEXT_COPY_AS_LINK', 'Link artikel');
define('TEXT_COPY_AS_DUPLICATE', 'Kloon artikel');

define('ERROR_CANNOT_LINK_TO_SAME_CATEGORY', 'Fout: Kan producten in dezelfde categorie niet linken.');
define('ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE', 'Fout: Catalogus images directory niet schrijfbaar: ' . DIR_FS_CATALOG_IMAGES);
define('ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST', 'Fout: Catalogus images directory bestaat niet: ' . DIR_FS_CATALOG_IMAGES);
define('ERROR_CANNOT_MOVE_CATEGORY_TO_PARENT', 'Fout: Categorie kan niet worden verplaatst naar een afgeleide categorie.');

// BOF qpbpp
define('TEXT_PRODUCTS_QTY_BLOCKS', 'Bundel aantal:');
define('TEXT_PRODUCTS_QTY_BLOCKS_HELP', '(een klant kan alleen in hele bundel aantallen bestellen, bv 4,8,12,16,...)');
define('TEXT_PRODUCTS_PRICE', 'Prijs niveau');
define('TEXT_PRODUCTS_QTY', 'Vanaf aantal');
define('TEXT_PRODUCTS_DELETE', 'Verwijderen');
define('TEXT_ENTER_QUANTITY', 'Aantal');
define('TEXT_PRICE_PER_PIECE', 'Prijs per stuk');
define('TEXT_SAVINGS', 'Uw voordeel');
define('TEXT_DISCOUNT_CATEGORY', 'Korting categorie:');
define('ERROR_UPDATE_INSERT_DISCOUNT_CATEGORY', 'Probleem tijdens update van tabel discount_categories');
// EOF qpbpp

?>