<?php
global $cst_global_id; 
if (isset($form['#node']) && $form['#node']->field_file['und'][0]['fid'] > 0): ?>
    <?php
        $node = $form['#node'];   
        $slot_id = $node->field_slot['und'][0]['target_id'];
        $slot_node = node_load($slot_id);
        $file_group_id = $slot_node->field_filegroup['und'][0]['target_id'];        
        $style_name = 'file_thumbnail';                
        $value = $node->field_file['und'];
        $path = (isset($value[0]['uri']) ? $value[0]['uri'] : null);      
        $file_name = (isset($value[0]['filemime']) ? $value[0]['filemime'] : null);    
                
        if($file_name == "text/plain")
            $img = theme('image', array('style_name' => "file_thumbnail", "alt" =>"Text file", 'attributes' => array('class' => 'text_thumblain'), "width" => 140, "height" => 140, 'path' => "/sites/all/themes/custom/aitico/assets/img/text-file-icon.png"));
        else 
            $img = theme('image_style', array('style_name' => "file_thumbnail", 'path' => $path));    
            
    ?>
    <div class="span2">
        <div class="form-item form-type-managed-file form-item-field-file-und-0">
           <div class="ace-file-input ace-file-multiple">
                <label class="selected"  for="edit-field-file-und-0-upload--5">
                <span class="large" >
                <?php print $img; ?>
                <i class="icon-picture"></i></span>
                </label>
                <?php if ($node->field_file_type_code['und'][0]['value'] == FILE_TYPE_CODE_SCHEDULE):?>
                    <a class="remove use-ajax remove-schedule-file" href="/remove-schedule-file/<?php print $file_group_id;?>/<?php echo $node->nid;?>" ><i class="icon-remove"></i></a>
                <?php else:?>
                    <a class="remove use-ajax" href="/remove-file/<?php echo $cst_global_id; ?>/<?php echo $node->nid;?>" ><i class="icon-remove"></i></a>
                <?php endif;?>
                
            </div>
        </div>    
    </div>
<?php else: ?>  
    <div class="span2">
        <?php  print drupal_render($form['field_file']); ?>                         
        <?php  print drupal_render_children($form); ?>
    </div>
<?php endif; ?>






