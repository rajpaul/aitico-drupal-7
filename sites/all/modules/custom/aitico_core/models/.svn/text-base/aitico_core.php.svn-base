<?php
/**
 * Custom model for database operation by query execute
 * 
 * @author : Julfiker
 */
class aitico_core_model {
    
    private $_treeArray;
    
    public function __construct() {
        
    }
    
/**
 * GET Hierarchy tree array based on node
 * 
 * return array
 */
    public function getTreeNode($type = null, $limit = 0, $sort_type = 'ASC') {
        
        $this->_makeNodeTreeArray($type, $limit, $sort_type);
        
        return $this->getTreeArray();
    }
    
/**
 * Making node tree array based on query
 * 
 * @param  $type
 *   node type
 * 
 * @param  $limit
 *   query limit
 * 
 * @param  $sort_type
 *   sorting order 
 */
    function _makeNodeTreeArray($type = null, $limit = 0, $sort_type = 'ASC'){
        
        $nids = array(); 
        $companies = array();        
        $res = array();
        
        $query = $this->_getNodeQuery($type, $limit);
        $query->orderBy('n.nid', $sort_type);
        $results = $query->execute();

        foreach ($results as $result) {            
          
          //Binding sites under the company
          if ($result->type == "company"){
            if ($this->is_company_view_access($result->nid))  {
                $statisticsByComapny = $this->_getStatisticsByCompany($result->nid);
                if($statisticsByComapny)
                    $nids[$result->nid]["statistics"] = $statisticsByComapny;

                if($result->entity_id){  
                    $nid[$result->entity_id] = $result->nid;           
                    $nids[$result->nid]["child"][$result->entity_id]["child"] = array();
                    $statisticsBySite = $this->_getStatisticsBySite($result->entity_id);
                    if($statisticsBySite)
                       $nids[$result->nid]["child"][$result->entity_id]["statistics"] = $statisticsBySite;
                }
                else{
                    $nids[$result->nid]["child"] = array();
                }
            }
          }         
          
          //Binding charging station under the site
          if ($result->type == "site"){ 
             if ($this->is_company_view_access($nid[$result->nid]))  { 
                 if($result->cst_entity_id){  
                    $dv[$result->cst_entity_id] = $nid;
                    $nids[$nid[$result->nid]]["child"][$result->nid]["child"][$result->cst_entity_id]["child"] = array();
                    $cstConnectionAlert = $this->_checkCSTConnectionAlert($result->cst_entity_id);
                    $statisticsByCST = $this->_getStatisticsByCST($result->cst_entity_id);//checkCSTConnectionAlert
                    if($cstConnectionAlert)
                        $statisticsByCST['connectionAlert'] = $cstConnectionAlert['connectionAlert'];
                    if($statisticsByCST)
                       $nids[$nid[$result->nid]]["child"][$result->nid]["child"][$result->cst_entity_id]["statistics"] = $statisticsByCST;

                 }
                 else {
                    $nids[$nid[$result->nid]]["child"][$result->nid]["child"]= array();  
                 }
             }
          }      
          
          //Binding devices under the charging station
          if ($result->type == "charging_station"){                  
                 if($result->dev_entity_id){
                     $statisticsByDevice = $this->_getLogsByDevices(array($result->dev_entity_id));
                     $statusByDevice = $this->_getStatusByDevices($result->dev_entity_id);
                     $dvs = (isset($dv[$result->nid]))? $dv[$result->nid]: array(); 
                     foreach ($dvs as $k => $v){
                         if ($this->is_company_view_access($v))  { 
                              if(array_key_exists($result->nid, $nids[$v]["child"][$k]["child"])){
                                 $nids[$v]["child"][$k]["child"][$result->nid]["child"][$result->dev_entity_id]["child"] = array();
                                 if($statusByDevice)
                                    $nids[$v]["child"][$k]["child"][$result->nid]["child"][$result->dev_entity_id]["status"] = $statusByDevice;  
                                 if($statisticsByDevice){
                                     $statisticsByDevice = $this->_chekForAlert($statisticsByDevice);
                                     $nids[$v]["child"][$k]["child"][$result->nid]["child"][$result->dev_entity_id]["statistics"] = $statisticsByDevice;  
                                 }
                              }
                        } 
                     }
                 }
             } 
          
          //$res[] = $result;
        } 
        //print_r($nids);
        $this->_treeArray = $nids;        
    }
    
/**
 * Get Device statistics by company
 * 
 * @param $companyId
 *   company node ID
 * 
 * @return $devStatistics
 */
    private function _getStatisticsByCompany($companyId){
        if(!$companyId)
           return false;

        $siteNodeIds = NULL;
        $devStatistics = NULL;
        $query = $this->_getNodeQuery(null);
        $query->condition('n.nid', $companyId);
        $query->condition('site.entity_id',  NULL, 'IS NOT NULL');
        $results = $query->execute();
         
        foreach ($results as $result) {
         $siteNodeIds[] = $result->entity_id;
        }
        if(!count($siteNodeIds))
           return false;
          
        foreach($siteNodeIds as $siteId){
           $siteDevStatistics[] = $this->_getStatisticsBySite($siteId);
        }
        
        $devStatistics['usageCount'] = 0;
        $devStatistics['usageDuration'] = 0;
        $devStatistics['usageRate'] = 0;
        
        foreach($siteDevStatistics as $stats){
           if(count($stats)){
                if(isset($stats['usageCount'])){
                 $devStatistics['usageCount'] += $stats['usageCount'];
                 $devStatistics['usageDuration'] += $stats['usageDuration'];
                 $devStatistics['usageRate'] += $stats['usageRate'];
                }
                if(!isset($devStatistics['statusBubble']) and isset($stats['statusBubble']))
                    $devStatistics['statusBubble'] = $stats['statusBubble'];
                if(!isset($devStatistics['usageRateAlert']) and isset($stats['usageRateAlert']))
                    $devStatistics['usageRateAlert'] = $stats['usageRateAlert'];
                if(!isset($devStatistics['usageDurationAlert']) and isset($stats['usageDurationAlert']))
                    $devStatistics['usageDurationAlert'] = $stats['usageDurationAlert'];
                if(!isset($devStatistics['usageCountAlert']) and isset($stats['usageCountAlert']))
                    $devStatistics['usageCountAlert'] = $stats['usageCountAlert'];
           }
        }
        if(count($siteNodeIds)> 1 and isset($devStatistics['usageRate']))
            $devStatistics['usageRate'] = round($devStatistics['usageRate']/count($siteNodeIds));
        
        
        return $devStatistics;
     
    }
    
/**
 * Get Device statistics by sites
 * 
 * @param $siteId
 *   site node ID
 * 
 * @return $devStatistics
 */
    private function _getStatisticsBySite($siteId){
        if(!$siteId)
           return false;

        $cstNodeIds = NULL;
        $devStatistics = NULL;
        $query = $this->_getNodeQuery(null);
        $query->condition('n.nid', $siteId);
        $query->condition('cst.entity_id',  NULL, 'IS NOT NULL');
        $results = $query->execute();
         
        foreach ($results as $result) {
         $cstNodeIds[] = $result->cst_entity_id;
        }
        
        if(!count($cstNodeIds))
           return false;

        foreach($cstNodeIds as $cstId){
           $siteDevStatistics[] = $this->_getStatisticsByCST($cstNodeIds);
        }
        
        
            $devStatistics['usageCount'] = 0;
            $devStatistics['usageDuration'] = 0;
            $devStatistics['usageRate'] = 0;
            
            foreach($siteDevStatistics as $stats){
               if(count($stats)){
                    if(isset($stats['usageCount'])){
                        $devStatistics['usageCount'] = $stats['usageCount'];
                        $devStatistics['usageDuration']= $stats['usageDuration'];
                        $devStatistics['usageRate'] = $stats['usageRate'];
                    }
                    if(!isset($devStatistics['statusBubble']) and isset($stats['statusBubble']))
                        $devStatistics['statusBubble'] = $stats['statusBubble'];
                        
                    if(!isset($devStatistics['usageRateAlert']) and isset($stats['usageRateAlert']))
                         $devStatistics['usageRateAlert'] = $stats['usageRateAlert'];
                    if(!isset($devStatistics['usageDurationAlert']) and isset($stats['usageDurationAlert']))
                         $devStatistics['usageDurationAlert'] = $stats['usageDurationAlert'];
                    if(!isset($devStatistics['usageCountAlert']) and isset($stats['usageCountAlert']))
                         $devStatistics['usageCountAlert'] = $stats['usageCountAlert'];
                        
               }
            }
            if(count($cstNodeIds)> 1 and isset($devStatistics['usageRate']))
                $devStatistics['usageRate'] = round($devStatistics['usageRate']/count($cstNodeIds));
        
                
        return $devStatistics;
     
    }

/**
 * Get Device statistics by charging station
 * 
 * @param $cstId
 *   cst node ID
 * 
 * @return $devStatistics
 */
    private function _getStatisticsByCST($cstId){
        if(!$cstId)
           return false;

        $devNodeIds = NULL;
        $devStatistics = NULL;
        $query = $this->_getNodeQuery(null);
        $query->condition('n.nid', $cstId);
        $query->condition('dev.entity_id',  NULL, 'IS NOT NULL');
        $results = $query->execute();
         
        foreach ($results as $result) {
         $devNodeIds[] = $result->dev_entity_id;
        }
        
        if(count($devNodeIds)){
            $devStatistics = $this->_getLogsByDevices($devNodeIds);
            $devStatistics['statusBubble'] = $this->_getStatusBubbleByDevices($devNodeIds);
            foreach ($devNodeIds as $devId) {
               $devIndividualStat = $this->_getLogsByDevices(array($devId));
               if(isset($devIndividualStat['usageCount'])){
                    $devIndividualStat = $this->_chekForAlert($devIndividualStat);
                    if(isset($devIndividualStat['usageRateAlert']) and !isset($devStatistics['usageRateAlert']))
                         $devStatistics['usageRateAlert'] = $devIndividualStat['usageRateAlert'];
                    if(isset($devIndividualStat['usageDurationAlert']) and !isset($devStatistics['usageDurationAlert']))
                         $devStatistics['usageDurationAlert'] = $devIndividualStat['usageDurationAlert'];
                    if(isset($devIndividualStat['usageCountAlert']) and !isset($devStatistics['usageCountAlert']))
                         $devStatistics['usageCountAlert'] = $devIndividualStat['usageCountAlert'];
               }
            }
            
        }
        
        return $devStatistics;
    }

/**
 * Query to get device and logs joined for device logs/statistics
 * 
 * @param $devNodeIds
 *   device node ID's array
 * 
 * @return $devStats
 */
    private function _getLogsByDevices($devNodeIds) {
        $query = db_select('node', 'n')
                ->fields('n', array('nid',"type"))
                ->fields("log", array('entity_id'));                
        $query->condition('n.nid', $devNodeIds, 'IN');
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
            $individualDuration = $this->_deviceLogDuration($logId);
            $logDuration += $individualDuration;
            if(isset($logNode->field_acquired['und'][0]['value']))
                $logUsageCount ++;
            $totalHoursUntilCreated = $this->_deviceUsage($result->nid);
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
 * Get status bubble for hierarchy
 * 
 * @param $devNodeIds
 *   device node ID's array
 * 
 * @return $devStats['statusBubble']
 *   device status bubble
 */
    private function _getStatusBubbleByDevices($devNodeIds) {
        $expiredIcon = '';
        $serviceIcon = '';
        
        foreach ($devNodeIds as $devId) {
            $devNode = node_load($devId);
            $device_type = isset($devNode->field_device_type['und'][0]['value'])?$devNode->field_device_type['und'][0]['value']:null;
            if($device_type and $device_type == 2)
                continue;
            $deviceStatus = $this->_getStatusByDevices($devId);
            if($deviceStatus and $deviceStatus['statusCode'] == 4)
                $expiredIcon = $deviceStatus['statusIcon'];
            if($deviceStatus and $deviceStatus['statusCode'] == 5)
                $serviceIcon = $deviceStatus['statusIcon'];
        }
        
        $devStats['statusBubble'] = $serviceIcon.$expiredIcon;
        return $devStats['statusBubble'];
        
    }
/**
 * Check CST connection for showing alert
 * 
 * @param $cstId
 *   cst node ID
 * 
 * @return boolean
 *  
 */    
    function _checkCSTConnectionAlert($cstId){
        
        $cstNode = node_load($cstId);
        $nextUpdateAlertSettings = variable_get('charging_station_connection');
        if(!$nextUpdateAlertSettings)
        return false;
        
        
        if(isset($cstNode->field_next_update['und'][0]['value']))
            $nextUpdateTimeStamp = $cstNode->field_next_update['und'][0]['value'];
        else
            return false;
        
        $currentTimeStamp = time();
        
        if($currentTimeStamp > $nextUpdateTimeStamp){
           $cstConnection['connectionAlert'] = "<i style=\"color:#d15b47;\"  class=\"icon-exclamation-sign\"></i> ".t("No connection");
           return $cstConnection;
        }
        else
           return false;
        
    }
    
/**
 * Check device alert, based on alert settings on usage statistics
 * 
 * @param $devStatistics
 * 
 * @return $devStatistics
 */
    private function _chekForAlert($devStatistics){
     
       $usageCountLowerLimitCheck = variable_get('device_usage_count_lower_limit');
       $usageCountLowerLimitValue = variable_get('device_usage_count_lower_limit_value');
       
       $usageCountHigherLimitCheck = variable_get('device_usage_count_higher_limit');
       $usageCountHigherLimitValue = variable_get('device_usage_count_higher_limit_value');
       
       $usageDurationLowerLimitCheck = variable_get('device_total_usage_count_lower_limit');
       $usageDurationLowerLimitValue = variable_get('device_total_usage_count_lower_limit_value');
       
       $usageDurationHigherLimitCheck = variable_get('device_total_usage_count_higher_limit');
       $usageDurationHigherLimitValue = variable_get('device_total_usage_count_higher_limit_value');
       
       $usageRateLowerLimitValue = variable_get('device_usage_lower_limit_value');
       $usageRateLowerLimitCheck = variable_get('device_usage_lower_limit');
       
       $usageRateHigherLimitCheck = variable_get('device_usage_higher_limit');
       $usageRateHigherLimitValue = variable_get('device_usage_higher_limit_value');
       
       if(($usageCountLowerLimitCheck and $devStatistics['usageCount'] < $usageCountLowerLimitValue) or ($usageCountHigherLimitCheck and $devStatistics['usageCount'] > $usageCountHigherLimitValue))
          $devStatistics['usageCountAlert'] = "<i style=\"color:#d15b47;\" class=\"icon-exclamation-sign\"></i>";
          
       if(($usageDurationLowerLimitCheck and $devStatistics['usageDuration'] < $usageDurationLowerLimitValue) or ($usageDurationHigherLimitCheck and $devStatistics['usageDuration'] > $usageDurationHigherLimitValue))
          $devStatistics['usageDurationAlert'] = "<i style=\"color:#d15b47;\" class=\"icon-exclamation-sign\"></i>";
          
       if(($usageRateLowerLimitCheck and $devStatistics['usageRate'] < $usageRateLowerLimitValue) or ($usageRateHigherLimitCheck and $devStatistics['usageRate'] > $usageRateHigherLimitValue))
          $devStatistics['usageRateAlert'] = "<i style=\"color:#d15b47;\"  class=\"icon-exclamation-sign\"></i>";
          
       return $devStatistics; 
    }
    
    
/**
 * Get device usage percentage
 * 
 * @param $deviceId
 *   device node ID
 * 
 * @return $totalHoursUntilCreated
 */
    private function _deviceUsage($deviceId){
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
 * Get device log duration by log node ID
 * 
 * @param $logId
 *   device log node ID
 * 
 * @return $totalHoursUntilCreated
 *   
 */
    private function _deviceLogDuration($logId){
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
 * Get device status by device node ID
 * 
 * @param $deviceId
 *   device node ID
 * 
 * @return $status
 */
    public function _getStatusByDevices($deviceId){
        $devNode = node_load($deviceId);
        $statusCode = $devNode->field_statuscode['und'][0]['value'];
        if($statusCode < 0 or $statusCode > 6)
            return false;
        $status = $this->_mapStatus($statusCode);
        return $status;
    }
    
/**
 * Status code mapping by status code
 * 
 * @param $statusCode
 * 
 * @return $status
 */
    private function _mapStatus($statusCode){
        switch($statusCode){
            case 0:
                $status['statusText'] = t("CHARGING");
                $status['statusIcon'] = "<i class=\"icon-refresh\"></i>";
                $status['cellClass'] = "class=\"muted\"";
                break;
            case 1:
                $status['statusText'] = t("Free");
                $status['statusIcon'] = "<i class=\"icon-ok\"></i>";
                $status['cellClass'] = "class=\"green\"";
                break;
            case 2:
                $status['statusText'] = t("Reserved");
                $status['statusIcon'] = "<i class=\"icon-ok\"></i>";
                $status['cellClass'] = "class=\"blue\"";
                break;
            case 3:
                $status['statusText'] = t("Loaned");
                $status['statusIcon'] = "<i class=\"icon-share-alt\"></i>";
                $status['cellClass'] = "class=\"blue\"";
                break;
            case 4:
                $status['statusText'] = t("Expired");
                $status['statusIcon'] = "<i class=\"icon-bell-alt orange\"></i>";
                $status['cellClass'] = "class=\"orange\"";
                break;
            case 5:
                $status['statusText'] = t("In service");
                $status['statusIcon'] = "<i class=\"icon-off red\"></i>";
                $status['cellClass'] = "class=\"red\"";
                break;
            case 6:
                $status['statusText'] = t("Prereserved");
                $status['statusIcon'] = "<i class=\"icon-ok\"></i>";
                $status['cellClass'] = "class=\"blue\"";
                break;
            default:
                $status['statusText'] = '';
                $status['statusIcon'] = '';
                $status['cellClass'] = '';
        }
        $status['statusCode'] = $statusCode;
        
        return $status;
    }
    
    
/**
 * Private query method
 * 
 * @param $type
 *   node type
 * 
 * @param $limit
 *   query limit
 * 
 * @return $query 
 *   query object
 */
    private function _getNodeQuery($type, $limit = 0) {
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
 *Getter treeArray
 * 
 * @return array
 */
    public function getTreeArray(){
        return $this->_treeArray;
    }   
    
    
/**
 * Implementation of access of company view based on company id 
 * 
 * @param $company_id
 *   Company node ID
 * 
 * @return boolean
 *   TRUE if user has access ,FALSE otherwise
 */

    function is_company_view_access($company_id) {
     
        global $user;
        $is_company_member = db_query("SELECT uid FROM {users_companies_sites} WHERE uid = :uid AND cid = :cid", array(
            ':uid' => $user->uid,
            ':cid' => $company_id
                ))->fetch();

        if ($is_company_member) {
            return user_access('COMPANY_VIEW_OWN');
        }

        return user_access('COMPANY_MANAGE') || user_access('COMPANY_VIEW_ALL');
    }


 }
