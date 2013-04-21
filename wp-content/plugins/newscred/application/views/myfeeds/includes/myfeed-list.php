<div id="col-right" class="nc-feed-right">
    <div class="col-wrap">
        <?php if( $this->myFeedList ): ?>
        <form id="posts-filter" action="" method="post">
            <div class="tablenav top">

                <div class="alignleft actions">
                    <select name="action">
                        <option value="-1" selected="selected">Bulk Actions</option>
                        <option value="delete">Delete</option>
                    </select>
                    <input type="submit" name="" id="doaction" class="button-secondary action" value="Apply">
                </div>
                <div class="tablenav-pages one-page">
                    <span class="displaying-num"><?php echo $this->num_rows;?> items</span>
                    <?php echo $this->app_pagin;?>
                </div>
                <br class="clear">
            </div>
            <table class="wp-list-table widefat fixed tags" cellspacing="0">
                <thead>
                <tr>
                    <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
                    <th scope="col" >
                        <span>Name</span><span class="sorting-indicator"></span>
                    </th>
                    <th scope="col" id="description">
                        <span>Auto Published</span>
                        <span class="sorting-indicator"></span>
                    </th>

                    <th scope="col">
                        <span>Publish Interval</span>
                        <span class="sorting-indicator"></span>
                    </th>

                </tr>
                </thead>

                <tfoot>
                <tr>
                    <th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
                    <th scope="col"  style="">
                        <span>Name</span><span class="sorting-indicator"></span>
                    </th>
                    <th scope="col" style="">
                        <span>Auto Published</span><span class="sorting-indicator"></span>
                    </th>
                    <th scope="col" style="">
                        <span>Publish Interval</span><span class="sorting-indicator"></span>
                    </th>

                </tr>
                </tfoot>

                <tbody id="the-list" class="list:tag">

                    <?php foreach($this->myFeedList as $myfeed ): ?>
                <tr id="tag-245" class="alternate">
                    <th scope="row" class="check-column">
                        <input type="checkbox" name="delete_feeds[]" value="<?php echo $myfeed->id;?>">
                    </th>
                    <td class="name column-name">
                        <strong>
                            <a class="row-title" href="<?php echo NC_MYFEEDS_URL; ?>&amp;action=edit&amp;id=<?php echo $myfeed->id;?>" title="Edit “Business”"><?php echo $myfeed->name; ?></a>
                        </strong>
                        <br>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo NC_MYFEEDS_URL; ?>&amp;action=edit&amp;id=<?php echo $myfeed->id;?>">Edit</a> |
                            </span>
                            <span class="delete">
                                <a class="delete-tag" href="<?php echo NC_MYFEEDS_URL; ?>&amp;action=delete&amp;id=<?php echo $myfeed->id;?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </span>
                        </div>

                    </td>
                    <td class="description column-description">
                        <?php if($myfeed->autopublish): ?>
                        Yes
                        <?php else: ?>
                        No
                        <?php endif;?>
                    </td>
                    <td class="description column-description">
                        <?php  if($myfeed->autopublish && $myfeed->publish_interval): ?>
                        <?php echo $myfeed->publish_interval; ?> hour

                        <?php endif;?>
                    </td>

                </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
            <div class="tablenav bottom">

                <div class="alignleft actions">
                    <select name="action2">
                        <option value="-1" selected="selected">Bulk Actions</option>
                        <option value="delete">Delete</option>
                    </select>
                    <input type="submit" name="" id="doaction2" class="button-secondary action" value="Apply">
                </div>

                <div class="tablenav-pages one-page">
                    <span class="displaying-num"><?php echo $this->num_rows;?> items</span>
                    <?php echo $this->app_pagin;?>
                </div>

                <br class="clear">
            </div>

            <br class="clear">
        </form>
        <?php endif; ?>
    </div>
</div><!-- /col-right -->