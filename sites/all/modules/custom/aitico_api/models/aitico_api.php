<?php

/**
 * Adding device or update using this method
 * 
 * @param type $device
 *      device node
 * @param type $cstReturnId 
 *      CST node id where the device will be returned to 
 */



function add_update_device($device, $cstReturnId){   
    $existingDevice = get_existing_device($device->deviceid);
    if(!$existingDevice)
       return null;
    
    $node = $existingDevice;
    $existingStatus = $node->field_statuscode['und'][0]['value'];
    if($existingStatus == LOANED || $existingStatus == EXPIRED){
        update_log_by_device($node, NULL, $cstReturnId);
    }    
    $node->field_device_type['und'][0]['value'] = 1;
    $node->field_statuscode['und'][0]['value'] = $device->statuscode;
    $node->status = 1;
    $node->field_batterylevel['und'][0]['value'] = $device->batterylevel;
    $node->field_charging_station['und'][0]['target_id'] = $cstReturnId;
    node_save($node);
    
}



/**
 * Find devices to mark LOANED from RESERVED
 * 
 * @param type $cst_id
 * @param type $devices
 */

function find_and_update_reserved_device($cst_id, $devices){
    $reserve_logs = get_reserve_log_by_cst($cst_id, $devices);
    
    if($reserve_logs){
        foreach($reserve_logs as $log){
            $log_node = node_load($log->nid);
            $reserve_device_id = $log_node->field_device['und'][0]['target_id'];
            $reserve_device = node_load($reserve_device_id);
            if($reserve_device->field_statuscode['und'][0]['value'] == RESERVED){
                    $reserve_device->field_statuscode['und'][0]['value'] = LOANED;
                    node_save($reserve_device);
                    $log_node->field_acquired['und'][0]['value'] = time();
                    
                    $duration = $log_node->field_duration['und'][0]['value'];
                    $cur_timestamp = time();
                    $duration_arr = explode(':', $duration);
                    $returned_timestamp = $cur_timestamp + $duration_arr[0]*3600 + $duration_arr[1]*60;
                    $log_node->field_returned['und'][0]['value'] = $returned_timestamp;
                    
                    node_save($log_node);
            }
        }
    }
}



/**
 * get all log with devices not acquired from the called CST
 * 
 * @param type $cst_id
 * @param type $devices
 * @return node log (if exists) otherwise null
 */


function get_reserve_log_by_cst($cst_id,$devices){

    $query = db_select('node', 'n')
          ->fields('n', array());
    $query->leftJoin('field_data_field_acquired', 'fa', 'n.nid = fa.entity_id');
    $query->leftJoin('field_data_field_device', 'fd', 'n.nid = fd.entity_id');
    $query->leftJoin('field_data_field_cst_loan_id', 'fc', 'n.nid = fc.entity_id');
    
    $query->condition('n.type', 'log' , '=');
    $query->condition('fc.field_cst_loan_id_target_id', $cst_id , '=');
    if(count($devices))
        $query->condition('fd.field_device_target_id', $devices , 'NOT IN');
    $query->condition('fa.field_acquired_value',  NULL, 'IS NULL');
    $query->orderBy('created', 'DESC');

    $result = $query->execute();
    $result_array = array();

    if($result){
        foreach($result as $res){
            $result_array[] = $res;
        }
        /*$node = array_slice($result_array, 0, 1);
        $node = current($node);
        return node_load($node->nid);*/
        return $result_array;
    }

    return null;
}


