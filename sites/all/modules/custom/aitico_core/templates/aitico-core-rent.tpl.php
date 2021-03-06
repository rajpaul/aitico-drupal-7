<div class="row-fluid">
    <div class="charging-stations">
        <?php if (!empty($cst_results)): ?>
            <?php foreach ($cst_results as $key => $val): ?>
                <div id ="rent-info"class="widget-box charging-info" data-cst-url ="/rent/info/<?php print $site_id; ?>">
                    <div class="widget-header">
                                <h4 class="grey smaller station-name">
                                    <?php if(!empty($val['title'])){ ?>                            
                                            <?php print filter_xss(t($val['title'])); ?>,
                                    <?php } ?>
                                    <span class="blue station-device-amount bold cst-device-info" 
                                          data-available-device ="<?php print count($val['device_available']) ?>"
                                          id="device_info">
                                        <?php
                                        if (isset($val['device_available'])) {
                                            print count($val['device_available']);
                                        } else {
                                            print '0';
                                        }
                                        ?>/<?php
                                        if (isset($val['device_all'])) {
                                            print count($val['device_all']);
                                        } else {
                                            print '0';
                                        }
                                        ?>
                                    </span>
                                </h4>
                            </div>
                    <div class="widget-body">
                        <div class="widget-main">
                            <p>
                                <?php if (isset($val['device_available']) && !empty($val['device_available'])): ?>
                                    <?php foreach ($val['device_available'] as $row): ?>
                                        <?php
                                        $device_node = node_load($row->dev_entity_id);

                                        $device_status = $device_node->field_statuscode['und'][0]['value'];
                                        if ($device_status == 1) {
                                            $device_class = 'label-success';
                                        } else {
                                            $device_class = 'label-important';
                                        }
                                        ?>
                                        <span data-original-title="<?php print $device_node->field_deviceid['und'][0]['value']; ?>" title="" data-placement="top" data-rel="tooltip" data-trigger="hover" class="label <?php print $device_class; ?> label-large"><?php print $device_node->field_batterylevel['und'][0]['value']; ?>%</span> 
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </p>
                            
                            <?php if(!empty($val['description'])){ ?>                            
                                <p> <?php print t($val['description']);?></p>  
                            <?php } ?>
                            
                        </div>
                    </div>                    
                </div>
    <?php endforeach; ?>
<?php endif; ?>

        <label for="form-description"><?php print t('Description'); ?></label>
        <textarea style="height: 90px; overflow: hidden; word-wrap: break-word; resize: none;" class="autosize-transition span12" id="form-description"></textarea>
        <label for="duration"><?php print t('Duration'); ?></label>
        <div id="duration_error" style = "color:red;"></div>
        <div class="bootstrap-timepicker"><div class="bootstrap-timepicker-widget dropdown-menu"><table><tbody><tr><td><a data-action="incrementHour" href="#"><i class="icon-chevron-up"></i></a></td><td class="separator">&nbsp;</td><td><a data-action="incrementMinute" href="#"><i class="icon-chevron-up"></i></a></td></tr><tr><td><input type="text" maxlength="2" class="bootstrap-timepicker-hour" name="hour"></td> <td class="separator">:</td><td><input type="text" maxlength="2" class="bootstrap-timepicker-minute" name="minute"></td> </tr><tr><td><a data-action="decrementHour" href="#"><i class="icon-chevron-down"></i></a></td><td class="separator"></td><td><a data-action="decrementMinute" href="#"><i class="icon-chevron-down"></i></a></td></tr></tbody></table></div><input type="text" class="input-small" id="duration"></div>
        <div class="form-actions">
            <a data-toggle="modal" class="btn btn-primary" role="button" href="#pinCode" id="rent-button" ><?php print t('Rent device'); ?></a>
        </div>

    </div><!--/charging-station-->
</div>

<?php print $aitico_core_pin; ?>