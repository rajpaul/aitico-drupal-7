<div class="log-view">
    <h2><?php print t('Log'); ?>: 
        <span class="blue"><?php print t($company_name); ?>
        <?php if ($site_name !=''):?>-<?php print t($site_name); endif; ?>
        <?php if ($cst_name !=''):?>-<?php print t($cst_name); endif; ?>
        
        </span></h2>
    <h4><?php print t('Time period'); ?>:<span class="blue"><?php print $date_range; ?></span></h4>
    <table class="table table-striped table-bordered table-hover" id="logtable">
        <thead>
            <tr>
                <th><?php print t('PIN'); ?></th>
                <th><?php print t('Duration'); ?></th>
                <th class="span8 hidden-phone"><?php print t('Description'); ?></th>
                <th><?php print t('Valid until'); ?></th>
                <th class="hidden-phone"><?php print t('User'); ?></th>                           
                <th><?php print t('Company'); ?></th>
                <th><?php print t('Site'); ?></th>
                <th><?php print t('Acquired from'); ?></th>
                <th><?php print t('Returned to'); ?></th>
                <th><?php print t('Device'); ?></th>
                <th><?php print t('Acquired'); ?></th>
                <th><?php print t('Returned'); ?></th>                               
            </tr>
        </thead>
        <tbody>
            
            <?php if (!empty($log_results)): ?>
                <?php foreach ($log_results as $row): ?>
                    <?php $log_node = node_load($row->log_entity_id);?>
                    <tr>
                        <td><?php print $log_node->field_pin['und'][0]['value']; ?></td>
                        <td><?php print $log_node->field_duration['und'][0]['value']; ?></td>
                        <td class="hidden-phone">
                            <?php 
                            if(!empty($log_node->field_description)):
                            $description = $log_node->field_description['und'][0]['value'];
                            print t($description);
                            endif;?>
                        </td>
                        <td>
                        <?php 
                            if(!empty($log_node->field_pin_valid_until)) {
                                $valid_until = $log_node->field_pin_valid_until['und'][0]['value'];
                                print date('m/d/Y h:i', $valid_until);
                            }
                        ?>
                        </td>
                        <td class="hidden-phone">
                        <?php
                            $user_id = $log_node->field_user['und'][0]['target_id'];
                            $user_info = user_load($user_id);
                            print $user_info->name;
                        ?>
                        </td>
                        <td><?php print t($company_name); ?></td>
                        <td><?php print t($row->site_title); ?></td>
                        <td><?php print t($row->loan_cst_title); ?></td>
                        <td><?php print t($row->return_cst_title); ?></td>
                        <td><?php print t($row->device_title); ?></td>                    
                        <td>
                        <?php
                            if (!empty($log_node->field_acquired)) {
                                $acquired_time = $log_node->field_acquired['und'][0]['value'];
                                print date('m/d/Y h:i', $acquired_time);
                            }
                         ?>
                        </td>
                        <td>
                         <?php 
                             if(!empty($log_node->field_returned)){
                                $returned_time = $log_node->field_returned['und'][0]['value'];
                                print date('m/d/Y h:i', $returned_time); 
                             } 
                         ?>
                        </td>

                    </tr>

                <?php endforeach; ?>

<?php endif; ?>
        </tbody>
    </table>
</div><!--/log-->