<div class="tree-view span6">
    <table class="table table-bordered table-condensed tree_table" id="example-basic">
        <thead>
            <tr>
                <th><?php print t('Company'); ?></th>
                <th><?php print t('Usage'); ?></th>
                <th><?php print t('Count'); ?></th>
                <th><?php print t('Hours'); ?></th>
                <th><?php print t('Status'); ?></th>
            </tr>
        </thead> 
        <tbody>
            <?php

            function _treeRender($nodes, $parent = null) { ?>
                <?php foreach ($nodes as $key => $val): $node = node_load($key); ?>     
                    <?php if ($node): ?>
                            <?php if (($node->type == "company" || $node->type == "site" || $node->type == "charging_station") || ($node->type == "device" && $node->field_device_type['und'][0]['value']!=2)):?>
                            <tr id="row-marker-<?php echo trim($key); ?>" data-tt-id="<?php echo trim($key); ?>" <?php if ($parent): ?> data-tt-parent-id="<?php echo trim($parent); ?>" <?php endif; ?> type="<?php echo $node->type; ?>"  nid="<?php echo $node->nid; ?>"
                                class =" <?php print $node->type; ?>"> 
                                <td>
                                    <?php
                                    $title = l(t($node->title), "administration/info/" . $node->type . "/" . $node->nid, array('attributes' => array('class' => array('use-ajax'))));
                                    print filter_xss($node->title);
                                    ?>                                    
                                        <span style="display:none;" class="ajax_link">
                                            <?php print $title; ?>
                                        </span>                                    
                                </td>
                                <td>
                                    <?php echo isset($val["statistics"]['usageRate']) ? round($val['statistics']['usageRate'],2). '%' : ''; ?>
                                    <?php echo isset($val["statistics"]['usageRateAlert']) ? $val['statistics']['usageRateAlert'] : ''; ?>
                                </td>  
                                <td>
                                    <?php echo isset($val["statistics"]['usageCount']) ? $val['statistics']['usageCount'] : ''; ?>
                                    <?php echo isset($val["statistics"]['usageCountAlert']) ? $val['statistics']['usageCountAlert'] : ''; ?>
                                </td>
                                <td>
                                    <?php echo isset($val["statistics"]['usageDuration']) ? $val['statistics']['usageDuration'] : ''; ?>
                                    <?php echo isset($val["statistics"]['usageDurationAlert']) ? $val['statistics']['usageDurationAlert'] : ''; ?>
                                </td>
                                <td <?php echo isset($val["status"]) ? $val['status']['cellClass'] : ''; ?>>

                                    <?php echo (isset($val["statistics"]['statusBubble']) and !isset($val["status"])) ? $val["statistics"]['statusBubble'] : ''; ?>
                                    <?php echo isset($val["status"]) ? $val['status']['statusIcon'] : ''; ?>
                                    <?php echo isset($val["status"]) ? $val['status']['statusText'] : ''; ?>
                                    <?php echo isset($val["statistics"]["connectionAlert"]) ? $val['statistics']['connectionAlert'] : ''; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                    <?php endif; ?>
                    <?php _treeRender($val["child"], $key); ?>
                <?php endforeach; ?>
            <?php }

            _treeRender($nodes); ?>   
        </tbody>
    </table>
    <div class="clearfix">
        <span class="pull-left">
            <a href="#" class="btn btn-small btn-grey link-collapse-all" onclick="jQuery('#example-basic').treetable('collapseAll'); return false;"><?php print t("Collapse all"); ?></a>
            <a href="#" class="btn btn-small btn-grey" onclick="jQuery('#example-basic').treetable('expandAll'); return false;"><?php print t("Expand all");?></a>
        </span>
        <span class="pull-right">
            <!-- To add superadmin/super contentadmin-->
            <?php if (user_access('USER_MANAGE_SYSTEM')): ?>
                <?php echo l(t('System'), "administration/system/user", array("attributes" => array("id" => "system-node",
                    "class" => "btn btn-small btn-primary use-ajax" , "title" => t("Modify system level users")))); ?>
            <?php endif; ?>
            <?php if (user_access('COMPANY_MANAGE')): ?> 
                <?php echo l(t('Add company'), "administration/company/add", array("attributes" => array("id" => "tree_url_company", "class" => "btn btn-small btn-primary"))); ?>

                <?php echo l(t('Delete'), "#", array("attributes" => array("id" => "delete-node", "class" => "btn btn-grey btn-small"))); ?>

                <?php echo l(t('Add company'), "administration/company/add", array("attributes" => array("id" => "tree_url", "class" => "btn btn-small btn-primary"))); ?>
                
            <?php endif; ?>
            <?php if (aitico_core_device_view_access()): ?> 
                <?php echo l(t('Delete Device'), "#", array("attributes" => array("id" => "delete-node-device", "class" => "btn btn-grey btn-small"))); ?>
                <?php echo l(t('Add Device'), "administration/device/add", array("attributes" => array("id" => "tree_url_device", "class" => "btn btn-small btn-primary"))); ?>
            <?php endif;?>
        </span>
    </div>
</div>

<script type="text/javascript">
(function($) {
        $(document).ready(function() {
            treeInfoText = $.cookie("aitico_tree_info")
            
            treeInfo = JSON.parse(treeInfoText);
            if(treeInfo){
                
                var rows = treeInfo.tree;
                for (var i=0;i<rows.length;i++)
                {
                    if(rows[i].company)
                    $("#example-basic").treetable("expandNode", $("#row-marker-"+rows[i].company).data("ttId"));
                    
                    if(rows[i].site)
                    $("#example-basic").treetable("expandNode", $("#row-marker-"+rows[i].site).data("ttId"));
                    
                    if(rows[i].cst)
                    $("#example-basic").treetable("expandNode", $("#row-marker-"+rows[i].cst).data("ttId"));
                }
            }
            
            treeInfoSelection = $.cookie("aitico_tree_info_selected")
            
            if(treeInfoSelection && typeof rows !== 'undefined')
            {
                $("#row-marker-"+treeInfoSelection).trigger('mousedown');
                $("#row-marker-"+treeInfoSelection).trigger('click');
            }
            
        });
    })(jQuery);

    
</script>