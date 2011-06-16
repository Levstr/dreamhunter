<?php

/* POST TYPES -------------------------------------------------------------------------------------------------------------- */

$labels = array(
  'name' => 'Портфолио',
  'singular_name' => 'Проект',
  'add_new' => 'Добавить проект',
  'add_new_item' => 'Создание проекта',
  'edit_item' => 'Править проект',
  'new_item' => 'Новый проект',
  'view_item' => 'Посмотреть проект',
  'search_items' => 'Искать проекты',
  'not_found' =>  'Проектов не найдено',
  'not_found_in_trash' => 'Корзина пуста',
  );
register_post_type("portfolio", array('labels' => $labels, 'public' => true,
  'show_in_nav_menus' => false,
  'menu_position' => 5,
  'supports' => array('title','editor','excerpt','thumbnail','author','custom-fields')
));

/* MAIN THEME PAGE ------------------------------------------------------------------------------------------------------------- */

class _WP_TEMPLATE_control_panel{
  var $options;
  function _WP_TEMPLATE_control_panel(){
    $this->options = array(
      "_WP_TEMPLATE_contacts"=>"Контакты"
    );
    add_action('admin_menu', array(&$this, 'action_add_control_panel'));
    foreach($this->options as $k=>$v) if(!get_option($k)) add_option($k, $v);
  }
  function action_add_control_panel(){
    add_menu_page("_WP_TEMPLATE", "_WP_TEMPLATE", 10, "_WP_TEMPLATE_theme", array(&$this, "_WP_TEMPLATE_control_panel_main_page"));
  }
  function message($content){
    return '<div class="updated below-h2" id="message"><p>'.$content.'</p></div>';
  }

  function _WP_TEMPLATE_control_panel_main_page(){ ?>
  <div class="wrap">
    <div class="icon32" id="icon-options-general"><br></div>
    <h2>Настройки _WP_TEMPLATE</h2>
    <?php if($_REQUEST["action"] == "update"){
      foreach($_REQUEST as $k=>$v) if(array_key_exists($k, $this->options)) update_option($k, $v);
      echo $this->message("Изменения были <strong>сохранены</strong>.");
    } ?>
    <form action="" method="post">
      <input type="hidden" value="privacy" name="option_page">
      <input type="hidden" value="update" name="action">

      <table class="form-table">
        <tbody>
          <tr valign="top"><th scope="row">Контакты</th><td><fieldset>
            <legend class="screen-reader-text"><span>Контакты</span></legend>
            <p><label for="contacts">Поддерживается html-разметка</label></p>
            <p><textarea class="large-text code" id="_WP_TEMPLATE_contacts" cols="50" rows="10" name="_WP_TEMPLATE_contacts"><?php echo stripslashes(get_option("_WP_TEMPLATE_contacts"))?></textarea></p>
          </fieldset></td></tr>
        </tbody>
      </table>

      <p class="submit"><input type="submit" value="Сохранить изменения" class="button-primary" name="Submit"></p>
    </form>
  </div>
<?php
  }
}
$_WP_TEMPLATE_cpanel = new _WP_TEMPLATE_control_panel();

/* OTHER PAGES -------------------------------------------------------------------------------------------------------------- */



