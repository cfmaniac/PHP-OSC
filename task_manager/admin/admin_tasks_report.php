<?php
/*

AnyPets OSM Online Store Manager
© Copyright 2005-2007 AnyPets Inc.
http://www.anypets.com/

Custom Developed by James Harvey
james@anypets.com
j.harvey@alteredpixels.net

http://www.alteredpixels.net/
*/

  require('includes/application_top.php');


  
  $current_boxes = DIR_FS_ADMIN . DIR_WS_BOXES;
  
  if ($HTTP_GET_VARS['action']) {
    switch ($HTTP_GET_VARS['action']) {
      case 'member_new':
        $check_email_query = tep_db_query("select admin_email_address from " . TABLE_ADMIN . "");
        while ($check_email = tep_db_fetch_array($check_email_query)) {
          $stored_email[] = $check_email['admin_email_address'];
        }
        
        if (in_array($HTTP_POST_VARS['admin_email_address'], $stored_email)) {
			if (isset($HTTP_GET_VARS['orgin']))
			  tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $HTTP_GET_VARS['gID'] . '&error=email&action=new_member&orgin=' . $HTTP_GET_VARS['orgin']));
		    else
	          tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'page=' . $HTTP_GET_VARS['page'] . 'mID=' . $HTTP_GET_VARS['mID'] . '&error=email&action=new_member'));
        } else {
          function randomize() {
            $salt = "abchefghjkmnpqrstuvwxyz0123456789";
            srand((double)microtime()*1000000); 
            $i = 0;
    	    while ($i <= 7) {
    		$num = rand() % 33;
    		$tmp = substr($salt, $num, 1);
    		$pass = $pass . $tmp;
    		$i++;
  	    }
  	    return $pass;
          }
          $makePassword = randomize();
        
          $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_groups_id']),
                                  'admin_firstname' => tep_db_prepare_input($HTTP_POST_VARS['admin_firstname']),
                                  'admin_lastname' => tep_db_prepare_input($HTTP_POST_VARS['admin_lastname']),
                                  'admin_email_address' => tep_db_prepare_input($HTTP_POST_VARS['admin_email_address']),
                                  'admin_password' => tep_encrypt_password($makePassword),
                                  'admin_created' => 'now()');
        	
          tep_db_perform(TABLE_ADMIN, $sql_data_array);
          $admin_id = tep_db_insert_id();
          $check_query=tep_db_query("select admin_groups_name from " . TABLE_ADMIN_GROUPS . " where admin_groups_id='" . $HTTP_POST_VARS['admin_groups_id'] . "'");
	  
	  if($check_result=tep_db_fetch_array($check_query))
	    $admin_groups_name=$check_result['admin_groups_name'];
	  
	  if ($admin_groups_name==TEXT_INSTRUCTOR_ENTRY){
	     $sql_data_array=array('admin_id'=>$admin_id);
	     tep_db_perform(TABLE_INSTRUCTORS,$sql_data_array);
	  }
	  //send email to admin account user
	$merge_details=array(TEXT_FN=>$HTTP_POST_VARS['admin_firstname'],
                                            TEXT_LN=>$HTTP_POST_VARS['admin_lastname'] ,
		                                    TEXT_LE=>$HTTP_POST_VARS['admin_email_address'],
											TEXT_LP=>$makePassword,
											TEXT_SM=>STORE_NAME,
											TEXT_SN=>STORE_OWNER,
											TEXT_SE=>STORE_OWNER_EMAIL_ADDRESS,
											TEXT_AL=>HTTP_SERVER . DIR_WS_ADMIN);
       $send_details=array(array('to_name'=>$admin_mail['admin_firstname'] . ' ' . $admin_mail['admin_lastname'],
	                                     'to_email'=>$admin_mail['admin_email_address'],
										 'from_name'=>STORE_OWNER,
										 'from_email'=>STORE_OWNER_EMAIL_ADDRESS));
      tep_send_default_email("AUT",$merge_details,$send_details);
	  if ($HTTP_GET_VARS['orgin']!='')
	   tep_redirect(tep_href_link($HTTP_GET_VARS['orgin']));
	  else
           tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $admin_id));
        }
        break;
      case 'member_edit':
        $admin_id = tep_db_prepare_input($HTTP_POST_VARS['admin_id']);
        $hiddenPassword = '-hidden-';
        $stored_email[] = 'NONE';
        
        $check_email_query = tep_db_query("select admin_email_address from " . TABLE_ADMIN . " where admin_id <> " . $admin_id . "");
        while ($check_email = tep_db_fetch_array($check_email_query)) {
          $stored_email[] = $check_email['admin_email_address'];
        }
        
        if (in_array($HTTP_POST_VARS['admin_email_address'], $stored_email)) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'page=' . $HTTP_GET_VARS['page'] . 'mID=' . $HTTP_GET_VARS['mID'] . '&error=email&action=edit_member'));
        } else {
          $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_groups_id']),
                                  'admin_firstname' => tep_db_prepare_input($HTTP_POST_VARS['admin_firstname']),
                                  'admin_lastname' => tep_db_prepare_input($HTTP_POST_VARS['admin_lastname']),
                                  'admin_email_address' => tep_db_prepare_input($HTTP_POST_VARS['admin_email_address']),
                                  'admin_modified' => 'now()');
        
          tep_db_perform(TABLE_ADMIN, $sql_data_array, 'update', 'admin_id = \'' . $admin_id . '\'');

          $check_query=tep_db_query("select admin_groups_name from " . TABLE_ADMIN_GROUPS . " where admin_groups_id='" . tep_db_prepare_input($HTTP_POST_VARS['admin_groups_id']) . "'");
	  
	  if($check_result=tep_db_fetch_array($check_query))
	    $admin_groups_name=$check_result['admin_groups_name'];

	  if ($admin_groups_name!=TEXT_INSTRUCTOR_ENTRY){
	    tep_db_query("DELETE from " . TABLE_INSTRUCTORS . " where admin_id='" . $admin_id ."'"); 
	  } else {
		  if ($admin_groups_name==TEXT_INSTRUCTOR_ENTRY){
			 $check_query=tep_db_query("select instructors_id from " . TABLE_INSTRUCTORS . " where admin_id='" . $admin_id . "'");
			 if (tep_db_num_rows($check_query)<=0){
			 	$sql_data_array=array('admin_id'=>$admin_id);
			 	tep_db_perform(TABLE_INSTRUCTORS,$sql_data_array);
			 }
		  }
	  }
	  //send email to admin account user
		$merge_details=array(TEXT_FN=>$HTTP_POST_VARS['admin_firstname'],
                                            TEXT_LN=>$HTTP_POST_VARS['admin_lastname'] ,
		                                    TEXT_LE=>$HTTP_POST_VARS['admin_email_address'],
											TEXT_LP=>$hiddenPassword,
											TEXT_SM=>STORE_NAME,
											TEXT_SN=>STORE_OWNER,
											TEXT_SE=>STORE_OWNER_EMAIL_ADDRESS,
											TEXT_AL=>HTTP_SERVER . DIR_WS_ADMIN);
       $send_details=array(array('to_name'=>$HTTP_POST_VARS['admin_firstname'] . ' ' . $HTTP_POST_VARS['admin_lastname'],
	                                     'to_email'=>$HTTP_POST_VARS['admin_email_address'],
										 'from_name'=>STORE_OWNER,
										 'from_email'=>STORE_OWNER_EMAIL_ADDRESS));
      tep_send_default_email("AUT",$merge_details,$send_details);
      tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $admin_id));
        }
        break;
      case 'member_delete':
        $admin_id = tep_db_prepare_input($HTTP_POST_VARS['admin_id']);
	tep_db_query("delete from " . TABLE_INSTRUCTORS . " where admin_id = '" . $admin_id . "'");
        tep_db_query("delete from " . TABLE_ADMIN . " where admin_id = '" . $admin_id . "'");
        
        tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'page=' . $HTTP_GET_VARS['page']));
        break;
      case 'group_define':
        $selected_checkbox = $HTTP_POST_VARS['groups_to_boxes'];
        $groups_type=$HTTP_POST_VARS['groups_type'];
	//if ($groups_type=='E'){
           //$define_files_query = tep_db_query("select admin_files_id from " . TABLE_ADMIN_FILES . " where admin_files_type='E' order by admin_files_id");}
	//else if ($groups_type=='A'){
           $define_files_query = tep_db_query("select admin_files_id from " . TABLE_ADMIN_FILES . " order by admin_files_id");
	//else{
	   //$define_files_query = tep_db_query("select admin_files_id from " . TABLE_ADMIN_FILES . " where admin_files_type!='E' order by admin_files_id");}

        while ($define_files = tep_db_fetch_array($define_files_query)) { 
          $admin_files_id = $define_files['admin_files_id'];
          
          if (in_array ($admin_files_id, $selected_checkbox)) {
            $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($HTTP_POST_VARS['checked_' . $admin_files_id]));
            //$set_group_id = $HTTP_POST_VARS['checked_' . $admin_files_id];
          } else {
            $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($HTTP_POST_VARS['unchecked_' . $admin_files_id]));
            //$set_group_id = $HTTP_POST_VARS['unchecked_' . $admin_files_id];
          }
          tep_db_perform(TABLE_ADMIN_FILES, $sql_data_array, 'update', 'admin_files_id = \'' . $admin_files_id . '\'');
        }
               
        tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $HTTP_POST_VARS['admin_groups_id']));
        break;
      case 'group_default':
         $group_id=(isset($HTTP_GET_VARS['gPath'])?(int)$HTTP_GET_VARS['gPath']:0);
	 if ($group_id<=0) tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS));
         $group_query=tep_db_query("SELECT admin_groups_name from " . TABLE_ADMIN_GROUPS . " where admin_groups_id='" . $group_id . "'");
	 $group_result=tep_db_fetch_array($group_query);
	 
	 tep_set_default_group_rights($group_result['admin_groups_name'],$group_id);

	 tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS,'action=define_group&gPath=' . $group_id));
      case 'group_delete':
        $set_groups_id = tep_db_prepare_input($HTTP_POST_VARS['set_groups_id']);
        
        tep_db_query("delete from " . TABLE_ADMIN_GROUPS . " where admin_groups_id = '" . $HTTP_GET_VARS['gID'] . "'");
        tep_db_query("alter table " . TABLE_ADMIN_FILES . " change admin_groups_id admin_groups_id set( " . $set_groups_id . " ) NOT NULL DEFAULT '1' ");
        tep_db_query("delete from " . TABLE_ADMIN . " where admin_groups_id = '" . $HTTP_GET_VARS['gID'] . "'");
               
        tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=groups'));
        break;        
      case 'group_edit':
        $admin_groups_name = ucwords(strtolower(tep_db_prepare_input($HTTP_POST_VARS['admin_groups_name'])));
        $name_replace = ereg_replace (" ", "%", $admin_groups_name);
        
        if (($admin_groups_name == '' || NULL) || (strlen($admin_groups_name) <= 5) ) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $HTTP_GET_VARS[gID] . '&gName=false&action=action=edit_group'));
        } else {
          $check_groups_name_query = tep_db_query("select admin_groups_name as group_name_edit from " . TABLE_ADMIN_GROUPS . " where admin_groups_id <> " . $HTTP_GET_VARS['gID'] . " and admin_groups_name like '%" . $name_replace . "%'");
          $check_duplicate = tep_db_num_rows($check_groups_name_query);
          if ($check_duplicate > 0){
            tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $HTTP_GET_VARS['gID'] . '&gName=used&action=edit_group'));
          } else {
            $admin_groups_id = $HTTP_GET_VARS['gID'];
            tep_db_query("update " . TABLE_ADMIN_GROUPS . " set admin_groups_name = '" . $admin_groups_name . "' where admin_groups_id = '" . $admin_groups_id . "'");
            tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $admin_groups_id));
          }
        }
        break;              
      case 'group_new':
        $admin_groups_name = ucwords(strtolower(tep_db_prepare_input($HTTP_POST_VARS['admin_groups_name'])));
        $name_replace = ereg_replace (" ", "%", $admin_groups_name);
        
        if (($admin_groups_name == '' || NULL) || (strlen($admin_groups_name) <= 5) ) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $HTTP_GET_VARS[gID] . '&gName=false&action=new_group'));
        } else {
          $check_groups_name_query = tep_db_query("select admin_groups_name as group_name_new from " . TABLE_ADMIN_GROUPS . " where admin_groups_name like '%" . $name_replace . "%'");
          $check_duplicate = tep_db_num_rows($check_groups_name_query);
          if ($check_duplicate > 0){
            tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $HTTP_GET_VARS['gID'] . '&gName=used&action=new_group'));
          } else {
            $sql_data_array = array('admin_groups_name' => $admin_groups_name);
            tep_db_perform(TABLE_ADMIN_GROUPS, $sql_data_array);
            $admin_groups_id = tep_db_insert_id();

            $set_groups_id = tep_db_prepare_input($HTTP_POST_VARS['set_groups_id']);
            $add_group_id = $set_groups_id . ',\'' . $admin_groups_id . '\'';
            tep_db_query("alter table " . TABLE_ADMIN_FILES . " change admin_groups_id admin_groups_id set( " . $add_group_id . ") NOT NULL DEFAULT '1' ");
            
            tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $admin_groups_id));
          }
        }
        break;        
    }
  }

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<?php require('includes/account_check.js.php'); ?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="165" valign="top"><table border="0" width="165" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->    
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE ?></td>
            <td class="pageHeading" align="right"><a href="admin_tasks_report.php" onClick="window.print()"><IMG SRC="images/printimage_over.gif" Alt="Print Report" Border="0"></a></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">


