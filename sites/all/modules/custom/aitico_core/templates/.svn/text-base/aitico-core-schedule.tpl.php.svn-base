<div class="schedule-view">
    <h2><?php print t('Scheduled file updates'); ?>: 
        <span class="blue"><?php print t($company_title); ?> - <?php print t($site_title); ?> - <?php print t($cst_title); ?></span></h2>
    <label class="select-group" for="group-select"><?php print t('Select group'); ?></label>       
    <select id="group-select">
        <?php if (isset($group_lists)): ?>

            <?php            
                $initial_group = array_shift(array_values($group_lists));
                $initial_group_id = $initial_group->nid;
                $files_slots = get_all_slot_of_a_group($initial_group_id);
                $slot_number = count($files_slots);
            ?>
            <?php foreach ($group_lists as $group): ?>
                <option value="<?php print $group->nid; ?>"><?php print t($group->title); ?></option>
            <?php endforeach; ?>
    <?php endif; ?>
    </select>
    <?php foreach ($group_lists as $group): ?>
            <?php echo l(t(''), "administration/schedule/group-view/$group->nid", array("attributes" => array("id" => "update_cst_group_$group->nid", "class" => "use-ajax hiden-link"))); ?>
            <?php echo l(t(''), "administration/schedule/add/$group->nid", array("attributes" => array("id" => "add_schedule_update_$group->nid", "class" => "use-ajax hiden-link"))); ?>        
        <?php endforeach; ?>
    <table class="table table-bordered table-hover" id="schedule-table">
        <thead>
            <tr>
                <th class="span4"><?php print t('Date of the update'); ?></th>
                
                    <?php $i = 0; ?>
                    <?php 
                        foreach ($files_slots as $file_slot_row): 
                            $file_slot_id = $file_slot_row->nid;
                            $file_slot_node = node_load($file_slot_id);
                            $slot_file_type = $file_slot_node->field_file_type['und'][0]['value'];
                            $slot_permission_type = $file_slot_node->field_permission['und'][0]['value'];
                            
                        ?>                        
                            <?php if (!user_access('FILE_MANAGE_ALL')):?>
                                <?php if (user_access('FILE_MANAGE_OWN') && $slot_permission_type > 1):?>
                                    <th> 
                                        <?php  print t('Slot '). ++$i; ?>
                                    </th>
                                <?php endif;?>
                            <?php else: ?>
                                <?php if ($slot_permission_type == 1):?> 
                                        <th><?php print t('Slot ') .++$i;?></th>
                                <?php endif;?>
                            <?php endif;?>                    
                    <?php endforeach;?>           
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule_info as $key => $val): ?>
                <tr nid="<?php print $key;?>" id="schedule_<?php echo $key;?>" class="schedule_file">
                    <?php
                    list($date, $time) = explode('|', date('d/m/Y|H:i', $val['schedule_time']));
                    ?>
                    <td>
                        <input  type="text" class="input-small schedule-date schedule-datepicker" id="schedule-date-<?php print $key;?>" value="<?php print $date; ?>"/>
                        <div class="bootstrap-timepicker">
                         <input type="text" class="input-small schedule-time" id="schedule-time-<?php print $key;?>" value="<?php print $time; ?>"/></div>
                    <a href="/update/schedule/<?php print $key; ?>" class="btn btn-grey save-schedule" style="display:block;width: 50px;" id="save-schedule-<?php echo $key;?>"><?php print t('Save'); ?></a>        
                    </td>
                       <?php foreach ($files_slots as $file_slot_row): ?>                                        
                                <?php
                                    $file_slot_id = $file_slot_row->nid;
                                    $file_slot_node = node_load($file_slot_id);
                                    $slot_file_type = $file_slot_node->field_file_type['und'][0]['value'];
                                    $slot_permission_type = $file_slot_node->field_permission['und'][0]['value'];
                                    $file_node = get_files_node($file_slot_id, FILE_TYPE_CODE_SCHEDULE, $key);
                                    $files_form = get_aitico_node_form('files', $file_slot_id, $file_node);
                                    $form = render($files_form);
                                ?>
                                <?php if (!user_access('FILE_MANAGE_ALL')):?>
                                    <?php if (user_access('FILE_MANAGE_OWN') && $slot_permission_type > 1):?>
                                        <td> 
                                            <?php  print $form; ?>
                                        </td>
                                    <?php endif;?>
                                <?php else: ?>
                                    <?php if ($slot_permission_type == 1):?> 
                                            <td><?php print $form;?></td>
                                    <?php endif;?>
                                <?php endif;?>                            
                       <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
        <a data-toggle="modal" class="btn btn-grey schedule-remove-url" role="button" href="#delete_schedule" id="schedule_remove_url" nid="" ><?php print t("Remove selected schedule"); ?></a>
        <div class="btn btn-primary" id="duplicate_schedule_update" style="display:none;"><?php print t("Duplicate Scheduled Update"); ?></div>
        <div class="btn btn-primary" id="add_schedule_update"><?php print t("Add scheduled update"); ?></div>
</div><!--/log-->

 <!-- start of #deleteSchedule-content -->
 
        <div id="delete_schedule" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="deleteUserLabel" aria-hidden="true">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
            <h3 id="deleteUserLabel"><?php print t('Delete Schedule');?></h3>
          </div>
          <div class="modal-body" id="modal_text">
            <center>
                <h1 class="red"><?php print t('Action is not reversable') ; ?></h1>
                <p><?php print t('Are you sure you want to delete schedule?') ; ?></p>
            </center>
          </div>
          <div class="modal-footer">
            <button id="schedule-cancel-button" class="btn btn-grey schedule-cancel-btn" data-dismiss="modal" aria-hidden="true"><?php print t('Cancel');?></button>
            <button id="schedule-delete-button" class="btn btn-danger schedule-delete-btn" data-schedule-id=""  aria-hidden="true"><?php print t('Delete schedule');?></button>
          </div>
        </div>
<!-- #deleteUser-content -->
