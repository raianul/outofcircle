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
        <ul id="dyna" class="nc-article-list">
            <?php include(NC_VIEW_PATH."/metabox/includes/articles.php") ?>
        </ul>
        <div class="clear"></div>
        <img class="nc-page-loader" src="<?php echo NC_IMAGES_URL . "/nc-page-loader.gif";?>" alt="">
    </div>

    <!-- images tab !-->

    <div class="tab-content nc-image-results">
        <ul class="thumbnail-box">
            <?php if( $this->images ): ?>
                <?php include(NC_VIEW_PATH."/metabox/includes/images.php") ?>
            <?php else: ?>
                <p>No Image found</p>
            <?php endif; ?>
        </ul>
        <div class="clear"></div>
        <img class="nc-page-loader" src="<?php echo NC_IMAGES_URL . "/nc-page-loader.gif";?>" alt="">
    </div>

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
        </ul>
    </div>

</div>