<?php if (!$target): ?>
    <div class="tab-pane" id="tab-files">  
        <div id="files_update">
        <?php endif; ?>    

        <?php global $cst_global_id;
        $cst_global_id = $cst_id; ?>             
<?php $link = l("refresh-files", "refresh-file/" . $cst_id, array('attributes' => array('class' => array('use-ajax')))); ?>

        <span style="display: none" class="ajax_link_files_refresh"> 
<?php print $link; ?>

        </span>
        <input type="hidden" name="global_cst_id" id="global_cst_id" value="<?php echo $cst_global_id; ?>" /> 

            <?php foreach ($file_groups as $file_group_title => $file_slots): ?>        
            <div class="group-wallpaper">
                <?php if (sizeof($file_slots) > 0): ?>
                    <h4 class="group-title"> <?php print $file_group_title; ?></h4>                    
                    <?php foreach ($file_slots as $slot_id): ?>

                        <?php
                        $slot_node = node_load($slot_id);
                        $slot_permission_type = $slot_node->field_permission['und'][0]['value'];
                        $file_node = get_files_node($slot_id, FILE_TYPE_CODE_NORMAL);
                        $files_form = get_aitico_node_form('files', $slot_id, $file_node);
                        $form = render($files_form);
                        ?>
                        <?php
                        if (!user_access('FILE_MANAGE_ALL')) {
                            if (user_access('FILE_MANAGE_OWN') && $slot_permission_type > 1) {
                                print $form;
                            }
                        } else {                            
                            if ($slot_permission_type == 1) {
                                print $form;
                            }
                        }
                        ?>                        
                    <?php endforeach; ?>   
                <?php endif; ?>     
                <div style="clear:both"></div>
            </div>
        <?php endforeach; ?>       
        <?php if (count($file_groups) > 0): ?>
        <div class="clearfix pull-right">
            <?php if (user_access('FILE_SCHEDULE_ALL') || user_access('FILE_SCHEDULE_OWN')): ?>
                <a href="administration/schedule/<?php print $cst_id; ?>" class="btn btn-small btn-primary schedule-button"><?php print t('Schedule updates'); ?></a>
            <?php endif; ?>
        </div>
        <?php endif;?>
        <?php if (!$target): ?>
        </div>       
    </div>
<?php endif; ?>
