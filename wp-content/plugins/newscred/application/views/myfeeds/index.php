<div class="wrap nosubsub">

    <?php if( !empty($this->access_key) ):?>

        <div id="col-container">

            <h2 class="headline">MyFeeds Settings 
                <a class="nc-logo" href="http://newscred.com" target="_blank" >
                    <img class="" src="<?php echo NC_IMAGES_URL."/newscred-logo.png" ?>" />
                </a>
            </h2>

            <?php if( !empty($this->message) ): ?>
                <?php foreach( $this->message as $message ): ?>
                    <div id="message" class="updated below-h2">
                        <p><?php echo $message; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <!-- myfeed  form -->
            <?php include(NC_VIEW_PATH."/myfeeds/includes/myfeed-form.php") ?>

            <!-- myfeed  list -->
            <?php include(NC_VIEW_PATH."/myfeeds/includes/myfeed-list.php") ?>

        </div>
    <?php else: ?>
        <div id="message" class="updated below-h2">
            <p>Please Add Newscred  <a href="<?php echo NC_SETTINGS_URL; ?>">Access Key</a></p>
        </div>
    <?php endif;?>
</div>

<!-- Create New API Call form -->
<?php include(NC_VIEW_PATH."/myfeeds/includes/api-form.php") ?>