<?php

// register ReaderWidget
add_action('widgets_init', create_function('', 'register_widget("ReaderWidget");'));
/**
 * Reader-Widget
 */
class ReaderWidget extends WP_Widget {
    /** constructor */
    function __construct() {
        parent::WP_Widget('ReaderWidget', __('Reader-Übersicht', 'cvtx'), array('description' => __('Veröffentlichte Reader anzeigen.', 'cvtx')));
    }
    
    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        global $post;
        $post_bak = $post;
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
        wp_reset_postdata();
        $post = $post_bak;
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
        parent::WP_Widget('CountWidget', __('Antrags-Statistik', 'cvtx'), array('description' => __('Zeigt an, wie viele Anträge und Änderungsanträge bisher veröffentlicht wurden.', 'cvtx')));
    }
    
    function widget($args,$instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo $before_widget;
        if($title)
            echo $before_title.$title.$after_title;
        $count_antraege = wp_count_posts('cvtx_antrag')->publish;
        $count_aeantrag = wp_count_posts('cvtx_aeantrag')->publish;
        echo __('Es sind online:','cvtx').'<p/>';
        echo '<strong>'.$count_antraege.'</strong> <em>'.($count_antraege == 1 ? __('Antrag','cvtx') : __('Anträge','cvtx')).'</em><br/>';
        echo '<strong>'.$count_aeantrag.'</strong> <em>'.($count_aeantrag == 1 ? __('Änderungsantrag','cvtx') : __('Änderungsanträge','cvtx')).'</em><br/>';
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

// register RSS-Antrags-Widget
add_action('widgets_init', create_function('', 'register_widget("RSS_aeantrag_Widget");'));

/**
 * Count-of-posts-Widget
 */
class RSS_aeantrag_Widget extends WP_Widget {
    function __construct() {
        parent::WP_Widget('RSS_aeantrag_Widget', __('RSS-Feed zu Änderungsanträgen', 'cvtx'), array('description' => __('Bietet einen Link zum RSS-Feed für neue Änderungsanträge eines spezifischen Antrags an.', 'cvtx')));
    }
    
    function widget($args,$instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        global $post;
        if($post->post_type == 'cvtx_antrag') {
            echo $before_widget;
            if($title)
                echo $before_title.$title.$after_title;
            $post_title = '<strong>"'.get_the_title($post->ID).'"</strong>';
            $rss_url    = '<a href="'.get_feed_link('rss2').'?post_type=cvtx_aeantrag&cvtx_aeantrag_antrag='.$post->ID.'">RSS-Feed</a>';
            printf(__('Immer auf dem Laufenden über %s bleiben?<p/> Abbonier doch einfach diesen %s mit allen Änderungsanträgen!', 'cvtx'), $post_title,$rss_url);
            echo $after_widget;        
        }
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
            $title = __('RSS-Feed');
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
    echo '<p class="sub">'.__('Veröffentlicht','cvtx').'</p>';
    echo '<table><tbody>';
    cvtx_dashboard_widget_helper(array('publish'));
    echo '</tbody></table>';
    echo '</div>';
    echo '<div class="table right">';
    echo '<p class="sub">'.__('Noch nicht freigeschaltet','cvtx').'</p>';
    echo '<table><tbody>';
    cvtx_dashboard_widget_helper(array('draft', 'pending'));
    echo '</tbody></table>';
    echo '</div>';
    echo '<div class="more">';
    echo '<p><a href="plugins.php?page=cvtx-config">'.__('Settings').'</a></p>';
    echo __('<p>Fragen? Hier gibt\'s <a href="http://cvtx-project.org">Antworten</a>!</p>','cvtx');
    echo '</div>';
} 

// Hook into the 'wp_dashboard_setup' action to register our other functions
add_action('wp_dashboard_setup', 'cvtx_add_dashboard_widgets');
/**
 * Create the function use in the action hook
 */
function cvtx_add_dashboard_widgets() {
    wp_add_dashboard_widget('cvtx_dashboard_widget', __('cvtx Antragstool', 'cvtx'), 'cvtx_dashboard_widget_function');
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
            case 'cvtx_antrag': if($count == 1) $name = __('Antrag','cvtx'); else $name = __('Anträge','cvtx'); break;
            case 'cvtx_aeantrag': if($count == 1) $name = __('Änderungsantrag','cvtx'); else $name = __('Änderungsanträge','cvtx'); break;
            default: $name = "";
        }
        
        echo '<tr>';
        echo '<td class="first b b-'.$type.'"><a href="edit.php?post_type='.$type.'">'.$count.'</a></td>';
        echo '<td class="t '.$type.'"><a href="edit.php?post_type='.$type.'" '.(in_array('draft', $perms) && $count > 0 ? 'class="pending"' : '').'>'.$name.'</a></td>';
        echo '</tr>';
    }
}

?>
