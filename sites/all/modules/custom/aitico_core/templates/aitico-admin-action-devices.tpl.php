<div id="tab-devices" class="tab-pane">
    <table class="table table-striped table-bordered table-hover" id="devicestable">
        <thead>
            <tr>
                <th> <?php print t('CST')?> </th>
                <th> <?php print t('Device') ?> </th>
                <th> <?php print t('Status') ?> </th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($devices)) {
            foreach ($devices as $key => $device):
                ?>
                <tr>
                    <td> <?php print filter_xss($device['cst']); ?> </td>
                    <td> <?php print filter_xss($device['title']); ?> </td>
                    <td <?php print isset($device['status']) ? $device['status']['cellClass'] : ''; ?>>
                        <?php print isset($device["status"]) ? $device['status']['statusIcon'] : ''; ?>
                        <?php print isset($device["status"]) ? $device['status']['statusText'] : ''; ?>
                    </td>
                </tr>
            <?php endforeach;
        } ?>

        </tbody>
    </table>
</div>

