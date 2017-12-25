<ul class="nav nav-tabs" id="myTab">
    <?php if (aitico_core_edit_view_access()): ?>
        <li class="active"><a data-toggle="tab" href="#tab-description"><i class="icon-edit bigger-110"></i> <?php print t('Edit') ?></a></li>
    <?php endif ?>   
    <?php if ($type == 'charging_station'): ?>

        <?php if(aitico_core_files_view_access()): ?>
            <li><a data-toggle="tab" href="#tab-files"><i class="icon-file-alt bigger-110"></i> <?php print t('Files') ?></a></li>           
        <?php endif; ?>   
        <?php if (user_access('SLOT_MANAGE')): ?>
            <li><a data-toggle="tab" href="#tab-files-admin"><i class="icon-file bigger-110"></i> <?php print t('File admin') ?></a></li>
        <?php endif; ?>

    <?php endif; ?>

    <?php if (aitico_core_log_view_access() && ($type != 'device')): ?>
        <li><a data-toggle="tab" href="#tab-logs"><i class="icon-copy bigger-110"></i> <?php print t('Logs') ?></a></li>           
    <?php endif ?>
    <?php global $user;
    if ($type != 'charging_station' && aitico_core_manage_user_access($user) && ($type != 'device')): ?>
        <li><a data-toggle="tab" href="#tab-users"><i class="icon-user bigger-110"></i> <?php print t('Users') ?></a></li>
        <?php if ($type == 'site'):?>
         <li><a data-toggle="tab" href="#tab-devices"><i class="bigger-110"></i> <?php print t('Devices') ?></a></li>
        <?php endif; ?>

    <?php endif; ?>
</ul>
<div class="clear-right-block"></div>
<div class="tab-content">   
<?php print $content; ?>
</div>
<script>
    (function($) {
        $(document).ready(function() {
            setTimeout(function(){
                $("#myTab li:eq(0) a").trigger('click');  
            },100);
        });
    })(jQuery);
</script>