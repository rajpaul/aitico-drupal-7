<?php

/**
 * Add user form
 * 
 * @param $companyId
 *   Company node ID
 * @param $siteId
 *   Site node ID
 * 
 * @return theme
 */
function add_user_form($companyId, $siteId=0) {
    $user_form = render(drupal_get_form('aitico_core_user_form', $companyId, $siteId));
    $title = "Edit User";
    return theme('content_entry_page', array('form_title' => $title,
                'node_add_form' => $user_form));
}

/**
 * User edit form
 * 
 * @param $account
 *   User account object
 * 
 * @return theme
 * 
 */
function edit_user_form($account) {
    $company_id = 0;
    $site_id = 0;
    $company_site = get_company_site_info($account->uid);
    if (!empty($company_site)) {
        $company_id = $company_site->cid;
        $site_id = $company_site->sid;
    }
    $title = "Edit User";
    $user_form = render(drupal_get_form('aitico_core_user_edit_form', $account));
    return theme('content_entry_page', array('form_title' => $title,
                'node_add_form' => $user_form));
}

/**
 * Delete node form
 * 
 * @param $node
 *   A node object
 * 
 * @return theme
 */
function delete_aitico_node_form($node) {
    $title = "Delete";
    $form = drupal_get_form('node_delete_confirm', $node);
    $node_edit_form = drupal_render($form);
    return theme('content_entry_page', array('form_title' => $title, 'node_add_form' => $node_edit_form));
}
/**
 * Delete Aitico schedule node and all file under this schedule
 * 
 * 
 */
function delete_aitico_schedule_node() {
    $nid = $_POST['schedule_nid'];
    $node = node_load($nid);
    $node_type = $node->type;
    if ($node_type == 'file_schedule') {
        $file_schedule_id = $nid;
        //get all child file for a schedule
        $slots_file_id = get_all_schedule_slot_file_id($file_schedule_id);

        $file_id_arr = array();
        foreach ($slots_file_id as $row) {
            $file_id_arr[] = $row->nid;
        }
        //delete file node
        node_delete_multiple($file_id_arr);
    }
    //delete schedule node
    node_delete($nid);
}

/**
 * Edit node form
 * 
 * @param $node
 *   A node object
 * 
 * @return theme 
 * 
 */
function edit_aitico_node_form($node) {

    module_load_include('inc', 'node', 'node.pages');
    $title = "Edit";
    $form = drupal_get_form($node->type . '_node_form', $node);
    $node_edit_form = drupal_render($form);
    return theme('content_entry_page', array('form_title' => $title, 'node_add_form' => $node_edit_form));
}

/**
 * Add site form
 * 
 * @param $parent_company_id
 *   Parent company node ID
 * 
 * @return theme 
 */
function add_site_form($parent_company_id) {
    $title = t("Add Site");
    $node_type = 'site';
    $site_form = get_aitico_node_form($node_type, $parent_company_id);
    return theme('content_entry_page', array('form_title' => $title, 'node_add_form' => $site_form));
}

/**
 * Add charging station form
 * 
 * @param $parent_site_id
 *   Parent site node ID
 * 
 * @return theme 
 * 
 */
function add_charging_station_form($parent_site_id) {
    $title = t("Add Charging Station");
    $node_type = 'charging_station';
    $charging_station_form = get_aitico_node_form($node_type, $parent_site_id);
    return theme('content_entry_page', array('form_title' => $title, 'node_add_form' => $charging_station_form));
}

/**
 * Company form 
 * 
 * @param $nid
 *   A node ID
 *  
 * @return theme
 */
function company_form($nid = null) {
    $node_type = "company";
    $form = get_aitico_node_form($node_type, $parent_id = null);
    return theme('company_form', array('form' => $form));
}

/**
 * Add user form
 * 
 * @param $form 
 * 
 * @param $form_state
 * 
 * @param $companyId
 *   A company node ID
 * 
 * @param $siteId
 *   A site node ID
 * 
 * @return $form 
 *   User add form
 * 
 */
function aitico_core_user_form($form, &$form_state, $companyId, $siteId) {
    $cancel_link = l(t('Cancel'), 'administration',array("attributes" => array("class" => "btn-cancel btn btn-grey btn-small")));            
    
    global $user;
    $form['#validate'][] = 'aitico_core_user_form_validate';
    $form['#companyId'] = $companyId;
    $form['#siteId'] = $siteId;
    // Account information.
    $form['account'] = array(
        '#type' => 'container',
        '#weight' => -10,
    );
    $form['account']['name'] = array(
        '#type' => 'textfield',
        '#title' => t('Name'),
        '#required' => TRUE,
    );
    $form['account']['mail'] = array(
        '#type' => 'textfield',
        '#title' => t('Email'),
        '#maxlength' => EMAIL_MAX_LENGTH,
        '#required' => TRUE,
    );

    $form['account']['pass'] = array(
        '#type' => 'password_confirm',
        '#size' => 25,
        '#required' => TRUE,
    );

    $options = aitico_core_user_form_roles($companyId, $siteId);
    $form['account']['roles'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#title' => t('Roles'),
        '#required' => TRUE,
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save User'),
    );
    $form['cancel'] = array(
          '#markup' => $cancel_link,
          '#weight' => '1000',
    );
    return $form;
}

