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
  
  $tasks_statuses = array();
  $tasks_status_array = array();
  $tasks_status_query = tep_db_query("select admin_status_id, admin_status_name from admin_tasks_statuses");
  while ($tasks_status = tep_db_fetch_array($tasks_status_query)) {
  $tasks_statuses[] = array('id' => $tasks_status['admin_status_id'],
                               'text' => $tasks_status['admin_status_name']);
  $tasks_status_array[$tasks_status['admin_status_id']] = $tasks_status['admin_status_name'];
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
			  tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS['projID'] . '&error=email&action=new_task&orgin=' . $HTTP_GET_VARS['orgin']));
		    else
	          tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . 'tID=' . $HTTP_GET_VARS['tID'] . '&error=email&action=new_task'));
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
								   'admin_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_id']),
                                  'admin_task_created' => 'now()');
        	
          tep_db_perform(TABLE_ADMIN_TASKS, $sql_data_array);
          $admin_id = tep_db_insert_id();
          $check_query=tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id='" . $HTTP_POST_VARS['admin_projects_id'] . "'");
	  
	  if($check_result=tep_db_fetch_array($check_query))
	    $admin_projects_name=$check_result['admin_projects_name'];
	  
	  if ($admin_projects_name==TEXT_INSTRUCTOR_ENTRY){
	     $sql_data_array=array('admin_id'=>$admin_id);
	     tep_db_perform(TABLE_INSTRUCTORS,$sql_data_array);
	  }
	  
           tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin_id));
        }
        break;
      case 'task_edit':
             
       $sql_data_array = array('admin_projects_id' => tep_db_prepare_input($HTTP_POST_VARS['admin_projects_id']),
                                  'admin_task_name' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_name']),
                                  'admin_task_description' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_description']),
								  'admin_task_status' => tep_db_prepare_input($HTTP_POST_VARS['admin_task_status']),
                                  'admin_task_modified' => 'now()');
        
          tep_db_perform(TABLE_ADMIN_TASKS, $sql_data_array, 'update', 'admin_task_id = \'' . $admin_task_id . '\'');

          $check_query=tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id='" . tep_db_prepare_input($HTTP_POST_VARS['admin_projects_id']) . "'");
	  
	  if($check_result=tep_db_fetch_array($check_query))
	    $admin_projects_name=$check_result['admin_projects_name'];

	  if ($admin_projects_name!=TEXT_INSTRUCTOR_ENTRY){
	    tep_db_query("DELETE from " . TABLE_INSTRUCTORS . " where admin_id='" . $admin_id ."'"); 
	  } else {
		  if ($admin_projects_name==TEXT_INSTRUCTOR_ENTRY){
			 $check_query=tep_db_query("select instructors_id from " . TABLE_INSTRUCTORS . " where admin_id='" . $admin_id . "'");
			 if (tep_db_num_rows($check_query)<=0){
			 	$sql_data_array=array('admin_id'=>$admin_id);
			 	tep_db_perform(TABLE_INSTRUCTORS,$sql_data_array);
			 }
		  }
      tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin_id));
        }
        break;
      case 'task_delete':
        $admin_task_id = tep_db_prepare_input($HTTP_POST_VARS['admin_task_id']);
	    //tep_db_query("delete from " . TABLE_INSTRUCTORS . " where admin_id = '" . $admin_id . "'");
        tep_db_query("delete from " . TABLE_ADMIN_TASKS . " where admin_task_id = '" . $admin_task_id . "'");
        
        tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page']));
        break;
      case 'project_define':
        $selected_checkbox = $HTTP_POST_VARS['projects_to_boxes'];
        $projects_type=$HTTP_POST_VARS['projects_type'];
	//if ($projects_type=='E'){
           //$define_files_query = tep_db_query("select admin_files_id from " . TABLE_ADMIN_FILES . " where admin_files_type='E' order by admin_files_id");}
	//else if ($projects_type=='A'){
          // $define_files_query = tep_db_query("select admin_files_id from " . TABLE_ADMIN_FILES . " order by admin_files_id");
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
      case 'project_default':
         $project_id=(isset($HTTP_GET_VARS['projPath'])?(int)$HTTP_GET_VARS['projPath']:0);
	 if ($project_id<=0) tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP));
         $project_query=tep_db_query("SELECT admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id='" . $project_id . "'");
	 $project_result=tep_db_fetch_array($project_query);
	 
	 tep_set_default_project_rights($project_result['admin_projects_name'],$project_id);

	 tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP,'action=define_project&projPath=' . $project_id));
      case 'project_delete':
        $set_projects_id = tep_db_prepare_input($HTTP_POST_VARS['set_projects_id']);
        
        tep_db_query("delete from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" . $HTTP_GET_VARS['projID'] . "'");
        //tep_db_query("alter table " . TABLE_ADMIN_FILES . " change admin_projects_id admin_projects_id set( " . $set_projects_id . " ) NOT NULL DEFAULT '1' ");
        //tep_db_query("delete from " . TABLE_ADMIN . " where admin_projects_id = '" . $HTTP_GET_VARS['projID'] . "'");
               
        tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=projects'));
        break;        
      case 'project_edit':
        $admin_projects_name = ucwords(strtolower(tep_db_prepare_input($HTTP_POST_VARS['admin_projects_name'])));
        $name_replace = ereg_replace (" ", "%", $admin_projects_name);
        
        if (($admin_projects_name == '' || NULL) || (strlen($admin_projects_name) <= 5) ) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS[projID] . '&gName=false&action=action=edit_project'));
        } else {
          $check_projects_name_query = tep_db_query("select admin_projects_name as project_name_edit from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id <> " . $HTTP_GET_VARS['projID'] . " and admin_projects_name like '%" . $name_replace . "%'");
          $check_duplicate = tep_db_num_rows($check_projects_name_query);
          if ($check_duplicate > 0){
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS['projID'] . '&gName=used&action=edit_project'));
          } else {
            $admin_projects_id = $HTTP_GET_VARS['projID'];
            tep_db_query("update " . TABLE_ADMIN_PROJECTS . " set admin_projects_name = '" . $admin_projects_name . "' where admin_projects_id = '" . $admin_projects_id . "'");
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $admin_projects_id));
          }
        }
        break;              
      case 'project_new':
        $admin_projects_name = ucwords(strtolower(tep_db_prepare_input($HTTP_POST_VARS['admin_projects_name'])));
        $name_replace = ereg_replace (" ", "%", $admin_projects_name);
        
        if (($admin_projects_name == '' || NULL) || (strlen($admin_projects_name) <= 5) ) {
          tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS[projID] . '&gName=false&action=new_project'));
        } else {
          $check_projects_name_query = tep_db_query("select admin_projects_name as project_name_new from " . TABLE_ADMIN_PROJECTS . " where admin_projects_name like '%" . $name_replace . "%'");
          $check_duplicate = tep_db_num_rows($check_projects_name_query);
          if ($check_duplicate > 0){
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS['projID'] . '&gName=used&action=new_project'));
          } else {
            $sql_data_array = array('admin_projects_name' => $admin_projects_name);
            tep_db_perform(TABLE_ADMIN_PROJECTS, $sql_data_array);
            $admin_projects_id = tep_db_insert_id();

            $set_projects_id = tep_db_prepare_input($HTTP_POST_VARS['set_projects_id']);
            $add_project_id = $set_projects_id . ',\'' . $admin_projects_id . '\'';
            //tep_db_query("alter table " . TABLE_ADMIN_FILES . " change admin_projects_id admin_projects_id set( " . $add_project_id . ") NOT NULL DEFAULT '1' ");
            
            tep_redirect(tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $admin_projects_id));
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
            <td class="smallText" align="right"><?php //echo tep_draw_form('status', FILENAME_ADMIN_TASKS_COMP, '', 'get'); ?>
              <?php //echo 'View By Status:' . ' ' . tep_draw_pull_down_menu('status', array_merge(array(array('id' => '', 'text' => 'All Tasks')), $tasks_statuses), '', 'onChange="this.form.submit();"'); ?>
              </form></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">

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
               <td class="dataTableHeadingContent" title="Number"> ID </td>
			    <td class="dataTableHeadingContent" title="CaseInsensitiveString">Task Name</td>
                <td class="dataTableHeadingContent" title="CaseInsensitiveString">Task Description</td>
                <td class="dataTableHeadingContent" align="center" title="CaseInsensitiveString">Project</td>
                <td class="dataTableHeadingContent" align="center" title="CaseInsensitiveString">Task Priority</td>
				<td class="dataTableHeadingContent" align="center" title="CaseInsensitiveString">Assigned To:</td>
				<td class="dataTableHeadingContent" align="center" title="Number">Task Status</td>
                <td class="dataTableHeadingContent" align="right">Action</td>
              </tr>
			  </THEAD>
