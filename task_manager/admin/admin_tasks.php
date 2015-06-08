<?php
/*

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright © 2003 osCommerce
  
Task Manager Contribution 2.0
Custom Developed by James Harvey aka DemonAngel
altered@alteredpixels.net
Copyright © 2003 Altered Pixels
 
http://www.alteredpixels.net/
*/

  require('includes/application_top.php');


  
  $current_boxes = DIR_FS_ADMIN . DIR_WS_BOXES;
  
  //For the Status View Drop Down
  $tasks_statuses = array();
  $tasks_status_array = array();
  $tasks_status_query = tep_db_query("select admin_status_id, admin_status_name from admin_tasks_statuses");
  while ($tasks_status = tep_db_fetch_array($tasks_status_query)) {
  $tasks_statuses[] = array('id' => $tasks_status['admin_status_id'],
                               'text' => $tasks_status['admin_status_name']);
  $tasks_status_array[$tasks_status['admin_status_id']] = $tasks_status['admin_status_name'];
  }
  //For the Customer Assigned Drop Down
  $customer_assigned = array();
  $customer_assigned_array = array();
  $customer_assigned_query = tep_db_query("select customers_id, customers_firstname, customers_lastname, customers_email_address from customers");
  while ($customer_assigned = tep_db_fetch_array($customer_assigned_query)) {
  $customer_assigned[] = array('id' => $customer_assigned['customer_id'],
                               'text' => $customer_assigned['customer_firstname'] .'&nbsp;'. $customer_assigned['customer_lastname']);
  $customer_assigned_array[$customer_assigned['customer_id']] = $customer_assigned['customer_firstname'] .'&nbsp;'. $customer_assigned['customer_lastname'];
  }
  //For the Project Drop Down View
  $tasks_projects = array();
  $tasks_projects_array = array();
  $tasks_projects_query = tep_db_query("select admin_projects_id, admin_projects_name from admin_projects");
  while ($tasks_projects = tep_db_fetch_array($tasks_projects_query)) {
  $tasks_projects_list[] = array('id' => $tasks_projects['admin_projects_id'],
                               'text' => $tasks_projects['admin_projects_name']);
  $tasks_projects_array[$tasks_projects['admin_project_id']] = $tasks_projects['admin_projects_name'];
  }
  
  if ($HTTP_GET_VARS['action']) {
    switch ($HTTP_GET_VARS['action']) {
      case 'task_new':
        $check_email_query = tep_db_query("select admin_email_address from " . TABLE_ADMIN . "");
        while ($check_email = tep_db_fetch_array($check_email_query)) {
          $stored_email[] = $check_email['admin_email_address'];
        }
        
        if (in_array($HTTP_POST_VARS['admin_email_address'], $stored_email)) {
			if (isset($HTTP_GET_VARS['orgin']))
			  tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projID'] . '&error=email&action=new_task&orgin=' . $HTTP_GET_VARS['orgin']));
		    else
	          tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . 'tID=' . $HTTP_GET_VARS['tID'] . '&error=email&action=new_task'));
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
        
          $sql_data_array = array('admin_projects_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_projects_id']),
                                  'admin_task_name' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_name']),
                                  'admin_task_description' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_description']),
								  'admin_task_priority' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_priority']),
						  		  'admin_task_status' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_status']),
								  'admin_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_id_assigned']),
								  'admin_task_due' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_due']),
                                  'admin_task_created' => 'now()');
        	
          tep_db_perform(TABLE_ADMIN_TASKS, $sql_data_array);
          $admin_id = tep_db_insert_id();
          $check_query=tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id='" . $HTTP_POST_VARS['admin_projects_id'] . "'");
	  
	  if($check_result=tep_db_fetch_array($check_query))
	    $admin_projects_name=$check_result['admin_projects_name'];
	  
	  //Sends Email to user Functions
	  $admin_mail_query = tep_db_query("select admin_firstname, admin_lastname, admin_email_address from admin where admin_id='".$HTTP_POST_VARS['admin_id_assigned']."'");
	  $admin_mail = tep_db_fetch_array ($admin_mail_query);
	  //Query to Get Priority Name
	  $priority_query = tep_db_query("select admin_priority_name from admin_tasks_priorities where admin_priority_id = '" . $HTTP_POST_VARS['admin_task_priority'] . "'");
$priority = tep_db_fetch_array ($priority_query);
//Query to get Project Name
$admin_project_query = tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" .$HTTP_POST_VARS['admin_projects_id'] . "'");
$admin_project = tep_db_fetch_array ($admin_project_query);
//Query to get Status name 
$status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $HTTP_POST_VARS['admin_task_status'] . "'");
$status = tep_db_fetch_array ($status_query);
	  $name = tep_db_prepare_input($admin_mail['admin_firstname']);
    $email_address = tep_db_prepare_input($admin_mail['admin_email_address']);
    $to_email = $admin_mail['admin_email_address'];
	$from_name = STORE_OWNER;
	$from_email = STORE_OWNER_EMAIL_ADDRESS;
	$email_subject = tep_db_prepare_input('New Task Added: ' .$HTTP_POST_VARS['admin_task_name']);
    $message = tep_db_prepare_input('Dear&nbsp;'. $admin_mail['admin_firstname'] .',<br><br> You have been Assigned a New Task.<br><br>
Your new task details are as follows:<br>
<b>Task Name:</b> '.$HTTP_POST_VARS['admin_task_name'].'<br>
<b>Task Description:</b> '.$HTTP_POST_VARS['admin_task_description'].'<br>
<b>Task Priority:</b> '.$priority['admin_priority_name'].'<br>
<b>Task Project:</b> '.$admin_project['admin_projects_name'].'<br>
<b>Task Status:</b> '.$status['admin_status_name']. '<br><br>
Please be sure to update your tasks, as soon as changes are made.<br>
Also, please be sure to complete your assigned tasks in a timely manner.<br><br>
Thank you, <br>
-AnyPets OSM Task Manager<br><br><br><br><br><br>
Please Note this is an automated email, <b>Do Not Reply</b>');
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_subject, $message, $name, $email_address);
	  //End Send Mail to user Functions
	  
	  tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin_id));
        }
        break;
      case 'task_edit':
             
       $sql_data_array = array('admin_projects_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_projects_id']),
                                  'admin_task_name' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_name']),
                                  'admin_task_description' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_description']),
								  'admin_task_status' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_status']),
								  'admin_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_id_assigned']),
								  'admin_task_due' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_due']),
                                  'admin_task_modified' => 'now()');
        
          tep_db_perform(TABLE_ADMIN_TASKS, $sql_data_array, 'update', 'admin_task_id = \'' . $admin_task_id . '\'');

	  //Sends Email to user Functions
	  $admin_mail_query = tep_db_query("select admin_firstname, admin_lastname, admin_email_address from admin where admin_id='".$HTTP_POST_VARS['admin_id_assigned']."'");
	  $admin_mail = tep_db_fetch_array ($admin_mail_query);
	  //Query to Get Priority Name
	  $priority_query = tep_db_query("select admin_priority_name from admin_tasks_priorities where admin_priority_id = '" . $HTTP_POST_VARS['admin_task_priority'] . "'");
