<?php
/*
   for Quantity Price Breaks Per Product, price break per discount category v1.0 2007/12/02
*/
  
define('HEADING_TITLE', 'Kortingscategorie');
define('HEADING_TITLE_PRODUCTS_TO_DISCOUNT_CATEGORIES', 'Producten aan kortingscategorieën');
define('HEADING_TITLE_SEARCH', 'zoek:');
define('HEADING_TITLE_NEW_DISCOUNT_CATEGORIES_NAME', '<b>Nieuwe korting&#160;categorie&#160;</b>');

define('TABLE_HEADING_NAME', 'Naam');
define('TABLE_HEADING_ID', 'dcID');
define('TABLE_HEADING_PRODUCTS_ID', 'productID');
define('TABLE_HEADING_MODEL', 'Model');
define('TABLE_HEADING_STATUS', 'Status');
define('TABLE_HEADING_DISCOUNT_CATEGORY', 'Kortingscategorie');
define('TABLE_HEADING_DISCOUNT_CATEGORIES_NAME', '/ <b>Naam</b>');
define('TABLE_HEADING_NEW_DISCOUNT_CATEGORIES_NAME', ' <b>Naam</b>');
define('TABLE_HEADING_DISCOUNT_CATEGORIES_ID', '<b>ID</b>');
define('TABLE_HEADING_ACTION', 'Actie');

define('ENTRY_DISCOUNT_CATEGORIES_NAME', '<b>Korting&#160;categorie&#160;naam</b>');
define('ENTRY_NEW_DISCOUNT_CATEGORIES_NAME', '<b>Nieuwe korting&#160;categorie&#160;</b>');
define('ENTRY_DISCOUNT_CATEGORIES_NAME_MAX_LENGTH', ' (max. lengte: 255 karakters)');

define('ERROR_DISCOUNT_CATEGORIES_NAME', 'Voer een naam in voor de kortingscategorie');
define('ERROR_DISCOUNT_CATEGORIES_ID', 'Geen kortingscategorie ID is gevonden voor deze actie');
define('ERROR_UPDATE_INSERT_DISCOUNT_CATEGORY', 'Een fout is opgetreden bij het updaten of toevoegen in de tabel kortingscategorieën');

define('TEXT_DELETE_INTRO', 'Bent u er zeker van dat deze kortingscategorie verwijderd wordt?');
define('TEXT_DISPLAY_NUMBER_OF_DISCOUNT_CATEGORIES', 'Tonen <b>%d</b> to <b>%d</b> (of <b>%d</b> kortingscategorieën)');
define('TEXT_INFO_HEADING_DELETE_DISCOUNT_CATEGORY', 'Verwijder kortingscategorie');
define('TEXT_NO_PRODUCTS','Geen producten gevonden');
define('TEXT_IMAGE_SWITCH_EDIT','Ga naar categorieën.php voor complete aanpassing');
define('SORT_BY_PRODUCTS_ID_ASC', 'Sorteer oplopend op product ID  --> 1-2-3 van bovenaf ');
define('SORT_BY_PRODUCTS_ID_DESC', 'Sorteer aflopend op product ID  --> 3-2-1 van bovenaf ');
define('SORT_BY_PRODUCTS_MODEL_ASC', 'Sorteer oplopend op productmodel  --> a-b-c van bovenaf ');
define('SORT_BY_PRODUCTS_MODEL_DESC', 'Sorteer aflopen op productmodel  --> z-y-x van bovenaf ');
define('SORT_BY_PRODUCTS_NAME_ASC', 'Sorteer oplopend op productnaam  --> a-b-c van bovenaf ');
define('SORT_BY_PRODUCTS_NAME_DESC', 'Sorteer aflopend op productnaam  --> z-y-x van bovenaf ');


define('TEXT_MAXI_ROW_BY_PAGE', 'Max. # rijen per pagina');
define('TEXT_ALL_MANUFACTURERS', 'Alle producenten');
define('TEXT_ALL_DISCOUNT_CATEGORIES', 'Alle kortingscategorieën');
define('TEXT_BACK_TO_DISCOUNT_CATEGORIES','Terug naar kortingscategorieën');
define('DISPLAY_CATEGORIES', 'Selecteer categorie:');
define('DISPLAY_MANUFACTURERS', 'Selecteer producent:'); 
define('DISPLAY_DISCOUNT_CATEGORIES', 'Selecteer kortingscategorie:');

define('DC_ICON_STATUS_RED_LIGHT', 'Inactief');
define('DC_ICON_STATUS_GREEN_LIGHT', 'Actief');
?>