<table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">Administrator</td>
                <td class="dataTableHeadingContent">Total Tasks Assigned</td>
                <td class="dataTableHeadingContent" align="center">Total Tasks Completed</td>
                <td class="dataTableHeadingContent" align="center">Total Tasks Remaining</td>
                <td class="dataTableHeadingContent" align="center">Total Recurring Tasks </td>
                <td class="dataTableHeadingContent" align="right">Admin Productivity</td>
              </tr>
<?php
  $db_admin_query_raw = "select * from " . TABLE_ADMIN . " order by admin_firstname";
  
  $db_admin_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_admin_query_raw, $db_admin_query_numrows);
  $db_admin_query = tep_db_query($db_admin_query_raw);
  //$db_admin_num_row = tep_db_num_rows($db_admin_query);
  
  while ($admin = tep_db_fetch_array($db_admin_query)) {
    $admin_group_query = tep_db_query("select admin_groups_name from " . TABLE_ADMIN_GROUPS . " where admin_groups_id = '" . $admin['admin_groups_id'] . "'");
    $admin_group = tep_db_fetch_array ($admin_group_query);
    if (((!$HTTP_GET_VARS['mID']) || ($HTTP_GET_VARS['mID'] == $admin['admin_id'])) && (!$mInfo) ) {
      $mInfo_array = array_merge($admin, $admin_group);
      $mInfo = new objectInfo($mInfo_array);
    }
   
    //if ( (is_object($mInfo)) && ($admin['admin_id'] == $mInfo->admin_id) ) {
    //  echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_MEMBERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $admin['admin_id'] . '&action=edit_member') . '\'">' . "\n";
   // } else {
   //   echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_MEMBERS, 'page=' . $HTTP_GET_VARS['page'] . '&mID=' . $admin['admin_id']) . '\'">' . "\n";
    //}
?>
                <tr class="dataTableRow"><td class="dataTableContent">&nbsp;<?php echo $admin['admin_firstname']; ?>&nbsp;<?php echo $admin['admin_lastname']; ?></td>
                <td align="center" class="dataTableContent"><b><?php 
				$admin_assigned_query = tep_db_query("select count(*) as count from " . TABLE_ADMIN_TASKS . " where admin_id = '" . $admin['admin_id'] . "'");
    $admin_assigned = tep_db_fetch_array ($admin_assigned_query);
	echo $admin_assigned['count'];
				 ?></b></td>
                <td class="dataTableContent" align="center"><b><font color="Green"><?php 
				$admin_completed_query = tep_db_query("select count(*) as count from " . TABLE_ADMIN_TASKS . " where admin_id = '" . $admin['admin_id'] . "' and admin_task_status ='1'");
    $admin_completed = tep_db_fetch_array ($admin_completed_query);
	echo $admin_completed['count'];
				 ?></font></b></td>
                <td class="dataTableContent" align="center"><b><font color="Red"><?php 
				$admin_pending_query = tep_db_query("select count(*) as count from " . TABLE_ADMIN_TASKS . " where admin_id = '" . $admin['admin_id'] . "' and admin_task_status <> 1");
    $admin_pending = tep_db_fetch_array ($admin_pending_query);
	echo $admin_pending['count'];
				 ?></font></b></td>
                <td class="dataTableContent" align="center"><b><font color="#0066CC"><?php 
				$admin_recurring_query = tep_db_query("select count(*) as count from " . TABLE_ADMIN_TASKS . " where admin_task_status>= '12'
AND admin_task_status <= '14' and admin_id='".$admin['admin_id']."'");
    $admin_recurring = tep_db_fetch_array ($admin_recurring_query);
	echo $admin_recurring['count'];
				 ?></font></b></td>
                <td class="dataTableContent" align="right"><b><font color="#663366"><?php
	$subtraction = $admin_assigned['count'] - $admin_recurring['count'];			
	
	//$division = $admin_completed['count'] / $subtraction;
	echo $percent=number_format(($admin_completed['count']/$subtraction)*100,2);
	?>%
	</font></b></td>
              </tr>
<?php
  } 
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  
                  <tr>
                    <td colspan="2" valign="top" class="smallText">This report uses the formula:<strong> Assigned Tasks - Recurring Tasks / Total Tasks Completed = Productivity </strong></td>
                    </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $db_admin_split->display_count($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_MEMBERS); ?><br><?php echo $db_admin_split->display_links($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                    <td class="smallText" valign="top" align="right">&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>

            </td>

          </tr>
        </table></td>
      </tr>
    </table></td>  
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
