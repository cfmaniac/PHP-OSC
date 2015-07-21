<?php
/*
  $Id: PriceFormatter.php admin version,v 1.7 2006/12/23 JanZ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

/*
    PriceFormatter.php - module to support quantity pricing

    Created 2003, Beezle Software based on some code mods by WasaLab Oy (Thanks!)
    
    Refactored 2008, Moved pricebreak data into dedicated table
*/

class PriceFormatter {

  function PriceFormatter() {
    $this->thePrice = -1;
    $this->taxClass = -1;
    $this->qtyBlocks = 1;
    $this->price_breaks = array();
    $this->hasQuantityPrice = false;  
    $this->hiPrice = -1;
    $this->lowPrice = -1;
    $this->hasSpecialPrice = false; //tep_not_null($this->specialPrice);
    $this->specialPrice = NULL; //$prices['specials_new_products_price'];
  }

  function loadProduct($product_id, $products_price, $products_tax_class_id, $qtyBlocks, $price_breaks_array = NULL)
  {
    //Collect required data
    //Price-breaks
    if (!tep_not_null($price_breaks_array)) {
      $price_breaks_array = array();
      $price_breaks_query = tep_db_query("select products_price, products_qty from " . TABLE_PRODUCTS_PRICE_BREAK . " where products_id = '" . tep_db_input($product_id) . "' order by products_qty");
      while ($price_break = tep_db_fetch_array($price_breaks_query)) {
        $price_breaks_array[] = $price_break;
      }
    }
    
    //Specials
    $products_special_price = NULL;
    $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . tep_db_input($product_id) . "'");
    if($special = tep_db_fetch_array($specials_query)) {
      $products_special_price = $special['specials_new_products_price'];
    }
    
    //Compose cachable structure
    $price_formatter_data = array(
      'products_price' => $products_price,
      'products_special_price' => $products_special_price,
      'products_tax_class_id' => $products_tax_class_id,
      'price_breaks' => $price_breaks_array,
      'qtyBlocks' => $qtyBlocks);
    
    //Assign members
    $this->thePrice = $price_formatter_data['products_price'];
    $this->taxClass = $price_formatter_data['products_tax_class_id'];
    $this->qtyBlocks = $price_formatter_data['qtyBlocks'];
    $this->price_breaks = $price_formatter_data['price_breaks'];
    $this->specialPrice = $price_formatter_data['products_special_price'];
    $this->hasSpecialPrice = tep_not_null($this->specialPrice);

    //Custom      
    $this->hasQuantityPrice = false;
    $this->hiPrice = $this->thePrice;
    $this->lowPrice = $this->thePrice;
    if (count($this->price_breaks) > 0) {
      $this->hasQuantityPrice = true;
      foreach($this->price_breaks as $price_break) {
        $this->hiPrice = max($this->hiPrice, $price_break['products_price']);
        $this->lowPrice = min($this->lowPrice, $price_break['products_price']);
      }
    }

