<?php

/**
 * Implementation of schedule page
 * 
 * @param $cst_id
 *   cst node ID
 * 
 * @return theme 
 * 
 */
function aitico_core_schedule_page($cst_id) {

    //get cst info
    $cst_node = node_load($cst_id);
    $cst_title = $cst_node->title;
    $site_id = $cst_node->field_parent_site['und'][0]['target_id'];

    //get site info
    $site_node = node_load($site_id);
    $site_title = $site_node->title;
    $company_id = $site_node->field_parent_company['und'][0]['target_id'];

    //get company info
    $company_node = node_load($company_id);
    $company_title = $company_node->title;

    $title = 'Schedule';
    $all_group_name = get_all_files_group($cst_id);

    //get first group id
    $initial_group = array_shift(array_values($all_group_name));
    $initial_group_id = $initial_group->nid;

    //get schedule info
    $schedule_info = get_all_schedule_info_by_group($initial_group_id);

    return theme('aitico-core-schedule', array(
                'title' => $title,
                'cst_title' => $cst_title,
                'site_title' => $site_title,
                'company_title' => $company_title,
                'group_lists' => $all_group_name,
                'schedule_info' => $schedule_info
            ));
}

/**
 * Get all file group for a cst
 * 
 * @param $cst_id
 *   cst node ID
 * 
 * @return $results
 *   file group info array
 */
function get_all_files_group($cst_id) {
    $query = "SELECT n.nid,n.title FROM {node} n ,{field_data_field_cst} cst 
                WHERE cst.entity_id = n.nid AND cst.field_cst_target_id = $cst_id";

    $results = db_query($query)->fetchAll();

    return $results;
}

/**
 * Get all slot for a file group
 * 
 * @param   $file_group_id
 *   File group node ID
 * 
 * @return  $results
 *   Slot array 
 */
function get_all_slot_of_a_group($file_group_id) {
    global $user;
    $uid = $user->uid;
    $role_query = db_query("SELECT rid FROM {users_roles} WHERE uid = :uid" , array(':uid' => $uid))->fetch(); 
    
     if ($role_query->rid == CONTENT_ADMIN_RID) {
        $permission = 2;
    } elseif ($role_query->rid == SUPER_CONTENT_ADMIN_RID) {
        $permission = 1;
    }
    
    
    $query = "SELECT n.* FROM  {node} n ,{field_data_field_filegroup} fg ,
                field_data_field_permission p
                WHERE fg.entity_id = n.nid AND fg.field_filegroup_target_id = $file_group_id
                and p.entity_id= n.nid and p.field_permission_value = $permission";
    $results = db_query($query)->fetchAll();

    return $results;
}

/**
 * Add a blank row in schedule page
 * 
 * @param $file_group_id
 *   File group node ID
 * 
 */
function aitico_core_add_blank_schedule($file_group_id) {

    $file_slots = get_all_slot_of_a_group($file_group_id);
    $slot_number = count($file_slot);
    $file_schedule = get_file_schedule($file_group_id);

    foreach ($file_slots as $file_slot_row) {
        $file_slot_id = $file_slot_row->nid;
        $file_slot_node = node_load($file_slot_id);
        $file_node = get_files_node($file_slot_id, FILE_TYPE_CODE_SCHEDULE, $file_schedule->nid);
    }
    aitico_core_group_schedule($file_group_id);
}

/**
 * Add table while file group changes in schedule page
 * 
 * @param  $file_group_id
 *   File group node ID
 * 
 * @return mixed 
 */
function aitico_core_group_schedule($file_group_id) {
   
    $file_slots = get_all_slot_of_a_group($file_group_id);    
    $slot_number = count($file_slots);
    
    //get schedule info
    $schedule_info = get_all_schedule_info_by_group($file_group_id);
    

    $group_info = theme('aitico-schedule-view-group', 
            array('file_group_id' => $file_group_id, 
                'slot_number' => $slot_number, 
                'files_slots' => $file_slots,
                'schedule_info'=>$schedule_info));
    
    $output = '<table class="table table-bordered table-hover" id="schedule-table">' . $group_info . '</table>';
    return ajax_deliver(array(
                '#type' => 'ajax',
                '#commands' => array(ajax_command_replace('#schedule-table', $output),
                    )));
}

