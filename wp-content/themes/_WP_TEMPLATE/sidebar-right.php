<ul id="sidebar-right">
  <li class="widget text">
    <h3>Right sidebar H3</h3>
    <?php wp_nav_menu(array('echo' => true,'container' => '','menu' => 'nav-right','menu_id' => 'nav-right','depth' => 1))?>
  </li>
<?php if(!dynamic_sidebar('right')) { ?>
<?php } ?>
</ul>