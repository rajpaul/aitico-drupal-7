<div id="tab-logs" class="tab-pane">
    <div id="invalid-time-range" style="display:none;" class="alert alert-block fade in alert-error">
        <h2 class="element-invisible">Error message</h2>
        <strong><i class="icon-remove"></i> Error</strong>
       <?php print t('Invalid time range'); ?>            
    </div>
    <p><?php print t('Select time period to browse logs.'); ?></p>
    <form id="search_logs_frm" action="administration/log/<?php print $company_id;?>/<?php print $site_id;?>/<?php print $cst_id;?>" target="_new">
        <div class="input-prepend input-append">
            <span class="add-on"><i class="icon-calendar"></i></span>
            <input class="span9" type="text"  name="date-range-picker" id="id-date-range-picker-1" />
            <input type="submit" id="search_logs" class="btn btn-primary btn-small" value="<?php print t("Search logs");?>">
        </div>
    </form>
</div>