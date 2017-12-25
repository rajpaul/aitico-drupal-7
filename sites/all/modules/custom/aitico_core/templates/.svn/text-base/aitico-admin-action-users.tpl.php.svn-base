<div id="tab-users" class="tab-pane">
    <table class="table table-striped table-bordered table-hover" id="usertable">
        
        
        <thead>
            <tr>
                <th> <?php print t('Name')?> </th> 
                <th> <?php print t('Email') ?> </th> 
                <th> <?php print t('Role') ?> </th>                    
            </tr>
        </thead>
        <tbody> 
            <?php
            if (!empty($user_lists)) {
                foreach ($user_lists as $row):
                    ?>
                    <tr data-user-id ="<?php print $row['uid']; ?>">
                        <td> <?php echo filter_xss($row['name']); ?> </td>
                        <td> <?php echo filter_xss($row['mail']); ?> </td>
                        <td> <?php echo filter_xss($row['role']); ?> </td>
                    </tr>
                <?php endforeach;
            } ?>
                
        </tbody>
    </table>
    <div id="tab-users-buttons" class="pull-right">
        <?php echo l(t('Delete'), "#", array("attributes" => array('id' => "user-delete", 'class' => "btn btn-small btn-grey disabled", 'data-toggle' => "modal"))) ?>
        <?php echo l(t('Edit'), "#", array("attributes" => array('id' => "user-edit", 'class' => "btn btn-small btn-grey disabled", 'data-toggle' => "modal"))) ?>
        <?php echo l(t('Add'), "administration/user/new/$company_id/$site_id", array("attributes" => array('id' => "user-add", 'class' => "btn btn-small btn-primary", 'data-toggle' => "modal"))) ?>
    </div>
</div>

<script>
    (function($) {
        $(document).ready(function() {
            $('#logtable').dataTable();
            $('#usertable').dataTable();               

        });
    })(jQuery);
</script>
