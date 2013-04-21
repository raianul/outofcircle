<div class="wrap">

    <div id="nc-editors-picks-div">
        <form  id="nc-editors-form" method="post" action="" style="display: block;" >

            <h2 class="headline"><a class="nc-logo" href="http://newscred.com" target="_blank" >
                <img class="" src="<?php echo NC_IMAGES_URL."/newscred-logo.png" ?>" />
            </a> Image Search Settings</h2>

            <div class="clear"></div>
            <?php if( !empty($this->message) ): ?>
            <div id="message" class="updated below-h2">
                <p><?php echo $this->message[0]; ?></p>
            </div>
            <?php endif; ?>
            <div class="clear"></div>
            <?php if( !empty($this->access_key) ):?>
                <ul id="content-filters">

                    <li>
                        <label for="minwidth" title="Choose the minimum width for images">Min Width</label>
                        <div class="filter-values">
                            <input id="minwidth" name="minwidth" value="<?php echo $this->image_settings['minwidth']; ?>" class="textbox" />
                        </div>
                        <div class="clear"></div>
                    </li>

                    <li>
                        <label for="minheigth" title="Choose the minimum height for images">Min Height</label>
                        <div class="filter-values">
                            <input  name="minheigth" id="minheigth" class="textbox" value="<?php echo $this->image_settings['minheigth']; ?>" />
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label for="safe_search" title="Only show images that have been approved for content">Safe Images</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" name="safe_search" id="safe_search" <?php  if($this->image_settings['safe_search'] == true ) echo 'checked=""'; ?>  value="true" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>

                    <li>
                        <label for="post_img_width" title="Choose the width of the image as it should appear in the post">Image width in Post</label>
                        <div class="filter-values">
                            <input  name="post_img_width" id="post_img_width" class="textbox" value="<?php echo $this->image_settings['post_img_width']; ?>" />
                        </div>
                        <div class="clear"></div>
                    </li>

                    <li>
                        <label for="post_img_height" title="Choose the height of the image as it should appear in the post">Image height in Post</label>
                        <div class="filter-values">
                            <input  name="post_img_height" id="post_img_height"  class="textbox" value="<?php echo $this->image_settings['post_img_height']; ?>" />
                        </div>
                        <div class="clear"></div>
                    </li>

                    <li>
                        <label for="myfeeds_feature_image" title="Add MyFeeds image as Feature Image in auto publish .">Enable MyFeeds Feature Image</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" name="myfeeds_feature_image" id="myfeeds_feature_image" <?php  if($this->image_settings['myfeeds_feature_image'] == true ) echo 'checked=""'; ?>  value="true" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>

                    <li class="no-border" style="border: none">
                        <input type="submit" id="nc-submit" name="image_settings_submit" value="Update" class="button-primary nc-right" />
                        <img id="loading-img" class="hide" src="/media/img/loading-small.gif" />
                        <div class="clear"></div>
                    </li>

                </ul>
            <?php else: ?>
            <div id="message" class="updated below-h2">
                <p>Please Add Newscred  <a href="<?php echo NC_SETTINGS_URL; ?>">Access Key</a></p>
            </div>
        <?php endif; ?>

        </form>
    </div>
</div>