<?php
  $db_admin_query_raw = "select * from " . TABLE_ADMIN_TASKS . " where admin_task_status = 1 order by admin_task_id";
  
  $db_admin_split = new splitPageResults($HTTP_GET_VARS['page'], MAX_DISPLAY_SEARCH_RESULTS, $db_admin_query_raw, $db_admin_query_numrows);
  $db_admin_query = tep_db_query($db_admin_query_raw);
  //$db_admin_num_row = tep_db_num_rows($db_admin_query);
  
  while ($admin = tep_db_fetch_array($db_admin_query)) {
    $admin_project_query = tep_db_query("select admin_projects_name from " . TABLE_ADMIN_PROJECTS . " where admin_projects_id = '" . $admin['admin_projects_id'] . "'");
    $admin_project = tep_db_fetch_array ($admin_project_query);
    if (((!$HTTP_GET_VARS['tID']) || ($HTTP_GET_VARS['tID'] == $admin['admin_task_id'])) && (!$tInfo) ) {
      $tInfo_array = array_merge($admin, $admin_project);
      $tInfo = new objectInfo($tInfo_array);
    }
   if ( (is_object($tInfo)) && ($admin['admin_task_id'] == $tInfo->admin_task_id) ) {
      echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_task_id'] . '&action=edit_task') . '\'">' . "\n";
    } else {
      echo '<tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_task_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $admin['admin_task_id']; ?></td>
				<td class="dataTableContent"><?php echo $admin['admin_task_name']; ?></td>
                <td class="dataTableContent"><?php echo $admin['admin_task_description']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $admin_project['admin_projects_name']; ?></td>
                <td class="dataTableContent" align="center"><?php //echo $admin['admin_task_priority'];
				if ($admin['admin_task_priority'] == '0'){
				echo '<b><i>No Priority Assigned</b></i>';
				} else {
				  $priority_query = tep_db_query("select admin_priority_name from admin_tasks_priorities where admin_priority_id = '" . $admin['admin_task_priority'] . "'");
    $priority = tep_db_fetch_array ($priority_query);
	echo $priority['admin_priority_name'];
	}?></td>
	<td class="dataTableContent"><?php //echo $admin['admin_id'];
	 $admin_name_query = tep_db_query("select admin_firstname, admin_lastname from admin where admin_id = '" . $admin['admin_id'] . "'");
    $admin_name = tep_db_fetch_array ($admin_name_query);
	echo '<b>'. $admin_name['admin_lastname'] . ',&nbsp;' . $admin_name['admin_firstname'] .'</b>';?></td>
	 <td class="dataTableContent" align="center"><?php //echo $admin['admin_task_priority'];
				if ($admin['admin_task_status'] == '0'){
				echo '<b><i>Not Started</b></i>';
				} else {
				  $status_query = tep_db_query("select admin_status_name from admin_tasks_statuses where admin_status_id = '" . $admin['admin_task_status'] . "'");
    $status = tep_db_fetch_array ($status_query);
	echo $status['admin_status_name'];
	}?></td>
	
                <td class="dataTableContent" align="right"><?php if ( (is_object($tInfo)) && ($admin['admin_id'] == $tInfo->admin_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $admin['admin_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
			  <tr class="dataTableRow"><td colspan="8" align="Center"><hr width="75%" color="#660000" noshade></td></tr>
<?php
  } 
?>
</table>
<SCRIPT type="text/javascript">
var st1 = new SortableTable(document.getElementById("table-1"),
	["Number", "CaseInsensitiveString", "CaseInsensitiveString", "CaseInsesitiveString", "CaseInsensitiveString", "CaseInsensitiveString", "Number", "None"]);

     </SCRIPT>
<table>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $db_admin_split->display_count($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $HTTP_GET_VARS['page'], TEXT_DISPLAY_NUMBER_OF_TASKS); ?><br><?php echo $db_admin_split->display_links($db_admin_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $HTTP_GET_VARS['page']); ?></td>
                    <td class="smallText" valign="top" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=projects') . '">' . tep_image_button('button_admin_projects.gif', 'Projects List') . '</a>'; echo ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $tInfo->admin__task_id . '&action=new_task') . '">' . tep_image_button('button_admin_task.gif', 'New Task') . '</a>'; ?>&nbsp;</td>
                  </tr>
                </table></td>
              </tr>
            </table>

            </td>
<?php
  $heading = array();
  $contents = array();
  switch ($HTTP_GET_VARS['action']) {  
    case 'new_task': 
      $heading[] = array('text' => '<b>Add a new Task</b>');

      if ($HTTP_GET_VARS['orgin']!='' && (int)$HTTP_GET_VARS['projID']!=0){
         $contents = array('form' => tep_draw_form('newtask', FILENAME_ADMIN_TASKS_COMP, 'action=task_new&orgin=' . $HTTP_GET_VARS['orgin'] . '&projID=' . $HTTP_GET_VARS["projID"], 'post', 'enctype="multipart/form-data"')); }
      else {
         $contents = array('form' => tep_draw_form('newtask', FILENAME_ADMIN_TASKS_COMP, 'action=task_new&page=' . $page . 'tID=' . $HTTP_GET_VARS['tID'], 'post', 'enctype="multipart/form-data"')); }
	 
      if ($HTTP_GET_VARS['error']) {
        $contents[] = array('text' => TEXT_INFO_ERROR); 
      }
      $contents[] = array('text' => '<br>&nbsp;Task Name:<br>&nbsp;' . tep_draw_input_field('admin_task_name')); 
      $contents[] = array('text' => '<br>&nbsp;Task Description:<br>&nbsp;' . tep_draw_input_field('admin_task_description'));
          
      if ($HTTP_GET_VARS['orgin']!='' && (int)$HTTP_GET_VARS['projID']!=0){
        $contents[] = array('text' => '<br>&nbsp;Assigned to Project:<br>&nbsp;' . tep_draw_hidden_field('admin_projects_id', $HTTP_GET_VARS['projID']) . TEXT_INSTRUCTOR_ENTRY);
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT, 'onClick="validateForm();return document.returnValue"') . ' <a href="' . tep_href_link($HTTP_GET_VARS['orgin']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      } else {
	  //Added for Assignees
	   $assignee_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $assignee_query = tep_db_query("select admin_id, admin_firstname, admin_lastname from admin");
	      while ($assignee = tep_db_fetch_array($assignee_query)) {
		$assignee_array[] = array('id' => $assignee['admin_id'],
					'text' => $assignee['admin_lastname'].',&nbsp;'. $assignee['admin_firstname']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;Assigned to:<br>&nbsp;' . tep_draw_pull_down_menu('admin_id', $assignee_array, '0')); 
		  //END
		  	  //Added for Priorities
	   $priority_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $priority_query = tep_db_query("select admin_priority_id, admin_priority_name from admin_tasks_priorities");
	      while ($priorities = tep_db_fetch_array($priority_query)) {
		$priorities_array[] = array('id' => $priorities['admin_priority_id'],
					'text' => $priorities['admin_priority_name']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;Task Priority:<br>&nbsp;' . tep_draw_pull_down_menu('admin_task_priority', $priorities_array, '0')); 
		  //END
	      $projects_array = array(array('id' => '0', 'text' => TEXT_NONE));
	      $projects_query = tep_db_query("select admin_projects_id, admin_projects_name from " . TABLE_ADMIN_PROJECTS);
	      while ($projects = tep_db_fetch_array($projects_query)) {
		$projects_array[] = array('id' => $projects['admin_projects_id'],
					'text' => $projects['admin_projects_name']);
	      }
	      $contents[] = array('text' => '<br>&nbsp;Assigned to Project:<br>&nbsp;' . tep_draw_pull_down_menu('admin_projects_id', $projects_array, '0')); 
              $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT, 'onClick="validateForm();return document.returnValue"') . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $HTTP_GET_VARS['tID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
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
     
      $contents = array('form' => tep_draw_form('edit', FILENAME_ADMIN_TASKS_COMP, 'action=task_delete&page=' . $page . '&tID=' . $admin['admin_task_id'], 'post', 'enctype="multipart/form-data"')); 
      $contents[] = array('text' => tep_draw_hidden_field('admin_task_id', $tInfo->admin_task_id));
      $contents[] = array('align' => 'center', 'text' =>  sprintf('Are you certain you wish to delete the current task'. $tInfo->admin_task_name .'?'));    
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $HTTP_GET_VARS['tID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');    
      break;
    case 'new_project':
      $heading[] = array('text' => '<b>Adding a New Project</b>');

      $contents = array('form' => tep_draw_form('new_project', FILENAME_ADMIN_TASKS_COMP, 'action=project_new&projID=' . $gInfo->admin_projects_id, 'post', 'enctype="multipart/form-data"')); 
      if ($HTTP_GET_VARS['gName'] == 'false') {
        $contents[] = array('text' => TEXT_INFO_GROUPS_NAME_FALSE . '<br>&nbsp;');
      } elseif ($HTTP_GET_VARS['gName'] == 'used') {
        $contents[] = array('text' => 'You cannot use that Project Name. Project Already Exists!<br>Try a new name.<br>&nbsp;');
      }
      $contents[] = array('text' => tep_draw_hidden_field('set_projects_id', substr($add_projects_prepare, 4)) );
      $contents[] = array('text' => 'New Project Name:<br>');      
      $contents[] = array('align' => 'center', 'text' => tep_draw_input_field('admin_projects_name'));
      $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $gInfo->admin_projects_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a> ' . tep_image_submit('button_next.gif', IMAGE_NEXT) );    
      break;
    case 'edit_project': 
      $heading[] = array('text' => '<b>Editing Project:&nbsp;'. $gInfo->admin_projects_name .'</b>');

      $contents = array('form' => tep_draw_form('edit_project', FILENAME_ADMIN_TASKS_COMP, 'action=project_edit&projID=' . $HTTP_GET_VARS['projID'], 'post', 'enctype="multipart/form-data"')); 
      if ($HTTP_GET_VARS['gName'] == 'false') {
        $contents[] = array('text' => TEXT_INFO_GROUPS_NAME_FALSE . '<br>&nbsp;');
      } elseif ($HTTP_GET_VARS['gName'] == 'used') {
        $contents[] = array('text' => TEXT_INFO_GROUPS_NAME_USED . '<br>&nbsp;');
      }      
      $contents[] = array('align' => 'center', 'text' => 'You can Edit the Projects name:<br>&nbsp;<br>' . tep_draw_input_field('admin_projects_name', $gInfo->admin_projects_name)); 
      $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $gInfo->admin_projects_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');    
      break;
    case 'del_project': 
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_GROUPS . '</b>');

      $contents = array('form' => tep_draw_form('delete_project', FILENAME_ADMIN_TASKS_COMP, 'action=project_delete&projID=' . $gInfo->admin_projects_id, 'post', 'enctype="multipart/form-data"')); 
      if ($gInfo->admin_projects_id == 1) {
        $contents[] = array('align' => 'center', 'text' => sprintf(TEXT_INFO_DELETE_GROUPS_INTRO_NOT, $gInfo->admin_projects_name));
        $contents[] = array('align' => 'center', 'text' => '<br><a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS['projID']) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a><br>&nbsp;');
      } else {
        $contents[] = array('text' => tep_draw_hidden_field('set_projects_id', substr($del_projects_prepare, 4)) );
        $contents[] = array('align' => 'center', 'text' => sprintf(TEXT_INFO_DELETE_GROUPS_INTRO, $gInfo->admin_projects_name));    
        $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS['projID']) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a><br>&nbsp;');    
      }
      break;
    case 'define_project':      
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DEFINE . '</b>');
      	
      $contents[] = array('text' => sprintf(TEXT_INFO_DEFINE_INTRO, $project_name['admin_projects_name']));
      if ($HTTP_GET_VARS['projPath'] == 1) {
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $HTTP_GET_VARS['projPath']) . '">' . tep_image_button('button_back.gif', IMAGE_CANCEL) . '</a><br>');
      } else {
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'action=project_default&projPath=' . $HTTP_GET_VARS['projPath']) . '">' . tep_image_button('button_set_default.gif', IMAGE_RIGHTS_SET_DEFAULT) . '</a><br>');
      }
      break;
    case 'show_project': 
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_GROUP . '</b>');
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
      $contents = array('form' => tep_draw_form('show_project', FILENAME_ADMIN_TASKS_COMP, 'action=show_project&projID=projects', 'post', 'enctype="multipart/form-data"')); 
      $contents[] = array('text' => $define_files['admin_files_name'] . tep_draw_input_field('level_edit', $checkEmail)); 
      //$contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_insert.gif', IMAGE_INSERT) . ' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $gInfo->admin_projects_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');    
      break;
    default:
      if (is_object($tInfo)) {
        $heading[] = array('text' => '<b>&nbsp;Task Information:</b>');
        $contents[] = array('align' => 'center', 'text' => '<a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $tInfo->admin_task_id . '&action=edit_task') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'page=' . $HTTP_GET_VARS['page'] . '&tID=' . $tInfo->admin_task_id . '&action=del_task') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a><br>&nbsp;');
        $contents[] = array('text' => '&nbsp;<b>Task Name:</b><br>&nbsp;' . $tInfo->admin_task_name);
        $contents[] = array('text' => '&nbsp;<b>Task Description:</b><br>&nbsp;' . $tInfo->admin_task_description);
        $contents[] = array('text' => '&nbsp;<b>Project Assigned to:<br></b>' . $tInfo->admin_projects_name);
        $contents[] = array('text' => '&nbsp;<b>Date Assigned:</b><br>&nbsp;' . $tInfo->admin_task_created);
        $contents[] = array('text' => '&nbsp;<b>Date Modified:</b><br>&nbsp;' . $tInfo->admin_task_modified);
        //$contents[] = array('text' => '&nbsp;<b>' . TEXT_INFO_LOGDATE . '</b><br>&nbsp;' . $tInfo->admin_logdate);
        //$contents[] = array('text' => '&nbsp;<b>' . TEXT_INFO_LOGNUM . '</b>' . $tInfo->admin_lognum);
        $contents[] = array('text' => '<br>');
      } elseif (is_object($gInfo)) {
        $heading[] = array('text' => '<b>&nbsp;Options for '. $gInfo->admin_projects_name.'</b>');
        $display_text =' <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $gInfo->admin_projects_id . '&action=edit_project') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_ADMIN_TASKS_COMP, 'projID=' . $gInfo->admin_projects_id . '&action=del_project') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>';
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