/**
 *Get minimum used device based on rented hours in total
 *or any free device that is never used.
 *
 * @param string $cstId
 * @return mininum used device node
*/
function getMinimumUsedDeviceCST($cstId){
    $devNodeIds = NULL;
    $devStatistics = NULL;
    $query = getNodeQuery(null);
    $query->condition('n.nid', $cstId);
    $query->condition('dev.entity_id',  NULL, 'IS NOT NULL');
    $results = $query->execute();
    $min_used_device = null;
    foreach ($results as $result) {
         $devStatistics = getLogsByDevices($result->dev_entity_id);
         $device = node_load($result->dev_entity_id);
         if($device->field_statuscode['und'][0]['value'] == READY){
            if(isset($devStatistics['usageDuration']) ){
                if(!isset($minDuration)){
                    $minDuration = $devStatistics['usageDuration'];
                    $min_used_device = $device;
                }
                elseif($devStatistics['usageDuration'] < $minDuration  ){
                    $minDuration = $devStatistics['usageDuration'];
                    $min_used_device = $device;
                }
            }
            else{
                $min_used_device = $device;
                return $min_used_device;
                
            }
         }
         
    }
    return $min_used_device;
}


function getNodeQuery($type, $limit = 0) {
    $query = db_select('node', 'n')
            ->fields('n', array('nid',"type"))
            ->fields("site", array('entity_id'))                
            ->fields("cst", array('entity_id'))
            ->fields("dev", array('entity_id'));      
    
    if ($limit) {
        $query->range(0, $limit);
    }

    $query->leftJoin('field_data_field_parent_company', 'site', 'n.nid = site.field_parent_company_target_id');
    $query->leftJoin('field_data_field_parent_site ', 'cst', 'n.nid = cst.field_parent_site_target_id');
    $query->leftJoin('field_data_field_charging_station ', 'dev', 'n.nid = dev.field_charging_station_target_id');
                            
    return $query;
}

/**
 *Get all logs by devices in rent log
 *
 * @param integer $devNodeIds
 * @return array statistics 
 */
function getLogsByDevices($devNodeIds) {
    $query = db_select('node', 'n')
            ->fields('n', array('nid',"type"))
            ->fields("log", array('entity_id'));                
    $query->condition('n.nid', $devNodeIds, '=');
    $query->condition('log.entity_id',  NULL, 'IS NOT NULL');
    $query->leftJoin('field_data_field_device', 'log', 'n.nid = log.field_device_target_id');
    $results = $query->execute();
    
    $logUsageCount = 0;
    $logDuration = 0;
    $logUsageRate = 0;
    $usageRate = 0;
    
    foreach ($results as $result) {
        $logId = $result->entity_id;
        $logNode = node_load($logId);
        $individualDuration = deviceLogDuration($logId);
        $logDuration += $individualDuration;
        if(isset($logNode->field_acquired['und'][0]['value']))
            $logUsageCount ++;
        $totalHoursUntilCreated = deviceUsage($result->nid);
        if($totalHoursUntilCreated)
        $usageRate = ($individualDuration/ $totalHoursUntilCreated)  *100;
        $logUsageRate += round($usageRate);
        
        $individualDuration = "";
    }
    
    if($logUsageCount > 1)
    $logUsageRate = $logUsageRate/$logUsageCount;
    
    $devStats['usageCount'] = $logUsageCount;
    $devStats['usageDuration'] = $logDuration;
    $devStats['usageRate'] = $logUsageRate;
    if(!$devStats['usageCount'])
       return false;
    
    return $devStats;
}

/**
 *Get device usage by rented hours
 *
 * @param integer $devNodeIds
 * @return array statistics 
 */
function deviceUsage($deviceId){
   $devNode = node_load($deviceId);
   $creationTimeStamp = $devNode->created;
   $creationDateObj = date_create(date('Y-m-d H:i', $creationTimeStamp));
   $todayDateObj = date_create(date('Y-m-d H:i'));
   $creationDateObj->format('Y-m-d H:i');
   $todayDateObj->format('Y-m-d H:i');
   $interval = $creationDateObj->diff($todayDateObj);
   $total = $interval->format('%a');
   $totalHoursUntilCreated = (($total * 24) + $interval->format('%h')) * 60 + $interval->format('%i') ;
   $totalHoursUntilCreated = round($totalHoursUntilCreated/60,2);
   
   return $totalHoursUntilCreated;
}


/**
 * Find rented hours for the device from log
 * 
 * @param type $logId
 * @return device_log_duration 
 */


