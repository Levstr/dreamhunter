<ul id="sidebar-left">
  <li class="widget text">
    <h3>Left sidebar H3</h3>
    <?php wp_nav_menu(array('echo' => true,'container' => '','menu' => 'nav-left','menu_id' => 'nav-left','depth' => 1))?>
  </li>
<?php if(!dynamic_sidebar('left')) { ?>
<?php } ?>
</ul>