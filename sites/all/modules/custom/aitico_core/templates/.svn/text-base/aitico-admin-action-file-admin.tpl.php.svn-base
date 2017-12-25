<div id="tab-files-admin" class="tab-pane">
  <div id="group-wallpaper-admin" class="clearfix">
    <?php foreach ($groupNslots as $group => $slots): $groupNode = node_load($group); ?>     
      <div class="widget-box group-info" data-group="<?php print $groupNode->title; ?>">
        <div class="widget-header">
          <h4 class="grey smaller group-name"><?php print $groupNode->title; ?></h4>
          <span class="widget-toolbar">
              <a href="#" data-action="collapse"><i class="icon-chevron-up"></i></a>
              <a href="#" data-action="close"><i class="icon-remove action-delete-filegroup" data-groupid="<?php print $group; ?>"></i></a>
              <form class="form-inline aitico-remove-filegroup-form" id="aitico-filegroup-remove-form-<?php print $group; ?>" accept-charset="UTF-8" method="post" action="/filegroup">
                 <input type="hidden" name="remove-filegroup-id" value="<?php print $group; ?>">
                 <input type="hidden" name="cst_id" value="<?php print $cstId; ?>" >  
              </form>
          </span>
        </div>
        <div class="widget-body">
        <form class="form-inline aitico-filegroup-forms" id="aitico-filegroup-slots-form-<?php print $group; ?>" accept-charset="UTF-8" method="post" action="/filegroup">
          <div class="widget-main" >
              <div id="widget-main-filegroup-<?php print $group; ?>">
              <?php foreach ($slots as $slot): $slotNode = node_load($slot); ?>        
                
                <div class="group-items" >
                    
                        <select name="slot-permission-select[]">
                            <option  <?php if ($slotNode->field_permission['und'][0]['value'] == '1'): ?>selected="selected"<?php endif; ?> value="1">Super Content admin</option>
                            <option <?php if ($slotNode->field_permission['und'][0]['value'] == '2'): ?>selected="selected"<?php endif; ?> value="2">Content admin</option>
                        </select>
                        <select name="slot-filetype-select[]">
                            <option <?php if ($slotNode->field_file_type['und'][0]['value'] == '1'): ?>selected="selected"<?php endif; ?> value="1">Image</option>
                            <option <?php if ($slotNode->field_file_type['und'][0]['value'] == '2'): ?>selected="selected"<?php endif; ?> value="2">Text</option>
                        </select>
                        <span class="input-buttons">
                            <input type="button" class="btn btn-primary btn-small remove-slot" value="<?php print t("Remove item");?>">
                        </span>
                        <input type="hidden" name="file-slot-id[]" value="<?php print $slot; ?>">
                        
                    <hr>
                </div>
                
              <?php endforeach; ?>
              
              </div>
              <div class="clearfix ">
                  <a href="#" class="btn btn-small btn-primary pull-right  filegroup-add-item" data-groupid="<?php print $group; ?>"><?php print t("Add item"); ?></a>
                  <a href="#" class="btn btn-small btn-primary   filegroup-submit-item" data-groupid="<?php print $group; ?>"><?php print t("Save item");?></a>
              </div>
              <input type="hidden" name="filegroup-id" value="<?php print $group; ?>">
              <input type="hidden" name="cst_id" value="<?php print $cstId; ?>" >  
              <input type="hidden" name="existing-file-slot-id" value="<?php print count($slots) ? implode(",",$slots): ''; ?>">
              
          </div>
        </form>
        </div>
      </div><!--/group-info-->
    <?php endforeach; ?>
  </div>

  <div class="clearfix">
    <div class="input-prepend input-append">
    <form class="form-inline aitico-filegroup-forms" id="aitico-filegroup-slots-form-<?php print $group; ?>" accept-charset="UTF-8" method="post" action="/filegroup">    
      <span class="add-on"><i class="icon-group"></i></span>
      <input class="input-large" type="text" name="filegroup-add" id="group-add" />
      <input type="submit" class="btn btn-primary btn-small" id="filegroup-add-group" value="<?php print t("Add group"); ?>">
      <input type="hidden" name="cst_id" value="<?php print $cstId; ?>" >  
      </form>  
    </div>
  </div>
</div>

<!-- hidden div to clone slots -->
<div class="group-items" id="slot-item-clone" style="display: none">
    
        <select name="slot-permission-select[]">
            <option value="1">Super Content admin</option>
            <option value="2">Content admin</option>
        </select>
        <select name="slot-filetype-select[]">
            <option value="1">Image</option>
            <option value="2">Text</option>
        </select>
        <span class="input-buttons">
            <input type="button" class="btn btn-primary btn-small remove-slot" value="<?php print t("Remove item")?>">
        </span>
        <input type="hidden" name="file-slot-id[]" value="">    
    <hr>
</div>


<div style="display:none" id="filegroup-validation-message" class="alert alert-block fade in alert-error">
    <h2 class="element-invisible">Error message</h2>
    <button data-dismiss="alert" class="close" type="button"><i class="icon-remove"></i></button>
    <strong><i class="icon-remove"></i> Error</strong>
    Can't create empty group.
</div>