function deviceLogDuration($logId){
   $acquiredTimeStamp = NULL;
   $returnedTimeStamp = NULL;
   $logNode = node_load($logId);
   
   if(isset($logNode->field_acquired['und'][0]['value']))
   $acquiredTimeStamp = $logNode->field_acquired['und'][0]['value'];
   if(isset($logNode->field_returned['und'][0]['value']))
   $returnedTimeStamp = $logNode->field_returned['und'][0]['value'];
   if(!$acquiredTimeStamp or !$returnedTimeStamp)
    return 0;
   $acquiredDateObj = date_create(date('Y-m-d H:i', $acquiredTimeStamp));
   
   $returnedDateObj = date_create(date('Y-m-d H:i', $returnedTimeStamp));
   
   $interval = $acquiredDateObj->diff($returnedDateObj);
   
   $total = $interval->format('%a');
   
   $totalHoursUntilCreated = (($total * 24) + $interval->format('%h')) * 60 + $interval->format('%i') ;
   $totalHoursUntilCreated = round($totalHoursUntilCreated/60,2);       
   return $totalHoursUntilCreated;
}

/**
 *Update log containing pincode with
 *CST loan or return info and save return
 *time when returned
 */
function update_log_by_device($device, $cstLoanId = null, $cstReturnId = null){
    
    $deviceLog = get_log_by_device($device);
    if($deviceLog) {
        $deviceLog->field_returned['und'][0]['value'] = time();
        if($cstLoanId)
            $deviceLog->field_cst_loan_id['und'][0]['target_id'] = $cstLoanId;
        if($cstReturnId)
            $deviceLog->field_cst_return_id['und'][0]['target_id'] = $cstReturnId;
        node_save($deviceLog);
    }
}



/**
 * get all logs by device that has a pincode log
 * 
 * @param type $device
 * @return log(if exists) , null otherwise
 */


function get_log_by_device($device){

    $query = new EntityFieldQuery();   
    
    $result = $query
      ->entityCondition('entity_type', 'node')
     ->propertyCondition('type', "log")      
     ->fieldCondition('field_device', 'target_id', $device->nid, '=')->execute();

   if($result){
        $result_array = array_reverse($result['node']);
        $node = array_slice($result_array, 0, 1);
        $node = current($node);
        return node_load($node->nid);
   }
   
   return null;    
}


/**Get device log for rent only (Change Device Status to LOANED)
 * @param $device
 * @return $device_node (if exist) , null otherwise;
 */

function get_device_log_for_rent($device){

    $query = db_select('node', 'n')
        ->fields('n', array());
    $query->leftJoin('field_data_field_acquired', 'fa', 'n.nid = fa.entity_id');
    $query->leftJoin('field_data_field_device', 'fd', 'n.nid = fd.entity_id');
    $query->condition('n.type', 'log' , '=');
    $query->condition('fd.field_device_target_id', $device->nid , '=');
    $query->condition('fa.field_acquired_value',  NULL, 'IS NULL');
    $query->orderBy('created', 'DESC');

    $result = $query->execute();
    $result_array = array();

    if($result){
        foreach($result as $res){
            $result_array[] = $res;
        }
        $node = array_slice($result_array, 0, 1);
        $node = current($node);
        return node_load($node->nid);
    }

    return null;
}



/**
 * Get device log for generic purpose
 * @param $device
 * @return $device_node (if exists) , null otherwise;
 */

function get_device_log($device){

    $query = db_select('node', 'n')
        ->fields('n', array());
    $query->leftJoin('field_data_field_acquired', 'fa', 'n.nid = fa.entity_id');
    $query->leftJoin('field_data_field_device', 'fd', 'n.nid = fd.entity_id');
    $query->condition('n.type', 'log' , '=');
    $query->condition('fd.field_device_target_id', $device->nid , '=');
    

    $result = $query->execute();
    $result_array = array();

    if($result){
        foreach($result as $res){
            $result_array[] = $res;
        }
        $result_array = array_reverse($result_array);
        $node = array_slice($result_array, 0, 1);
        $node = current($node);
        return node_load($node->nid);
    }

    return null;
}



