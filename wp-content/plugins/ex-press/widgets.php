<?php

register_widget('Ex_Widget_Text');
register_widget('Ex_Widget_RecentPosts');
class Ex_Widget_Text extends WP_Widget_Text{
  function Ex_Widget_Text() {
		$widget_ops = array('classname' => 'widget_classtext', 'description' => __('Arbitrary text or HTML with class'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('classtext', __('Text with Class'), $widget_ops, $control_ops);
	}
	
	function widget( $args, $instance ){
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
		$text = apply_filters( 'widget_text', $instance['text'], $instance );

		echo preg_replace("/class=\"widget/", "class=\"widget ".$instance['class'], $before_widget);
	  echo $text;
		echo $after_widget;
	}
	
	function form( $instance ){
	  parent::form($instance);
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'class' => '' ) );
		$class = esc_attr($instance['class']);
		?>
		<p><label for="<?php echo $this->get_field_id('class'); ?>"><?php _e('CSS Class:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('class'); ?>" name="<?php echo $this->get_field_name('class'); ?>" type="text" value="<?php echo $class ?>" /></p>
  <?php
	}
	
	function update( $new_instance, $old_instance ) {
		parent::update($new_instance, $old_instance);
		$old_instance = $new_instance;
	  $old_instance['class'] = stripslashes($new_instance['class']);
	  return $old_instance;
	}
}

class Ex_Widget_RecentPosts extends WP_Widget {
  function Ex_Widget_RecentPosts(){
    $widget_ops = array('classname' => 'widget_latest_news', 'description' => __('Latest News for TrueParallel'));
		$control_ops = array('width' => 250, 'height' => 350);
		$this->WP_Widget('latest_news', __('Latest News'), $widget_ops, $control_ops);
  }
  
  function form( $instance ){
    parent::form($instance);
    $instance = wp_parse_args((array) $instance, array('title' => '', 
      'categories' => '', 
      'post_type' => 'post', 
      'number' => 3,
      'date_format' => 'm/d/Y'));
    $helper = new Widget_Helper($this, $instance);
    $helper->renderTextField('title', array('title' => 'Title:'));
    $helper->renderTextField('categories', array('title' => 'Categories to show:'));
    $helper->renderTextField('number', array('title' => 'Number of posts:'));
    $helper->renderTextField('post_type', array('title' => 'Post Type: '));
    $helper->renderCheckboxField('show_learn_more', array('title' => 'Show Learn More'));
    $helper->renderTextField('learn_more_text', array('title' => 'Learn More Text:'));
    $helper->renderCheckboxField('show_excerpts', array('title' => 'Show excerpts'));
    $helper->renderTextField('excerpts_class', array('title' => 'Excerpts class:'));
    $helper->renderCheckBoxField('show_date', array('title' => 'Show Date'));
    $helper->renderTextField('date_format', array('title' => 'Date Format'));
  }
  
  function update($new, $old){
    $res = parent::update($new, $old);
    $res['title'] = stripslashes($new['title']);
    $res['categories'] = stripslashes(preg_replace('/(\s|,|;)+/', ',', $new['categories']));
    $res['post_type'] = stripslashes($new['post_type']);
    $res['show_learn_more'] = isset($new['show_learn_more']);
    $res['learn_more_text'] = stripslashes($new['learn_more_text']);
    $res['show_excerpts'] = isset($new['show_excerpts']);
    $res['excerpts_class'] = stripslashes($new['excerpts_class']);
    $res['show_date'] = isset($new['show_date']);
    $res['date_format'] = stripslashes($new['date_format']);
    return $res;
  }
  
  function widget($args, $instance){
		echo $args['before_widget'];
		$this->render_widget_title($args, $instance);
		echo "<ul>\r\n";
		$posts = get_posts(array('post_type' => $instance['post_type'], 'numberposts' => $instance['number'], 
		  'cat' => $instance['categories']));
		$last = $posts[count($posts) - 1];
		$first = $posts[0];
		if ($instance['excerpts_class']) {
			$ex_class = 'class="'.$instance['excerpts_class'].'"';
		} else $ex_class = '';
		foreach($posts as $post){
		    if ($last->ID == $post->ID){
		      $class = 'class="last"';
		    }
		    if ($first->ID == $post->ID){
		      $class = 'class="first"';
		    }
		    echo "<li $class>";
		    if(! $instance['show_learn_more']){
		      echo('<a href="' . get_permalink($post->ID) . '">' . $post->post_title . "</a>");
		    } else echo "<span class=\"news-title\">" . $post->post_title . "</span>";
		    if ($instance['show_date']){
		      echo "<br><span class=\"post-date\">" . mysql2date($instance['date_format'], $post->post_date) . "</span><br>";
		    }
		    if ($instance['show_learn_more']){
		      $lm = '<a class="widget-learn-more" href="' . get_permalink($post->ID) . '">' . $instance['learn_more_text'] . "</a>";
		    }
				if ($instance['show_excerpts'] && $post->post_excerpt){
					echo("<p $ex_class>$post->post_excerpt $lm</p>");
				} else {
					echo $lm;
				}
		    echo "</li>";
		     
		  }
		echo("</ul>");
		echo $args['after_widget'];
  }
  
  function render_widget_title($args, $instance){
    $instance['title'] = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		if ( !empty($instance['title']) )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
  }
}

class Ex_Widget extends WP_Widget{
  
  
}



class Widget_Helper {
  var $widget;
  var $instance;
  
  function __construct($widget, $instance){
    $this->widget = $widget;
    $this->instance = $instance;
  }
  
  function renderTextField($name, $options = array()){
    $options = array_merge(array('title' => $name), $options);
    ?>
    <p><label for="<?php echo $this->widget->get_field_id($name); ?>"><?php _e($options['title']); ?></label>
		<input class="widefat" id="<?php echo $this->widget->get_field_id($name); ?>" name="<?php echo $this->widget->get_field_name($name); ?>" type="text" value="<?php echo esc_attr($this->instance[$name]); ?>" /></p>
		<?php
  }
  
  function renderCheckboxField($name, $options = array()){
    $options = array_merge(array('title' => $name), $options);
    v
    ?>
    <input class="checkbox" type="checkbox" <?php checked($this->instance[$name], true) ?> id="<?php echo $this->widget->get_field_id($name); ?>" name="<?php echo $this->widget->get_field_name($name); ?>" />
		<label for="<?php echo $this->widget->get_field_id($name); ?>"><?php _e($options['title']); ?></label><br />
		<?php
  }
  
}
?>