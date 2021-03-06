
<table style="width:100%" border="1" class="tree_table">
    <tr>
        <th> Name </th> 
        <th colspan="3"> Action </th>    
    </tr>  
    <?php       
    function _treeRender($nodes, $dash=null) { ?>
    <?php foreach ($nodes as $key => $val): $node = node_load($key); ?>     
        <?php if ($node): ?>
                <tr class="" type="<?php echo $node->type; ?>"  nid="<?php echo $node->nid; ?>" > 
                    <td> <?php echo $dash; ?> <?php echo $node->title; ?> </td>
                    
                    <?php if (node_access('update', $node)):?>
                        <td> <a href="/node/<?php echo $key; ?>/edit/<?php echo $node->type; ?>">edit </a> </td>
                    <?php endif;?>     
                    <td> <?php echo l("delete", "node/$key/delete/" . $node->type); ?> </td>
                    <td> <?php
                            if ($node->type == 'company') {
                                print l("Add logo", "company/$key/logo");
                            }
                           ?>                             
                    </td>
                </tr>  
            <?php endif; ?>
            <?php _treeRender($val["child"], "&nbsp;" . $dash . "--"); ?>
    <?php endforeach; ?>
<?php }

_treeRender($nodes); ?>    
</table>
<a href="/company/new" id="tree_url"> Add company </a>



<div class="tree-view span6">
                    <table class="table table-bordered table-condensed" id="example-basic">
                        <thead>
                          <tr>
                              <th>Company</th>
                            <th>Usage</th>
                            <th>Count</th>
                            <th>Hours</th>
                            <th>Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr data-tt-id="1">
                            <td>Company 1</td>
                            <td>75%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td><i class="icon-off red"></i> <i class="icon-bell-alt orange"></i></td>
                          </tr>
                          <tr data-tt-id="1.1" data-tt-parent-id="1">
                            <td>Site 1</td>
                            <td>55%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td><i class="icon-off red"></i></td>
                          </tr>
                          <tr data-tt-id="1.1.1" data-tt-parent-id="1.1">
                            <td>Charging station 1</td>
                            <td>75%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td><i class="icon-off red"></i></td>
                          </tr>
                          <tr data-tt-id="1.1.1.1" data-tt-parent-id="1.1.1">
                            <td>Device 1</td>
                            <td>75%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td class="red"><i class="icon-off"></i> In service</td>
                          </tr>
                          <tr data-tt-id="1.1.1.2" data-tt-parent-id="1.1.1">
                            <td>Device 2</td>
                            <td>45%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td class="green"><i class="icon-ok"></i> Free</td>
                          </tr>
                          <tr data-tt-id="1.1.1.3" data-tt-parent-id="1.1.1">
                            <td>Device 3</td>
                            <td>45%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td class="blue"><i class="icon-share-alt"></i> Loaned</td>
                          </tr>
                          <tr data-tt-id="1.2" data-tt-parent-id="1">
                            <td>Site 1</td>
                            <td>55%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td><i class="icon-off red"></i> <i class="icon-bell-alt orange"></i></td>
                          </tr>
                          <tr data-tt-id="1.2.2" data-tt-parent-id="1.2">
                            <td>Charging station 1</td>
                            <td>75%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td><i class="icon-off red"></i> <i class="icon-bell-alt orange"></i></td>
                          </tr>
                          <tr data-tt-id="1.2.2.2" data-tt-parent-id="1.2.2">
                            <td>Device 1</td>
                            <td>75%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td class="red"><i class="icon-off"></i> In service</td>
                          </tr>
                          <tr data-tt-id="1.2.2.2" data-tt-parent-id="1.2.2">
                            <td>Device 2</td>
                            <td>45%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td class="muted"><i class="icon-refresh"></i> Charging</td>
                          </tr>
                          <tr data-tt-id="1.2.2.3" data-tt-parent-id="1.2.2">
                            <td>Device 3</td>
                            <td>45%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td class="blue"><i class="icon-ok"></i> Reserved</td>
                          </tr>
                          <tr data-tt-id="1.2.2.4" data-tt-parent-id="1.2.2">
                            <td>Device 4</td>
                            <td>45%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td class="orange"><i class="icon-bell-alt"></i> Expired</td>
                          </tr>
                          <tr data-tt-id="2">
                            <td>Company without subitems</td>
                            <td>75%</td>
                            <td>345112</td>
                            <td>1234</td>
                            <td></td>
                          </tr>
                        </tbody>
                      </table>
                      <div class="clearfix">
                        <a href="#" class="btn btn-small btn-grey link-collapse-all" onclick="jQuery('#example-basic').treetable('collapseAll'); return false;">Collapse all</a>
                        <a href="#" class="btn btn-small btn-grey" onclick="jQuery('#example-basic').treetable('expandAll'); return false;">Expand all</a>

                        <span class="pull-right"><a href="#" id="delete-node" class="btn btn-grey btn-small" data-toggle="modal">Delete</a> <a href="#" id="add-node" class="btn btn-small btn-primary">Add</a></span>
                      </div>
                </div><!--/tree-view-->
