<?php
/*
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright © 2003 osCommerce
  
  Task Manager Contribution
  Custom Developed by James Harvey
  altered@alteredpixels.net
  Copyright © 2003 Altered Pixels
 
  http://www.alteredpixels.net/

  Contribution Support:
  http://forums.oscommerce.com/index.php?showtopic=203284
*/
?>
<!-- catalog //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => 'Task Manager',
                     'link'  => tep_href_link(FILENAME_ADMIN_TASKS, tep_get_all_get_params(array('selected_box')) . 'selected_box=task_manager'));

  if ($selected_box == 'task_manager' || $menu_dhtml == true) {
    $contents[] = array('text'  =>
	'<a href="' . tep_href_link("admin_tasks.php") . '" class="menuBoxContentLink">Task Manager</a><br>' .
	'<a href="' . tep_href_link("admin_tasks.php?projID=projects") . '" class="menuBoxContentLink">Projects Manager</a><br><br>' .
	'<a href="' . tep_href_link("admin_tasks.php?page=1&tID=&action=new_task") . '" class="menuBoxContentLink">Add a Task</a><br>' .
	 '<a href="' . tep_href_link("admin_tasks.php?projID=1&action=new_project") . '" class="menuBoxContentLink">Add a Project</a><br><br>'.
'<a href="' . tep_href_link("admin_tasks_all.php") . '" class="menuBoxContentLink">Review ALL Tasks</a><br>'.
'<a href="' . tep_href_link("admin_tasks_completed.php") . '" class="menuBoxContentLink">Review Completed Tasks</a><br><br>'.
'<a href="' .tep_href_link("admin_tasks_report.php") .'" class="menuBoxContentLink">Task Manager Report</a><br>'
);
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- catalog_eof //-->