/**
 * Edit user form
 * 
 * @param $form
 * 
 * @param $form_state
 * 
 * @param $account
 *   A user account object
 * 
 * @return $form
 *   User edit form
 */
function aitico_core_user_edit_form($form, &$form_state, $account) {
    $cancel_link = l(t('Cancel'), 'administration',array("attributes" => array("class" => "btn-cancel btn btn-grey btn-small")));            
    global $user;

    $company_site = get_company_site_info($account->uid);
    $company_id = 0;
    $site_id = 0;
    if (!empty($company_site)) {
        $company_id = $company_site->cid;
        $site_id = $company_site->sid;
    }

    $form['#account'] = $account;
    $form_state['#account'] = $account;
    // Account information.
    $form['account'] = array(
        '#type' => 'container',
        '#weight' => -10,
    );
    $form['account']['name'] = array(
        '#type' => 'textfield',
        '#title' => t('Name'),
        '#required' => TRUE,
        '#default_value' => ($account ? $account->name : ''),
    );
    $form['account']['mail'] = array(
        '#type' => 'textfield',
        '#title' => t('Email'),
        '#maxlength' => EMAIL_MAX_LENGTH,
        '#required' => TRUE,
        '#default_value' => ($account ? $account->mail : ''),
    );

    $form['account']['pass'] = array(
        '#type' => 'password_confirm',
        '#size' => 25,
    );

    $options = aitico_core_user_form_roles($company_id, $site_id);
    $form['account']['roles'] = array(
        '#type' => 'select',
        '#options' => $options,
        '#title' => t('Roles'),
        '#required' => TRUE,
        '#default_value' => ($account ? array_keys($account->roles) : ''),
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save User'),
    );
    $form['cancel'] = array(
          '#markup' => $cancel_link,
          '#weight' => '1000',
    );

    return $form;
}

/**
 * Password change form.
 * 
 * @param $form
 * 
 * @param $form_state
 * 
 * @param $account
 *   A user account object
 * 
 * @return $form
 *   Password change form
 */
function aitico_core_password_form($form, &$form_state, $account) {
    $cancel_link = l(t('Cancel'), 'administration',array("attributes" => array("class" => "btn-cancel btn btn-grey btn-small")));            
    
    global $user;
    $form['#account'] = $account;
    $form_state['#account'] = $account;
    $form['#user'] = $account;
    $current_pass_description = t('Enter your current password to change the  %pass.', array('%pass' => t('password')));

    $form['account']['current_pass'] = array(
        '#type' => 'password',
        '#title' => t('Current password'),
        '#size' => 25,
        '#required' => TRUE,
        '#description' => $current_pass_description,
        '#attributes' => array('autocomplete' => 'off'),
    );

    $form['account']['pass'] = array(
        '#type' => 'password_confirm',
        '#size' => 25,
        '#required' => TRUE,
        '#description' => t('To change the current user password, enter the new password in both fields.'),
    );

    $form['submit'] = array(
        '#type' => 'submit',
        '#value' => t('Save'),
    );
    $form['cancel'] = array(
          '#markup' => $cancel_link,
          '#weight' => '1000',
    );

    return $form;
}

/**
 * change user password
 * 
 * @return theme 
 */
function change_user_password() {
    global $user;
    $title = "Change Password";
    // $account = user_load($uid);
    $account = $user;
    $form = drupal_get_form('aitico_core_password_form', $account);
    $change_password_form = drupal_render($form);

    return theme('content_entry_page', array('form_title' => $title, 'node_add_form' => $change_password_form));
}

/**
 * Get user roles list
 * 
 * @param $company_id
 *   Company node ID
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return $options
 *   User roles array
 */