/**
 * Adding device or update using this method
 * 
 * @param type $device
 * @param type $chst 
 */
function save_device($device, $chst){   
    
    try {
        $node = new stdClass();
        $node->type = 'device';
        node_object_prepare($node);
        
        if($nod = get_existing_device($device->deviceid)){
            $dstatus = $nod->field_statuscode['und'][0]['value'];             
            if(($dstatus == LOANED || $dstatus == EXPIRED) && ($device->statuscode == CHARGING || $device->statuscode == READY || $device->statuscode == SERVICE)){
                update_log($nod, $chst);
            }
            $node = $nod;
        }        
        $node->status = 1;
        $node->uid = 1;
        $node->title = $device->deviceid;
        $node->language = 'und';
        $node->field_deviceid['und'][0]['value'] = $device->deviceid;
        $node->field_statuscode['und'][0]['value'] = $device->statuscode;
        //$node->field_loaneduntil['und'][0]['value'] = $device->loaneduntil;
        $node->field_batterylevel['und'][0]['value'] = $device->batterylevel;
        $node->field_charging_station['und'][0]['target_id'] = $chst;
        node_save($node);  
    }
    catch (Exception $e){
        drupal_set_message("<pre>".$e->getTraceAsString() ."</pre>","error");  
    }    
}
/**
 * Update log for keep alive
 * 
 * @param type $device
 * @param type $cst_id 
 */
function update_log($device, $cst_id){
    $pin_code = get_pin_code_by_device($cst_id, $device); 
    if($pin_code) {
        //$pin_code->field_returned['und'][0]['value'] = time();
        node_save($pin_code);
    }
}
/**
 * Adding log functionality  
 * 
 * @param type $device
 * @param type $cst 
 */
function save_log($request){
    
    $deviceId = (isset($request['deviceid']))? $request['deviceid'] : '';
    $pincode = (isset($request['pincode']))? $request['pincode'] : '';
    $cstid = (isset($request['cstid']))? $request['cstid'] : '';
    $messageid = (isset($request['messageid']))? $request['messageid'] : '';
    $hash = (isset($request['hash']))? $request['hash'] : '';
    $device = get_existing_device($deviceId);
    
    if(!$device) return;
    
    try {
        $node = new stdClass();
        $node->type = 'log';
        node_object_prepare($node);        
        $node->status = 1;
        $node->uid = 1;
        $node->title = "log from api";
        $node->language = 'und';
        $node->field_pin['und'][0]['value'] = $pincode;
        $node->field_acquired['und'][0]['value'] = time();
        $node->field_duration['und'][0]['value'] = 0; 
        $node->field_description['und'][0]['value'] =  $messageid;       
        $node->field_device['und'][0]['target_id'] = $device->nid;        
        //$node->field_user['und'][0]['target_id'] = $user->getId();
        node_save($node);  
    }
    catch (Exception $e){
        drupal_set_message("<pre>".$e->getTraceAsString() ."</pre>","error");  
    }   
}

/**
 * Checking existing device 
 * 
 * @param type $deviceId
 * @return device_node (if exists) , null otherwise 
 */
function get_existing_device($deviceId){
    
    $query = new EntityFieldQuery();
    
    $result = $query
      ->entityCondition('entity_type', 'node')
      //->propertyCondition('status', 1)
      ->propertyCondition('type', "device")      
      ->fieldCondition('field_deviceid', 'value', $deviceId, '=')->execute();   
    
   if($result){
        $node = array_slice($result['node'], 0, 1);
        $node = current($node);
        return node_load($node->nid);
   }    
   
   return null;
}

/**
 * GET CST Node infomation filter by cstid
 * 
 * @param type $cstId 
 * @return cst node (if exists) , null otherwise
 */
