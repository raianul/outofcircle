<?php global $nc_utility ;?>
<?php $article_settings =  get_option("nc_plugin_article_settings"); ?>
<?php $image_options = get_option("nc_plugin_image_settings"); ?>
<?php if($this->articles): ?>

<?php $index = $this->index; ?>
<?php foreach( $this->articles as $article): ?>
    <?php

        $time_old = $article->published_at;

        if($article_settings['publish_time']){
            // set publish time as  system time zone
            $hours= get_option('gmt_offset') ;
            $time_new = strtotime($time_old);
            $time_new = $time_new + (60 * 60 * $hours);
            $article->published_at =  date("Y-m-d H:i:s", $time_new);
        }
    ?>
    <li class="box-hover tooltip-from-element" data-tooltip-id="tooltip-example-<?php echo $index; ?>">

        <h2><a href="javascript:void(0);"><?php echo $article->title ?></a></h2>
        <span><?php echo $nc_utility->elapsed_time( strtotime( $time_old ) ); ?> <?php echo $tt ;?></span>
        <p class="tag"><strong>Source:- </strong><span><?php echo $article->source->name; ?></span></p>
        <div class="contenthover">
            <a class="button nc-article-title" index="<?php echo $index; ?>" href="javascript:void(0);"><img width="17" height="10" src="<?php echo NC_IMAGES_URL."/inserticons.png";?>" /> Click to Insert</a>
            <a class="button nc-article-title-removed" id="nc-article-title-removed-<?php echo $index; ?>" href="javascript:void(0);"><img width="11" height="11" src="<?php echo NC_IMAGES_URL."/remove.png";?>" /> Click to Remove</a>
            <small class="nc-hidden-category"><?php if( $article->categories ) echo implode(",", $article->categories);  ?></small>
            <p class="nc-publish-date">
                <span class="nc-mm"><?php echo date( "m", strtotime($article->published_at)); ?></span>
                <span class="nc-dd"><?php echo date( "d", strtotime($article->published_at)); ?></span>
                <span class="nc-yy"><?php echo date( "Y", strtotime($article->published_at)); ?></span>
                <span class="nc-hh"><?php echo date( "H", strtotime($article->published_at)); ?></span>
                <span class="nc-ii"><?php echo date( "i", strtotime($article->published_at)); ?></span>
                <span class="nc-author"><?php if($article_settings['article-author']== "author") echo $article->author; else echo $article->source->name; ?></span>
            </p>

            <!-- Image set markup  start !-->
            <div class="nc_hidden_image_sets">
                <?php if($article->image_set):?>
                    <?php  $imageindex=1;  ?>

                            <ul class="image-set-thumbnail-box<?php echo $index; ?> nc-image-sets">
                                <?php foreach($article->image_set as $image):?>
                                <li data-tooltip-id="tooltip<?php echo $index; ?>-thumbnail-<?php echo $imageindex; ?>" >
                                    <img class="nc-large-image" id="nc-large-image-<?php echo $index; ?>-<?php echo $imageindex; ?>" width="70" height="41" src="" url="<?php echo $image->image_large;?>" />
                                    <img class="nc-add-image-loading" id="nc-add-iamge-loading-<?php echo $index; ?>-<?php echo $imageindex; ?>" src="<?php echo NC_IMAGES_URL."/nc-loading2.gif";?>" />


                                    <div id="tooltip<?php echo $index; ?>-thumbnail-<?php echo $imageindex; ?>" class="thumbnail-inner">
                                        <div class="image-tooltip">
                                            <img height="146" width="260"  class="bimg" />
                                            <span><?php echo $nc_utility->elapsed_time( strtotime($image->published_at) ); ?>
                                                <em>W &nbsp;<input class="wh post_width" type="text" name="" default_value="<?php echo $image_options['post_img_width']; ?>" actual_value="<?php echo $image->width; ?>" value="<?php echo $image->width; ?>" title="Click to Edit"> H &nbsp;
                                                <input class="wh post_height" type="text" name="" default_value="<?php echo $image_options['post_img_height']; ?>" actual_value="<?php echo $image->height; ?>"  value="<?php echo $image->height; ?>" title="Click to Edit"></em>
                                            </span>
                                            <p class="tag"><strong>Source:-</strong><span  class="nc-image-source"><?php if($image->attribution_text) echo $image->attribution_text; else  echo $image->source->name; ?></span></p>

                                            <textarea class="captionstyle image-caption" title="Click to Edit"><?php echo $image->caption; ?></textarea>

                                            <a href="#" class="button nc-insert-to-post"><img width="17" height="10" src="<?php echo NC_IMAGES_URL."/inserticons.png";?>" /> Insert into post</a>&nbsp;&nbsp;<a href="javascript:void(0)" class="button nc-add-feature-image" index="-1" url="<?php echo $image->image_large;?>"><img width="16" height="12" src="<?php echo NC_IMAGES_URL."/setf.png";?>" /> Set as a featured</a>
                                            <div class="nc-large-image-url" large_url="<?php echo $image->image_large;?>"></div>

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
        </div>

        <div id="tooltip-example-<?php echo $index; ?>" class="tipcontent">
            <div>
                <h2><?php echo $article->title ?></h2>
                <span><?php echo $nc_utility->elapsed_time( strtotime($article->published_at) ); ?></span>
                <p class="excerpt">
                    <?php echo wp_trim_words( strip_tags( $article->description ), 50); ?>
                </p>
                <p class="tag"><strong>Source:-</strong><span><?php echo $article->source->name; ?></span></p>
            </div>
        </div>


        <div class="hidden-description">
            <?php echo  $article->description; ?>
        </div>

        <?php $tags = "" ;?>
        <?php if( $article->topics):?>
        <?php foreach($article->topics as $topic): ?>
            <?php  $tags .=  $topic->name . ","; ?>
            <?php endforeach;?>
        <?php $tags = rtrim($tags, ","); ?>
        <?php endif; ?>
        <small class="nc-tags"><?php echo $tags; ?></small>


    </li>
    <?php $index++;?>
    <?php endforeach; ?>
<?php else: ?>
    <li>No Article found</li>
<?php endif; ?>