$priority = tep_db_fetch_array ($priority_query);
//Query to get Project Name
$admin_project_query = tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" .$HTTP_POST_VARS['admin_projects_id'] . "'");
$admin_project = tep_db_fetch_array ($admin_project_query);
//Query to get Status name 
$status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $HTTP_POST_VARS['admin_task_status'] . "'");
$status = tep_db_fetch_array ($status_query);
	  $name = tep_db_prepare_input($admin_mail['admin_firstname']);
    $email_address = tep_db_prepare_input($admin_mail['admin_email_address']);
    $to_email = $admin_mail['admin_email_address'];
	$from_name = STORE_OWNER;
	$from_email = STORE_OWNER_EMAIL_ADDRESS;
	$email_subject = tep_db_prepare_input('Editted Task: ' .$HTTP_POST_VARS['admin_task_name']);
    $message = tep_db_prepare_input('Dear&nbsp;'. $admin_mail['admin_firstname'] .',<br><br> You or another Administrator has Editted your assigned Task:<b> '.$HTTP_POST_VARS['admin_task_name'] .'</b>.
Your updated task details are as follows:<br>
<b>Task Name:</b> '.$HTTP_POST_VARS['admin_task_name'].'<br>
<b>Task Description:</b> '.$HTTP_POST_VARS['admin_task_description'].'<br>
<b>Task Priority:</b> '.$priority['admin_priority_name'].'<br>
<b>Task Project:</b> '.$admin_project['admin_projects_name'].'<br>
<b>Task Status:</b> '.$status['admin_status_name']. '<br><br>
Please be sure to update your tasks, as soon as changes are made.<br>
Also, please be sure to complete your assigned tasks in a timely manner.<br><br>
Thank you, <br>
-AnyPets OSM Task Manager<br><br><br><br><br><br>
Please Note this is an automated email, <b>Do Not Reply</b>');
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_subject, $message, $name, $email_address);
	  //End Send Mail to user Functions

          $check_query=tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id='" . tep_db_prepare_input($HTTP_POST_VARS['admin_projects_id']) . "'");
	  
	  if($check_result=tep_db_fetch_array($check_query))
	    $admin_projects_name=$check_result['admin_projects_name'];


	  if ($admin_projects_name!=TEXT_INSTRUCTOR_ENTRY){
	  	  } else {
		  if ($admin_projects_name==TEXT_INSTRUCTOR_ENTRY){
			// $check_query=tep_db_query("select instructors_id from " . TABLE_INSTRUCTORS . " where admin_id='" . $admin_id . "'");
			 }
			 
      tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin_id));
        }
        break;
      case 'task_delete':
        $admin_task_id = tep_db_prepare_input($HTTP_POST_VARS['admin_task_id']);
	    tep_db_query("delete from " . TABLE_ADMIN_TASKS . " where admin_task_id = '" . $admin_task_id . "'");
        
        tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page']));
        break;
      case 'project_define':
        $selected_checkbox = $HTTP_POST_VARS['projects_to_boxes'];
        $projects_type=$HTTP_POST_VARS['projects_type'];
	
       while ($define_files = tep_db_fetch_array($define_files_query)) { 
          $admin_files_id = $define_files['admin_files_id'];
          
          if (in_array ($admin_files_id, $selected_checkbox)) {
            $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($HTTP_POST_VARS['checked_' . $admin_files_id]));
            //$set_group_id = $HTTP_POST_VARS['checked_' . $admin_files_id];
          } else {
            $sql_data_array = array('admin_groups_id' => tep_db_prepare_input($HTTP_POST_VARS['unchecked_' . $admin_files_id]));
            //$set_group_id = $HTTP_POST_VARS['unchecked_' . $admin_files_id];
          }
          //tep_db_perform(TABLE_ADMIN_FILES, $sql_data_array, 'update', 'admin_files_id = \'' . $admin_files_id . '\'');
        }
               
        tep_redirect(tep_href_link(FILENAME_ADMIN_MEMBERS, 'gID=' . $HTTP_POST_VARS['admin_groups_id']));
        break;
      case 'project_default':
         $project_id=(isset($HTTP_GET_VARS['projPath'])?(int)$HTTP_GET_VARS['projPath']:0);
	 if ($project_id<=0) tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS));
         $project_query=tep_db_query("SELECT admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id='" . $project_id . "'");
	 $project_result=tep_db_fetch_array($project_query);
	 
	 tep_set_default_project_rights($project_result['admin_projects_name'],$project_id);

	 tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS,'action=define_project&projPath=' . $project_id));
      case 'project_delete':
        $set_projects_id = tep_db_prepare_input($HTTP_POST_VARS['set_projects_id']);
        
        tep_db_query("delete from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" . $HTTP_GET_VARS['projID'] . "'");
        //tep_db_query("alter table " . TABLE_ADMIN_FILES . " change admin_projects_id admin_projects_id set( " . $set_projects_id . " ) NOT NULL DEFAULT '1' ");
        //tep_db_query("delete from " . TABLE_ADMIN . " where admin_projects_id = '" . $HTTP_GET_VARS['projID'] . "'");
               
        tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=projects'));
        break;        
      case 'project_edit':
        $admin_projects_name = ucwords(strtolower(tep_db_prepare_input($HTTP_POST_VARS['admin_projects_name'])));
        $name_replace = ereg_replace (" ", "%", $admin_projects_name);
        $name_replace = ereg_replace ("'", "\'", $admin_projects_name);
        if (($admin_projects_name == '' || NULL) || (strlen($admin_projects_name) <= 3) ) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS[projID] . '&gName=false&action=action=edit_project'));
        } else {
          $check_projects_name_query = tep_db_query("select admin_projects_name as project_name_edit from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id <> " . $HTTP_GET_VARS['projID'] . " and admin_projects_name like '%" . $name_replace . "%'");
          $check_duplicate = tep_db_num_rows($check_projects_name_query);
          if ($check_duplicate > 0){
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projID'] . '&gName=used&action=edit_project'));
          } else {
            $admin_projects_id = $HTTP_GET_VARS['projID'];
            tep_db_query("update " . TABLE_ADMIN_PROJECTS . " set admin_projects_name = '" . $admin_projects_name . "', admin_projects_customer ='".$admin_projects_customer ."' where admin_projects_id = '" . $admin_projects_id . "'");
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $admin_projects_id));
          }
        }
        break;              
      case 'project_new':
        $admin_projects_name = ucwords(strtolower(tep_db_prepare_input($HTTP_POST_VARS['admin_projects_name'])));
        $name_replace = ereg_replace (" ", "%", $admin_projects_name);
        $name_replace = ereg_replace ("'", "\'", $admin_projects_name);
        if (($admin_projects_name == '' || NULL) || (strlen($admin_projects_name) <= 3) ) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS[projID] . '&gName=false&action=new_project'));
        } else {
          $check_projects_name_query = tep_db_query("select admin_projects_name as project_name_new from " . TABLE_ADMIN_PROJECTS . " where admin_projects_name like '%" . $name_replace . "%'");
          $check_duplicate = tep_db_num_rows($check_projects_name_query);
          if ($check_duplicate > 0){
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projID'] . '&gName=used&action=new_project'));
          } else {
            $sql_data_array = array('admin_projects_name' => $admin_projects_name,
			                        'admin_projects_customer' => $admin_projects_customer);
            //$sql_data_array = array('admin_projects_name' => tep_db_prepare_input($HTTP_POST_VARS['admin_projects_name']));
			tep_db_perform(TABLE_ADMIN_PROJECTS, $sql_data_array);
            $admin_projects_id = tep_db_insert_id();

            $set_projects_id = tep_db_prepare_input($HTTP_POST_VARS['set_projects_id']);
            $add_project_id = $set_projects_id . ',\'' . $admin_projects_id . '\'';
            //tep_db_query("alter table " . TABLE_ADMIN_FILES . " change admin_projects_id admin_projects_id set( " . $add_project_id . ") NOT NULL DEFAULT '1' ");
            
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $admin_projects_id));
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
<script type="text/javascript" src="includes/calendarDateInput.js">
</script>
<script type="text/javascript">
//Specify display mode. 3 possible values are:
//1) "always"- This makes the fade-in box load each time the page is displayed
//2) "oncepersession"- This uses cookies to display the fade-in box only once per browser session
//3) integer (ie: 5)- Finally, you can specify an integer to display the box randomly via a frequency of 1/integer...
// For example, 2 would display the box about (1/2) 50% of the time the page loads.

var displaymode="oncepersession"
//var displaymode="always" //used for debugging, primarily

var enablefade="yes" //("yes" to enable fade in effect, "no" to disable)
var autohidebox=["yes", 30] //Automatically hide box after x seconds? [yes/no, if_yes_hide_after_seconds]
var showonscroll="yes" //Should box remain visible even when user scrolls page? ("yes"/"no)
var IEfadelength=1 //fade in duration for IE, in seconds
var Mozfadedegree=0.2 //fade in degree for NS6+ (number between 0 and 1. Recommended max: 0.2)

////////No need to edit beyond here///////////

if (parseInt(displaymode)!=NaN)
var random_num=Math.floor(Math.random()*displaymode)

function displayfadeinbox(){
var ie=document.all && !window.opera
var dom=document.getElementById
iebody=(document.compatMode=="CSS1Compat")? document.documentElement : document.body
objref=(dom)? document.getElementById("fadeinbox") : document.all.fadeinbox
var scroll_top=(ie)? iebody.scrollTop : window.pageYOffset
var docwidth=(ie)? iebody.clientWidth : window.innerWidth
docheight=(ie)? iebody.clientHeight: window.innerHeight
var objwidth=objref.offsetWidth
objheight=objref.offsetHeight
objref.style.left=docwidth/2-objwidth/2+"px"
objref.style.top=scroll_top+docheight/2-objheight/2+"px"

if (showonscroll=="yes")
showonscrollvar=setInterval("staticfadebox()", 50)

if (enablefade=="yes" && objref.filters){
objref.filters[0].duration=IEfadelength
objref.filters[0].Apply()
objref.filters[0].Play()
}
objref.style.visibility="visible"
if (objref.style.MozOpacity){
if (enablefade=="yes")
mozfadevar=setInterval("mozfadefx()", 90)
else{
objref.style.MozOpacity=1
controlledhidebox()
}
}
else
controlledhidebox()
}

function mozfadefx(){
if (parseFloat(objref.style.MozOpacity)<1)
objref.style.MozOpacity=parseFloat(objref.style.MozOpacity)+Mozfadedegree
else{
clearInterval(mozfadevar)
controlledhidebox()
}
}

function staticfadebox(){
var ie=document.all && !window.opera
var scroll_top=(ie)? iebody.scrollTop : window.pageYOffset
objref.style.top=scroll_top+docheight/2-objheight/2+"px"
}

function hidefadebox(){
objref.style.visibility="hidden"
if (typeof showonscrollvar!="undefined")
clearInterval(showonscrollvar)
}

function controlledhidebox(){
if (autohidebox[0]=="yes"){
var delayvar=(enablefade=="yes" && objref.filters)? (autohidebox[1]+objref.filters[0].duration)*1000 : autohidebox[1]*1000
setTimeout("hidefadebox()", delayvar)
}
}

function initfunction(){
setTimeout("displayfadeinbox()", 100)
}

function get_cookie(Name) {
var search = Name + "="
var returnvalue = ""
if (document.cookie.length > 0) {
offset = document.cookie.indexOf(search)
if (offset != -1) {
offset += search.length
end = document.cookie.indexOf(";", offset)
if (end == -1)
end = document.cookie.length;
returnvalue=unescape(document.cookie.substring(offset, end))
}
}
return returnvalue;
}


if (displaymode=="oncepersession" && get_cookie("fadedin")=="" || displaymode=="always" || parseInt(displaymode)!=NaN && random_num==0){
if (window.addEventListener)
window.addEventListener("load", initfunction, false)
else if (window.attachEvent)
window.attachEvent("onload", initfunction)
else if (document.getElementById)
window.onload=initfunction
document.cookie="fadedin=yes"
}


</script>
<script language="javascript1.5">
// Begin Sortable Tables Scripts
function SortableTable(oTable, oSortTypes) {

	this.sortTypes = oSortTypes || [];

	this.sortColumn = null;
	this.descending = null;

	var oThis = this;
	this._headerOnclick = function (e) {
		oThis.headerOnclick(e);
	};

	if (oTable) {
		this.setTable( oTable );
		this.document = oTable.ownerDocument || oTable.document;
	}
	else {
		this.document = document;
	}


	// only IE needs this
	var win = this.document.defaultView || this.document.parentWindow;
	this._onunload = function () {
		oThis.destroy();
	};
	if (win && typeof win.attachEvent != "undefined") {
		win.attachEvent("onunload", this._onunload);
	}
}

SortableTable.gecko = navigator.product == "Gecko";
SortableTable.msie = /msie/i.test(navigator.userAgent);
// Mozilla is faster when doing the DOM manipulations on
// an orphaned element. MSIE is not
SortableTable.removeBeforeSort = SortableTable.gecko;

SortableTable.prototype.onsort = function () {};

// default sort order. true -> descending, false -> ascending
SortableTable.prototype.defaultDescending = false;

// shared between all instances. This is intentional to allow external files
// to modify the prototype
SortableTable.prototype._sortTypeInfo = {};

SortableTable.prototype.setTable = function (oTable) {
	if ( this.tHead )
		this.uninitHeader();
	this.element = oTable;
	this.setTHead( oTable.tHead );
	this.setTBody( oTable.tBodies[0] );
};

SortableTable.prototype.setTHead = function (oTHead) {
	if (this.tHead && this.tHead != oTHead )
		this.uninitHeader();
	this.tHead = oTHead;
	this.initHeader( this.sortTypes );
};

