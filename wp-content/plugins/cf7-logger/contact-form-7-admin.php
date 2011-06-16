<?php if (!current_user_can('manage_options')) wp_die(__('You do not have sufficient permissions to access this page.')); ?>

<div id="cf7_logger_log_wrapper" class="wrap">
  <h2><?php echo $options['title']; ?></h2>

  <form id="posts-filter" class="search-box" method="get">
    <input type="hidden" name="post_type" value="<?php echo $post_type; ?>" />
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>" />
    <input type="hidden" name="post_status" value="<?php echo $_REQUEST['post_status']?>" />

      <ul class="subsubsub">
        <li><a href='edit.php?post_type=<?php echo $_REQUEST['post_type']?>&amp;page=<?php echo $_REQUEST['page']?>' class="<?php echo !isset($_REQUEST['post_status']) ? 'current' : ''?>">
          All</a> |
        </li>
        <li><a href='edit.php?post_type=<?php echo $_REQUEST['post_type']?>&amp;page=<?php echo $_REQUEST['page']?>&amp;post_status=published' class="<?php echo isset($_REQUEST['post_status']) && $_REQUEST['post_statys'] = 'publish' ? 'current' : ''?>">
          Published</a> |
        </li>
        <li><a href='edit.php?post_type=<?php echo $_REQUEST['post_type']?>&amp;page=<?php echo $_REQUEST['page']?>&amp;post_status=trash' class="<?php echo isset($_REQUEST['post_status']) && $_REQUEST['post_statys'] = 'trash' ? 'current' : ''?>">
          Trash</a>
        </li>
      </ul>
      <div class="tablenav">
        Date from <input type="text" id="date_from" name="date_from" value="<?php echo isset($_REQUEST['date_from']) ? esc_attr($_REQUEST['date_from']) : date('Y-m-d')?>" />
        to <input type="text" id="date_to" name="date_to" value="<?php echo isset($_REQUEST['date_to']) ? esc_attr($_REQUEST['date_to']): date('Y-m-d') ?>" />
        title contains <input type="text" value="<?php echo esc_attr(stripslashes($_REQUEST['title_contains']))?>" name="title_contains" id="post-search-input">
        <?php foreach($meta_keys as $meta_key) : ?>
          <?php if (!isset($options['filter_columns']) || in_array($meta_key, $options['filter_columns'])): ?>
            <select name="meta_<?php echo $meta_key; ?>">
              <option value="">Select <?php echo $meta_key?></option>
              <?php foreach(cf7_logger_select_log_meta_values($post_type, $meta_key) as $meta_value) : ?>
                <?php if (isset($meta_value['meta_value']) && !empty($meta_value['meta_value'])): ?>
                <option value="<?php echo $meta_value['meta_value']; ?>" <?php if($_GET["meta_".$meta_key] == $meta_value['meta_value']) echo 'selected="selected"'; ?>>
                  <?php echo $meta_value['meta_value']; ?>
                </option>
                <?php endif ?>
              <?php endforeach; ?>
            </select>
          <?php endif ?>
        <?php endforeach; ?>
        <input type="submit" class="button" value="Filter">
      </div>

    </div>
          <div class="clear"></div>
  </form>
  <br/>  <br/>
  <table id="cf7_logger_log" class="widefat post fixed">
    <thead>
      <tr>
        <th width="0">Title</th>
        <th width="0">Date</th>
        <?php foreach($meta_keys as $meta_key) : ?>
          <?php if ( !isset($options['show_columns']) || in_array($meta_key, $options['show_columns'])): ?>
          <th><?php echo ucfirst($meta_key); ?></th>
          <?php endif ?>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
    <?php if(count($posts) > 0) foreach($posts as $post) : ?>

      <tr class="<?php if($odd) {$odd=false; echo 'alternate ';} else {$odd=true;} ?>author-self status-publish iedit">
        <td><a href="<?php bloginfo('url'); ?>/wp-admin/post.php?post=<?php echo $post->ID; ?>&amp;action=edit"><?php echo $post->post_title; ?></a></td>
        <td nowrap="nowrap" width="0"><?php echo $post->post_date; ?></td>
        <?php foreach($meta_keys as $meta_key) : ?>

          <?php if (!isset($options['show_columns']) || in_array($meta_key, $options['show_columns'])): ?>
          <td>
            <?php echo apply_filters('log_format_column', get_post_meta($post->ID, $meta_key, true))?>
          </td>
          <?php endif ?>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <?php foreach($meta_keys as $meta_key) : ?>
          <?php if ( !isset($options['show_columns']) || in_array($meta_key, $options['show_columns'])): ?>
          <th><?php echo ucfirst($meta_key); ?></th>
          <?php endif ?>
        <?php endforeach; ?>
      </tr>
    </tfoot>
  </table>
  <br/>
  <div class="totally" style="float:right">Total: <?php echo count($posts); ?></div>

  <form name="export" method="post" action="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'] .'&cf7_log_export=1'; ?>">
    <input type="hidden" name="post_type" value='<?php echo $post_type; ?>'>
    <input class="csv button" id="submit" type="submit" value="Download CSV file" />
  </form>
</div>

<script type="text/javascript">
jQuery(function($) {
  var format = "yy-mm-dd";
  var options = {dateFormat: format};
  $("#date_from").datepicker(options);
  $("#date_to").datepicker(options);
});
</script>