function get_cst_node($cstId){
    
    $query = new EntityFieldQuery();
    
    $result = $query
      ->entityCondition('entity_type', 'node')
      //->propertyCondition('status', 1)
      ->propertyCondition('type', "charging_station")      
      ->fieldCondition('field_cst_id', 'value', $cstId, '=')->execute();   
    
   if($result){
        $node = array_slice($result['node'], 0, 1);
        $node = current($node);
        return node_load($node->nid);
   }    
   
   return null; 
}


/**
 * Get pin code node by site
 * 
 * 
 */

/**
 * Get log information by pin code and site id  
 * @param type $site_id
 * @param type $pincode
 * @return log node containg the pincode (if exists) , null otherwise
 */
function get_pin_code_by_site($site_id, $pincode){
    if (lock_acquire('get_pin_code_by_site')) {
        $time = time();
        $query = new EntityFieldQuery();   
        
        $result = $query
          ->entityCondition('entity_type', 'node')
          ->propertyCondition('type', "log")      
          ->fieldCondition('field_pin', 'value', $pincode, '=')
          ->fieldCondition('field_pin_valid_until', 'value', $time, '>')
          ->fieldCondition('field_site_id', 'target_id', $site_id, '=')->execute();      
        
        if($result){
            $node = array_slice($result['node'], 0, 1);
            $node = current($node);
            return node_load($node->nid);
        }    
        
        return null;
        lock_release('get_pin_code_by_site');
    }
    else{
        return null;
    }

}




/**
 * Get log node based on cstid and pincode
 * 
 * @param type $cst_id
 * @param type $pincode
 * @return log node (if exists) , null otherwise
 */


function get_pin_code($cst_id, $pincode){    
    $query = new EntityFieldQuery();   
    
    $result = $query
      ->entityCondition('entity_type', 'node')
      //->propertyCondition('status', 1)
      ->propertyCondition('type', "log")      
      ->fieldCondition('field_pin', 'value', $pincode, '=')
      ->fieldCondition('field_log_cst', 'target_id', $cst_id, '=')->execute();      
    
   if($result){
        $node = array_slice($result['node'], 0, 1);
        $node = current($node);
        return node_load($node->nid);
   }    
   
   return null;
}


/**
 * Get Log node by device and cst id
 *  
 * @param type $cst_id
 * @param type $device
 * @return log node (if exists) , null otherwise
 */
function get_pin_code_by_device($cst_id, $device){
    $query = new EntityFieldQuery();   
    
    $result = $query
      ->entityCondition('entity_type', 'node')
     ->propertyCondition('type', "log")      
     ->fieldCondition('field_device', 'target_id', $device->nid, '=')
     ->fieldCondition('field_log_cst', 'target_id', $cst_id, '=')->execute();      

   if($result){
        $node = array_slice($result['node'], 0, 1);
        $node = current($node);
        return node_load($node->nid);
   }    
   
   return null;
}


/**
 * Update device status under cst 
 * @param type $cst_id
 */

function update_device_status_under_cst($cst_id){
       
       $nodes = array();
       
       $query = new EntityFieldQuery();          
       $result = $query
      ->entityCondition('entity_type', 'node')
      ->propertyCondition('type', "device")      
      ->fieldCondition('field_charging_station', 'target_id', $cst_id, '=')->execute(); 
       
       if($result) {
           foreach ($result["node"] as $key => $res){
               $nodes[] =  $key;
           }
       }
       
      $_devices = node_load_multiple($nodes);
      
      foreach ($_devices as $device){
          $device->field_statuscode['und'][0]['value'] = SERVICE;
          node_save($device);
      }
      
}

/**
 * Get Http request data 
 * 
 * @param type $url
 * @param type $data
 * @return http object 
 */
function get_http_data($url, $data){
  
  $options = array(
    'method' => 'POST',
    'data' => $data,
    'headers' => array('Content-Type' => 'application/json; charset=UTF-8'),
  );
  
  return drupal_http_request($url, $options);
}


/**
 * Encrypt string 
 * 
 * @param type $string
 * @return encrypted string 
 */