/**
 * Update Schedule Information 
 * 
 * @param $schedule_id
 *   Schedule node ID
 */

function update_schedule_info($schedule_id) {

    $date_str = $_POST['date'];
    $date_str_arr = explode('/', $date_str);
    $date_str_f = $date_str_arr[0] . '-' . $date_str_arr[1] . '-' . $date_str_arr[2];
    $time_str = $_POST['time'];

    $date_time = $date_str_f . ' ' . $time_str;
    $date_to_timestamp = strtotime($date_time);

    $schedule_node = node_load($schedule_id);
    $schedule_node->field_schedule_time['und'][0]['value'] = $date_to_timestamp;
    node_save($schedule_node);
}

/**
 * Get schedule info by file group id
 * 
 * @param   $group_id
 *   File group node ID
 * 
 * @return  $schedule_info 
 * 
 */
function get_all_schedule_info_by_group($group_id) {   
    global $user;
    $query = "SELECT n.nid as schedule_file_id,n.type,
                    fs.field_file_schedule_target_id as parent_schedule_id,
                    field_slot_target_id as parent_slot_id,
                    st.field_schedule_time_value as schedule_time
                FROM {node} n 
                LEFT JOIN {field_data_field_file_schedule} fs ON fs.entity_id = n.nid
                LEFT JOIN {field_data_field_parent_file_group} fg ON fg.entity_id = fs.field_file_schedule_target_id
                LEFT JOIN {field_data_field_slot} sl ON sl.entity_id = n.nid
                LEFT JOIN {field_data_field_schedule_time} st ON  st.entity_id = fs.field_file_schedule_target_id
                WHERE n.type = 'files' AND fs.field_file_schedule_target_id!=''
                AND n.uid = :uid 
                AND fg.field_parent_file_group_target_id = :group_id ";

    $results = db_query($query , array(':uid' => $user->uid , ':group_id' => $group_id ))->fetchAll();

    $schedule_info = array();
    $schedule_id = 0;
    foreach ($results as $row) {
        if ($row->parent_schedule_id != $schedule_id)
            $schedule_id = $row->parent_schedule_id;

        $schedule_info[$schedule_id]['schedule_time'] = $row->schedule_time;
        $schedule_info[$schedule_id]['files_info'][] = $row;
    }
    return $schedule_info;
}

/**
 * Remove a schedule file
 * 
 * @param  $file_group_id
 *   File group node ID
 * 
 * @param  $node_id
 *   File node ID
 * 
 */
function aitico_core_remove_schedule_file($file_group_id, $node_id) {
    node_delete($node_id);
    aitico_core_group_schedule($file_group_id);
}
/**
 * Duplicate a schedule info
 * 
 * @param  $file_group_id
 *   File group node ID
 * 
 * @param  $schedule_id
 *   Schedule node ID 
 * 
 * 
 */
function aitico_core_duplicate_schedule($file_group_id , $schedule_id) {
    
    $new_schedule = node_load($schedule_id); 
    $original_schedule_id = $schedule_id;    
    unset($new_schedule->nid);
    unset($new_schedule->vid);
    node_save($new_schedule);
   
    $file_slots = get_all_slot_of_a_group($file_group_id);         

    foreach ($file_slots as $file_slot_row) {        
        $file_slot_id = $file_slot_row->nid;              
        $file_node = get_files_node($file_slot_id, FILE_TYPE_CODE_SCHEDULE, $original_schedule_id);
        unset($file_node->nid);
        unset($file_node->vid);     
        $file_node->field_file_schedule['und'][0]['target_id'] = $new_schedule->nid;
        node_save($file_node);
    }
    
}
