<ul class="nav ace-nav pull-right">
    <li class="light-blue user-profile">
        <a class="user-menu dropdown-toggle" href="#" data-toggle="dropdown">
            <?php if(isset($user->company_logo)): ?>
                <?php echo theme_image_style(array(
                    'style_name' => 'company-logo',
                    'path' => $user->company_logo,
                    'alt' => 'Company Logo',
                    'width' => 48,
                    'height' => 48,
                    'attributes' => array('class' => 'nav-user-photo')
                )) ?>
            <?php else: ?>
                <?php echo theme_image(array(
                    'path' => $company_logo,
                    'alt' => 'Company Logo',
                    'width' => 48,
                    'height' => 48,
                    'attributes' => array('class' => 'nav-user-photo')
                )) ?>
            <?php endif ?>
            <span id="user_info">
                <small><?php echo t('Welcome') ?>,</small> <?php echo $user->name ?>
            </span>
            <i class="icon-caret-down"></i>
        </a>
        <ul id="user_menu" class="pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-closer">
            <li> <?php echo l(t('<i class="icon-user"></i>' . t('Change Password')), "user/change-password", array("attributes" => array(), 'html' => true)); ?></li>
            <li> <?php echo l(t('<i class="icon-cog"></i>' . t('Change Language')), "user/change-language", array("attributes" => array(), 'html' => true)); ?></li>
            <li class="divider"></li>
            <li> <?php echo l(t('<i class="icon-off"></i>' . t('Logout')), "user/logout", array("attributes" => array(), 'html' => true)); ?></li>
        </ul>
    </li>
</ul>