function encrypt_string($string){
    return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(AITICO_ENCRYPT_KEY), $string, MCRYPT_MODE_CBC, md5(md5(AITICO_ENCRYPT_KEY))));
}

/**
 * DecryptedString encrypted string
 * @param type $encrypted
 * @return decrypted string
 * 
 */
function decrypted_string($encrypted){
     return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(AITICO_ENCRYPT_KEY), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5(AITICO_ENCRYPT_KEY))), "\0");
}



/**
 * Save Api log for debugging
 * @param type $request
 * @param type $title
 * @param type $cstid
 * @param type $hash
 */
function add_api_log($request, $title, $cstid = null, $hash = null){
   
    try {
        
        $node = new stdClass();
        $node->type = 'api_log';
        node_object_prepare($node);       
        $node->status = 1;
        $node->title = "Api from ".$title;
        $node->language = 'und';
        $node->body['und'][0]['value'] = json_encode($request);
        $node->field_hash['und'][0]['value'] =  $hash;    
        $node->field_api_cst_id['und'][0]['value'] =  $cstid;       
        node_save($node);  
    }
    catch (Exception $e){
        drupal_set_message("<pre>".$e->getTraceAsString() ."</pre>","error");  
    }   
    
}


/*
 * Get hand shaking with md5
 * 
 * return boolean;
 */
function get_hand_shaking($hash, $salt){  
    
    $query = drupal_get_query_parameters($_REQUEST, array("hash","q"));  
    
    if(isset($salt))
      $query  = $query+array("salt" => $salt);
    
    $uri = drupal_http_build_query($query);
    
    //echo md5($uri);
    
    if(md5($uri) == $hash )
        return true;
    
    return false;
}

/**
 * Hand shaking for keep alive
 * 
 * @param type $hash
 * @param type $salt
 * @return boolean 
 */
function get_hand_shaking_for_keep_alive($hash, $salt){
    
    //$json = '[{"devices":[{"deviceid":"d-500","statuscode":0,"loaneduntil":null,"batterylevel":400},{"deviceid":"d-600","statuscode":5,"loaneduntil":null,"batterylevel":400}],"cstid":"C-200-250-531"}]'.'12eRfTgDs4';

    $query = drupal_get_query_parameters($_REQUEST, array("hash","q"));  
    $data = isset($query['data']) ? $query['data'] : "";
    
    if($data) {    
        $jsonData = trim($data).$salt;
        
        if(md5($jsonData) == $hash )
            return true;    
    }
    
    return false;
}

/**
 * Get All files under cst
 * 
 * @param type $cst_id
 * @return array file_group  
 */
function get_file_groups($cst_id){
    
   $file_groups  = array();
   $fgroups  = get_filegroup_by_cst($cst_id);
   
   foreach ($fgroups as $fg_id => $files){       
       $_files = array();
       $last_update = null;
       $max = null;
       foreach ($files["slots"] as $slot ){
            $file = get_files_node($slot , FILE_TYPE_CODE_NORMAL);           
            $max  = $file->changed;
            if ($max > $last_update){                
                $last_update = $file->changed;
            }
            $value = $file->field_file['und'];
            $path = (isset($value[0]['uri']) ? $value[0]['uri'] : null);      
            $file_name = (isset($value[0]['filemime']) ? $value[0]['filemime'] : null); 
            $_files[] = array(
                "url" => file_create_url($path),
                "filetype" => $file_name ,                 
            );
       }
       
       $fp = node_load($fg_id);
       $file_groups[] = array(
           "title" => $fp->title,
           "lastupdate" => $last_update,
           "slots" => $_files     
       );
   }
   
   return $file_groups;
}

/**
 * Helper function to get filegroup from a specific cst_id
 * @param type $cst_id
 * @return array $file_group_array
 */