    /*
    Change support special prices
    If any price level has a price greater than the special
    price lower it to the special price
    */
    if (true == $this->hasSpecialPrice) {
      foreach($this->price_breaks as $price_break) {
        $price_break['products_price'] = min($price_break['products_price'], $this->specialPrice);
      }
    }
    //end changes to support special prices
  }
  
  function computePrice($qty, $nof_other_items_in_cart_same_cat = 0)
  {
    $qty = $this->adjustQty($qty);

    // Add the number of other items in the cart from the same category to see if a price break is reached
    $qty += $nof_other_items_in_cart_same_cat;

    // Compute base price, taking into account the possibility of a special
    $price = (true == $this->hasSpecialPrice) ? $this->specialPrice : $this->thePrice;

    foreach($this->price_breaks as $price_break) {
      if($qty >= $price_break['products_qty']) {
        $price = $price_break['products_price'];
      }
    }

    return $price;
  }

  function adjustQty($qty, $qtyBlocks = NULL) {
    // Force QTY_BLOCKS granularity
    if(!tep_not_null($qtyBlocks))
    {
      $qtyBlocks = $this->getQtyBlocks();
    }
    
    if ($qty < 1)
      $qty = 1;

    if ($qtyBlocks >= 1)
    {
      if ($qty < $qtyBlocks)
        $qty = $qtyBlocks;

      if (($qty % $qtyBlocks) != 0)
        $qty += ($qtyBlocks - ($qty % $qtyBlocks));
    }
    return $qty;
  }
  
  function getQtyBlocks() {
    return $this->qtyBlocks;
  }

  function getPrice() {
    return $this->thePrice;
  }

  function getLowPrice() {
    return $this->lowPrice;
  }

  function getHiPrice() {
    return $this->hiPrice;
  }

  function hasSpecialPrice() {
    return $this->hasSpecialPrice;
  }

  function hasQuantityPrice() {
    return $this->hasQuantityPrice;
  }

  function getDiscountSaving($original_price, $discount_price) {
    $difference = $original_price - $discount_price;
    return round (($difference / $original_price) * 100) . '%';
  }

  function getPriceString($style='productPriceInBox') {
    global $currencies;

    // If you want to change the format of the price/quantity table
    // displayed on the product information page, here is where you do it.

    if(true == $this->hasQuantityPrice) {
      $lc_text = '<table border="0" cellspacing="0" cellpadding="0" class="infoBox" align="right">
              <tr valign="top">
              <td>
              <table border="0" cellspacing="1" cellpadding="4" class="infobox">';
      $lc_text .= '<tr valign="top"><td width="120" class="infoBoxHeading">' . TEXT_ENTER_QUANTITY .'</td><td align="center" class="infoBoxHeading">1+'
             . '</td>';

      foreach($this->price_breaks as $price_break) {
        $lc_text .= '<td align="center" width="50" class="infoBoxHeading">'
          . $price_break['products_qty']
          .'+&nbsp;</td>';
      }

      $lc_text .= '<tr valign="top"><td width="120" class="infoBoxContents">' . TEXT_PRICE_PER_PIECE . '</td><td align="center" width="50" class="infoBoxContents">';

      if (true == $this->hasSpecialPrice) {
        $lc_text .= '<s>'
        . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
        . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">'
        . $currencies->display_price($this->specialPrice, tep_get_tax_rate($this->taxClass))
        . '</span>&nbsp;'
        .'</td>';
      } else {
        $lc_text .= ''
        . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
        . '</td>';
      }

      foreach($this->price_breaks as $price_break) {
        $lc_text .= '<td align="center" width="50" class="infoBoxContents">'
          . $currencies->display_price($price_break['products_price'], tep_get_tax_rate($this->taxClass))
          .'</td>';
      }
      $lc_text .= '</tr>';
  
      // Begin saving calculation
      $lc_text .= '<tr valign="top"><td width="120" class="infoBoxContents">' . TEXT_SAVINGS . '</td>';
      if (true == $this->hasSpecialPrice) {
        $lc_text .= '<td align="center" class="infoBoxContents">'
        . $this->getDiscountSaving($this->thePrice, $this->specialPrice)
        .'</td>';
      } else {
        $lc_text .= '<td align="center" class="infoBoxContents">- </td>';
      }

      foreach($this->price_breaks as $price_break) {
        $lc_text .= '<td align="center" width="50" class="infoBoxContents">'
        . $this->getDiscountSaving($this->thePrice, $price_break['products_price'])
        .'</td>';
      }
      $lc_text .= '</tr></table></td></tr></table>';
    } else {
      if (true == $this->hasSpecialPrice) {
        $lc_text = '&nbsp;<s>'
        . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
        . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">'
        . $currencies->display_price($this->specialPrice, tep_get_tax_rate($this->taxClass))
        . '</span>&nbsp;';
      } else {
        $lc_text = '&nbsp;'
        . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
        . '&nbsp;';
      }
    }
    return $lc_text;
  }

  function getPriceStringShort() {
    global $currencies;

    if(true == $this->hasQuantityPrice) {
	    $lc_text = '&nbsp;<big>' . TEXT_PRICE_BREAKS . ' '
	    . $currencies->display_price($this->lowPrice, tep_get_tax_rate($this->taxClass))
	 	  . '&nbsp;</big><br><br><br>';
    } else {
      if (true == $this->hasSpecialPrice) {
        $lc_text = '&nbsp;<big><s>'
	      . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
	      . '</s>&nbsp;&nbsp;<br><span class="productSpecialPrice">' . TEXT_ON_SALE . ' '
        . $currencies->display_price($this->specialPrice, tep_get_tax_rate($this->taxClass))
	      . '</big></span>&nbsp;<br><br><br>';
      } else {
	      $lc_text = '&nbsp;<big>'
	      . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
	      . '&nbsp;</big><br><br><br>';
      }
    }
    return $lc_text;
  }

/* Old (original formatting)
  function getPriceString($style='"productPriceInBox"') {
    global $currencies;

    if (true == $this->hasSpecialPrice) {
      $lc_text = '<table align="top" border="1" cellspacing="0" cellpadding="0">';
      $lc_text .= '<tr><td align="center" class=' . $style. ' colspan="2">';
      $lc_text .= '&nbsp;<s>'
        . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
        . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">'
        . $currencies->display_price($this->specialPrice, tep_get_tax_rate($this->taxClass))
        . '</span>&nbsp;'
        .'</td></tr>';
    }
    else
    {
      $lc_text = '<table align="top" border="1" cellspacing="0" cellpadding="0">';
      $lc_text .= '<tr><td align="center" class=' . $style. ' colspan="2">'
        . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
        . '</td></tr>';
    }
    
    // If you want to change the format of the price/quantity table
    // displayed on the product information page, here is where you do it.

    if(true == $this->hasQuantityPrice) {
      foreach($this->price_breaks as $price_break) {
        $lc_text .= '<tr><td class='.$style.'>'
          . $price_break['products_qty']
          .'+&nbsp;</td><td class='.$style.'>'
          . $currencies->display_price($price_break['products_price'], tep_get_tax_rate($this->taxClass))
          .'</td></tr>';
      }
      $lc_text .= '</table>';
    } else {
      if (true == $this->hasSpecialPrice) {
        $lc_text = '&nbsp;<s>'
          . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
          . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">'
          . $currencies->display_price($this->specialPrice, tep_get_tax_rate($this->taxClass))
          . '</span>&nbsp;';
      } else {
        $lc_text = '&nbsp;'
          . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
          . '&nbsp;';
      }
    }

    return $lc_text;
  }

  function getPriceStringShort() {
    global $currencies;

    if (true == $this->hasSpecialPrice) {
      $lc_text = '&nbsp;<s>'
        . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
        . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">'
        . $currencies->display_price($this->specialPrice, tep_get_tax_rate($this->taxClass))
        . '</span>&nbsp;';
    } else {
      if(true == $this->hasQuantityPrice) {
        $lc_text = '&nbsp;'
          . $currencies->display_price($this->lowPrice, tep_get_tax_rate($this->taxClass))
          . ' - '
          . $currencies->display_price($this->hiPrice, tep_get_tax_rate($this->taxClass))
          . '&nbsp;';
      } else {
        $lc_text = '&nbsp;'
          . $currencies->display_price($this->thePrice, tep_get_tax_rate($this->taxClass))
          . '&nbsp;';
      }
    }
    return $lc_text;
  }
  */
}
?>