SortableTable.prototype.setTBody = function (oTBody) {
	this.tBody = oTBody;
};

SortableTable.prototype.setSortTypes = function ( oSortTypes ) {
	if ( this.tHead )
		this.uninitHeader();
	this.sortTypes = oSortTypes || [];
	if ( this.tHead )
		this.initHeader( this.sortTypes );
};

// adds arrow containers and events
// also binds sort type to the header cells so that reordering columns does
// not break the sort types
SortableTable.prototype.initHeader = function (oSortTypes) {
	if (!this.tHead) return;
	var cells = this.tHead.rows[0].cells;
	var doc = this.tHead.ownerDocument || this.tHead.document;
	this.sortTypes = oSortTypes || [];
	var l = cells.length;
	var img, c;
	for (var i = 0; i < l; i++) {
		c = cells[i];
		if (this.sortTypes[i] != null && this.sortTypes[i] != "None") {
			img = doc.createElement("IMG");
			img.src = "images/arrows.gif";
			c.appendChild(img);
			if (this.sortTypes[i] != null)
				c._sortType = this.sortTypes[i];
			if (typeof c.addEventListener != "undefined")
				c.addEventListener("click", this._headerOnclick, false);
			else if (typeof c.attachEvent != "undefined")
				c.attachEvent("onclick", this._headerOnclick);
			else
				c.onclick = this._headerOnclick;
		}
		else
		{
			c.setAttribute( "_sortType", oSortTypes[i] );
			c._sortType = "None";
		}
	}
	this.updateHeaderArrows();
};

// remove arrows and events
SortableTable.prototype.uninitHeader = function () {
	if (!this.tHead) return;
	var cells = this.tHead.rows[0].cells;
	var l = cells.length;
	var c;
	for (var i = 0; i < l; i++) {
		c = cells[i];
		if (c._sortType != null && c._sortType != "None") {
			c.removeChild(c.lastChild);
			if (typeof c.removeEventListener != "undefined")
				c.removeEventListener("click", this._headerOnclick, false);
			else if (typeof c.detachEvent != "undefined")
				c.detachEvent("onclick", this._headerOnclick);
			c._sortType = null;
			c.removeAttribute( "_sortType" );
		}
	}
};

SortableTable.prototype.updateHeaderArrows = function () {
	if (!this.tHead) return;
	var cells = this.tHead.rows[0].cells;
	var l = cells.length;
	var img;
	for (var i = 0; i < l; i++) {
		if (cells[i]._sortType != null && cells[i]._sortType != "None") {
			img = cells[i].lastChild;
			if (i == this.sortColumn)
				img.className = "sort-arrow " + (this.descending ? "descending" : "ascending");
			else
				img.className = "sort-arrow";
		}
	}
};

SortableTable.prototype.headerOnclick = function (e) {
	// find TD element
	var el = e.target || e.srcElement;
	while (el.tagName != "TD")
		el = el.parentNode;

	this.sort(SortableTable.msie ? SortableTable.getCellIndex(el) : el.cellIndex);
};

// IE returns wrong cellIndex when columns are hidden
SortableTable.getCellIndex = function (oTd) {
	var cells = oTd.parentNode.childNodes
	var l = cells.length;
	var i;
	for (i = 0; cells[i] != oTd && i < l; i++)
		;
	return i;
};

SortableTable.prototype.getSortType = function (nColumn) {
	return this.sortTypes[nColumn] || "String";
};

// only nColumn is required
// if bDescending is left out the old value is taken into account
// if sSortType is left out the sort type is found from the sortTypes array

SortableTable.prototype.sort = function (nColumn, bDescending, sSortType) {
	if (!this.tBody) return;
	if (sSortType == null)
		sSortType = this.getSortType(nColumn);

	// exit if None
	if (sSortType == "None")
		return;

	if (bDescending == null) {
		if (this.sortColumn != nColumn)
			this.descending = this.defaultDescending;
		else
			this.descending = !this.descending;
	}
	else
		this.descending = bDescending;

	this.sortColumn = nColumn;

	if (typeof this.onbeforesort == "function")
		this.onbeforesort();

	var f = this.getSortFunction(sSortType, nColumn);
	var a = this.getCache(sSortType, nColumn);
	var tBody = this.tBody;

	a.sort(f);

	if (this.descending)
		a.reverse();

	if (SortableTable.removeBeforeSort) {
		// remove from doc
		var nextSibling = tBody.nextSibling;
		var p = tBody.parentNode;
		p.removeChild(tBody);
	}

	// insert in the new order
	var l = a.length;
	for (var i = 0; i < l; i++)
		tBody.appendChild(a[i].element);

	if (SortableTable.removeBeforeSort) {
		// insert into doc
		p.insertBefore(tBody, nextSibling);
	}

	this.updateHeaderArrows();

	this.destroyCache(a);

	if (typeof this.onsort == "function")
		this.onsort();
};

SortableTable.prototype.asyncSort = function (nColumn, bDescending, sSortType) {
	var oThis = this;
	this._asyncsort = function () {
		oThis.sort(nColumn, bDescending, sSortType);
	};
	window.setTimeout(this._asyncsort, 1);
};

SortableTable.prototype.getCache = function (sType, nColumn) {
	if (!this.tBody) return [];
	var rows = this.tBody.rows;
	var l = rows.length;
	var a = new Array(l);
	var r;
	for (var i = 0; i < l; i++) {
		r = rows[i];
		a[i] = {
			value:		this.getRowValue(r, sType, nColumn),
			element:	r
		};
	};
	return a;
};

SortableTable.prototype.destroyCache = function (oArray) {
	var l = oArray.length;
	for (var i = 0; i < l; i++) {
		oArray[i].value = null;
		oArray[i].element = null;
		oArray[i] = null;
	}
};

SortableTable.prototype.getRowValue = function (oRow, sType, nColumn) {
	// if we have defined a custom getRowValue use that
	if (this._sortTypeInfo[sType] && this._sortTypeInfo[sType].getRowValue)
		return this._sortTypeInfo[sType].getRowValue(oRow, nColumn);

	var s;
	var c = oRow.cells[nColumn];
	if (typeof c.innerText != "undefined")
		s = c.innerText;
	else
		s = SortableTable.getInnerText(c);
	return this.getValueFromString(s, sType);
};

SortableTable.getInnerText = function (oNode) {
	var s = "";
	var cs = oNode.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				s += SortableTable.getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				s += cs[i].nodeValue;
				break;
		}
	}
	return s;
};

SortableTable.prototype.getValueFromString = function (sText, sType) {
	if (this._sortTypeInfo[sType])
		return this._sortTypeInfo[sType].getValueFromString( sText );
	return sText;
	/*
	switch (sType) {
		case "Number":
			return Number(sText);
		case "CaseInsensitiveString":
			return sText.toUpperCase();
		case "Date":
			var parts = sText.split("-");
			var d = new Date(0);
			d.setFullYear(parts[0]);
			d.setDate(parts[2]);
			d.setMonth(parts[1] - 1);
			return d.valueOf();
	}
	return sText;
	*/
	};

SortableTable.prototype.getSortFunction = function (sType, nColumn) {
	if (this._sortTypeInfo[sType])
		return this._sortTypeInfo[sType].compare;
	return SortableTable.basicCompare;
};

SortableTable.prototype.destroy = function () {
	this.uninitHeader();
	var win = this.document.parentWindow;
	if (win && typeof win.detachEvent != "undefined") {	// only IE needs this
		win.detachEvent("onunload", this._onunload);
	}
	this._onunload = null;
	this.element = null;
	this.tHead = null;
	this.tBody = null;
	this.document = null;
	this._headerOnclick = null;
	this.sortTypes = null;
	this._asyncsort = null;
	this.onsort = null;
};

// Adds a sort type to all instance of SortableTable
// sType : String - the identifier of the sort type
// fGetValueFromString : function ( s : string ) : T - A function that takes a
//    string and casts it to a desired format. If left out the string is just
//    returned
// fCompareFunction : function ( n1 : T, n2 : T ) : Number - A normal JS sort
//    compare function. Takes two values and compares them. If left out less than,
//    <, compare is used
// fGetRowValue : function( oRow : HTMLTRElement, nColumn : int ) : T - A function
//    that takes the row and the column index and returns the value used to compare.
//    If left out then the innerText is first taken for the cell and then the
//    fGetValueFromString is used to convert that string the desired value and type

SortableTable.prototype.addSortType = function (sType, fGetValueFromString, fCompareFunction, fGetRowValue) {
	this._sortTypeInfo[sType] = {
		type:				sType,
		getValueFromString:	fGetValueFromString || SortableTable.idFunction,
		compare:			fCompareFunction || SortableTable.basicCompare,
		getRowValue:		fGetRowValue
	};
};

// this removes the sort type from all instances of SortableTable
SortableTable.prototype.removeSortType = function (sType) {
	delete this._sortTypeInfo[sType];
};

SortableTable.basicCompare = function compare(n1, n2) {
	if (n1.value < n2.value)
		return -1;
	if (n2.value < n1.value)
		return 1;
	return 0;
};

SortableTable.idFunction = function (x) {
	return x;
};

SortableTable.toUpperCase = function (s) {
	return s.toUpperCase();
};

SortableTable.toDate = function (s) {
	var parts = s.split("-");
	var d = new Date(0);
	d.setFullYear(parts[0]);
	d.setDate(parts[2]);
	d.setMonth(parts[1] - 1);
	return d.valueOf();
};


// add sort types
SortableTable.prototype.addSortType("Number", Number);
SortableTable.prototype.addSortType("CaseInsensitiveString", SortableTable.toUpperCase);
SortableTable.prototype.addSortType("Date", SortableTable.toDate);
SortableTable.prototype.addSortType("String");
// None is a special case
//End Sortable tables Scripts
</script>

<style>
.sort-table {
	font:		Icon;
	/*border:		1px Solid ThreeDShadow;*/
	background:	Window;
	color:		WindowText;
}

/*.sort-table thead {
	background:	ButtonFace;
}*/

.sort-table td {
	padding:	0px 0px;
}

.sort-table thead td {
	/*border:			1px solid;*/
	border-color:	ButtonHighlight ButtonShadow
					ButtonShadow ButtonHighlight;
	cursor:			default;
}

.sort-table thead td:active {
	border-color:	ButtonShadow ButtonHighlight
					ButtonHighlight ButtonShadow;
	padding:		0px 0px 0px 0px;
}

