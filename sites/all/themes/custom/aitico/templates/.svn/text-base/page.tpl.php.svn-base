<?php if ($page['header']): ?>
    <?php print render($page['header']); ?>
<?php endif; ?>

<div class="navbar navbar-inverse">
    <div class="navbar-inner">
        <div class="container-fluid">

            <?php echo l('<small><i class="icon-leaf"></i>'.t('Aitico').'</small>',"",
                    array("attributes"=>array("class"=>"brand"),"html"=>true))?>

            <?php if ($page['navigation']): ?>
                <?php print render($page['navigation']) ?>
            <?php endif ?>

        </div><!--/.container-fluid-->
    </div><!--/.navbar-inner-->
</div><!--/.navbar-->

<div class="container-fluid" id="main-container">
    <div id="main-content" class="clearfix no-menu">
        <div id="page-content" class="clearfix">

            <?php echo $messages ?>
            <div style="overflow: hidden">
                <div class="loader_right">
                    <div id="loader" style="display: none">
                        <div class="ajax-progress ajax-progress-throbber">
                            <div class="throbber">
                                <div style="margin-top: -2px; color:#4189B1"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php print t("Loading");?>.. </div>                                
                            </div>                            
                        </div>                        
                    </div> 
                </div>
                <div class="title_left"> 
                    <?php echo render($title_prefix) ?>
                    <?php if ($title): ?>
                        <h3 class="title" id="page-title"><?php print $title ?></h3>
                    <?php endif ?>
                    <?php echo render($title_suffix) ?>
                </div>   
            </div>
            
            <div class="row-fluid">
                <!-- start of #main content -->
                <?php print render($page['content']) ?>
            </div><!--/row-->

        </div><!--/#page-content-->
    </div><!-- #main-content -->
</div><!--/.fluid-container#main-container-->

<?php if ($page['footer']): ?>
    <?php print render($page['footer']); ?>
<?php endif; ?>