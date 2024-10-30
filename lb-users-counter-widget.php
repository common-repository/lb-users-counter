<?php
/*    WIDGET    */

if (!function_exists('lb_init_widget') && !class_exists('lb_widget')) {

  add_action('widgets_init', 'lb_init_widget');

  function lb_init_widget() {
    return register_widget('lb_widget');
  }

  class lb_widget extends WP_Widget {

    function lb_widget() {
      parent::__construct('lb_widget', 'LB Users Counter Widget');
    }

    function form($instance) {

      $title = esc_attr($instance['title']);
      $flag = esc_attr($instance['flag']);

      if (esc_attr($instance['userNam'])) {
        $selected = esc_attr($instance['lb-privacy']);
        $flag = 0;
      } else {
        $selected = 0;
        $flag = 1;
      }
      ?>
      <p>
        <label for="<?php echo $this->get_field_id('title'); ?>">
          <?php _e('Titilo:&nbsp;', PLUGIN_NAME); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </label>
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('userOff'); ?>">
          <input id="<?php echo $this->get_field_id('userOff'); ?>" name="<?php echo $this->get_field_name('userOff'); ?>" type="checkbox" value="1" <?php checked($instance['userOff'], 1); ?> />
          <?php _e('Utenti Offline', PLUGIN_NAME); ?>
        </label>
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('userOn'); ?>">
          <input id="<?php echo $this->get_field_id('userOn'); ?>" name="<?php echo $this->get_field_name('userOn'); ?>" type="checkbox" value="1" <?php checked($instance['userOn'], 1); ?> />
          <?php _e('Utenti Online', PLUGIN_NAME); ?>
        </label>
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('userTot'); ?>">
          <input id="<?php echo $this->get_field_id('userTot'); ?>" name="<?php echo $this->get_field_name('userTot'); ?>" type="checkbox" value="1" <?php checked($instance['userTot'], 1); ?> />
          <?php _e('Totale Utenti', PLUGIN_NAME); ?>
        </label>
      </p>
      <p>
        <label for="<?php echo $this->get_field_id('userNam'); ?>">
          <input id="<?php echo $this->get_field_id('userNam'); ?>" name="<?php echo $this->get_field_name('userNam'); ?>" type="checkbox" value="1" onclick="handleClick(this);" <?php checked($instance['userNam'], 1); ?> />
          <?php _e('Visualizza nome utenti online', PLUGIN_NAME); ?>
        </label>
      </p>
      <p>
        <input type="hidden" name="<?php echo $this->get_field_name('flag'); ?>" id="<?php echo $this->get_field_id('flag'); ?>" value="<?php
        if ($flag)
          echo 'true';
        else
          echo 'false';
        ?>"/>
        <label>
          <?php echo __('Visualizza: ', PLUGIN_NAME); ?>
          <select id="<?php echo $this->get_field_id('lb-privacy'); ?>" name="<?php echo $this->get_field_name('lbuc-privacy'); ?>" <?php if ($flag) echo 'disabled'; ?>>
            <option value="0" <?php echo selected(0, $selected); ?>><?php _e('Solo utenti loggati', PLUGIN_NAME); ?></option>
            <option value="1" <?php echo selected(1, $selected); ?>><?php _e('Tutti gli utenti', PLUGIN_NAME); ?></option>
          </select>
        </label>
        <script type="text/javascript">
            function handleClick(cb) {
              var state = !cb.checked;
              document.getElementById("<?php echo $this->get_field_id('lb-privacy'); ?>").disabled = state;
              document.getElementById("<?php echo $this->get_field_id('flag'); ?>").value = state;
            }
        </script>
      </p>
      <?php
    }

    function update($new_instance, $old_instance) {
      return $new_instance;
    }

    function widget($args, $instance) {
      extract($args);
      echo $before_widget;
      echo $before_title . $instance['title'] . $after_title;

      $html = '<ul>';

      if ($instance['userOff'])
        $html .= '<li>' . __('Utenti Offline:', PLUGIN_NAME) . ' ' . getUserOff() . '</li>';

      if ($instance['userOn'])
        $html .= '<li>' . __('Utenti Online:', PLUGIN_NAME) . ' ' . getUserOn() . '</li>';

      if ($instance['userTot'])
        $html .= '<li>' . __('Totale Utenti:', PLUGIN_NAME) . ' ' . (getUserOn() + getUserOff()) . '</li>';

      if ($instance['userNam']) {
        $names = '<li>' . __('Nomi Utenti Online:', PLUGIN_NAME) . '</li>';
        $names .= '<li><p>' . getNameUserOn() . '<p></li>';
        if ($instance['lb-privacy']) {//all user
          $html .= $names;
        } else {//only logged
          $current_user = wp_get_current_user();
          if (0 != $current_user->ID) {
            $html .= $names;
          }
        }
      }

      $html .= '</ul>';

      echo $html;
      echo $after_widget;
    }

  }

}
?>
