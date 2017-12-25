<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="tab-users" class="tab-pane">
<table class="table table-striped table-bordered table-hover" id="usertable">
    <thead>
        <tr>
            <th> Name </th> 
            <th> Email </th> 
            <th> Role </th>

        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($user_lists)) {
            foreach ($user_lists as $row):
                ?>
                <tr>
                    <td> <?php echo $row['name']; ?> </td> 
                    <td> <?php echo $row['mail']; ?> </td> 
                    <td> <?php echo $row['role']; ?> </td>

                </tr>
            <?php endforeach;
        } else { ?>
            <tr>
                <td colspan="3"> <?php echo t('Theres is no user'); ?> </td> 
            </tr>
        <?php } ?>
    </tbody>
</table>
</div>