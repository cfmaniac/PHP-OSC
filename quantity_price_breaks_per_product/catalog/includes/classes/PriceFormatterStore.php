<?php
/* $Id: PriceFormatterStore.php v 1.2 2008/05/03
   an object to store the price breaks and products_quantity of a product once queried by the 
   class PriceFormatter.php to avoid it being queried more than once and tep_get_stock to be executed
   for each product on the page shopping_cart.php
   
   osCommerce, Open Source E-Commerce Solutions
   http://www.oscommerce.com

   Copyright (c) 2006 osCommerce

   Released under the GNU General Public License
*/

class PriceFormatterStore {
  var $priceFormatterData = array();
  
  function PriceFormatterStore() {
    global $cart, $languages_id;

		if (is_object($cart)) {
			$product_id_list = $cart->get_product_id_list();
			if (tep_not_null($product_id_list)) {
				// get rid of attributes first
				$product_id_list_array = array();
				$product_id_list_temp_array = explode(",", $product_id_list);
				foreach ($product_id_list_temp_array as $key => $value) {
          // only add valid values: issue with the first value in the product id list
          // being empty which gave an error in the next query [e.g. products_id in (,52,48)]
          // on checkout
          $valid_value = tep_get_prid($value);
          if (tep_not_null($valid_value)) {
					  $product_id_list_array[] = $valid_value;
          }
				}
				$product_id_list_array = array_unique($product_id_list_array);
				unset($product_id_list);
				$product_id_list = implode(",", $product_id_list_array);
				// now do one query for all products in the shopping basket
   $sql = "select pd.products_name, p.products_model, p.products_image, p.products_id," .
   " p.manufacturers_id, p.products_price, p.products_weight, p.products_quantity," .
   " p.products_qty_blocks, p.products_tax_class_id," .
   " IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price," .
   " ptdc.discount_categories_id from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on " .
   " p.products_id = s.products_id left join " . TABLE_PRODUCTS_TO_DISCOUNT_CATEGORIES . " ptdc on " .
   " p.products_id = ptdc.products_id, " .
   " " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1'" .
   " and pd.products_id = p.products_id " .
   " and p.products_id in (" . $product_id_list . ")" .
   " and pd.language_id = '". (int)$languages_id ."'";

				$product_info_query = tep_db_query($sql);
			  while ($product_info = tep_db_fetch_array($product_info_query)) {					  
				  $this->addPriceFormatterData($product_info['products_id'], $product_info);
				}
				$price_breaks_query = tep_db_query("select products_id, products_price, products_qty from  " . TABLE_PRODUCTS_PRICE_BREAK . " where products_id in (" . $product_id_list . ") order by products_id, products_qty");
        while ($price_break = tep_db_fetch_array($price_breaks_query)) {
          $price_breaks_array[$price_break['products_id']][] = array('products_price' => $price_break['products_price'], 'products_qty' => $price_break['products_qty']);
        }
        $no_of_pricebreaks = count($price_breaks_array);
        if ($no_of_pricebreaks > 0) {
          foreach ($this->priceFormatterData as $products_id => $price_break_array) {
            foreach ($price_breaks_array as $pb_products_id => $pb_price_break) {
              if ($pb_products_id == $products_id) {
                $this->priceFormatterData[$products_id]['price_breaks'] = $pb_price_break;
              }
            }
          } // end foreach ($this->priceFormatterData as $products_id etc.
        } // end if ($no_of_pricebreaks > 0)
			} // end if tep_not_null($product_id_list)
		} // end if (is_object($cart)
  }

  function addPriceFormatterData($products_id, $price_formatter_data) {
    $this->priceFormatterData[$products_id] = $price_formatter_data;
  }
  
  function getPriceFormatterData($product_id) {
    $products_id = tep_get_prid($product_id);
    if(isset($this->priceFormatterData[$products_id]) && tep_not_null($this->priceFormatterData[$products_id])) {
      return $this->priceFormatterData[$products_id];
    }	else {
      return NULL;
    }
  }

  function getStock($product_id) {
		$products_id = tep_get_prid($product_id);
		if(isset($this->priceFormatterData[$products_id]) && tep_not_null($this->priceFormatterData[$products_id])) {
			return $this->priceFormatterData[$products_id]['products_quantity'];
			}	else {
				return false;
		}
	}
}
?>
