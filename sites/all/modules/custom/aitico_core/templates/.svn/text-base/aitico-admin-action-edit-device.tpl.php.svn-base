<div id="tab-description" class="tab-pane in active">
    <div class="form-item">
        <label><?php print t('Device Id') ?>:</label>
        <span>
            <?php print filter_xss($device_node->title); ?>
        </span>
    </div>
    <div class="form-item">
        <label><?php print t('MAC Address') ?>:</label>
        <span>
            <?php print filter_xss($device_node->field_device_mac_address['und'][0]['value']); ?>
        </span>
    </div>
    <div class="form-item">
        <label><?php print t('Device Type') ?>:</label>
        <span>
            <?php
            $device_type_val = $device_node->field_device_type['und'][0]['value'];
            if ($device_type_val == 2) {
                print t('Inactive');
            } else {
                print t('In use');
            }
            ?>
        </span>
    </div>

    <div class="form-actions form-wrapper">
        <?php
        $in_service_btn_link = l(t('Put it in service'), "administration/device/change-status/" . $device_node->nid, array('attributes' => array('class' => array('device-change-status btn btn-primary'))));
        print $in_service_btn_link;
        ?>
    </div>
</div>