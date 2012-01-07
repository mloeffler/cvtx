<?php

// register ReaderWidget
add_action('widgets_init', create_function('', 'register_widget("ReaderWidget");'));
/**
 * Reader-Widget
 */
class ReaderWidget extends WP_Widget {
    /** constructor */
    function __construct() {
        parent::WP_Widget('ReaderWidget', 'Reader-Übersicht', array('description' => 'Veröffentlichte Reader anzeigen'));
    }
    
    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        global $post;
        $loop = new WP_Query(array('post_type' => 'cvtx_reader',
                                   'order'     => 'ASC',
                                   'nopaging'  => true));
        if ($loop->have_posts()) {
            extract($args);
            $title = apply_filters('widget_title', $instance['title']);
            echo $before_widget;
            if ($title) {
                echo $before_title.$title.$after_title;
            }
            if (isset($instance['description']))
                echo $instance['description'].'<p/>';
            echo '<ul>';
            while ($loop->have_posts()) {
                $loop->the_post();
                echo the_title('<li><a href="'.cvtx_get_file($post, 'pdf').'" title="PDF ansehen" class="extern">', '</a></li>');
            }
            echo '</ul>';
            echo $after_widget;
        }
    }
    
    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['description'] = strip_tags($new_instance['description']);
        return $instance;
    }
    
    /** @see WP_Widget::form */
    function form($instance) {
        if ($instance) {
            $title = esc_attr($instance['title']);
            if(isset($instance['description']))
                $description = esc_attr($instance['description']);
            else
                $description = '';
        } else {
            $title = __('Antragsmappen', 'text_domain');
            $description = __('Beschreibung', 'text_domain');
        }
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:'); ?></label>
        <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $description; ?></textarea>
        </p>
        <?php 
    }

} // class ReaderWidget

// register CountWidget
add_action('widgets_init', create_function('', 'register_widget("CountWidget");'));

/**
 * Count-of-posts-Widget
 */
class CountWidget extends WP_Widget {
    function __construct() {
        parent::WP_Widget('CountWidget', 'Antrags-Statistik', array('description' => 'Zeigt an, wieviele Anträge/Änderungsanträge veröffentlicht wurden'));
    }
    
    function widget($args,$instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if($title)
            echo $before_title.$title.$after_title;
        echo 'Es sind online:<p/>';
        echo '<strong>'.wp_count_posts('cvtx_antrag')->publish.'</strong> <em>Anträge</em><br/>';
        echo '<strong>'.wp_count_posts('cvtx_aeantrag')->publish.'</strong> <em>Änderungsanträge</em>';
        echo $after_widget;
    }
    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
    
    function form($instance) {
        if($instance)
            $title = esc_attr($instance['title']);
        else
            $title = __('Statistik', 'text_domain');
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title') ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type ="text" value="<?php echo $title; ?>" />
        </p>
        <?php
    }
}

/**
 * Cvtx dashboard widget
 */
function cvtx_dashboard_widget_function() {
    // show tops
    echo '<div class="table left">';
    echo '<p class="sub">Veröffentlicht</p>';
    echo '<table><tbody>';
    cvtx_dashboard_widget_helper(array('publish'));
    echo '</tbody></table>';
    echo '</div>';
    echo '<div class="table right">';
    echo '<p class="sub">Noch nicht freigeschaltet</p>';
    echo '<table><tbody>';
    cvtx_dashboard_widget_helper(array('draft', 'pending'));
    echo '</tbody></table>';
    echo '</div>';
    echo '<div class="more">';
    echo '<p><a href="plugins.php?page=cvtx-config">Konfiguration</a></p>';
    echo '<p>Fragen? Hier gibt\'s <a href="http://cvtx.de">Antworten</a>!</p>';
    echo '</div>';
} 

// Hook into the 'wp_dashboard_setup' action to register our other functions
add_action('wp_dashboard_setup', 'cvtx_add_dashboard_widgets');
/**
 * Create the function use in the action hook
 */
function cvtx_add_dashboard_widgets() {
    wp_add_dashboard_widget('cvtx_dashboard_widget', 'cvtx Antragstool', 'cvtx_dashboard_widget_function');
}

/**
 *
 */
function cvtx_dashboard_widget_helper($perms) {
    global $cvtx_types;
    foreach($cvtx_types as $type => $value) {
        $count = 0;
        foreach ($perms as $perm) {
            $count += wp_count_posts($type)->$perm;
        }
        
        switch($type) {
            case 'cvtx_top': if($count == 1) $name = "TOP"; else $name = "TOPs"; break;
            case 'cvtx_reader': $name = "Reader"; break;
            case 'cvtx_antrag': if($count == 1) $name = "Antrag"; else $name = "Antr&auml;ge"; break;
            case 'cvtx_aeantrag': if($count == 1) $name = "&Auml;nderungsantrag"; else $name = "&Auml;nderungsantr&auml;ge"; break;
            default: $name = "";
        }
        
        echo '<tr>';
        echo '<td class="first b b-'.$type.'"><a href="edit.php?post_type='.$type.'">'.$count.'</a></td>';
        echo '<td class="t '.$type.'"><a href="edit.php?post_type='.$type.'" '.(in_array('draft', $perms) && $count > 0 ? 'class="pending"' : '').'>'.$name.'</a></td>';
        echo '</tr>';
    }
}

?>
