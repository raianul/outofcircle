<?php if( !empty($this->access_key) ):?>
    <!-- Newscred Search form  start !-->
    <div class="form">
        <input class="searchfiled" autocomplete="off" type="text"  name="nc-search" id="nc-search" placeholder="enter keyword..." style="width: 160px" />
        <img class="nc-search-loading" src="<?php echo NC_IMAGES_URL."/nc-loading.gif";?>" />
        <input type="button" name="nc-search-submit" id="nc-search-submit"   value="Search" class="button tagadd" />
    </div>
    <!-- Newscred Search form  end !-->

    <!-- autosuggestion  start !-->
    <div class="auto-suggest-holder">
        <div class="clear"></div>
        <div class="nc-fliter-tag"></div>
    </div>
    <!-- autosuggestion  end !-->

    <!-- sort by start !-->
    <div class="radioBtns">
        <span class="radioYes">
            <input id="relevance" type="radio" name="sort" checked="checked" value="relevance" /><label class="sort-label" for="relevance">Sort By Relevance</label>
        </span>
        <span class="radioNo">
            <input id="date" type="radio" name="sort" value="date" /><label class="sort-label" for="date">Sort By Date</label>
        </span>
    </div>
    <!-- sort by end !-->
    <div id="nc-message-box" style="clear: both">
    </div>
    <!-- results tab start  !-->
    <div id="metabox-search-result">

        <div class="clear"></div>
        <ul class="tabs">
            <li><a tab="1" href="#">Articles</a></li>
            <li><a tab="2" href="#">Images</a></li>
            <li><a tab="3" href="#">MyFeeds</a></li>
        </ul>

        <!-- tab "panes" -->
        <div class="panes">
            <!-- articles tab !-->
            <div class="tab-content nc-aritcle-results">
                <p>Perform a search to see article results</p>
                <ul id="dyna" class="nc-article-list">

                </ul>
                <div class="clear"></div>
                <img class="nc-page-loader" src="<?php echo NC_IMAGES_URL . "/nc-page-loader.gif";?>" alt="">
            </div>
            <!-- images tab !-->
            <div class="tab-content nc-image-results">
                <ul class="thumbnail-box">
                    <p>Perform a search to see image results</p>
                </ul>
                <div class="clear"></div>
                <img class="nc-page-loader" src="<?php echo NC_IMAGES_URL . "/nc-page-loader.gif";?>" alt="">
            </div>
            <!-- myFeeds tab !-->
            <div class="tab-content nc-myfeeds-results">
                <?php if($this->myFeeds):?>
                <select name="myFeeds" id="myFeeds">
                    <option value="">Select Myfeeds </option>
                    <?php foreach($this->myFeeds as $myFeed ): ?>
                    <option value="<?php echo $myFeed->id; ?>"><?php echo $myFeed->name; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
                <ul id="myFeeds-content" class="nc-feed-list">
                    <li><p>Please enter a search query and select a MyFeeds to see myFeeds results. An empty search will return the latest articles</p></li>
                </ul>
                <div class="clear"></div>
                <img class="nc-page-loader" src="<?php echo NC_IMAGES_URL . "/nc-page-loader.gif";?>" alt="">
            </div>
        </div>

    </div>
    <!-- results tab end  !-->

<?php else: ?>
    <div id="message" class="updated below-h2">
        <p>Please Add Newscred  <a href="<?php echo NC_SETTINGS_URL; ?>">Access Key</a></p>
    </div>
<?php endif ?>
<div class="copyright">
    <p>
        Powered by <a href="http://www.newscred.com/" class="company">NewsCred Inc.</a> | <a href="#">Terms of Use</a> | <a href="#">FAQ</a>
    </p>
</div>
<input type="hidden" name="nc-selected-category"  id="nc-selected-category" value=""  />
<input type="hidden" name="nc-post-author"  id="nc-post-author" value=""  />
<input type="hidden" name="nc-current-tab"  id="nc-current-tab" value=""  />
<input type="hidden" name="nc-add-post"  id="nc-add-post" value=""  />

<input type="hidden" name="nc_publish_time"  id="nc_publish_time" value="<?php echo $this->article_settings['publish_time'] ?>"  />
<input type="hidden" name="nc_tags"  id="nc_tags" value="<?php echo $this->article_settings['tags'] ?>"  />
<input type="hidden" name="nc_categories"  id="nc_categories" value="<?php echo $this->article_settings['categories'] ?>"  />
<input type="hidden" name="nc_categories"  id="nc_categories" value="<?php echo $this->article_settings['categories'] ?>"  />

<!-- search auto suggestions  !-->
<div class="auto-suggest-popup">
    <a class="topic_remove close-autosuggestion" href="javascript:void(0);" title="Close"></a>
    <dl></dl>
</div>

<!-- images set div   !-->
<div id="nc-image-set-content">
    <div class="handlediv" title="Click to toggle"><br></div>
    <h3 class="hndle image_set_heading"><img src="<?php echo NC_IMAGES_URL."/newscred-logo.png";?>" alt=""/>Attached Images
        <img class="nc-image-set-loading" src="<?php echo NC_IMAGES_URL."/nc-loading2.gif";?>" />
    </h3>
    <div class="inside">
    </div>
</div>
