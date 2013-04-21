<?php global $nc_utility ;?>
<?php $image_options = get_option("nc_plugin_image_settings"); ?>
<!-- Image set markup  start !-->
<div class="nc_hidden_image_sets">
    <?php if($this->images):?>
    <?php  $imageindex=1; $index = 0 ; ?>

    <ul class="image-set-thumbnail-box<?php echo $index; ?> nc-image-sets">
        <?php foreach($this->images as $image):?>
            <li data-tooltip-id="tooltip<?php echo $index; ?>-thumbnail-<?php echo $imageindex; ?>" >
                <img class="nc-large-image" id="nc-large-image-<?php echo $index; ?>-<?php echo $imageindex; ?>" width="70" height="41" src="<?php echo $image['url'];?>?width=70&height=41" url="<?php echo $image['url'];?>" />
                <img class="nc-add-image-loading" id="nc-add-iamge-loading-<?php echo $index; ?>-<?php echo $imageindex; ?>" src="<?php echo NC_IMAGES_URL."/nc-loading2.gif";?>" />


                <div id="tooltip<?php echo $index; ?>-thumbnail-<?php echo $imageindex; ?>" class="thumbnail-inner">
                    <div class="image-tooltip">
                        <img src="<?php echo $image['url'];?>?width=260&height=146" class="bimg" />
                                                <span><?php echo $nc_utility->elapsed_time( strtotime($image['published_at']) ); ?>
                                                    <em>W &nbsp;<input class="wh post_width" type="text" name="" default_value="<?php echo $image_options['post_img_width']; ?>" actual_value="<?php echo $image['width']; ?>" value="<?php echo $image['width']; ?>" title="Click to Edit"> H &nbsp;
                                                        <input class="wh post_height" type="text" name="" default_value="<?php echo $image_options['post_img_height']; ?>" actual_value="<?php echo $image['height']; ?>"  value="<?php echo $image['height']; ?>" title="Click to Edit"></em>
                                                </span>
                        <p class="tag"><strong>Source:-</strong><span  class="nc-image-source"><?php echo $image['source']; ?></span></p>

                        <textarea class="captionstyle image-caption" title="Click to Edit"><?php echo $image['caption']; ?></textarea>

                        <a href="#" class="button nc-insert-to-post"><img width="17" height="10" src="<?php echo NC_IMAGES_URL."/inserticons.png";?>" /> Insert into post</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="button nc-add-feature-image" index="-1" url="<?php echo $image['url'];?>"><img width="16" height="12" src="<?php echo NC_IMAGES_URL."/setf.png";?>" /> Set as a featured</a>
                        <div class="nc-large-image-url" large_url="<?php echo $image['url'];?>"></div>

                    </div>
                </div>
            </li>
        <?php $imageindex++;?>
        <?php endforeach;?>
    </ul>
    <div class="clear"></div>
    <?php else: ?>
    <p>No image set found </p>
    <?php endif; ?>
</div>
<!-- Image set markup  end  !-->