function get_filegroup_by_cst($cst_id){
    
    $file_group_ids = db_query("SELECT  n.nid , n.title , fcst.entity_id FROM 
                        {field_data_field_cst} fcst , {node} n
                        WHERE n.nid = fcst.entity_id AND fcst.field_cst_target_id  = :field_cst_target_id", array(':field_cst_target_id' => $cst_id))->fetchAll();

    $file_group_title = '';
    $file_group_array = array();
    foreach ($file_group_ids as $file_group) {
        $file_group_array[$file_group->nid]["slots"] = get_file_slot_by_filegroup($file_group->nid);
    }
    
    return $file_group_array;
}

/**
 * Get file slot from file group
 * @param type $group_id
 * @return array $slots
 */
function get_file_slot_by_filegroup($group_id) {
    $slots = array();
    $query = db_select('node', 'n')
            ->fields('n', array('nid', "type", 'title'))
            ->fields("slot", array('entity_id', 'field_filegroup_target_id'));
    $query->condition('slot.field_filegroup_target_id', $group_id);
    $query->leftJoin('field_data_field_filegroup', 'slot', 'n.nid = slot.field_filegroup_target_id');
    $results = $query->execute();
    foreach ($results as $result) {
        $slots[] = $result->entity_id;
    }
    return $slots;
}


/**
 * Device Info API call
 * @return json response
 * 
 * 
 */


function device_info(){

    $request = drupal_get_query_parameters();
    $handshaking = FALSE;
    $error = false;
    $status = 'success';
    $device_mac = (isset($request['mac']))? $request['mac'] : '';
    $hash = (isset($request['hash']))? $request['hash'] : '';
    $salt = $device_mac;
    $return_timestamp = time();
    
    if(!$request){
        return drupal_not_found();
    }

    add_api_log($request, "device-info", null, $hash);

    if(ENABLED_HAND_SHAKE && isset($salt)){
        $handshaking = get_hand_shaking($hash, $salt);
        if(!$handshaking){
            $error = true;
            $status = "Error";
            $status_message = "Hand shaking failed";
        }
    }
    if(!ENABLED_HAND_SHAKE or $handshaking){
        if (!$device_mac){
            $error = true;
            $status = 'Error';
            $status_message = t('Device MAC address missing !');
        }else{
             $device_node = get_device_from_mac_address($device_mac);
             if($device_node){
                $cst_nid = $device_node->field_charging_station['und'][0]['target_id'];
                $cst_node = node_load($cst_nid);
                $log = get_device_log_for_rent($device_node); 
                $device_status = $device_node->field_statuscode['und'][0]['value'];
                
                if (!empty($log)){                    
                    $device_node->field_statuscode['und'][0]['value'] = LOANED;
                    node_save($device_node);
                    $log->field_acquired['und'][0]['value'] = time();

                    $duration = $log->field_duration['und'][0]['value'];
                    $cur_timestamp = time();
                    $duration_arr = explode(':', $duration);
                    $returned_timestamp = $cur_timestamp + $duration_arr[0]*3600 + $duration_arr[1]*60;
                    $log->field_returned['und'][0]['value'] = $returned_timestamp;
                    
                    node_save($log);   
                    $return_timestamp = $returned_timestamp;
                }else{ 
                    if($device_status == LOANED || $device_status == RESERVED){
                        $existing_log = get_device_log($device_node);
                        $return_timestamp = $existing_log->field_returned['und'][0]['value'];
                    }else{
                        $error = true;
                        $status = 'Error';
                        $status_message = t('Invalid Device Info call !');
                    }                    
                }
                
                $_files = get_file_groups($cst_node->nid);
            }else{
                $error = true;
                $status = 'Error';
                $status_message = t('Device MAC address incorrect !');
            }
        }

    }

    if ($error){
        $responses = array(
            "status"               => $status,
            "responseid"           => md5(time()),
            "message"              => $status_message,
        );

        json_response_error($responses);


    }else{
        $responses = array(
            "status"           => $status,
            "returntimestamp"   => $return_timestamp,
            "responseid"        => md5(time()) ,
            "filegroups"           => $_files
        );

        if($salt){
            $json_message = drupal_json_encode($responses);
            $json_message = str_replace(" ", "", $json_message);
            $response_hash = md5($json_message.$salt);
        }
        else{
            $response_hash = null;
        }
        //Json response
        json_response_success($responses, $response_hash);
    }


}


/**
 * Get device from mac address
 * @param $mac
 * @return $device node
 */

function get_device_from_mac_address($mac){

    $query = new EntityFieldQuery();
    /*$result = $query
        ->entityCondition('entity_type', 'node')
        ->propertyCondition('type', 'device')
        ->fieldCondition('field_device_mac_address', 'value', trim($mac), '=')->execute();

    if(!empty($result)) {
        $device_array = node_load_multiple(array_keys($result['node']));
        print_r($device_array);
        $device_node = current($device_array);
        echo "-------------------";
        print_r($device_node);
        return $device_node;
    }*/
    $device_mac_colon = str_replace("-",":",$mac);
    $device_mac_dash = str_replace(":","-",$mac);
    $query = "SELECT nid,title,mc.field_device_mac_address_value 
                FROM node n,field_data_field_device_mac_address mc 
                WHERE n.type= :type 
                AND (mc.field_device_mac_address_value = :device_mac_colon OR mc.field_device_mac_address_value = :device_mac_dash)
                AND n.nid = mc.entity_id order by nid desc" ;
    $result = db_query($query, array(':type' => 'device', ':device_mac_colon' => $device_mac_colon, ':device_mac_dash' => $device_mac_dash))->fetch();
    
    if(!empty($result)) {
        $device_node = node_load($result->nid);
        return $device_node;
    }

    return null;
}

/**
 * Check a device is exists or not in cms
 * @param type $device_id
 * @param type $device_mac_address 
 * @return existing_device (if exists) , null otherwise
 */
function is_device_exists($device_id, $device_mac_address) {
    $device_mac_colon = str_replace("-",":",$device_mac_address);
    $device_mac_dash = str_replace(":","-",$device_mac_address);
    
    $type = 'device';
    $query = "SELECT nid,title,mc.field_device_mac_address_value 
                FROM node n,field_data_field_device_mac_address mc 
                WHERE n.type= :type 
                AND (n.title=:device_id OR mc.field_device_mac_address_value = :device_mac_colon OR mc.field_device_mac_address_value = :device_mac_dash)
                AND n.nid = mc.entity_id";
    $result = db_query($query, array(':type' => $type, ':device_id' => $device_id, ':device_mac_colon' => $device_mac_colon, ':device_mac_dash' => $device_mac_dash))->fetch();
    if ($result) {
        return $result;
    }
    return null;
}
/**
 * Add Device while initial keep alive call
 * @param type $device
 * @param type $cst_id 
 */
function initial_add_device($device, $cst_id) {
    try {
        $node = new stdClass();
        $node->type = 'device';
        node_object_prepare($node);

        $check_device = is_device_exists($device->deviceid, $device->mac);
        if (!$check_device) {
            $node->status = 1;
            $node->uid = 1;
            $node->title = $device->deviceid;
            $node->language = 'und';
            $node->field_deviceid['und'][0]['value'] = $device->deviceid;
            $node->field_device_mac_address['und'][0]['value'] = str_replace(":","-",$device->mac);
            $node->field_device_type['und'][0]['value'] = $device->type;
            //set in-service
            $node->field_statuscode['und'][0]['value'] = SERVICE;
            $node->field_charging_station['und'][0]['target_id'] = $cst_id;
            node_save($node);
        } else {
            //update device
            $device_nid = $check_device->nid;
            $device_node = node_load($device_nid);
            
            $device_node->field_device_type['und'][0]['value'] = $device->type;
            //set in-service
            $device_node->field_statuscode['und'][0]['value'] = SERVICE;
            $device_node->field_charging_station['und'][0]['target_id'] = $cst_id;
            node_save($device_node);
        }
    } catch (Exception $e) {
        drupal_set_message("<pre>" . $e->getTraceAsString() . "</pre>", "error");
    }
}