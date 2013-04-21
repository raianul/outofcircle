<div style='display:none'>
    <div id='inline_content' style='padding:10px; background:#fff;'>

        <div id="nc-editors-picks-div" style="width: 100%">
            <form action="" method="post" id="myfeed-api-form">
                <ul id="content-filters">

                    <li>
                        <label title="Select your categories">Categories</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" name="categories[]"   value="world" class="chkbox_category" /> World &nbsp;
                                <input type="checkbox" name="categories[]"   value="u-s" class="chkbox_category" /> U.S. &nbsp;
                                <input type="checkbox" name="categories[]"   value="u-k" class="chkbox_category" /> U.K. &nbsp;
                                <input type="checkbox" name="categories[]"   value="europe" class="chkbox_category" /> Europe &nbsp;
                                <input type="checkbox" name="categories[]"   value="asia" class="chkbox_category" /> Asia &nbsp;
                                <input type="checkbox" name="categories[]"   value="africa" class="chkbox_category" /> Africa &nbsp;
                                <input type="checkbox" name="categories[]"   value="south-america" class="chkbox_category" /> South America &nbsp;
                                <input type="checkbox" name="categories[]"   value="technology" /> Technology &nbsp;
                                <div style="margin: 10px 0;">
                                    <input type="checkbox" name="categories[]"   value="business" class="chkbox_category" /> Business &nbsp;
                                    <input type="checkbox" name="categories[]"   value="environment" /> Environment &nbsp;
                                    <input type="checkbox" name="categories[]"   value="health" /> Health &nbsp;
                                    <input type="checkbox" name="categories[]"   value="sports" /> Sports &nbsp;
                                    <input type="checkbox" name="categories[]"   value="entertainment" /> Entertainment &nbsp;
                                    <input type="checkbox" name="categories[]"  value="travel" /> Travel &nbsp;
                                    <input type="checkbox" name="categories[]"   value="lifestyle"/> LifeStyle &nbsp;
                                </div>
                                <input type="checkbox" name="categories[]"   value="science" /> Science &nbsp;
                                <input type="checkbox" name="categories[]"   value="politics" class="chkbox_category"/> Politics &nbsp;
                                <input type="checkbox" name="categories[]"   value="offbeat"/> Offbeat &nbsp;
                                <input type="checkbox" name="categories[]"   value="regional"/> Regional &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label title="Select the sources you want content from">Sources</label>
                        <div class="filter-values">
                            <select data-placeholder="Select sources" name="source_guids[]" id="source_guids" multiple>
                            </select>
                            <?php ?>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label title="Choose a source list for your content">Source List</label>
                        <div class="filter-values">
                            <?php if($this->sources): ?>
                            <select name="source_filter_name" id="source_filter_name">
                                <option value="">Select Source List</option>
                                <?php foreach($this->sources as $guid=>$name): ?>
                                <option  class="source-list" value="<?php echo $guid; ?>"><?php echo $name ?></option>
                                <?php endforeach;?>
                            </select>
                            <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                    </li>

                    <li>
                        <label title="Select the topics you want content on">Topics</label>
                        <div class="filter-values">
                            <select data-placeholder="Select topics" name="topic_guids[]" id="topic_guids" multiple>
                            </select>

                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label title="Choose a topic list for your content">Topic List</label>
                        <div class="filter-values">
                            <?php if($this->topics): ?>
                            <select name="topic_filter_name" id="topic_filter_name">
                                <option value="">Select Topic List</option>
                                <?php foreach($this->topics as $guid=>$name): ?>
                                <option  class="source-list" value="<?php echo $guid; ?>"><?php echo $name ?></option>
                                <?php endforeach;?>
                            </select>
                            <?php endif; ?>
                        </div>
                        <div class="clear"></div>
                    </li>

                    <li>
                        <label for="fulltext" title="Only search for full text sources">Full Text</label>
                        <div class="filter-values">
                            <div id="text_search" class="checkboxes">
                                <input type="checkbox" name="fulltext" id="fulltext" value="true" checked="" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li>
                        <label for="has_images" title="Only search for articles with images">Has Images</label>
                        <div class="filter-values">
                            <div class="checkboxes">
                                <input type="checkbox" checked=""  name="has_images" id="has_images" value="true" /> &nbsp;
                            </div>
                        </div>
                        <div class="clear"></div>
                    </li>
                    <li class="no-border" style="border: none;">
                        <input type="hidden" name="action" value="ncajax-create-api-call" />
                        <img class="nc-search-loading" src="<?php echo NC_IMAGES_URL."/nc-loading.gif";?>" />
                        <input type="submit" class="button-primary nc-right" name="article_settings_submit" value="Create Api Call" />
                        <div class="clear"></div>
                    </li>

                </ul>
            </form>
        </div>
    </div>
</div>