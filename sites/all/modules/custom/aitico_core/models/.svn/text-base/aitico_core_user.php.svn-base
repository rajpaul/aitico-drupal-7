<?php
/**
 * Custom User model for database operation by query execute
 * 
 * @author : Julfiker
 */
class aitico_core_user {
   
    public function __construct() {
        
    }
   
/**
* Check user Access previlage to have access based on role key
* 
* @return boolean
*/
   public function hasAccess($key = null){
       
     if ($key == null)return false;
     
     return user_access($key);  
   }
   
/**
* Get User object 
* 
* @global $user
 * 
* @return $user 
*/
   function getUser(){
       global $user; 
       
       return $user;
   }
   
/**
* Check is log user
* 
* @return boolean 
*/
   function isLogin(){
       
       if ($this->getUser()->uid)
           return true;
       
       return false;
   }    
/**
 * Get users
 * 
 * @param  $node
 * @param  $limit
 * @return $users 
 */   
   public function getUsers($node = null, $limit=0){
      
     $usrs = array();
     
     $query = $this->_getUserQuery($node, $limit);
    // $query->orderBy('u.created', $sort_type);
     $results = $query->execute();
     
     foreach ($results as $result ){
         $usrs[]= $result->uid;
     }
     
     $users = user_load_multiple($usrs);
     
     return $users;     
   }
/**
 * User query
 * 
 * @param  $node
 * 
 * @param  $limit
 * 
 * @return $query
 *  query object 
 */   
   private function _getUserQuery($node, $limit){
       $query = db_select('users', 'u')
                ->fields('u', array('uid'));
               
        if ($limit) {
            $query->range(0, $limit);
        }
        
        return $query;
   }
   
   
   
  
}