.sort-table thead td[_sortType=None]:active {
	border-color:	ButtonHighlight ButtonShadow
					ButtonShadow ButtonHighlight;
	padding:		0px 0px;
}

.sort-arrow {
	width:					7px;
	height:					7px;
	background-position:	center center;
	background-repeat:		no-repeat;
	margin:					0px;
}

.sort-arrow.descending {
	/*background-image:		url("assets/down.png");*/

}

.sort-arrow.ascending {
	/*background-image:		url("assets/up.png");*/
}

#fadeinbox{
position:absolute;
width: 500px;
left: 0;
top: -400px;
border: 2px solid black;
background-color: lightyellow;
padding: 4px;
z-index: 100;
visibility:hidden;
}
</style>
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
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="smallText" align="right"><?php echo tep_draw_form('status', FILENAME_ADMIN_TASKS, '', 'get'); ?>
             <?php echo VIEW_BY_STATUS . ' ' . tep_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => 'All Tasks')), $tasks_statuses), '', 'onChange="this.form.submit();"'); ?>              </form><br>
		<?php echo tep_draw_form('project', FILENAME_ADMIN_TASKS, '', 'get'); ?>
		<?php echo VIEW_BY_PROJECT . ' ' . tep_draw_pull_down_menu('project', array_merge(array(array('id' => '', 'text' => 'All Projects')), $tasks_projects_list), '', 'onChange="this.form.submit();"'); ?>
		</form>	   </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
