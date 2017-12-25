<?php foreach ($message_groups as $type => $messages): ?>
    <?php foreach ($messages as $message): ?>
        <?php if ($type == 'status'): ?>
            <div class="alert alert-block fade in alert-success">
                <h2 class="element-invisible"><?php echo t('Status message') ?></h2>
                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                <strong class="green"><i class="icon-ok"></i> <?php print t("Success"); ?></strong>
                <?php echo t($message); ?>
            </div>
        <?php elseif ($type == 'warn'): ?>
            <div class="alert alert-block fade in alert-warning">
                <h2 class="element-invisible"><?php echo t('Warning message') ?></h2>
                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                <strong class="yellow"><i class="icon-bell-alt"></i> <?php print t("Warning");?></strong>
                <?php echo t($message); ?>
            </div>
        <?php elseif ($type == 'error'): ?>
            <div class="alert alert-block fade in alert-error">
                <h2 class="element-invisible"><?php echo t('Error message') ?></h2>
                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                <strong><i class="icon-remove"></i> <?php print t("Error"); ?></strong>
                <?php echo t($message); ?>
            </div>
        <?php elseif ($type == 'info'): ?>
            <div class="alert alert-block fade in alert-info">
                <h2 class="element-invisible"><?php echo t('Info message') ?></h2>
                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                <strong><i class="icon-info-sign"></i> Info</strong>
                <?php echo t($message); ?>
            </div>
        <?php else: ?>
            <div class="alert alert-block fade in alert-info">
                <button type="button" class="close" data-dismiss="alert"><i class="icon-remove"></i></button>
                <strong><i class="icon-info-sign"></i> <?php print t("Message");?></strong>
                <?php echo t($message); ?>
            </div>
        <?php endif ?>
    <?php endforeach ?>
<?php endforeach ?>
