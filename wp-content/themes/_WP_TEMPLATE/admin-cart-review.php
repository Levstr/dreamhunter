<?php
	/*$post_ids = isset($_REQUEST['post']) ? array_map( 'intval', (array) $_REQUEST['post'] ) : explode(',', $_REQUEST['ids']);
	$doaction = $_REQUEST['action'];
	switch ( $doaction ) {
		case 'trash':
			$trashed = 0;
			foreach( (array) $post_ids as $post_id ) {
				if ( !wp_trash_post($post_id) )
					wp_die( __('Error in moving to Trash.') );
				$trashed++;
			}
			$sendback = add_query_arg( array('trashed' => $trashed, 'ids' => join(',', $post_ids)), $sendback );
			break;
		case 'publish':
			$published = 0;
			foreach( (array) $post_ids as $post_id ) {
				wp_publish_post($post_id);
				$published++;
			}
			$sendback = add_query_arg( array('published' => $published, 'ids' => join(',', $post_ids)), $sendback );
			break;
	}*/
?>

<div class="wrap">
  <h2>Заказы на оформлении</h2>
  <form id="ideas-filter" action="" method="post">
    <!--div class="tablenav"><div class="alignleft actions">
      <select name="action">
        <option value="publish" selected="selected"><?php _e('Approve'); ?></option>
        <option value="trash"><?php _e('Move to Trash'); ?></option>
      </select>
      <input type="submit" value="<?php esc_attr_e('Apply'); ?>" name="doaction" id="doaction" class="button-secondary action" />
      <br class="clear" />
    </div></div-->

    <table class="widefat post fixed">
      <thead>
        <tr scope="col">
          <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
          <th scope="col" id="title" class="manage-column column-title" style="">Заголовок</th>
          <th scope="col" id="text" class="manage-column column-content" style="">Список туров</th>
          <th scope="col" id="date" class="manage-column column-date" style="">Дата</th>
          <th scope="col" id="author" class="manage-column column-author" style="">Покупатель</th>
        </tr>
      </thead>
      <tfoot>
        <tr scope="col">
          <th scope="col" id="cb" class="manage-column column-cb check-column" style=""><input type="checkbox"></th>
          <th scope="col" id="title" class="manage-column column-title" style="">Заголовок</th>
          <th scope="col" id="text" class="manage-column column-content" style="">Список туров</th>
          <th scope="col" id="date" class="manage-column column-date" style="">Дата</th>
          <th scope="col" id="author" class="manage-column column-author" style="">Покупатель</th>
        </tr>
      </tfoot>
      <tbody>
        <?php $i=0; $orders=get_posts(array('post_type'=>'cart','cart_status'=>'review','numberposts'=>-1,'orderby'=>'date','order'=>'DESC'));
        if(count($orders)): ?>
          <?php foreach($orders as $order): ?>
            <tr>
              <th class="check-column" scope="row">
                <input type="checkbox" value="<?php echo $order->ID?>" name="post[]">
              </th>
              <td class="post-title column-title">
                <a href="<?php echo admin_url('post.php')."?action=edit&post=$order->ID"?>"><?php echo $order->post_title?></a>
                <p><?php echo $order->post_content?></p>
              </td>
              <td class="date column-content">
                <?php //echo get_post_meta LinkToTour?>
                <?php //echo get_post_meta TourInfoHTML?>
              </td>
              <td class="date column-date">
                <?php echo $order->post_date?>
              </td>
              <td class="date column-author">
				<?php $author_arr = get_userdata($order->post_author)?>
                <a href="<?php echo admin_url('user-edit.php')."?user_id=$order->author_id"?>"><?php echo $author_arr->user_login?></a>
				<br/><a href="mailto:<?php echo $author_arr->user_email?>"><?php echo $author_arr->user_email?></a>
				<br/><?php echo get_cimyFieldValue($order->post_author, 'PHONE')?>
              </td>
            </tr>
          <?php endforeach?>
        <?php else: ?>
          <tr><td colspan="5"><strong>No pending orders yet</strong></td></tr>
        <?php endif ?>
      </tbody>
    </table>
  </form>
</div>