<?php
 if ($HTTP_GET_VARS['projPath']) {
   $project_name_query = tep_db_query("select admin_projects_type,admin_projects_name, admin_projects_customer from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = " . $HTTP_GET_VARS['projPath']);
   $project_name = tep_db_fetch_array($project_name_query);
   if ($HTTP_GET_VARS['projPath'] == 1) {
     echo tep_draw_form('defineForm', FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projPath']);
   } elseif ($HTTP_GET_VARS['projPath'] != 1) {
     echo tep_draw_form('defineForm', FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projPath'] . '&action=project_define', 'post', 'enctype="multipart/form-data"');
     echo tep_draw_hidden_field('admin_projects_id', $HTTP_GET_VARS['projPath']); 
   }
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td colspan=2 class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_GROUPS_DEFINE; ?></td>
              </tr>
<?php
  // get type of event
  $project_name['admin_projects_type']='D';
  if ($project_name['admin_projects_type']=='E'){ // if project type is event 
     $db_boxes_query = tep_db_query("select admin_files_id as admin_boxes_id, admin_files_name as admin_boxes_name, admin_projects_id as boxes_project_id,admin_files_type as boxes_type from " . TABLE_ADMIN_FILES . " where admin_files_is_boxes = '1' and admin_files_type='E' order by admin_files_name");
     echo tep_draw_hidden_field('projects_type','E'); //events
  }else{
     //if ($HTTP_GET_VARS['projPath'] == 1) {
        $db_boxes_query = tep_db_query("select admin_files_id as admin_boxes_id, admin_files_name as admin_boxes_name, admin_projects_id as boxes_project_id,admin_files_type as boxes_type from " . TABLE_ADMIN_FILES . " where admin_files_is_boxes = '1' order by admin_files_name");
        //echo tep_draw_hidden_field('projects_type','A');} //administrator
     /*else {
        echo tep_draw_hidden_field('projects_type','O'); // others
        $db_boxes_query = tep_db_query("select admin_files_id as admin_boxes_id, admin_files_name as admin_boxes_name, admin_projects_id as boxes_project_id,admin_files_type as boxes_type from " . TABLE_ADMIN_FILES . " where admin_files_is_boxes = '1' and admin_files_type!='E' order by admin_files_name");}*/
  }
  while ($project_boxes = tep_db_fetch_array($db_boxes_query)) {
    $project_boxes_files_query = tep_db_query("select admin_files_id, admin_files_name, admin_projects_id,admin_files_help_id from " . TABLE_ADMIN_FILES . " where admin_files_is_boxes = '0' and admin_files_to_boxes = '" . $project_boxes['admin_boxes_id'] . "' order by admin_files_help_id,admin_files_name");

    $selectedGroups = $project_boxes['boxes_project_id'];
    $projectsArray = explode(",", $selectedGroups);

    if (in_array($HTTP_GET_VARS['projPath'], $projectsArray)) {     
      $del_boxes = array($HTTP_GET_VARS['projPath']);
      $result = array_diff ($projectsArray, $del_boxes);
      sort($result);
      $checkedBox = $selectedGroups;
      $uncheckedBox = implode (",", $result);
      $checked = true;
    } else {
      $add_boxes = array($HTTP_GET_VARS['projPath']);
      $result = array_merge ($add_boxes, $projectsArray);
      sort($result);
      $checkedBox = implode (",", $result);
      $uncheckedBox = $selectedGroups;
      $checked = false;
    }
    //added below for event    
	// if the box type is event
?>
<?php if ($project_boxes['boxes_type']=='E'){ ?>
              <tr class="dataTableRowBoxes">
                <td class="dataTableContent" width="23"><?php echo tep_draw_checkbox_field('projects_to_boxes[]', $project_boxes['admin_boxes_id'], $checked, '', 'id="projects_' . $project_boxes['admin_boxes_id'] . '" onClick="checkGroups(this)"'); ?></td>
                <td class="dataTableContent"><b><?php echo ucwords(substr_replace ($project_boxes['admin_boxes_name'], '', -4)) . ' ' . tep_draw_hidden_field('checked_' . $project_boxes['admin_boxes_id'], $checkedBox) . tep_draw_hidden_field('unchecked_' . $project_boxes['admin_boxes_id'], $uncheckedBox); ?></b></td>
              </tr>
              <tr class="dataTableRow">
                <td class="dataTableContent">&nbsp;</td>
                <td class="dataTableContent">
                  <table border="0" cellspacing="0" cellpadding="0">
<?php
     $project_help_id='';
     //$project_boxes_files_query = tep_db_query("select admin_files_id, admin_files_name, admin_projects_id from " . TABLE_ADMIN_FILES . " where admin_files_is_boxes = '0' and admin_files_to_boxes = '" . $project_boxes['admin_boxes_id'] . "' order by admin_files_name");
     while($project_boxes_files = tep_db_fetch_array($project_boxes_files_query)) {
       $selectedGroups = $project_boxes_files['admin_projects_id'];
       $projectsArray = explode(",", $selectedGroups);

       if (in_array($HTTP_GET_VARS['projPath'], $projectsArray)) {     
         $del_boxes = array($HTTP_GET_VARS['projPath']);
         $result = array_diff ($projectsArray, $del_boxes);
         sort($result);
         $checkedBox = $selectedGroups;
         $uncheckedBox = implode (",", $result);
         $checked = true;
       } else {
         $add_boxes = array($HTTP_GET_VARS['projPath']);
         $result = array_merge ($add_boxes, $projectsArray);
         sort($result);
         $checkedBox = implode (",", $result);
         $uncheckedBox = $selectedGroups;
         $checked = false;
       }
       if ($project_help_id!=$project_boxes_files['admin_files_help_id']){
            $project_help_id=$project_boxes_files['admin_files_help_id'];
?>
		    <tr><td><?php echo tep_draw_separator('pixel_trans.gif',4,10) ?></td></tr>
 		    <tr>
		       <td width colspan="2" class="main"><?php echo '<b>' . tep_get_help_text($project_help_id) .'</b>' ; ?></td>
		    </tr>
		    <tr><td><?php echo tep_draw_separator('pixel_trans.gif',4,5) ?></td></tr>
<?php } ?>
                    <tr>
                      <td width="20"><?php echo tep_draw_checkbox_field('projects_to_boxes[]', $project_boxes_files['admin_files_id'], $checked, '', 'id="subprojects_' . $project_boxes['admin_boxes_id'] . '" onClick="checkSub(this)"'); ?></td>
                      <td class="dataTableContent"><?php echo $project_boxes_files['admin_files_name'] . ' ' . tep_draw_hidden_field('checked_' . $project_boxes_files['admin_files_id'], $checkedBox) . tep_draw_hidden_field('unchecked_' . $project_boxes_files['admin_files_id'], $uncheckedBox);?></td>
                    </tr>
<?php       
     }
?>
                  </table>
                </td>
<?php } else { ?>
              <tr class="dataTableRowBoxes">
                <td class="dataTableContent" width="23"><?php echo tep_draw_checkbox_field('projects_to_boxes[]', $project_boxes['admin_boxes_id'], $checked, '', 'id="projects_' . $project_boxes['admin_boxes_id'] . '" onClick="checkGroups(this)"'); ?></td>
                <td class="dataTableContent"><b><?php echo ucwords(substr_replace ($project_boxes['admin_boxes_name'], '', -4)) . ' ' . tep_draw_hidden_field('checked_' . $project_boxes['admin_boxes_id'], $checkedBox) . tep_draw_hidden_field('unchecked_' . $project_boxes['admin_boxes_id'], $uncheckedBox); ?></b></td>
              </tr>
              <tr class="dataTableRow">
                <td class="dataTableContent">&nbsp;</td>
                <td class="dataTableContent">
                  <table border="0" cellspacing="0" cellpadding="0">
<?php
     //$project_boxes_files_query = tep_db_query("select admin_files_id, admin_files_name, admin_projects_id from " . TABLE_ADMIN_FILES . " where admin_files_is_boxes = '0' and admin_files_to_boxes = '" . $project_boxes['admin_boxes_id'] . "' order by admin_files_name");
     while($project_boxes_files = tep_db_fetch_array($project_boxes_files_query)) {
       $selectedGroups = $project_boxes_files['admin_projects_id'];
       $projectsArray = explode(",", $selectedGroups);

       if (in_array($HTTP_GET_VARS['projPath'], $projectsArray)) {     
         $del_boxes = array($HTTP_GET_VARS['projPath']);
         $result = array_diff ($projectsArray, $del_boxes);
         sort($result);
         $checkedBox = $selectedGroups;
         $uncheckedBox = implode (",", $result);
         $checked = true;
       } else {
         $add_boxes = array($HTTP_GET_VARS['projPath']);
         $result = array_merge ($add_boxes, $projectsArray);
         sort($result);
         $checkedBox = implode (",", $result);
         $uncheckedBox = $selectedGroups;
         $checked = false;
       }
?>
                                       
                    <tr>
                      <td width="20"><?php echo tep_draw_checkbox_field('projects_to_boxes[]', $project_boxes_files['admin_files_id'], $checked, '', 'id="subprojects_' . $project_boxes['admin_boxes_id'] . '" onClick="checkSub(this)"'); ?></td>
                      <td class="dataTableContent"><?php echo $project_boxes_files['admin_files_name'] . ' ' . tep_draw_hidden_field('checked_' . $project_boxes_files['admin_files_id'], $checkedBox) . tep_draw_hidden_field('unchecked_' . $project_boxes_files['admin_files_id'], $uncheckedBox);?></td>
                    </tr>
<?php       
     }
?>
                  </table>
                </td>
              </tr>
<?php
  // end of event condition
  }
  }
?>
              <tr class="dataTableRowBoxes">
                <td colspan=2 class="dataTableContent" valign="top" align="right"><?php if ($HTTP_GET_VARS['projPath'] != 1) { echo  '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projPath']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a> ' . tep_image_submit('button_save.gif', IMAGE_INSERT); } else { echo tep_image_submit('button_back.gif', IMAGE_BACK); } ?>&nbsp;</td>
              </tr>
            </table></form>
<?php
 } elseif ($HTTP_GET_VARS['projID']) {
?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2" class="sort-table" id="projects">
              <THEAD>
			  <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_PROJECTS; ?></td>
				<td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_PROJECT_TASKS; ?></td>
				<td class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_PROJECT_CUSTOMER; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PROJECT_ACTION; ?>&nbsp;</td>
              </tr>
</THEAD>
   <COL />
       <COL />
       <COL />
<?php
  $db_projects_query = tep_db_query("select * from " . TABLE_ADMIN_PROJECTS . " order by admin_projects_id");
  
  $add_projects_prepare = '\'0\'' ;
  $del_projects_prepare = '\'0\'' ;
  $count_projects = 0;
  while ($projects = tep_db_fetch_array($db_projects_query)) {
    $add_projects_prepare .= ',\'' . $projects['admin_projects_id'] . '\'' ;
    if (((!$HTTP_GET_VARS['projID']) || ($HTTP_GET_VARS['projID'] == $projects['admin_projects_id']) || ($HTTP_GET_VARS['projID'] == 'projects')) && (!$gInfo) ) {
      $gInfo = new objectInfo($projects);
    }
   
    if ( (is_object($gInfo)) && ($projects['admin_projects_id'] == $gInfo->admin_projects_id) ) {
      echo '                <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $projects['admin_projects_id'] . ($projects['admin_projects_type']!='E'?'&action=edit_project':'')) . '\'">' . "\n";
    } else {
      echo '                <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $projects['admin_projects_id']) . '\'">' . "\n";
      $del_projects_prepare .= ',\'' . $projects['admin_projects_id'] . '\'' ;
    }
?>
                <td class="dataTableContent">&nbsp;<?php echo tep_draw_form('project', FILENAME_ADMIN_TASKS, '', 'get'); ?>
<a href="admin_tasks.php?project=<?php echo $projects['admin_projects_id']?>" onClick="this.form.submit();"><b><?php echo $projects['admin_projects_name']; ?></b></a></form></td>
			   
	<td class="dataTableContent">&nbsp;<i><?php $proj_tasks_query = tep_db_query("select count(*) as count from " . TABLE_ADMIN_TASKS . " where admin_projects_id='". $projects['admin_projects_id'] ."'");
    $proj_tasks = tep_db_fetch_array($proj_tasks_query);
	echo $proj_tasks['count'];?></i></td>
	 <td class="dataTableContent">&nbsp;<b><?php $proj_customers_query = tep_db_query("select customers_lastname, customers_firstname from customers where customers_id='". $projects['admin_projects_customer'] ."'");
    $proj_customers = tep_db_fetch_array($proj_customers_query);
	if ($projects['admin_projects_customer'] =='0'){
	echo 'This is not a Customer Project';
	}else{
	echo $proj_customers['customers_lastname'] .',&nbsp;'. $proj_customers['customers_firstname'];}
	?></b></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($gInfo)) && ($projects['admin_projects_id'] == $gInfo->admin_projects_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $projects['admin_projects_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    $count_projects++;
  } 
?>
            </table>
			     <SCRIPT type="text/javascript">

var st1 = new SortableTable(document.getElementById("projects"),
	["CaseInsensitiveString", "Number", "CaseInsensitiveString"]);

     </SCRIPT>
			<table>
			  <tr>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo TEXT_COUNT_GROUPS . $count_projects; ?></td>
                    <td class="smallText" valign="top" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a> <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $gInfo->admin_projects_id . '&action=new_project') . '">' . tep_image_button('button_admin_project.gif', IMAGE_NEW_GROUP) . '</a>'; ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
<?php
 } else {
?> 
            <table border="0" width="100%" cellspacing="0" cellpadding="2" class="sort-table" id="table-1">
			<COL />
			<COL />
       <COL />
       <COL />
       <COL />
       <COL />
       <COL />
       <COL />
       <COL />
	  	   <THEAD>
			  <tr class="dataTableHeadingRow">
               <td width="21" align="center" valign="top" class="dataTableHeadingContent">&nbsp;</td>
			   <td width="21" align="center" valign="top" class="dataTableHeadingContent"><?php echo TABLE_HEADING_MARK ?></td>
					    <td width="121" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString"><?php echo TABLE_HEADING_TNAME ?></td>
                <td width="128" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString"><?php echo TABLE_HEADING_TDESC ?></td>
                <td width="55" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString"><?php echo TABLE_HEADING_TPROJ ?></td>
                <td width="56" align="center" valign="top" class="dataTableHeadingContent"title="CaseInsensitiveString"><?php echo TABLE_HEADING_PRIOR ?></td>
				<td width="48" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString"> <?php echo TABLE_HEADING_STATUS ?></td>
				<td width="72" align="center" valign="top" class="dataTableHeadingContent" title="Date"><?php echo TABLE_HEADING_DUED ?></td>
                <td width="50" align="right" valign="top" class="dataTableHeadingContent"><?php echo TABLE_HEADING_ACTION ?></td>
              </tr>
			  </THEAD>
<?php
 //if (isset($HTTP_GET_VARS['tID'])) {
 // $tID = tep_db_prepare_input($HTTP_GET_VARS['tID']);
 // $db_admin_query_raw = "select * from " . TABLE_ADMIN_TASKS . " where admin_id='".$login_id."' and admin_task_status <> 1 order by admin_task_priority";
 // }
 if (is_numeric($HTTP_GET_VARS['status']) && ($HTTP_GET_VARS['status'] > 0)) {
  $status = tep_db_prepare_input($HTTP_GET_VARS['status']);
$db_admin_query_raw = "select * from " . TABLE_ADMIN_TASKS . " where admin_id='".$login_id."' and admin_task_status ='". $HTTP_GET_VARS['status'] ."'  order by admin_task_priority";
   }  elseif (is_numeric($HTTP_GET_VARS['project']) && ($HTTP_GET_VARS['project'] > 0)) {
$project = tep_db_prepare_input($HTTP_GET_VARS['project']);
$db_admin_query_raw = "select * from " . TABLE_ADMIN_TASKS . " where admin_id='".$login_id."' and admin_projects_id ='". $HTTP_GET_VARS['project'] ."'  order by admin_task_priority";
} else {
 $db_admin_query_raw = "select * from " . TABLE_ADMIN_TASKS . " where admin_id='".$login_id."' and admin_task_status <> 1 order by admin_task_priority";
}
$db_admin_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_admin_query_raw, $db_admin_query_numrows);
$db_admin_query = tep_db_query($db_admin_query_raw);
  
  while ($admin = tep_db_fetch_array($db_admin_query)) {
$admin_project_query = tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" . $admin['admin_projects_id'] . "'");
$admin_project = tep_db_fetch_array ($admin_project_query);
    if (((!$HTTP_GET_VARS['tID']) || ($HTTP_GET_VARS['tID'] == $admin['admin_task_id'])) && (!$tInfo)) {
      $tInfo_array = array_merge($admin, $admin_project);
      $tInfo = new objectInfo($tInfo_array);
    }

 if ( (is_object($tInfo)) && ($admin['admin_task_id'] == $tInfo->admin_task_id) ) {
      echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_task_id'] . '&action=edit_task') . '\'" >' . "\n";
    }  else {
      echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_task_id']) . '\'">' . "\n";
    }
?>

<td valign="top" class="dataTableContent"><div align="center"><a href="admin_tasks.php?tID=<?php echo $admin['admin_task_id']?>&action=edit_task"><IMG SRC="images/icons/image_edit.gif" border="0"></a></div></td>
<td align="center" valign="top" class="dataTableContent"><form name="newtask" Action="admin_tasks.php?action=task_edit&tID=<?php echo $admin['admin_task_id']?>" method="post">
<input type="hidden" name="admin_task_id" value="<?php echo $admin['admin_task_id'];?>">
<input type="hidden" name="admin_id_assigned" value="<?php echo $admin['admin_id'];?>">
<input type="hidden" name="admin_projects_id" value="<?php echo $admin['admin_projects_id'];?>">
<input type="hidden" name="admin_task_name" value="<?php echo $admin['admin_task_name'];?>">
<input type="hidden" name="admin_task_description" value="<?php echo $admin['admin_task_description'];?>">
<input type="hidden" name="admin_task_priority" value="<?php echo $admin['admin_task_priority'];?>">
<input type="hidden" name="admin_task_due" value="<?php echo $admin['admin_task_due'];?>">
<input type="hidden" name="admin_task_created" value="<?php echo $admin['admin_task_created'];?>">
<input type="checkbox" name="admin_task_status" value="1" onChange="this.form.submit();">
</form></td>
        <td width="121" valign="top" class="dataTableContent"><div align="center"><a href="admin_tasks.php?tID=<?php echo $admin['admin_task_id']?>&action=edit_task"><?php echo $admin['admin_task_name']; ?></a></div></td>
                <td valign="top" class="dataTableContent"><div align="center"><a href="admin_tasks.php?tID=<?php echo $admin['admin_task_id']?>&action=edit_task"><?php $admin['admin_task_description']; 
$words=split(" ",$admin['admin_task_description']); 
print join(" ",array_slice($words,0,4)); 
print "...";?></a></div></td>
                <td align="center" valign="top" class="dataTableContent"><div align="center"><?php echo $admin_project['admin_projects_name']; ?></div></td>
                <td align="center" valign="top" class="dataTableContent"><div align="center">
                  <?php //echo $admin['admin_task_priority'];
				if ($admin['admin_task_priority'] == '0'){
				echo '<b><i>No Priority Assigned</b></i>';
				} else {
    $priority_query = tep_db_query("select admin_priority_name from admin_tasks_priorities where admin_priority_id = '" . $admin['admin_task_priority'] . "'");
    $priority = tep_db_fetch_array ($priority_query);
	echo $priority['admin_priority_name'];
	}
	?>
    </div></td>
	 <td align="center" valign="top" class="dataTableContent"><div align="center">
	   <?php //echo $admin['admin_task_priority'];
				if ($admin['admin_task_status'] == '0'){
				echo '<b><i>Not Started</b></i>';
				} else {
				  $status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $admin['admin_task_status'] . "'");
    $status = tep_db_fetch_array ($status_query);
	echo $status['admin_status_name'];
	}?>
	   </div></td>
	<td valign="top" class="dataTableContent"><div align="center">
	<?php //echo $admin['admin_task_due']; 
	if ($admin['admin_task_status'] == '12' || ($admin['admin_task_status']== '13' || ($admin['admin_task_status'] == '14'))){
		 echo '<i><font color="#0000cc">Recurring</font></i>';
			  } else {
$exp_date = $admin['admin_task_due'];
if ($exp_date == NULL){
echo '<b><i>No Due date assigned</b></i>';
} else {
$todays_date = date("Y-m-d");
$today = strtotime($todays_date);
$expiration_date = strtotime($exp_date);

if ($expiration_date > $today) {
$valid = "yes"; 
echo $admin['admin_task_due'] .'<br><b><font color="Green">This Task is nearing it\'s Due Date.</font></b>';}
else { $valid = "no";
echo '<b><i><font color="#ffffcc">'. $admin['admin_task_due'] .'<br>This task is over due.</font></i></b>'; 
if ($admin['admin_overdue_mail_sent'] == '1') {
echo '<br><b><i>--Email Sent--</b></i>';
} else {
//Sends Over Due Email to user Functions
	  $admin_mail_query = tep_db_query("select admin_firstname, admin_lastname, admin_email_address from admin where admin_id='".$HTTP_POST_VARS['admin_id_assigned']."'");
	  $admin_mail = tep_db_fetch_array ($admin_mail_query);
	  //Query to Get Priority Name
	  $priority_query = tep_db_query("select admin_priority_name from admin_tasks_priorities where admin_priority_id = '" . $admin['admin_task_priority'] . "'");
$priority = tep_db_fetch_array ($priority_query);
//Query to get Project Name
$admin_project_query = tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" .$admin['admin_projects_id'] . "'");
$admin_project = tep_db_fetch_array ($admin_project_query);
//Query to get Status name 
$status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $admin['admin_task_status'] . "'");
$status = tep_db_fetch_array ($status_query);
	  $name = tep_db_prepare_input($admin_mail['admin_firstname']);
    $email_address = tep_db_prepare_input($admin_mail['admin_email_address']);
    $to_email = $admin_mail['admin_email_address'];
	$from_name = STORE_OWNER;
	$from_email = STORE_OWNER_EMAIL_ADDRESS;
	$email_subject = tep_db_prepare_input('Task Overdue: ' .$admin['admin_task_name']);
    $message = tep_db_prepare_input('Dear&nbsp;'. $admin_mail['admin_firstname'] .',<br><br> You have been Assigned a task, and now that task: <b>'.$admin['admin_task_name'].'</b> happens to be overdue.<br><br>
Please Review your task, and ensure it\'s prompt completion:<br>
<b>Task Name:</b> '.$admin['admin_task_name'].'<br>
<b>Task Description:</b> '.$admin['admin_task_description'].'<br>
<b>Task Priority:</b> '.$priority['admin_priority_name'].'<br>
<b>Task Project:</b> '.$admin_project['admin_projects_name'].'<br>
<b>Task Status:</b> '.$status['admin_status_name']. '<br><br>
OverDue Tasks are not helping in our Operating Progress, as this task may be effecting others productivity. It is Recommended that you complete this task, as soon as possible.<br><br>
Thank you, <br>
-AnyPets OSM Task Manager<br><br><br><br><br><br>
Please Note this is an automated email, <b>Do Not Reply</b>');
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_subject, $message, $name, $email_address);
	  $sql_data_array = array('admin_projects_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_projects_id']),
                                  'admin_task_name' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_name']),
                                  'admin_task_description' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_description']),
								  'admin_task_status' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_status']),
								  'admin_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_id_assigned']),
								  'admin_task_due' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_due']),
								  'admin_overdue_mail_sent' => tep_db_prepare_input('1'),
                                  'admin_task_modified' => 'now()');
        
          tep_db_perform(TABLE_ADMIN_TASKS, $sql_data_array, 'update', 'admin_task_id = \'' . $admin_task_id . '\'');
	  //End Send Mail to user Functions
}
}
}
}
?></div> </td>
                <td align="right" valign="top" class="dataTableContent"><?php if ( (is_object($tInfo)) && ($admin['admin_id'] == $tInfo->admin_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
			  <tr class="dataTableRow"><td colspan="9" align="Center"><hr width="75%" color="#660000" noshade></td></tr>
<?php
  } 
?>
</table>
<SCRIPT type="text/javascript">
var st1 = new SortableTable(document.getElementById("table-1"),
	["None", "None", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsesitiveString", "CaseInsensitiveString", "Number", "Date", "None"]);

     </SCRIPT>
	 <DIV id="fadeinbox" style="filter:progid:DXImageTransform.Microsoft.RandomDissolve(duration=1) progid:DXImageTransform.Microsoft.Shadow(color=gray,direction=135) ; -moz-opacity:0">
<CENTER><span class="pageHeading">Hello <?php $admin_name_query = tep_db_query("select admin_firstname, admin_lastname from admin where admin_id = '" . $login_id . "'");
    $admin_welcome = tep_db_fetch_array ($admin_name_query);
	echo $admin_welcome['admin_firstname'] .'&nbsp;'. $admin_welcome['admin_lastname'];?>, Your Highest Priority Tasks</span></CENTER>

 <table border="0" width="100%" cellspacing="0" cellpadding="2" class="sort-table" id="table-1">
			<COL />
       <COL />
       <COL />
       <COL />
       <COL />
       <COL />
       <COL />
       <COL />
	  	   <THEAD>
			  <tr class="dataTableHeadingRow">
               <td width="21" align="center" valign="top" class="dataTableHeadingContent">&nbsp;</td>
					    <td width="121" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString">Task Name</td>
                <td width="128" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString">Task Description</td>
                <td width="55" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString">Project</td>
                <td width="56" align="center" valign="top" class="dataTableHeadingContent"title="CaseInsensitiveString"> Priority</td>
				<td width="48" align="center" valign="top" class="dataTableHeadingContent" title="CaseInsensitiveString"> Status</td>
				<td width="72" align="center" valign="top" class="dataTableHeadingContent" title="Date">Due Date</td>
                <td width="50" align="right" valign="top" class="dataTableHeadingContent">Action</td>
              </tr>
			  </THEAD>
<?php
  $db_admin_query_init = "select * from " . TABLE_ADMIN_TASKS . " where admin_id='".$login_id."' and admin_task_priority >= '1' and admin_task_priority <= '2' and admin_task_status <> 1 order by admin_task_priority";
  
  $db_admin_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_admin_query_init, $db_admin_query_numrows);
  $db_admin_query = tep_db_query($db_admin_query_init);
  //$db_admin_num_row = tep_db_num_rows($db_admin_query);
  
  while ($admin = tep_db_fetch_array($db_admin_query)) {
    $admin_project_query = tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" . $admin['admin_projects_id'] . "'");
    $admin_project = tep_db_fetch_array ($admin_project_query);
    if (((!$HTTP_GET_VARS['tID']) || ($HTTP_GET_VARS['tID'] == $admin['admin_task_id'])) && (!$tInfo) ) {
      $tInfo_array = array_merge($admin, $admin_project);
      $tInfo = new objectInfo($tInfo_array);
    }
   if ( (is_object($tInfo)) && ($admin['admin_task_id'] == $tInfo->admin_task_id) ) {
      echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_task_id'] . '&action=edit_task') . '\'">' . "\n";
    } else {
      echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_task_id']) . '\'">' . "\n";
    }
?>
<td valign="top" class="dataTableContent"><div align="center"><a href="admin_tasks.php?tID=<?php echo $admin['admin_task_id']?>&action=edit_task"><IMG SRC="images/icons/image_edit.gif" border="0"></a></div></td>
        <td width="121" valign="top" class="dataTableContent"><div align="center"><a href="admin_tasks.php?tID=<?php echo $admin['admin_task_id']?>&action=edit_task"><?php echo $admin['admin_task_name']; ?></a></div></td>
                <td valign="top" class="dataTableContent"><div align="center"><a href="admin_tasks.php?tID=<?php echo $admin['admin_task_id']?>&action=edit_task"><?php echo $admin['admin_task_description']; ?></a></div></td>
                <td align="center" valign="top" class="dataTableContent"><div align="center"><?php echo $admin_project['admin_projects_name']; ?></div></td>
                <td align="center" valign="top" class="dataTableContent"><div align="center">
                  <?php //echo $admin['admin_task_priority'];
				if ($admin['admin_task_priority'] == '0'){
				echo '<b><i>No Priority Assigned</b></i>';
				} else {
				  $priority_query = tep_db_query("select admin_priority_name from admin_tasks_priorities where admin_priority_id = '" . $admin['admin_task_priority'] . "'");
    $priority = tep_db_fetch_array ($priority_query);
	echo $priority['admin_priority_name'];
	}?>
                </div></td>
	 <td align="center" valign="top" class="dataTableContent"><div align="center">
	   <?php //echo $admin['admin_task_priority'];
				if ($admin['admin_task_status'] == '0'){
				echo '<b><i>Not Started</b></i>';
				} else {
				  $status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $admin['admin_task_status'] . "'");
    $status = tep_db_fetch_array ($status_query);
	echo $status['admin_status_name'];
	}?>
	   </div></td>
	<td valign="top" class="dataTableContent"><div align="center">
	<?php //echo $admin['admin_task_due']; 
	if ($admin['admin_task_status'] == '12' || ($admin['admin_task_status']== '13' || ($admin['admin_task_status'] == '14'))){
		 echo '&nbsp;';
			  } else {
$exp_date = $admin['admin_task_due'];
if ($exp_date == NULL){
echo '<b><i>No Due date assigned</b></i>';
} else {
$todays_date = date("Y-m-d");
$today = strtotime($todays_date);
$expiration_date = strtotime($exp_date);

if ($expiration_date > $today) {
$valid = "yes"; 
echo $admin['admin_task_due'] .'<br><b><font color="Green">This Task is nearing it\'s Due Date.</font></b>';}
else { $valid = "no";
echo '<b><i><font color="#ffffcc">'. $admin['admin_task_due'] .'<br>This task is over due.</font></i></b>'; 
//Sends Email to user Functions
	  $admin_mail_query = tep_db_query("select admin_firstname, admin_lastname, admin_email_address from admin where admin_id='".$HTTP_POST_VARS['admin_id_assigned']."'");
	  $admin_mail = tep_db_fetch_array ($admin_mail_query);
	  //Query to Get Priority Name
	  $priority_query = tep_db_query("select admin_priority_name from admin_tasks_priorities where admin_priority_id = '" . $admin['admin_task_priority'] . "'");
$priority = tep_db_fetch_array ($priority_query);
//Query to get Project Name
$admin_project_query = tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" .$admin['admin_projects_id'] . "'");
$admin_project = tep_db_fetch_array ($admin_project_query);
//Query to get Status name 
$status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $admin['admin_task_status'] . "'");
$status = tep_db_fetch_array ($status_query);
	  $name = tep_db_prepare_input($admin_mail['admin_firstname']);
    $email_address = tep_db_prepare_input($admin_mail['admin_email_address']);
    $to_email = $admin_mail['admin_email_address'];
	$from_name = STORE_OWNER;
	$from_email = STORE_OWNER_EMAIL_ADDRESS;
	$email_subject = tep_db_prepare_input('Task Overdue: ' .$admin['admin_task_name']);
    $message = tep_db_prepare_input('Dear&nbsp;'. $admin_mail['admin_firstname'] .',<br><br> You have been Assigned a task, and now that task: <b>'.$admin['admin_task_name'].'</b> happens to be overdue.<br><br>
Please Review your task, and ensure it\'s prompt completion:<br>
<b>Task Name:</b> '.$admin['admin_task_name'].'<br>
<b>Task Description:</b> '.$admin['admin_task_description'].'<br>
<b>Task Priority:</b> '.$priority['admin_priority_name'].'<br>
<b>Task Project:</b> '.$admin_project['admin_projects_name'].'<br>
<b>Task Status:</b> '.$status['admin_status_name']. '<br><br>
OverDue Tasks are not helping in our Operating Progress, as this task may be effecting others productivity. It is Recommended that you complete this task, as soon as possible.<br><br>
Thank you, <br>
-AnyPets OSM Task Manager<br><br><br><br><br><br>
Please Note this is an automated email, <b>Do Not Reply</b>');
      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $email_subject, $message, $name, $email_address);
	  //End Send Mail to user Functions
}
}
}
?></div> </td>
                <td align="right" valign="top" class="dataTableContent"><?php if ( (is_object($tInfo)) && ($admin['admin_id'] == $tInfo->admin_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
  } 
?>
</table>

<div align="right"> <a href="#" onClick="hidefadebox();return false">Hide Box</a>
</div>
</DIV>
<table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $db_admin_split->display_count($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_TASKS); ?><br>
                      <?php echo $db_admin_split->display_links($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                    <td class="smallText" valign="top" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=projects') . '">' . tep_image_button('button_admin_projects.gif', 'Projects List') . '</a>'; echo ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $tInfo->admin__task_id . '&action=new_task') . '">' . tep_image_button('button_admin_task.gif', 'New Task') . '</a>'; ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>
			
<?php
 }
?>
            </td>
<?php
  $heading = array();
  $contents = array();
  switch ($HTTP_GET_VARS['action']) {  
    case 'new_task': 
      $heading[] = array('text' => '<b>Add a new Task</b>');

      if ($HTTP_GET_VARS['orgin']!='' && (int)$HTTP_GET_VARS['projID']!=0){
         $contents = array('form' => tep_draw_form('newtask', FILENAME_ADMIN_TASKS, 'action=task_new&orgin=' . $HTTP_GET_VARS['orgin'] . '&projID=' . $HTTP_GET_VARS["projID"], 'post', 'enctype="multipart/form-data"')); }
      else {
         $contents = array('form' => tep_draw_form('newtask', FILENAME_ADMIN_TASKS, 'action=task_new&page=' . $page . 'tID=' . $HTTP_GET_VARS['tID'], 'post', 'enctype="multipart/form-data"')); }
	 
      if ($HTTP_GET_VARS['error']) {
        $contents[] = array('text' => TEXT_INFO_ERROR); 
      }
      $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASKNAME.'<br>&nbsp;' . tep_draw_input_field('admin_task_name')); 
      $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_DESCRIPTION.'<br>&nbsp;' . //tep_draw_input_field('admin_task_description'));
tep_draw_textarea_field('admin_task_description', 'soft', '15', '15'));         
      if ($HTTP_GET_VARS['orgin']!='' && (int)$HTTP_GET_VARS['projID']!=0){
        $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_PROJECT.'<br>&nbsp;' . tep_draw_hidden_field('admin_projects_id', $HTTP_GET_VARS['projID']) . TEXT_INSTRUCTOR_ENTRY);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT, 'onClick="validateForm();return document.returnValue"') . ' <a href="' . tep_href_link($HTTP_GET_VARS['orgin']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      } else {
	  //Added for Assignees
	   $assignee_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $assignee_query = tep_db_query("select admin_id, admin_firstname, admin_lastname from admin");
	      while ($assignee = tep_db_fetch_array($assignee_query)) {
		$assignee_array[] = array('id' => $assignee['admin_id'],
					'text' => $assignee['admin_lastname'].',&nbsp;'. $assignee['admin_firstname']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_ASSIGNEE.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_id_assigned', $assignee_array, '0')); 
		  //END
		  	  //Added for Priorities
	   $priority_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $priority_query = tep_db_query("select admin_priority_id, admin_priority_name from admin_tasks_priorities");
	      while ($priorities = tep_db_fetch_array($priority_query)) {
		$priorities_array[] = array('id' => $priorities['admin_priority_id'],
					'text' => $priorities['admin_priority_name']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_PRIORITY.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_task_priority', $priorities_array, '0')); 
		  //END
	      $projects_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $projects_query = tep_db_query("select admin_projects_id, admin_projects_name from " . TABLE_ADMIN_PROJECTS);
	      while ($projects = tep_db_fetch_array($projects_query)) {
		$projects_array[] = array('id' => $projects['admin_projects_id'],
					'text' => $projects['admin_projects_name']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_PROJECT.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_projects_id', $projects_array, '0')); 
		  //Added for Status
	      $status_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $status_query = tep_db_query("select admin_status_id, admin_status_name from admin_tasks_statuses");	      
		  while ($status = tep_db_fetch_array($status_query)) {
		$status_array[] = array('id' => $status['admin_status_id'],
					'text' => $status['admin_status_name']);
	      }
		$contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_STATUS.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_task_status', $status_array, '0')); 
		  	  //END
  $contents[] = array('text' => '<br>&nbsp;'.TEXT_TASK_DUE.'<br>&nbsp;' . //tep_draw_input_field('admin_task_due', $tInfo->admin_task_due) .
		'&nbsp; <script>DateInput(\'admin_task_due\', true, \'YYYY-MM-DD\')</script>');
              $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT, 'onClick="validateForm();return document.returnValue"') . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $HTTP_GET_VARS['tID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      }
      break;
    case 'edit_task': 
      $heading[] = array('text' => '<b>'. TEXT_EDIT_TASK.'&nbsp;'. $tInfo->admin_task_name .'</b>');
      
      $contents = array('form' => tep_draw_form('newtask', FILENAME_ADMIN_TASKS, 'action=task_edit&page=' . $page . '&tID=' . $HTTP_GET_VARS['tID'], 'post', 'enctype="multipart/form-data"')); 
      if ($HTTP_GET_VARS['error']) {
        $contents[] = array('text' => TEXT_INFO_ERROR); 
      }
      $contents[] = array('text' => tep_draw_hidden_field('admin_task_id', $tInfo->admin_task_id));
	  //$contents[] = array('text' => tep_draw_hidden_field('admin_id_assigned', $tInfo->admin_id));  
      $contents[] = array('text' => '<br>&nbsp;'.TEXT_TASKNAME.'<br>&nbsp;' . tep_draw_input_field('admin_task_name', $tInfo->admin_task_name)); 
      $contents[] = array('text' => '<br>&nbsp;'.TEXT_TASK_DESCRIPTION.'<br>&nbsp;' . //tep_draw_input_field('admin_task_description', $tInfo->admin_task_description));
tep_draw_textarea_field('admin_task_description', 'soft', '15', '15', $tInfo->admin_task_description));
       //Added for Assignees
	   $assignee_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $assignee_query = tep_db_query("select admin_id, admin_firstname, admin_lastname from admin");
	      while ($assignee = tep_db_fetch_array($assignee_query)) {
		$assignee_array[] = array('id' => $assignee['admin_id'],
					'text' => $assignee['admin_lastname'].',&nbsp;'. $assignee['admin_firstname']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_ASSIGNEE.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_id_assigned', $assignee_array, $tInfo->admin_id)); 
		  //END
		  	  //Added for Priorities
	   $priority_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $priority_query = tep_db_query("select admin_priority_id, admin_priority_name from admin_tasks_priorities");
	      while ($priorities = tep_db_fetch_array($priority_query)) {
		$priorities_array[] = array('id' => $priorities['admin_priority_id'],
					'text' => $priorities['admin_priority_name']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;'. TEXT_TASK_PRIORITY.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_task_priority', $priorities_array, $tInfo->admin_task_priority)); 
		  //END
	   //Added for Project
	   
	   $project_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $project_query = tep_db_query("select admin_projects_id, admin_projects_name from admin_projects");	      
		  while ($project = tep_db_fetch_array($project_query)) {
		$project_array[] = array('id' => $project['admin_projects_id'],
					'text' => $project['admin_projects_name']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;'.TEXT_TASK_PROJECT.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_projects_id', $project_array, $tInfo->admin_projects_id)); 
	  	  //Added for Status
	      $status_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $status_query = tep_db_query("select admin_status_id, admin_status_name from admin_tasks_statuses");	      
		  while ($status = tep_db_fetch_array($status_query)) {
		$status_array[] = array('id' => $status['admin_status_id'],
					'text' => $status['admin_status_name']);
	      }
		 if ($tInfo->admin_task_status == '14'){
		 $status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $tInfo->admin_task_status . "'");
    $status = tep_db_fetch_array ($status_query);
	//echo $status['admin_status_name'];
		$contents[] = array('text' => '<br>&nbsp;'.TEXT_TASK_STATUS.'<input type="hidden" name="admin_task_status" value="'.$tInfo->admin_task_status.'"><br>' . $status['admin_status_name']);
		} else {
		$contents[] = array('text' => '<br>&nbsp;'.TEXT_TASK_STATUS.'<br>&nbsp;' . tep_draw_pull_down_menu('admin_task_status', $status_array, $tInfo->admin_task_status)); 
	}
		  	  //END
			  if ($tInfo->admin_task_status == '12' || ($tInfo->admin_task_status == '13' || ($tInfo->admin_task_status == '14'))){
		 $contents[] = array('text'=>TEXT_RECURRING);
			  } else {
	    $contents[] = array('text' => '<br>&nbsp;'.TEXT_TASK_DUE.'<br>&nbsp;' . //tep_draw_input_field('admin_task_due', $tInfo->admin_task_due) .
		'&nbsp; <script>DateInput(\'admin_task_due\', true, \'YYYY-MM-DD\')</script>');

						  }
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT, 'onClick="validateForm();return document.returnValue"') . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $HTTP_GET_VARS['tID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');    
	  break;
	 
    case 'del_task': 
      $heading[] = array('text' => '<b>Delete '.$admin['admin_task_name'] . '</b>');
     
      $contents = array('form' => tep_draw_form('edit', FILENAME_ADMIN_TASKS, 'action=task_delete&page=' . $page . '&tID=' . $admin['admin_task_id'], 'post', 'enctype="multipart/form-data"')); 
      $contents[] = array('text' => tep_draw_hidden_field('admin_task_id', $tInfo->admin_task_id));
      $contents[] = array('align' => 'center', 'text' =>  sprintf('Are you certain you wish to delete the current task'. $tInfo->admin_task_name .'?'));    
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $HTTP_GET_VARS['tID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');    
      break;
    case 'new_project':
      $heading[] = array('text' => '<b>Adding a New Project</b>');

      $contents = array('form' => tep_draw_form('new_project', FILENAME_ADMIN_TASKS, 'action=project_new&projID=' . $gInfo->admin_projects_id, 'post', 'enctype="multipart/form-data"')); 
      if ($HTTP_GET_VARS['gName'] == 'false') {
        $contents[] = array('text' => 'Your Project Name Must have a Minimum of 3 characters.<br>&nbsp;');
      } elseif ($HTTP_GET_VARS['gName'] == 'used') {
        $contents[] = array('text' => 'You cannot use that Project Name. Project Already Exists!<br>Try a new name.<br>&nbsp;');
      }
      $contents[] = array('text' => tep_draw_hidden_field('set_projects_id', substr($add_projects_prepare, 4)) );
      $contents[] = array('text' => 'New Project Name:<br>');      
      $contents[] = array('align' => 'center', 'text' => tep_draw_input_field('admin_projects_name'));
	  //Added for Customer Assignment
	   $customer_assigned_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $customer_assigned_query = tep_db_query("select customers_id, customers_firstname, customers_lastname from customers");
	      while ($customer_assigned = tep_db_fetch_array($customer_assigned_query)) {
		$customer_assigned_array[] = array('id' => $customer_assigned['customers_id'],
					'text' => $customer_assigned['customers_lastname'].',&nbsp;'. $customer_assigned['customers_firstname']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;Customer Project:<br>&nbsp;' . tep_draw_pull_down_menu('admin_projects_customer', $customer_assigned_array, '0')); 
		  //END
      $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $gInfo->admin_projects_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a> ' . tep_image_submit('button_next.gif', IMAGE_NEXT) );    
      break;
    case 'edit_project': 
      $heading[] = array('text' => '<b>Editing Project:&nbsp;'. $gInfo->admin_projects_name .'</b>');

      $contents = array('form' => tep_draw_form('edit_project', FILENAME_ADMIN_TASKS, 'action=project_edit&projID=' . $HTTP_GET_VARS['projID'], 'post', 'enctype="multipart/form-data"')); 
      if ($HTTP_GET_VARS['gName'] == 'false') {
        $contents[] = array('text' => '<font color="red"><b>ERROR:</b> The Project name must have more than 3 characters!</font>');
      } elseif ($HTTP_GET_VARS['gName'] == 'used') {
        $contents[] = array('text' => '<font color="red"><b>ERROR:</b><br>You cannot use that Project Name. Project Already Exists!<br>Try a new name.<br>&nbsp;');
      }      
      $contents[] = array('align' => 'center', 'text' => 'You can Edit the Projects name:<br>&nbsp;<br>' . tep_draw_input_field('admin_projects_name', $gInfo->admin_projects_name)); 
	  //Added for Customer Assignment
	   $customer_assigned_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $customer_assigned_query = tep_db_query("select customers_id, customers_firstname, customers_lastname from customers");
	      while ($customer_assigned = tep_db_fetch_array($customer_assigned_query)) {
		$customer_assigned_array[] = array('id' => $customer_assigned['customers_id'],
					'text' => $customer_assigned['customers_lastname'].',&nbsp;'. $customer_assigned['customers_firstname']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;Customer Project:<br>&nbsp;' . tep_draw_pull_down_menu('admin_projects_customer', $customer_assigned_array, $gInfo->admin_projects_customer)); 
		  //END
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $gInfo->admin_projects_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');    
      break;
    case 'del_project': 
      $heading[] = array('text' => '<b>Are you Certain you wish to remove the Project?</b>');

      $contents = array('form' => tep_draw_form('delete_project', FILENAME_ADMIN_TASKS, 'action=project_delete&projID=' . $gInfo->admin_projects_id, 'post', 'enctype="multipart/form-data"')); 
      if ($gInfo->admin_projects_id == 1) {
        $contents[] = array('align' => 'center', 'text' => sprintf('Curently Deleting:&nbsp;', $gInfo->admin_projects_name));
        $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a><br>&nbsp;');
      } else {
        $contents[] = array('text' => tep_draw_hidden_field('set_projects_id', substr($del_projects_prepare, 4)) );
        $contents[] = array('align' => 'center', 'text' => sprintf('Currently Deleting:&nbsp;', $gInfo->admin_projects_name));    
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a><br>&nbsp;');    
      }
      break;
    case 'define_project':      
      $heading[] = array('text' => '<b>Name of the New Project:</b>');
      	
      $contents[] = array('text' => sprintf(TEXT_INFO_DEFINE_INTRO, $project_name['admin_projects_name']));
      if ($HTTP_GET_VARS['projPath'] == 1) {
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $HTTP_GET_VARS['projPath']) . '">' . tep_image_button('button_back.gif', IMAGE_CANCEL) . '</a><br>');
      } else {
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'action=project_default&projPath=' . $HTTP_GET_VARS['projPath']) . '">' . tep_image_button('button_set_default.gif', IMAGE_RIGHTS_SET_DEFAULT) . '</a><br>');
      }
      break;
    case 'show_project': 
      $heading[] = array('text' => '<b>Rename this Project:</b>');
        $check_email_query = tep_db_query("select admin_email_address from " . TABLE_ADMIN . "");
        //$stored_email[];
        while ($check_email = tep_db_fetch_array($check_email_query)) {
          $stored_email[] = $check_email['admin_email_address'];
        }
        
        if (in_array($HTTP_POST_VARS['admin_email_address'], $stored_email)) {
          $checkEmail = "true";
        } else {
          $checkEmail = "false";
        }
      $contents = array('form' => tep_draw_form('show_project', FILENAME_ADMIN_TASKS, 'action=show_project&projID=projects', 'post', 'enctype="multipart/form-data"')); 
      $contents[] = array('text' => $define_files['admin_files_name'] . tep_draw_input_field('level_edit', $checkEmail)); 
      //$contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $gInfo->admin_projects_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');    
      break;
    default:
      if (is_object($tInfo)) {
        $heading[] = array('text' => '<b>&nbsp;'.TEXT_TASK_INFO.'</b>');
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $tInfo->admin_task_id . '&action=edit_task') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $tInfo->admin_task_id . '&action=del_task') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a><br>&nbsp;');
        $contents[] = array('text' => '&nbsp;<b>'.TEXT_TASKNAME.'</b><br>&nbsp;' . $tInfo->admin_task_name);
        $contents[] = array('text' => '&nbsp;<b>'.TEXT_TASK_DESCRIPTION.'</b><br>&nbsp;' . $tInfo->admin_task_description);
        $contents[] = array('text' => '&nbsp;<b>'.TEXT_TASK_PROJECT.'<br></b>' . $tInfo->admin_projects_name);
        $contents[] = array('text' => '&nbsp;<b>'.TEXT_DATE_ASSIGNED.'</b><br>&nbsp;' . $tInfo->admin_task_created);
        $contents[] = array('text' => '&nbsp;<b>'.TEXT_DATE_MODDED.'</b><br>&nbsp;' . $tInfo->admin_task_modified);
		if ($tInfo->admin_task_status == '12' || ($tInfo->admin_task_status == '13' || ($tInfo->admin_task_status == '14'))){
		 $contents[] = array('text'=>'&nbsp;<b>'.TEXT_TASK_DUE.'</b><br>Recurring Tasks do not need a Due Date');
			  } else {
	     				$contents[] = array('text' => '&nbsp;<b>'.TEXT_TASK_DUE.'</b><br>&nbsp;<b>' . $tInfo->admin_task_due .'</b>');
								  }
	
        //$contents[] = array('text' => '&nbsp;<b>' . TEXT_INFO_LOGDATE . '</b><br>&nbsp;' . $tInfo->admin_logdate);
        //$contents[] = array('text' => '&nbsp;<b>' . TEXT_INFO_LOGNUM . '</b>' . $tInfo->admin_lognum);
        $contents[] = array('text' => '<br>');
      } elseif (is_object($gInfo)) {
        $heading[] = array('text' => '<b>&nbsp;Options for '. $gInfo->admin_projects_name.'</b>');
        $display_text =' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $gInfo->admin_projects_id . '&action=edit_project') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_ADMIN_TASKS, 'projID=' . $gInfo->admin_projects_id . '&action=del_project') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>';
        $contents[] = array('align' => 'center', 'text' => $display_text);
	}
	  }
  
  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
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
