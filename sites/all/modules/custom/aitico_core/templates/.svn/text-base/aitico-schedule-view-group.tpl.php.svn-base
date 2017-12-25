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
        <tr nid="<?php print $key;?>" class="schedule_file">
            <?php
            list($date, $time) = explode('|', date('d/m/Y|H:i', $val['schedule_time']));
            ?>
            <td><input type="text" class="input-small schedule-date schedule-datepicker" id="schedule-date-<?php print $key;?>" value="<?php print $date; ?>"/>
                <div class="bootstrap-timepicker">
                    <input  type="text" class="input-small schedule-time" id="schedule-time-<?php print $key;?>" value="<?php print $time; ?>"/>
                </div>
        <a href="/update/schedule/<?php print $key; ?>" class="btn btn-grey save-schedule" style="display:block;width: 50px;" id="save-schedule-<?php echo $key;?>"><?php print t('Save'); ?></a>        
            </td>
             <?php foreach ($files_slots as $file_slot_row): ?>
                           
                    <?php
                        $file_slot_id = $file_slot_row->nid;
                        $file_slot_node = node_load($file_slot_id);
                        $slot_permission_type = $file_slot_node->field_permission['und'][0]['value'];
                        $slot_file_type = $file_slot_node->field_file_type['und'][0]['value'];
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