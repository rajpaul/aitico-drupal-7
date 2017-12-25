<?php

/**
 * Implementation of rent page callback
 * 
 * @return theme
 */
function aitio_core_rent_page() {
    global $user;
    $company_id = 0;
    $site_id =0;
    
    $user_info = db_query("SELECT cid, sid FROM users_companies_sites WHERE uid =:uid", array(':uid' => $user->uid))->fetch();
   
    if(!empty($user_info)){
        $company_id = $user_info->cid;
        $site_id = $user_info->sid;
    }

    $pin_code = get_generated_pincode($site_id , 4);
    $aitico_core_pin = theme('aitico-core-rent-pin', array('pin_code' => $pin_code));
    $cst_info = get_all_charging_station_info($company_id , $site_id);
    
    return theme('aitico-core-rent', array(
                'aitico_core_pin' => $aitico_core_pin,
                'cst_results' => $cst_info,
                'site_id'  => $site_id,
            ));
}

/**
 * Rent device and save rent inforation (CST) into "log"
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return $pin_code 
 */

function add_rent_info($site_id) {

    global $user;
    $pin_code = get_generated_pincode($site_id , 4);
    $description = $_POST['description'];
    $duration = $_POST['duration'];
    $pin_expiration_time = variable_get('pin_expiration_time_value');
    
    $now = date('Y-m-d H:i:s');
    $cur_timestamp = strtotime($now);
    $duration_arr = explode(':', $duration);
    $returned_timestamp = $cur_timestamp + $duration_arr[0]*3600 + $duration_arr[1]*60;
    $valid_until = $cur_timestamp + $pin_expiration_time*3600;

    $node = new stdClass();
    $node->uid = $user->uid;
    $node->type = 'log';
    $node->title = 'Log-'.$now;
    $node->status = 1;
    $node->field_pin['und'][0]['value'] = $pin_code;
    $node->field_duration['und'][0]['value'] = $duration;
    //$node->field_returned['und'][0]['value'] = $returned_timestamp;
    $node->field_pin_valid_until['und'][0]['value'] = $valid_until;
    $node->created = time();
    $node->field_description = array(
        'und' => array(
            array(
                'value' => $description
            )
        )
    );
    
    $node->field_site_id['und'][0]['target_id'] = $site_id;
    $node->field_user['und'][0]['target_id'] = $user->uid;
    
    node_save_action($node);
    print $pin_code;
}

/**
 * Generate random pin code for user
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @param $length
 *   generated random code length
 * 
 * @return $random_code
 */

function get_generated_pincode($site_id , $length) {

    do{
        $random_code = substr(number_format(time() * rand(), 0, '', ''), 0, $length);
        $check_pin_exist = check_pin_exist($random_code , $site_id);
    }while($check_pin_exist);

    return $random_code;
}

/**
 * Check a pin is exists or not
 * 
 * @param $random_code
 *    Generated random code
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return $check_pin_exist
 */


function check_pin_exist($random_code , $site_id){
    
    $month_time = 30*24*60*60;
    $query = "SELECT pin.field_pin_value FROM {node} n
        LEFT JOIN {field_data_field_pin} pin ON (pin.entity_id = n.nid )
        LEFT JOIN {field_data_field_pin_valid_until} pin_valid ON (pin_valid.entity_id = n.nid )
        LEFT JOIN {field_data_field_site_id} site ON (site.entity_id = n.nid )
        WHERE n.type = ':type' AND pin.field_pin_value = ':pin_value'
              AND site.field_site_id_target_id = ':site_id'
              AND pin_valid.field_pin_valid_until_value > ':valid_time'
              AND (':curr_time' - n.changed ) < ':month_time' ";

    $check_pin_exist = db_query($query ,
                                array(
                                     ':type' => 'log',
                                     ':pin_value' => $random_code,
                                     ':valid_time' => time(),
                                     ':site_id' => $site_id,
                                     ':curr_time' => time(),
                                     ':month_time' => $month_time
                                ))->fetch();
    
    return $check_pin_exist;
    
}

/**
 * Get charging station info by company id and site id
 * 
 * @param $company_id
 *   Company node ID
 * 
 * @param $site_id
 *   Site node ID
 * 
 * @return $cst_info_arr
 *   cst info array 
 * 
 */
function get_all_charging_station_info($company_id, $site_id) {

    $query = "SELECT
                    n.nid AS nid,
                    n.type AS type,
                    site.entity_id AS site_entity_id,
                    cst.entity_id AS cst_entity_id,
                    (SELECT n.title FROM node n WHERE n.nid = cst.entity_id) as cst_title,
 		    (SELECT fdb.body_value FROM field_data_body fdb WHERE fdb.entity_id = cst.entity_id) as cst_description,
                    dev.entity_id AS dev_entity_id,
                    (SELECT st.field_statuscode_value FROM field_data_field_statuscode st WHERE st.entity_id = dev.entity_id) as device_status
                        
                  FROM {node} n LEFT OUTER JOIN {field_data_field_parent_company} site
                    ON n.nid = site.field_parent_company_target_id 
                    LEFT OUTER JOIN {field_data_field_parent_site} cst
                    ON site.entity_id = cst.field_parent_site_target_id 
                    LEFT OUTER JOIN {field_data_field_charging_station} dev
                    ON cst.entity_id = dev.field_charging_station_target_id 
                    
                    WHERE n.nid= $company_id AND site.entity_id = $site_id";
    
    $results = db_query($query)->fetchAll();

    $cst_id = 0;
    $cst_info_arr = array();
    foreach ($results as $row) {
        if ($row->cst_entity_id != $cst_id)
            $cst_id = $row->cst_entity_id;

        $cst_title = $row->cst_title;
        $cst_description = $row->cst_description;

        $cst_info_arr[$cst_id]['title'] = $cst_title;
        $cst_info_arr[$cst_id]['description'] = $cst_description;
        
        if($row->dev_entity_id !=''){
            
        $cst_info_arr[$cst_id]['device_all'][] = $row;
                
        if ($row->device_status == 0 || $row->device_status == 1) {
                                 
            $cst_info_arr[$cst_id]['device_available'][] = $row;
           
        }
        
       }
    }
    
    return $cst_info_arr;
}