function aitico_core_user_form_roles($company_id, $site_id) {

    global $user;
    $roles = array();
    $sql = "select rp.permission pr
             from users u, users_roles ur, role r, role_permission rp 
             where u.uid=ur.uid 
             and ur.rid = r.rid 
             and r.rid = rp.rid 
             and r.rid !=2
             and u.uid=" . $user->uid;
    $rows = db_query($sql);
    $roleNames = "";
    foreach ($rows as $row) {
        if ($company_id != 0) {
            if ($site_id == 0) {

                $roleNames = $roleNames .
                        "'" . COMPANY_ADMIN . "'" . ',' . "'" . CONTENT_ADMIN . "'" . ',';
            } else {
                $roleNames = $roleNames . "'" . OPERATOR . "'" . ',';
            }
        } else {

            $roleNames = $roleNames . "'" . SUPER_ADMIN . "'" . ',' . " '" . SUPER_CONTENTADMIN . "'" . ',';
        }
    }

    $options = array();
    if ($roleNames != "") {
        $lastComa = strripos($roleNames, ",");
        $roleNames = substr($roleNames, 0, $lastComa);
        // now get the role list from db
        $roleSql = "select rid, name 
                    from role
                    where name in (" . $roleNames . ")";
        $roleRows = db_query($roleSql);
        foreach ($roleRows as $row) {
            $options[$row->rid] = t($row->name);
        }
    }

    return $options;
}

/**
 * Override of the standard user deletion page. Shows a confirmation box
 * with the user profile visible underneath.
 * 
 * @param $account
 *   User account object
 * 
 * @return theme
 */
function aitico_core_page_user_delete($account) {
    $company_id = 0;
    $site_id = 0;
    $company_site = get_company_site_info($account->uid);
    if (!empty($company_site)) {
        $company_id = $company_site->cid;
        $site_id = $company_site->sid;
    }

    module_load_include('inc', 'user', 'user.pages');
    $title = "Delete User";
    return theme('content_entry_page', array('form_title' => $title, 'node_add_form' => render(drupal_get_form('user_cancel_confirm_form', $account))));
}

/**
 * Language Change
 * 
 * @return mixed
 */
function change_user_language() {
    global $user;
    $title = t("Change Language");    
    $account = $user;

    return "&nbsp;";
}

/**
 * Get user list based on company id and site id
 * 
 * @param $company_id 
 *   Company node ID
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return $user_list
 *   user list array 
 */
function user_render($company_id, $site_id = null) {
    //query for user list
    $user_sql = "SELECT u.*,ucs.cid,ucs.sid,r.name as role 
            FROM {users} u ,{users_companies_sites} ucs,{users_roles} ur,{role} r 
            WHERE ucs.uid = u.uid AND ur.uid=u.uid AND ur.rid = r.rid AND ucs.cid = $company_id";
    if (isset($site_id) && $site_id != "0") {
        $user_sql .= " AND ucs.sid = $site_id";
    }
    $user_list = db_query($user_sql)->fetchAll(PDO::FETCH_ASSOC);

    return $user_list;
}

/**
 * Rendering tree node list view * 
 * 
 * @return $nodes
 *   Nodes array 
 */
function company_render() {
    $model = get_model();
    $nodes = $model->getTreeNode();    
    return $nodes;
}

/**
 * Get company and site id based on user id 
 * 
 * @param $uid 
 *   User ID
 * 
 * @return $company_site
 *   company and site id 
 * 
 * 
 */

function get_company_site_info($uid) {
    $company_site = db_query("SELECT cid, sid FROM {users_companies_sites} WHERE uid =:uid", array(':uid' => $uid))->fetch();
    return $company_site;
}

/**
 * Get site id based on company id 
 * 
 * @param $company_id
 *   Company node ID
 * 
 * @return site id
 * 
 */

function get_site_id_for_company($company_id) {
    $site_info = db_query("SELECT sid FROM {users_companies_sites} WHERE cid =:cid", array(':cid' => $company_id))->fetch();
    return $site_info->sid;
}

/**
 * Get company id based on site id 
 * 
 * @param $site_id
 *   Site node ID 
 * 
 * @return company id
 *   Parent company ID of a site
 * 
 */

function get_company_id_for_site($site_id) {
    // $company_info = db_query("SELECT cid FROM {users_companies_sites} WHERE sid =:sid", array(':sid' => $site_id))->fetch();
    $site_node = node_load($site_id);
    return $site_node->field_parent_company['und'][0]['target_id'];
}

/**
 * Implementation of admin page callback
 * 
 * @return theme
 */

function aitio_core_admin_page() {
    $company_list = theme('aitico-company-list', array('nodes' => company_render()));
    $admin_action = theme('aitico-admin-action', array());
    return theme('aitico-admin-page', array('company_list' => $company_list, 'admin_action' => $admin_action));
}

/**
 *
 * Implementation of admin action edit
 * 
 * @param $node
 * 
 * @return theme
 * 
 * 
 * 
 */

