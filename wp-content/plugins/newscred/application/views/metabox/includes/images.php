<?php global $nc_utility ;?>
<?php $image_options = get_option("nc_plugin_image_settings"); ?>
<?php $index = $this->index;?>
<?php if($this->images): ?>
    <?php foreach( $this->images as $image): ?>
        <li data-tooltip-id="tooltip-thumbnail-<?php echo $index; ?>" <?php if($index % 3 == 0) echo "class='no-margin'";?>>
            <img class="nc-large-image" id="nc-large-image-<?php echo $index; ?>" add="1" width="70" height="41" src="<?php echo $image->image_large;?>?width=70&height=41" url="<?php echo $image->image_large;?>" />
            <img class="nc-add-image-loading" id="nc-add-iamge-loading-<?php echo $index; ?>" src="<?php echo NC_IMAGES_URL."/nc-loading2.gif";?>" />


            <div id="tooltip-thumbnail-<?php echo $index; ?>" class="thumbnail-inner" add="0">
                <div class="image-tooltip">
                    <img  width="260" height="146" class="bimg" />
                    <span><?php echo $nc_utility->elapsed_time( strtotime($image->published_at) ); ?>
                        <em>W &nbsp;<input class="wh post_width" type="text" name="" default_value="<?php echo $image_options['post_img_width']; ?>" actual_value="<?php echo $image->width; ?>" value="<?php echo $image->width; ?>" title="Click to Edit"> H &nbsp;
                            <input class="wh post_height" type="text" name="" default_value="<?php echo $image_options['post_img_height']; ?>" actual_value="<?php echo $image->height; ?>"  value="<?php echo $image->height; ?>" title="Click to Edit"></em>
                    </span>
                    <p class="tag"><strong>Source:-</strong><span class="nc-image-source"><?php if($image->attribution_text) echo $image->attribution_text; else  echo $image->source->name; ?></span></p>

                    <textarea class="captionstyle image-caption" title="Click to Edit"><?php echo $image->caption; ?></textarea>

                    <a href="#" class="button nc-insert-to-post"><img width="17" height="10" src="<?php echo NC_IMAGES_URL."/inserticons.png";?>" /> Insert into post</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="button nc-add-feature-image" index="<?php echo $index; ?>" url="<?php echo $image->image_large;?>"><img width="16" height="12" src="<?php echo NC_IMAGES_URL."/setf.png";?>" /> Set as a featured</a>
                    <div class="nc-large-image-url" large_url="<?php echo $image->image_large;?>"></div>

                </div>
            </div>
        </li>
    <?php $index++;?>
    <?php endforeach; ?>
<?php else: ?>
    <p>No image found</p>
<?php endif; ?>
