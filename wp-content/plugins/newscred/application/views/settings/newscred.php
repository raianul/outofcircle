<div class="wrap">

    <div id="nc-editors-picks-div">
        <form  id="nc-editors-form" method="post" action="" style="display: block;" >

            <h2 class="headline"><a class="nc-logo" href="http://newscred.com" target="_blank" >
                <img class="" src="<?php echo NC_IMAGES_URL."/newscred-logo.png" ?>" />
            </a> Content Management System</h2>

            <div class="clear"></div>
            <?php if( !empty($this->message) ): ?>
            <div id="message" class="updated below-h2">
                <p><?php echo $this->message[0]; ?></p>
            </div>
            <?php endif; ?>
            <div class="clear"></div>
            <ul id="content-filters">

                <li>
                    <label for="access_key" title="NewsCred API access key">Access Key</label>
                    <div class="filter-values">
                        <input type="text" name="access_key" id="access_key" class="textbox" value="<?php echo $this->access_key; ?>" />
                    </div>
                    <div class="clear"></div>
                </li>

                <li class="no-border" style="border: none">
                    <input type="submit" name="nc-settings-submit"  id="nc-submit" value="Update" class="button-primary nc-right"  />
                    <div class="clear"></div>
                </li>


            </ul>
        </form>
    </div>
</div>
