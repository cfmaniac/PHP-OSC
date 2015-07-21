<?php
/*
  $Id: dutch.php,v 1.1061 2005/01/21 00:18:31 gjw Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

// look in your $PATH_LOCALE/locale directory for available locales..
// on RedHat6.0 I used 'de_DE'
// on FreeBSD 4.0 I use 'de_DE.ISO_8859-1'
// this may not work under win32 environments..
// setlocale(LC_TIME, 'nl_NL.ISO_8859-1');
// @setlocale (LC_TIME, 'Dutch');
@setlocale(LC_TIME, 'nl_NL.ISO_8859-1' , 'Dutch');
@setlocale (LC_TIME, 'Dutch');

define('DATE_FORMAT_SHORT', '%d.%m.%Y');       // this is used for strftime()
define('DATE_FORMAT_LONG', '%A, %d. %B %Y');   // this is used for strftime()
define('DATE_FORMAT', 'd.m.Y');                // this is used for strftime()
define('PHP_DATE_TIME_FORMAT', 'd.m.Y H:i:s'); // this is used for date()
define('DATE_TIME_FORMAT', DATE_FORMAT_SHORT . ' %H:%M:%S');

////
// Return date in raw format
// $date should be in format mm/dd/yyyy
// raw date is in format YYYYMMDD, or DDMMYYYY
function tep_date_raw($date, $reverse = false) {
  if ($reverse) {
    return substr($date, 0, 2) . substr($date, 3, 2) . substr($date, 6, 4);
  } else {
    return substr($date, 6, 4) . substr($date, 3, 2) . substr($date, 0, 2);
  }
}

// Global entries for the <html> tag
define('HTML_PARAMS','dir="ltr" lang="nl"');

// charset for web pages and emails
define('CHARSET', 'iso-8859-1');

// page title
define('TITLE', 'Verander');
define('TITLE', 'naar behoeven deze en bovenstaande regel');

// header text in includes/header.php
define('HEADER_TITLE_TOP', 'Administratie');
define('HEADER_TITLE_SUPPORT_SITE', 'Support website');
define('HEADER_TITLE_ONLINE_CATALOG', 'Online catalogus');
define('HEADER_TITLE_ADMINISTRATION', 'Administratie');

// text for gender
define('MALE', 'Man');
define('FEMALE', 'Vrouw');

// text for date of birth example
define('DOB_FORMAT_STRING', 'dd/mm/jjjj');

// configuration box text in includes/boxes/configuration.php
define('BOX_HEADING_CONFIGURATION', 'Configuratie');
define('BOX_CONFIGURATION_MYSTORE', 'Mijn winkel');
define('BOX_CONFIGURATION_LOGGING', 'Logging');
define('BOX_CONFIGURATION_CACHE', 'Cache');
define('BOX_CONFIGURATION_ADMINISTRATORS', 'Beheerders');

// modules box text in includes/boxes/modules.php
define('BOX_HEADING_MODULES', 'Modules');
define('BOX_MODULES_PAYMENT', 'Betaling');
define('BOX_MODULES_SHIPPING', 'Verzenden');
define('BOX_MODULES_ORDER_TOTAL', 'Totaal bestelling');

// categories box text in includes/boxes/catalog.php
define('BOX_HEADING_CATALOG', 'Catalogus');
define('BOX_CATALOG_CATEGORIES_PRODUCTS', 'Categorieën / Artikelen');
// BOF qpbpp
define('BOX_CATALOG_CATEGORIES_DISCOUNT_CATEGORIES', 'Kortings Categorieën');
// EOF qpbpp
define('BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES', 'Artikelopties');
define('BOX_CATALOG_MANUFACTURERS', 'Fabrikanten');
define('BOX_CATALOG_REVIEWS', 'Recensies');
define('BOX_CATALOG_SPECIALS', 'Speciale aanbiedingen');
define('BOX_CATALOG_PRODUCTS_EXPECTED', 'Verwachte producten');

// customers box text in includes/boxes/customers.php
define('BOX_HEADING_CUSTOMERS', 'Klanten');
define('BOX_CUSTOMERS_CUSTOMERS', 'Klanten');
define('BOX_CUSTOMERS_ORDERS', 'Orders');

// taxes box text in includes/boxes/taxes.php
define('BOX_HEADING_LOCATION_AND_TAXES', 'Locatie / BTW');
define('BOX_TAXES_COUNTRIES', 'Landen');
define('BOX_TAXES_ZONES', 'Zones');
define('BOX_TAXES_GEO_ZONES', 'Zones');
define('BOX_TAXES_TAX_CLASSES', 'BTW tariefgroepen');
define('BOX_TAXES_TAX_RATES', 'BTW tarieven');

// reports box text in includes/boxes/reports.php
define('BOX_HEADING_REPORTS', 'Rapporten');
define('BOX_REPORTS_PRODUCTS_VIEWED', 'Bekeken artikelen');
define('BOX_REPORTS_PRODUCTS_PURCHASED', 'Gekochte artikelen');
define('BOX_REPORTS_ORDERS_TOTAL', 'Totaalbedrag klantenorders');

// tools text in includes/boxes/tools.php
define('BOX_HEADING_TOOLS', 'Gereedschap');
define('BOX_TOOLS_BACKUP', 'Database backup-manager');
define('BOX_TOOLS_BANNER_MANAGER', 'Banner-manager');
define('BOX_TOOLS_CACHE', 'Cache-manager');
define('BOX_TOOLS_DEFINE_LANGUAGE', 'Talen-manager');
define('BOX_TOOLS_FILE_MANAGER', 'File-manager');
define('BOX_TOOLS_MAIL', 'Stuur Email');
define('BOX_TOOLS_NEWSLETTER_MANAGER', 'Nieuwsbrief-manager');
define('BOX_TOOLS_SERVER_INFO', 'Server info');
define('BOX_TOOLS_WHOS_ONLINE', 'Wie is er online?');

// localizaion box text in includes/boxes/localization.php
define('BOX_HEADING_LOCALIZATION', 'Lokalisatie');
define('BOX_LOCALIZATION_CURRENCIES', 'Valuta');
define('BOX_LOCALIZATION_LANGUAGES', 'Talen');
define('BOX_LOCALIZATION_ORDERS_STATUS', 'Order status');

// javascript messages
define('JS_ERROR', 'Er zijn fouten opgetreden tijdens het verwerken van uw formulier!\nBreng de volgende wijzigingen aan:\n\n');

define('JS_OPTIONS_VALUE_PRICE', '* U dient het nieuwe artikel een prijs te geven\n');
define('JS_OPTIONS_VALUE_PRICE_PREFIX', '* U dient de nieuwe artikelprijs een voorvoegsel te geven\n');

define('JS_PRODUCTS_NAME', '* Voer een artikelnaam in\n');
define('JS_PRODUCTS_DESCRIPTION', '* Voer een artikelomschrijving in\n');
define('JS_PRODUCTS_PRICE', '* Voer een prijs in\n');
define('JS_PRODUCTS_WEIGHT', '* Voer een gewicht in\n');
define('JS_PRODUCTS_QUANTITY', '* Voer een aantal in\n');
define('JS_PRODUCTS_MODEL', '* Voer een model in\n');
define('JS_PRODUCTS_IMAGE', '* Voer een afbeelding in\n');

define('JS_SPECIALS_PRODUCTS_PRICE', '* Er dient een nieuwe prijs voor dit artikel te worden ingevoerd\n');

define('JS_GENDER', '* Het \'geslacht\' dient te worden gekozen.\n');
define('JS_FIRST_NAME', '* De  \'voornaam\' dient minstens ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' tekens te bevatten.\n');
define('JS_LAST_NAME', '* De \'achternaam\' dient minstens ' . ENTRY_LAST_NAME_MIN_LENGTH . ' tekens te bevatten.\n');
define('JS_DOB', '* De  \'geboortedatum\' dient in het volgende formaat te zijn: xx/xx/xxxx (dag/maand/jaar).\n');
define('JS_EMAIL_ADDRESS', '* Het \'Emailadres\' dient minstens ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' tekens te bevatten.\n');
define('JS_ADDRESS', '* Het \'adres\' dient minstens ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' tekens te bevatten.\n');
define('JS_POST_CODE', '* De \'postcode\' dient minstens ' . ENTRY_POSTCODE_MIN_LENGTH . ' tekens te bevatten.\n');
define('JS_CITY', '* De \'plaats\' dient minstens ' . ENTRY_CITY_MIN_LENGTH . ' tekens te bevatten.\n');
define('JS_STATE', '* De \'provincie\' dient geselecteerd te zijn\n');
define('JS_STATE_SELECT', '-- Selecteer --');
define('JS_ZONE', '* De \'provincie\' dient geselecteerd te zijn.');
define('JS_COUNTRY', '* Het \'land\' dient geselecteerd te zijn.\n');
define('JS_TELEPHONE', '* Het \'telefoonnummer\' dient minstens ' . ENTRY_TELEPHONE_MIN_LENGTH . ' tekens te bevatten.\n');
define('JS_PASSWORD', '* De velden \'wachtwoord\' en \'bevestiging\' moeten exact gelijk zijn en minimaal ' . ENTRY_PASSWORD_MIN_LENGTH . ' tekens bevatten.\n');
define('JS_ORDER_DOES_NOT_EXIST', 'Bestelnummer %s komt niet voor in de database!');

define('CATEGORY_PERSONAL', 'Persoonlijk');
define('CATEGORY_ADDRESS', 'Adres');
define('CATEGORY_CONTACT', 'Contactpersoon');
define('CATEGORY_COMPANY', 'Bedrijfsnaam');
define('CATEGORY_OPTIONS', 'Opties');

define('ENTRY_GENDER', 'Geslacht:');
define('ENTRY_GENDER_ERROR', '&nbsp;<span class="errorText">verplicht</span>');
define('ENTRY_FIRST_NAME', 'Voornaam:');
define('ENTRY_FIRST_NAME_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_FIRST_NAME_MIN_LENGTH . ' tekens</span>');
define('ENTRY_LAST_NAME', 'Achternaam:');
define('ENTRY_LAST_NAME_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_LAST_NAME_MIN_LENGTH . ' tekens</span>');
define('ENTRY_DATE_OF_BIRTH', 'Geboortedatum:');
define('ENTRY_DATE_OF_BIRTH_ERROR', '&nbsp;<span class="errorText">(bijv. 21/05/1970)</span>');
define('ENTRY_EMAIL_ADDRESS', 'Emailadres:');
define('ENTRY_EMAIL_ADDRESS_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_EMAIL_ADDRESS_MIN_LENGTH . ' tekens</span>');
define('ENTRY_EMAIL_ADDRESS_CHECK_ERROR', '&nbsp;<span class="errorText">Het emailadres lijkt ongeldig te zijn!</span>');
define('ENTRY_EMAIL_ADDRESS_ERROR_EXISTS', '&nbsp;<span class="errorText">Dit emailadres bestaat al!</span>');
define('ENTRY_COMPANY', 'Bedrijfsnaam');
define('ENTRY_COMPANY_ERROR', '');
define('ENTRY_STREET_ADDRESS', 'Adres:');
define('ENTRY_STREET_ADDRESS_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_STREET_ADDRESS_MIN_LENGTH . ' tekens</span>');
define('ENTRY_SUBURB', 'Wijk:');
define('ENTRY_SUBURB_ERROR', '');
define('ENTRY_POST_CODE', 'Postcode:');
define('ENTRY_POST_CODE_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_POSTCODE_MIN_LENGTH . ' tekens</span>');
define('ENTRY_CITY', 'Woonplaats:');
define('ENTRY_CITY_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_CITY_MIN_LENGTH . ' tekens</span>');
define('ENTRY_STATE', 'Provincie:');
define('ENTRY_STATE_ERROR', '&nbsp;<span class="errorText">verplicht</span>');
define('ENTRY_COUNTRY', 'Land:');
define('ENTRY_COUNTRY_ERROR', '');
define('ENTRY_TELEPHONE_NUMBER', 'Telefoonnummer:');
define('ENTRY_TELEPHONE_NUMBER_ERROR', '&nbsp;<span class="errorText">min ' . ENTRY_TELEPHONE_MIN_LENGTH . ' tekens</span>');
define('ENTRY_FAX_NUMBER', 'Faxnummer:');
define('ENTRY_FAX_NUMBER_ERROR', '');
define('ENTRY_NEWSLETTER', 'Nieuwsbrief:');
define('ENTRY_NEWSLETTER_YES', 'Wel geabonneerd');
define('ENTRY_NEWSLETTER_NO', 'Niet geabonneerd');
define('ENTRY_NEWSLETTER_ERROR', '');

// images
define('IMAGE_ANI_SEND_EMAIL', 'Email wordt verzonden');
define('IMAGE_BACK', 'Terug');
define('IMAGE_BACKUP', 'Backup');
define('IMAGE_CANCEL', 'Annuleer');
define('IMAGE_CONFIRM', 'Bevestig');
define('IMAGE_COPY', 'Kopieer');
define('IMAGE_COPY_TO', 'Kopieer naar');
define('IMAGE_DETAILS', 'Details');
define('IMAGE_DELETE', 'Verwijder');
define('IMAGE_EDIT', 'Wijzigen');
define('IMAGE_EMAIL', 'Email');
define('IMAGE_FILE_MANAGER', 'File-manager');
define('IMAGE_ICON_STATUS_GREEN', 'Actief');
define('IMAGE_ICON_STATUS_GREEN_LIGHT', 'Activeren');
define('IMAGE_ICON_STATUS_RED', 'Niet actief');
define('IMAGE_ICON_STATUS_RED_LIGHT', 'Deactiveren');
define('IMAGE_ICON_INFO', 'Informatie');
define('IMAGE_INSERT', 'Invoegen');
define('IMAGE_LOCK', 'Vastzetten');
define('IMAGE_MODULE_INSTALL', 'Module installeren');
define('IMAGE_MODULE_REMOVE', 'Module verwijderen');
define('IMAGE_MOVE', 'Verplaatsen');
define('IMAGE_NEW_BANNER', 'Nieuwe banner');
define('IMAGE_NEW_CATEGORY', 'Nieuwe categorie');
define('IMAGE_NEW_COUNTRY', 'Nieuw land');
define('IMAGE_NEW_CURRENCY', 'Nieuwe valuta');
define('IMAGE_NEW_FILE', 'Nieuw bestand');
define('IMAGE_NEW_FOLDER', 'Nieuwe directorie');
define('IMAGE_NEW_LANGUAGE', 'Nieuwe taal');
define('IMAGE_NEW_NEWSLETTER', 'Nieuwe nieuwsbrief');
define('IMAGE_NEW_PRODUCT', 'Nieuw artikel');
define('IMAGE_NEW_TAX_CLASS', 'Nieuwe BTW tariefgroep');
define('IMAGE_NEW_TAX_RATE', 'Nieuw BTW tarief');
define('IMAGE_NEW_TAX_ZONE', 'Nieuwe BTW tariefzone');
define('IMAGE_NEW_ZONE', 'Nieuwe zone');
define('IMAGE_ORDERS', 'Orders');
define('IMAGE_ORDERS_INVOICE', 'Factuur');
define('IMAGE_ORDERS_PACKINGSLIP', 'Pakbon');
define('IMAGE_PREVIEW', 'Voorbeeld');
define('IMAGE_RESTORE', 'Herstel');
define('IMAGE_RESET', 'Reset');

define('IMAGE_SAVE', 'Opslaan');
define('IMAGE_SEARCH', 'Zoeken');
define('IMAGE_SELECT', 'Selecteer');
define('IMAGE_SEND', 'Verstuur');
define('IMAGE_SEND_EMAIL', 'Stuur email');
define('IMAGE_UNLOCK', 'Vrijgeven');
define('IMAGE_UPDATE', 'Bijwerken');
define('IMAGE_UPDATE_CURRENCIES', 'Wisselkoersen bijwerken');
define('IMAGE_UPLOAD', 'Upload');
// BOF qpbpp
define('IMAGE_SHOW_PRODUCTS', 'Geef producten weer');
// EOF qpbpp

define('ICON_CROSS', 'Niet waar');
define('ICON_CURRENT_FOLDER', 'Huidige directorie');
define('ICON_DELETE', 'Verwijder');
define('ICON_ERROR', 'Fout');
define('ICON_FILE', 'Bestand');
define('ICON_FILE_DOWNLOAD', 'Download');
define('ICON_FOLDER', 'Map');
define('ICON_LOCKED', 'Vastgezet');
define('ICON_PREVIOUS_LEVEL', 'Vorige directorie');
define('ICON_PREVIEW', 'Voorbeeld');
define('ICON_STATISTICS', 'Statistieken');
define('ICON_SUCCESS', 'Succes');
define('ICON_TICK', 'Waar');
define('ICON_UNLOCKED', 'Vrijgegeven');
define('ICON_WARNING', 'Waarschuwing');

// constants for use in tep_prev_next_display function
define('TEXT_RESULT_PAGE', 'Pagina %s van %d');
define('TEXT_DISPLAY_NUMBER_OF_BANNERS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> banners)');
define('TEXT_DISPLAY_NUMBER_OF_COUNTRIES', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> landen)');
define('TEXT_DISPLAY_NUMBER_OF_CUSTOMERS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> klanten)');
define('TEXT_DISPLAY_NUMBER_OF_CURRENCIES', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> valuta)');
define('TEXT_DISPLAY_NUMBER_OF_LANGUAGES', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> talen)');
define('TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> fabrikanten)');
define('TEXT_DISPLAY_NUMBER_OF_NEWSLETTERS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> nieuwsbrieven)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> bestellingen)');
define('TEXT_DISPLAY_NUMBER_OF_ORDERS_STATUS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> order statussen)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> artikelen)');
define('TEXT_DISPLAY_NUMBER_OF_PRODUCTS_EXPECTED', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> verwachte producten)');
define('TEXT_DISPLAY_NUMBER_OF_REVIEWS', 'Vertoont <b>%d</b> tot <b>%d</b> (vanf <b>%d</b> artikelrecensies)');
define('TEXT_DISPLAY_NUMBER_OF_SPECIALS', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> speciale aanbiedingen)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_CLASSES', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> BTW tariefgroepen)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_ZONES', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> BTW tariefzones)');
define('TEXT_DISPLAY_NUMBER_OF_TAX_RATES', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> BTW tarieven)');
define('TEXT_DISPLAY_NUMBER_OF_ZONES', 'Vertoont <b>%d</b> tot <b>%d</b> (van <b>%d</b> zones)');

define('PREVNEXT_BUTTON_PREV', '&lt;&lt;');
define('PREVNEXT_BUTTON_NEXT', '&gt;&gt;');

define('TEXT_DEFAULT', 'standaard');
define('TEXT_SET_DEFAULT', 'Als standaard defini&euml;ren');
define('TEXT_FIELD_REQUIRED', '&nbsp;<span class="fieldRequired">* Verplicht</span>');
define('ERROR_NO_DEFAULT_CURRENCY_DEFINED', 'Fout: Er is geen standaard valuta aanwezig. Definieer deze s.v.p.: Administratie gereedschap -&gt; Lokalisatie -&gt; Valuta');

define('TEXT_CACHE_CATEGORIES', 'Categori&euml;n box');
define('TEXT_CACHE_MANUFACTURERS', 'Fabrikanten box');
define('TEXT_CACHE_ALSO_PURCHASED', 'Eveneens gekocht module');

define('TEXT_NONE', '--geen--');
define('TEXT_TOP', 'Top');

define('ERROR_DESTINATION_DOES_NOT_EXIST', 'Fout: Bestemming bestaat niet.');
define('ERROR_DESTINATION_NOT_WRITEABLE', 'Fout: bestemming niet schrijfbaar.');
define('ERROR_FILE_NOT_SAVED', 'Fout: Bestand upload niet bewaard.');
define('ERROR_FILETYPE_NOT_ALLOWED', 'Fout: Upload bestand niet toegestaan.');
define('SUCCESS_FILE_SAVED_SUCCESSFULLY', 'Success: Bestand met succes geupload.');
define('WARNING_NO_FILE_UPLOADED', 'Waarschuwing: Geen bestand geupload.');
define('WARNING_FILE_UPLOADS_DISABLED', 'Waarschuwing: Bestanden uploaden niet toegestaan in php.ini configuratie bestand.');

?>