function edit_admin_action($node) {
    module_load_include('inc', 'node', 'node.pages');
    $form = drupal_get_form($node->type . '_node_form', $node);
    $form['#action'] = url('node/' . $node->nid . '/edit/' . $node->type);
    $node_edit_form = drupal_render($form);
    if($node->type =='device'){
    
    return theme('aitico-admin-action-edit-device', array('device_node' => $node));    
    }
    return theme('aitico-admin-action-edit', array('edit_node_form' => $node_edit_form));
}

/**
 * Implementation of admin action files
 * 
 * @param $cst_id
 *   cst node ID
 * 
 * @param $target
 * 
 * @return theme
 * 
 */

function get_files_admin_action($cst_id, $target = false) {
    $file_group_ids = db_query("SELECT  n.nid , n.title , fcst.entity_id FROM 
                        {field_data_field_cst} fcst , {node} n
                        WHERE n.nid = fcst.entity_id AND fcst.field_cst_target_id  = :field_cst_target_id", array(':field_cst_target_id' => $cst_id))->fetchAll();

    $file_group_title = '';
    $file_group_array = array();
    foreach ($file_group_ids as $file_group) {
        if ($file_group_title != $file_group->title)
            $file_group_title = $file_group->title;
        $file_group_array[$file_group_title] = getFileSlotByFilegroup($file_group->nid);
    }

    // print_r($file_group_array);exit;

    return theme('aitico-admin-action-files', array(
                'cst_id' => $cst_id,
                'target' => $target,
                'file_groups' => $file_group_array));
}

/**
 * Implementation of admin action file admin
 * 
 * @param $cstId
 *   cst node ID
 * 
 * @return theme
 * 
 */

function admin_file_admin_action($cstId) {

    if (!$cstId)
        return false;

    $fileGroup = getFileGroupsByCST($cstId);
    foreach ($fileGroup as $group) {
        $groupNslots[$group] = getFileSlotByFilegroup($group);
    }

    
    return theme('aitico-admin-action-file-admin', array('groupNslots' => $groupNslots, 'cstId' => $cstId));
}

/**
 * Get file slot by file group
 * 
 * @param $group
 *   File group node ID
 * 
 * @return $slots
 *   File slot array
 * 
 */


function getFileSlotByFilegroup($group) {
    $slots = array();
    $query = db_select('node', 'n')
            ->fields('n', array('nid', "type", 'title'))
            ->fields("slot", array('entity_id', 'field_filegroup_target_id'));
    $query->condition('slot.field_filegroup_target_id', $group);
    $query->leftJoin('field_data_field_filegroup', 'slot', 'n.nid = slot.field_filegroup_target_id');
    $results = $query->execute();
    foreach ($results as $result) {
        $slots[] = $result->entity_id;
    }
    return $slots;
}


/**
 * Get file group by cst
 * 
 * @param $cstId
 *   cst node ID
 * 
 * @return $fileGroup
 *   File group array
 * 
 */


function getFileGroupsByCST($cstId) {
    $fileGroup = array();
    $query = db_select('node', 'n')
            ->fields('n', array('nid', "type", 'title'))
            ->fields("fg", array('entity_id', 'field_cst_target_id'));
    $query->condition('fg.field_cst_target_id', $cstId);
    $query->leftJoin('field_data_field_cst', 'fg', 'n.nid = fg.field_cst_target_id');
    $results = $query->execute();
    foreach ($results as $result) {
        $fileGroup[] = $result->entity_id;
    }
    return $fileGroup;
}

/**
 * Implementation of admin action user list by company id and site id
 * 
 * @param $company_id
 *   Company node ID
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return theme
 * 
 */

function get_users_admin_action($company_id, $site_id) {
    $user_lists = user_render($company_id, $site_id);
    return theme('aitico-admin-action-users', array(
                'user_lists' => $user_lists,
                'company_id' => $company_id,
                'site_id' => $site_id,
            ));
}

/**
 * Implementation of admin action logs
 * 
 * @param $company_id
 *   Company node ID
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @param $cst_id
 *   cst node ID
 * 
 * @return theme
 */

function get_logs_admin_action($company_id, $site_id, $cst_id) {
    return theme('aitico-admin-action-logs', array(
                'company_id' => $company_id,
                'site_id' => $site_id,
                'cst_id' => $cst_id
            ));
}

/**
 * Implementation of logs view page with log info
 * 
 * @param $company_id
 *   Company node ID
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @param $cst_id
 *   cst node ID
 * 
 * @return theme
 *   all log info in a view
 * 
 */
function get_all_log_info($company_id,$site_id,$cst_id) {
    $company_node = node_load($company_id);
    $company_name = $company_node->title;
    if ($site_id != 0) {
        $site_node = node_load($site_id);
        $site_name = $site_node->title;
    } else {
        $site_name = '';
    }
    if ($cst_id != 0) {
        $cst_node = node_load($cst_id);
        $cst_name = $cst_node->title;
    } else {
        $cst_name = '';
    }

    $date_range = $_GET['date-range-picker'];
    $date_range_arr = explode('-', $date_range);

    $acquired_time = $date_range_arr[0];
    $acquired_time_val = strtotime($acquired_time);

    $returned_time = $date_range_arr[1];
    $returned_time_val = strtotime($returned_time);

    $returned_time_val = $returned_time_val + 86399;


    /*****Start Query******/
    $query = "SELECT
                    n.nid AS nid,
                    n.type AS type,
                    site.entity_id AS site_entity_id,(SELECT n.title FROM node n where n.nid = site.entity_id) as site_title,
                    cst.entity_id AS cst_entity_id,(SELECT n.title FROM node n where n.nid = cst.entity_id) as cst_title,
                    cstl.field_cst_loan_id_target_id AS cstl_entity_id,(SELECT n.title FROM node n where n.nid = cstl_entity_id) as loan_cst_title,
		    cstr.field_cst_return_id_target_id AS cstr_entity_id,(SELECT n.title FROM node n where n.nid = cstr_entity_id) as return_cst_title,
                    dev.entity_id AS dev_entity_id,(SELECT n.title FROM node n where n.nid = dev.entity_id) as device_title,
                    log.entity_id AS log_entity_id
                  FROM {node} n LEFT OUTER JOIN {field_data_field_parent_company} site
                    ON n.nid = site.field_parent_company_target_id 
                    LEFT OUTER JOIN {field_data_field_parent_site} cst
                    ON site.entity_id = cst.field_parent_site_target_id 
                    LEFT OUTER JOIN {field_data_field_charging_station} dev
                    ON cst.entity_id = dev.field_charging_station_target_id 
                    LEFT OUTER JOIN {field_data_field_device} log
                    ON dev.entity_id=log.field_device_target_id
                    LEFT OUTER JOIN {field_data_field_cst_loan_id} cstl
                    ON log.entity_id=cstl.entity_id
                    LEFT OUTER JOIN {field_data_field_cst_return_id} cstr
                    ON log.entity_id=cstr.entity_id
                    LEFT OUTER JOIN {field_data_field_acquired} acq
                    ON log.entity_id = acq.entity_id 
                    LEFT OUTER JOIN {field_data_field_returned} ret
                    ON log.entity_id = ret.entity_id AND ret.field_returned_value <= $returned_time_val
                    WHERE n.nid= $company_id";

    if ($site_id != 0) {
        $query .= " AND site.entity_id = $site_id";
    }
    if ($cst_id != 0) {
        $query .= " AND cst.entity_id = $cst_id";
    }
    $query .=" AND acq.field_acquired_value >= $acquired_time_val AND  acq.field_acquired_value <= $returned_time_val";

    $query .=" AND log.entity_id !=''";

    $results = db_query($query)->fetchAll();

    /******End Query********/

    return theme('aitico-logs-view', array(
                'company_name' => $company_name,
                'site_name' => $site_name,
                'cst_name' => $cst_name,
                'date_range' => $date_range,
                'log_results' => $results
            ));
}


/**
 * Implementation of help page callback
 * 
 * @return theme
 */

function aitico_core_help_page() {

    global $user;
    $nodeId = false;
    $help_content = '';
    if (in_array('Super Admin', $user->roles)) {
        $nodeId = getHelpByRole(1);
    } else if (in_array('Super Content Admin', $user->roles)) {
        $nodeId = getHelpByRole(2);
    } else if (in_array('Content Admin', $user->roles)) {
        $nodeId = getHelpByRole(3);
    } else if (in_array('Company Admin', $user->roles)) {
        $nodeId = getHelpByRole(4);
    } else if (in_array('Operator', $user->roles)) {
        $nodeId = getHelpByRole(5);

    }
    if ($nodeId)
        $node = node_load($nodeId);


    if (isset($node->body['und'][0]['value']))
        $help_content = $node->body['und'][0]['value'];
    
    return theme('aitico-help', array(
                'help_content' => $help_content,

            ));
}

/**
 * Get all site id list by a company
 * 
 * @param $nid
 *   company node ID
 * 
 * @return $site_list
 *   site id list array
 */
function get_all_child_site_of_a_company($nid) {
    $type = "site";
    $sql = "SELECT n.nid FROM {node} n LEFT JOIN  {field_data_field_parent_company} pc ON  pc.entity_id = n.nid 
            WHERE pc.field_parent_company_target_id = :nid AND n.type = :type";
    $site_list = db_query($sql, array(':nid' => $nid, ':type' => $type))->fetchAll();
    return $site_list;
}

/**
 * Get CST id list by a site id lists
 * 
 * @param $site_id_lists
 *   Site id list array
 * 
 * @return $charging_station_list
 *   cst id list array 
 */
function get_all_charging_station_of_a_company($site_id_lists) {
    $type = "charging_station";
    $sql = "SELECT n.nid FROM {node} n LEFT JOIN  {field_data_field_parent_site} ps ON  ps.entity_id = n.nid 
            WHERE ps.field_parent_site_target_id IN($site_id_lists) AND n.type = :type";
    $charging_station_list = db_query($sql, array(':type' => $type))->fetchAll();
    return $charging_station_list;
}

/**
 * Get CST id list by a site id
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return $charging_station_list
 *   cst id list array 
 */
function get_all_charging_station_of_a_site($site_id) {
    $type = "charging_station";
    $sql = "SELECT n.nid FROM {node} n LEFT JOIN  {field_data_field_parent_site} ps ON  ps.entity_id = n.nid 
            WHERE ps.field_parent_site_target_id = :site_id AND n.type = :type";
    $charging_station_list = db_query($sql, array(':site_id' => $site_id, ':type' => $type))->fetchAll();
    return $charging_station_list;
}

/**
 * Implementation of updating admin action in right block based on
 * clicking on left block 
 * 
 * @param $node_type
 *   Node Type
 * 
 * @param $nid
 *   Node ID
 * 
 * @return mixed
 */

function update_admin_action($node_type, $nid) {
    global $user;
    $output = '';
    $company_id = 0;
    $site_id = 0;
    $cst_id = 0;

    if ($node_type == 'company') {
        $company_id = $nid;
        $edit_node = node_load($company_id);
    } else if ($node_type == 'site') {
        $site_id = $nid;
        $company_id = get_company_id_for_site($site_id);
        $edit_node = node_load($site_id);
    } else if ($node_type == 'charging_station') {
        $cst_id = $nid;
        $edit_node = node_load($cst_id);
        $site_id = $edit_node->field_parent_site['und'][0]['target_id'];
        $company_id = get_company_id_for_site($site_id);
    }elseif($node_type == 'device'){
        $device_id = $nid;
        $edit_node = node_load($device_id);
     }

    //edit view
    if (aitico_core_edit_view_access()) {
        $admin_action_edit = edit_admin_action($edit_node);
        $output.= $admin_action_edit;

    }

    $admin_action_files = get_files_admin_action($cst_id);
    if ($cst_id)
        $admin_action_file_admin = admin_file_admin_action($cst_id);
    else
        $admin_action_file_admin = "Charging station not selected.";

    //files view
    if (($node_type == 'charging_station') && aitico_core_files_view_access()) {

        $output.= $admin_action_files;
    }

    //file admin view
    if (($node_type == 'charging_station') && user_access('SLOT_MANAGE')) {

        $output.= $admin_action_file_admin;
    }

    //logs view
    if (aitico_core_log_view_access() && ($node_type !='device')){
        $admin_action_logs = get_logs_admin_action($company_id, $site_id, $cst_id);
        $output.= $admin_action_logs;
    }
    //users view
    if ($node_type != 'charging_station' && $node_type != 'device' && aitico_core_manage_user_access($user)) {
        $admin_action_users = get_users_admin_action($company_id, $site_id);
        $output.= $admin_action_users;
    }

    // Devices vew
    if ($node_type == 'site') {
        $admin_action_devices = get_devices_admin_action($site_id);
        $output.= $admin_action_devices;
    }

    if (empty($output)) {
        $style = 'style="display:none"';
    }

    $page_content = theme('aitico-core-right-page', array('type' => $node_type, 'content' => $output));
    $output = '<div id= "tab-group" class="tabbable"' . $style . '>' . $page_content . '</div>';


    return ajax_deliver(array(
                '#type' => 'ajax',
                '#commands' => array(ajax_command_replace('#tab-group', $output),
                    )));
}



/**
 * Get all devices under a site
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return $content
 *   Devices list view
 */

function get_devices_admin_action($site_id){

    $aitico_obj = get_model();
    $cst_node_list = get_all_charging_station_of_a_site($site_id);
    $cst_id_arr = array();
    foreach ($cst_node_list as $cst_id_row) {
        $cst_id_arr[] = $cst_id_row->nid;
    }
    $cst_id_lists = implode(',', $cst_id_arr);
    $all_devices_under_csts = get_all_devices_under_csts($cst_id_lists);
    $device_arr = array();
    $inactive_status = array( 'statusText' => t('Inactive') , 'cellClass' => "class=\"red\"");

    if (!empty($all_devices_under_csts)) {
        foreach ($all_devices_under_csts as $device){
            $device_node = node_load($device->nid);
            $device_type = $device_node->field_device_type['und'][0]['value'];
            $cst_node = node_load($device->field_charging_station_target_id);
            $device_arr[$device_node->nid]['title'] = $device_node->title;
            $device_arr[$device_node->nid]['cst'] = $cst_node->title;

            if (isset($device_type) && $device_type == 2){
                $device_arr[$device_node->nid]['status'] = $inactive_status;
            }else{
                $device_arr[$device_node->nid]['status'] = $aitico_obj->_getStatusByDevices($device_node->nid);
            }
        }
    }

    $content = theme('aitico-core-devices-list', array('devices' => $device_arr));
    return $content;
}



/**
 *  Get All devices under a spcific list of cst's
 * 
 *  @params $cst_lists
 *    cst id list array
 * 
 *  @return $devices
 */

function get_all_devices_under_csts($cst_lists){
    $type = 'device';
    if ($cst_lists){
        $query =  "SELECT * from {node} n ,{field_data_field_charging_station} fcs
                     WHERE fcs.entity_id = n.nid AND n.type = :type
                     AND fcs.field_charging_station_target_id IN ($cst_lists)";

        $devices = db_query($query , array(':type' => $type))->fetchAll();

        return $devices;
    }
    return null;
}



/**
 * Get file slots by file group id
 * 
 * @param $groupId
 *   File group node ID
 * 
 * @return $slots
 *   File slots array
 *
 */

function getSlotsByFileGroupId($groupId) {
    $query = db_select('node', 'n')
            ->fields('n', array('nid', "type"))
            ->fields("slot", array('entity_id'));
    $query->condition('slot.field_filegroup_target_id', $groupId);
    $query->leftJoin('field_data_field_filegroup', 'slot', 'n.nid = slot.field_filegroup_target_id');
    $results = $query->execute();
    foreach ($results as $result) {
        $slots[] = $result->entity_id;
    }
    return $slots;
}
/**
 * Implements file group slots form 
 * 
 * @return mixed 
 */
function aitico_filegroup_slots_form_management() {
    
    if (isset($_REQUEST['cst_id']))
        $cst_id = $_REQUEST['cst_id'];
    else
        return "No CST selected";

    global $user;

    if (isset($_REQUEST['remove-filegroup-id'])) {

        $slots = getSlotsByFileGroupId($_REQUEST['remove-filegroup-id']);
        foreach ($slots as $slot) {
            node_delete($slot);
        }
        node_delete($_REQUEST['remove-filegroup-id']);
    }


    if (isset($_REQUEST['filegroup-add'])) {
        $node = new stdClass();
        $node->type = 'file_group';
        $node->uid = $user->uid;
        $node->name = (isset($user->name) ? $user->name : '');
        $node->language = 'und';
        $node->status = 1;

        $node->title = $_REQUEST['filegroup-add'];
        $node->language = 'und';
        $node->field_cst['und'][0]['target_id'] = $_REQUEST['cst_id'];

        node_object_prepare($node);
        node_save($node);
    }

    $existingSlotsReturned = array();


    if (isset($_REQUEST['slot-permission-select']))
        for ($i = 0; $i < count($_REQUEST['slot-permission-select']); $i++) {
            if ($_REQUEST['file-slot-id'][$i] != '') {
                $existingSlotsReturned[] = $_REQUEST['file-slot-id'][$i];
                $node = node_load($_REQUEST['file-slot-id'][$i]);
            } else {
                $node = new stdClass();
            }

            $node->type = 'file_slot';
            $node->uid = $user->uid;
            $node->name = (isset($user->name) ? $user->name : '');
            $node->language = 'und';
            $node->status = 1;

            $node->title = "slot";
            $node->language = 'und';
            $node->field_filegroup['und'][0]['target_id'] = $_REQUEST['filegroup-id'];
            $node->field_permission['und'][0]['value'] = $_REQUEST['slot-permission-select'][$i];
            $node->field_file_type['und'][0]['value'] = $_REQUEST['slot-filetype-select'][$i];

            node_object_prepare($node);
            node_save($node);
        }
    if (isset($_REQUEST['existing-file-slot-id']) and $_REQUEST['existing-file-slot-id'] != '') {

        $existingSlotsSent = explode(",", $_REQUEST['existing-file-slot-id']);
        if (count($existingSlotsSent)) {
            $removedSlotIds = array_diff($existingSlotsSent, $existingSlotsReturned);

            foreach ($removedSlotIds as $nids) {
                node_delete($nids);
            }
        }
    }

    drupal_set_message(t("File admin updated succesfully"));
}

/**
 * Get system level user list
 * 
 * @return $user_list 
 *   user list array
 */
function get_system_users() {

    $user_sql = "SELECT u.*,r.name as role FROM {users} u,{users_roles} ur ,{role} r
                WHERE u.uid = ur.uid AND ur.rid = r.rid
                AND (r.name = '" . SUPER_ADMIN . "' OR r.name ='" . SUPER_CONTENTADMIN . "')";

    $user_list = db_query($user_sql)->fetchAll(PDO::FETCH_ASSOC);

    return $user_list;
}

/**
 * Handle system users admin action
 * 
 * @return theme
 */
function get_system_users_admin_action() {

    $user_lists = get_system_users();
    return theme('aitico-admin-system-users', array(
                'user_lists' => $user_lists,
            ));
}

/**
 * Update system level admin action (clicking on left admin menu)
 * 
 * @return mixed
 * 
 */
function update_admin_system_action() {
    
    $system_users = get_system_users_admin_action();
    $system_user_page = theme('aitico-core-system-page', array('system_user_lists' => $system_users));
    $output = '<div id= "tab-group" class="tabbable">' . $system_user_page . '</div>';

    return ajax_deliver(array(
                '#type' => 'ajax',
                '#commands' => array(ajax_command_replace('#tab-group', $output),
                    )));
}

/**
 * Get Help page based on user role
 * 
 * @param $helpRole
 *   User role
 * 
 * @return $nodeId
 *   Help content node ID 
 */

function getHelpByRole($helpRole) {
		global $language;
    $query = db_select('node', 'n')
            ->fields('n', array('nid', "type"))
            ->fields("role", array('entity_id'));
    $query->condition('n.type', 'aitico_help');
		$query->condition('n.language',$language->language);
    $query->condition('role.field_help_role_value', $helpRole);
    $query->leftJoin('field_data_field_help_role', 'role', 'n.nid = role.entity_id');

    $results = $query->execute();

    foreach ($results as $result) {
        $nodeId = $result->nid;
    }
    return $nodeId;
}

/**
 * Handle file removal and refresh the file view
 * 
 * @param $cst_id
 *   cst node ID
 * 
 * @param $node_id
 *   File node ID
 * 
 */
function aitico_core_remove_file($cst_id, $node_id) {
    $node = node_load($node_id);    
    node_delete($node->nid);
    refresh_files($cst_id);
}

/**
 * Refreshing file
 * 
 * @param type $cst_id
 *   cst node ID
 */
function aitico_core_refresh_files($cst_id) {
    refresh_files($cst_id);
}

/**
 * Refreshing file
 * 
 * @param $cst_id
 *   cst node ID
 * 
 * @return mixed 
 */
function refresh_files($cst_id) {

    $files_content = get_files_admin_action($cst_id, true);
    $output = "<div id='files_update'>" . $files_content . "</div>";
    return ajax_deliver(array(
                '#type' => 'ajax',
                '#commands' => array(ajax_command_insert('#files_update', $output),
                    )));
}

/**
 * Get files id list by schedule id
 * 
 * @param $file_schedule_id
 *   Schedule node ID
 * 
 * @return $file_list
 *   File id list array 
 * 
 */
function get_all_schedule_slot_file_id($file_schedule_id) {
    $type = "files";
    $sql = "SELECT n.nid                                      
                FROM {node} n 
                LEFT JOIN {field_data_field_file_schedule} fs ON fs.entity_id = n.nid              
                LEFT JOIN {field_data_field_slot} sl ON sl.entity_id = n.nid               
                WHERE n.type = :type AND fs.field_file_schedule_target_id=:file_schedule_id";
    $file_list = db_query($sql, array(':file_schedule_id' => $file_schedule_id, ':type' => $type))->fetchAll();

    return $file_list;
}


/**
 * Delete file schedule based on schedule id
 * 
 * @param $file_schedule_id
 *   Schedule node ID
 * 
 */
function file_schedule_delete($file_schedule_id) {    
        //get all child file for a schedule
        $slots_file_id = get_all_schedule_slot_file_id($file_schedule_id);
        $file_id_arr = array();
        foreach ($slots_file_id as $row) {
            $file_id_arr[] = $row->nid;
        }
        
        node_delete_multiple($file_id_arr);
    
}

/**
 * Add Device form
 * 
 * @param $parent_cst_id
 *   Parent cst node ID
 * 
 * @return  theme 
 */
function add_device_form($parent_cst_id) {
    $title = t("Add Devices");
    $node_type = 'device';
    $device_form = get_aitico_node_form($node_type, $parent_cst_id);
    return theme('content_entry_page', array('form_title' => $title, 'node_add_form' => $device_form));
}
/**
 * Change Device status to in service
 * 
 * @param $node
 *   Device node object 
 */
function change_device_status($node){
    $node->field_statuscode['und'][0]['value'] = SERVICE;
    node_save($node);
    drupal_set_message(t('Device status changed'));
}
