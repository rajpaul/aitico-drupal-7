<tr nid="<?php print $file_schedule_id; ?>" id="schedule_<?php echo $file_schedule_id;?>" class="schedule_file_new">
    <td>
        <input type="text" class="input-small schedule-date" id="schedule-date-<?php print $file_schedule_id;?>" />
        <div class="bootstrap-timepicker">
            <input type="text" class="input-small schedule-time" id="schedule-time-<?php print $file_schedule_id;?>" /></div>        
        <a href="/update/schedule/<?php print $file_schedule_id; ?>" class="btn btn-grey save-schedule" style="display:none;" id="save-schedule-<?php echo $file_schedule_id;?>"><?php print t('Save'); ?></a>        
    </td>
     <?php foreach ($file_slots as $file_slot_row): ?>
        <td>             
            <?php
                $file_slot_id = $file_slot_row->nid;
                $file_slot_node = node_load($file_slot_id);
                $slot_file_type = $file_slot_node->field_file_type['und'][0]['value'];
                $file_node = get_files_node($file_slot_id, FILE_TYPE_CODE_SCHEDULE, $file_schedule_id);
                $files_form = get_aitico_node_form('files', $file_slot_id, $file_node);
                $form = render($files_form);
            ?>
            <?php print $form; ?>            
        </td>
    <?php endforeach; ?>   
</tr>