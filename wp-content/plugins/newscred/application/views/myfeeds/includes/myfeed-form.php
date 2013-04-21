<?php $article_settings = get_option( "nc_plugin_article_settings" );?>
<div id="col-left" class="nc-feed-left">
    <div class="col-wrap">
        <div class="form-wrap">
            <h3><?php if($this->submit_value == "Save" ) echo "Add New"; else echo "Edit"; ?> MyFeed</h3>
            <form  method="post" action="" class="nc-feed-form">

                <div class="form-field form-required">
                    <label for="name" title="Choose a name for your feed ">Name</label>
                    <input name="name" id="name" type="text" value="<?php if(isset($this->data['name'])) echo $this->data['name']; ?>" size="40" aria-required="true">
                </div>

                <div class="form-field">
                    <label for="apicall" class="nc-label">API Call</label>
                    <a class='inline' id="nc_api_create" href="#inline_content">Create new</a>
                    <textarea name="apicall" id="apicall" rows="10" cols="40"><?php if(isset($this->data['apicall'])) echo $this->data['apicall']; ?></textarea>

                </div>

                <div class="form-field">
                    <label for="myfeed-autopublish" title="Select this to automatically publish articles of your choice to your blog" class="nc-label">Auto Publish</label>
                    <input name="autopublish" id="myfeed-autopublish" type="checkbox" <?php if(isset($this->data['autopublish']) && $this->data['autopublish'] == 1 ){?> checked="" <?php } ?> value="" >
                </div>
                <div class="clear"></div>
                <div id="myfeeds-settings">
                    <div class="form-field">
                        <label for="" title="You can publish or choose to save as a draft" >Publish Status</label>
                        <input type="radio" name="publish_status" value="1"
                            <?php if(isset($this->data['publish_status'])){ if($this->data['publish_status'] == 1 ) echo 'checked=""'; }else{?> checked="" <?php } ?> /> Publish
                        <input type="radio" name="publish_status" value="0"  <?php if(isset($this->data['publish_status']) && $this->data['publish_status'] == 0) echo 'checked=""'; ?> /> Save as Draft
                    </div>
                    <div class="form-field">
                        <label title="Choose the intervals between which the articles will be published">Publish Interval</label>
                        <select name="publish_interval">
                            <?php for( $i=1;$i<=10; $i++ ) :?>
                            <?php
                            $selected = "" ;
                            if( isset( $this->data['publish_interval'] ) && $this->data['publish_interval']== $i)
                                $selected = 'selected=""';
                            ?>
                            <option <?php echo $selected; ?>  value="<?php echo $i; ?>"><?php echo $i; ?> Hour</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-field">
                        <label title="Choose the category you wish to publish under">Category</label>
                        <select data-placeholder="Select Categories" id="myfeed_category" name="myfeed_category[]" id="" multiple>
                            <?php
                            $category_list = "";
                            if( isset( $this->data['myfeed_category'] ) && $this->data['myfeed_category'] != "" )
                                $category_list = array_flip( unserialize($this->data['myfeed_category']));
                            ?>
                            <?php foreach( $this->categories as $category )  : ?>
                            <?php
                            $selected = "";
                            if($category_list && array_key_exists($category->term_id, $category_list))
                                $selected = 'selected=""';
                            ?>
                            <option <?php echo $selected; ?>  value="<?php echo $category->term_id; ?>"><?php  echo $category->cat_name; ?></option>
                            <?php endforeach; ?>
                        </select>

                        <a href="javascript:void(0)" id="myfeed-create-category">Create New</a>
                        <div class="clear"></div>
                        <div id="myfeed-category-box">
                            <input type="text" name="category" id="category" />
                            <input type="button" id="add_feed_category" value="add category" class="button" />
                            <img class="nc-category-loading" src="<?php echo NC_IMAGES_URL."/nc-loading.gif";?>" />
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="form-field">
                        <label for="feed_tag" class="nc-label" title="Preserve the tags from the original article">Keep Tags</label>
                        <input id="feed_tag" class="keeptags" type="checkbox" <?php if(isset($this->data['feed_tag'])){ if($this->data['feed_tag'] == 1 ) echo 'checked=""'; } else{ ?> checked="" <?php } ?> name="feed_tag" />
                    </div>
                    <?php /*if( $article_settings['custom-post-type']){?>
                        <div class="clear"></div>
                        <div class="form-field">
                            <label for="feed_tag" class="nc-label" title="Select post type">Post Type</label>
                            <select name="custom-post-type">
                                <option value="post" <?php if($this->data['custom_post_type'] == "post") echo 'selected=""';?>  >post</option>
                                <?php
                                    if($article_settings['custom-post-type']){
                                        $custom_posts = explode(",", $article_settings['custom-post-type']);
                                        if($custom_posts){
                                            foreach($custom_posts as $custom_post){
                                                $selected = "";
                                                if($this->data['custom_post_type'] == $custom_post)
                                                    $selected = 'selected=""';
                                                echo '<option '.$selected.'  value="'.$custom_post.'">'.$custom_post.'</option>';
                                            }
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    <?php } */?>
                </div>
                <p class="submit">
                    <input type="hidden" name="id" value="<?php if(isset($this->data['id'])) echo $this->data['id']; ?>" />
                    <input type="submit" name="submit" id="submit" class="button-primary" value="<?php echo $this->submit_value; ?>">
                </p>

            </form>
        </div>

    </div>
</div><!-- /col-left -->