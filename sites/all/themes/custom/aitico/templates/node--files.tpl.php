
<div id="upload_ajax">
    <?php if ($content){
        
     var_dump($content['field_file']["#object"]->nid);
     
           $nodid = arg(1);        
           $node = node_load($nodid);
           if($node) {
               $arr  = array(
                   "nid" => $node->nid,
                   "filename" => $node->field_file['und'][0]["filename"],
                   "fileslot_id" => $node->field_slot["und"][0]["target_id"]
               );
               echo json_encode($arr);
           }
      }
    ?>
</div>    
