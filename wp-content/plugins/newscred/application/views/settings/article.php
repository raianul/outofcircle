<div class="wrap">

    <div id="nc-editors-picks-div">
        <form  id="nc-editors-form" method="post" action="" style="display: block;" >

            <h2 class="headline">Article Search Settings <a class="nc-logo" href="http://newscred.com" target="_blank" >
                <img class="" src="<?php echo NC_IMAGES_URL."/newscred-logo.png" ?>" />
            </a></h2>

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
                        <label for="fulltext" title="Only search for full text sources">Full Text</label>
                        <div class="filter-values">
                            <div id="text_search" class="checkboxes">
                                <input type="checkbox" name="fulltext" id="fulltext" value="true" <?php  if($this->article_settings['fulltext'] == true ) echo 'checked=""'; ?>  /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label for="has_images" title="Only search for articles with images">Has Images</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" <?php  if($this->article_settings['has_images'] == true ) echo 'checked=""'; ?>  name="has_images" id="has_images" value="true" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label title="Set author or source of the original article as the WP author">Author </label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="radio" <?php if( $this->article_settings['article-author'] == "author" ) echo 'checked=""'; ?>   name="article-author" value="author" /> Author name &nbsp; &nbsp;
                                <input type="radio" <?php if( $this->article_settings['article-author'] == "source" ) echo 'checked=""'; ?>   name="article-author" value="source" /> Source name
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label for="publish_time" title="Preserves the timestamp of the original article ">Keep Publish Time </label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" <?php  if($this->article_settings['publish_time'] == true ) echo 'checked=""'; ?>  name="publish_time" id="publish_time" value="true" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label for="tags" title="Preserve the tags from the original article">Keep Tags</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" <?php  if($this->article_settings['tags'] == true ) echo 'checked=""'; ?>  name="tags" id="tags" value="true" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label for="categories" title="Preserve the category from the original article">Keep Categories</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" <?php  if($this->article_settings['categories'] == true ) echo 'checked=""'; ?>  name="categories" id="categories" value="true" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label title="Choose who would you to like to publish as">Author Role</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <select name="article-author-role" >
                                    <?php foreach($this->roles as $key=>$value): ?>
                                        <option  <?php if( $this->article_settings['article-author-role'] == $key ) echo 'selected=""'; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label for="categories" title="Preserve the category from the original article">Custom Post Type</label>
                        <div class="filter-values">
                            <div class="texts">
                                <input name="custom-post-type" value="<?php if(isset($this->article_settings['custom-post-type'])) echo $this->article_settings['custom-post-type']; ?>" type="text" size="38" />
                            </div>
                            <small>Add your custom post type slug as , separated way</small>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li class="no-border" style="border: none">
                        <input type="submit" id="nc-submit" class="button-primary nc-right" name="article_settings_submit" value="Update" />
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