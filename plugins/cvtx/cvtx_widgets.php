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
            echo($before_widget);
            if ($title) {
                echo $before_title.$title.$after_title;
            }
            if (isset($instance['description'])) {
                echo($instance['description'].'<p/>');
            }
            echo('<ul>');
            while ($loop->have_posts()) {
                $loop->the_post();
                echo(the_title('<li><a href="'.cvtx_get_file($post, 'pdf').'" title="'.__('View PDF', 'cvtx').'" class="extern">', '</a></li>'));
            }
            echo('</ul>');
            echo($after_widget);
        }
        wp_reset_postdata();
        $post = $post_bak;
    }
    
    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
        $instance                = $old_instance;
        $instance['title']       = strip_tags($new_instance['title']);
        $instance['description'] = strip_tags($new_instance['description']);
        return $instance;
    }
    
    /** @see WP_Widget::form */
    function form($instance) {
        if ($instance) {
            $title = esc_attr($instance['title']);
            if (isset($instance['description'])) {
                $description = esc_attr($instance['description']);
            } else {
                $description = '';
            }
        } else {
            $title       = __('Reader', 'cvtx');
            $description = __('Description', 'cvtx');
        }
        
        echo('<p>');
        echo(' <label for="'.$this->get_field_id('title').'">'.__('Title:').'</label> ');
        echo(' <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />');
        echo('</p>');
        echo('<p>');
        echo(' <label for="'.$this->get_field_id('description').'">'.__('Description:').'</label>');
        echo(' <textarea class="widefat" id="'.$this->get_field_id('description').'" name="'.$this->get_field_name('description').'">'.$description.'</textarea>');
        echo('</p>');
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
        echo($before_widget);

        if($title) echo $before_title.$title.$after_title;
        $count_antraege    = wp_count_posts('cvtx_antrag')->publish;
        $count_aeantrag    = wp_count_posts('cvtx_aeantrag')->publish;
        $count_application = wp_count_posts('cvtx_application')->publish;
        
        echo(__('Es sind online:', 'cvtx').'<p>');
        echo(' <strong>'.$count_antraege.'</strong> <em>'.($count_antraege == 1 ? __('Antrag', 'cvtx') : __('Anträge', 'cvtx')).'</em><br/>');
        echo(' <strong>'.$count_aeantrag.'</strong> <em>'.($count_aeantrag == 1 ? __('Änderungsantrag', 'cvtx') : __('Änderungsanträge', 'cvtx')).'</em><br/>');
        echo(' <strong>'.$count_application.'</strong> <em>'.($count_application == 1 ? __('application', 'cvtx') : __('applications', 'cvtx')).'</em><br/>');
        echo('</p>');
        echo($after_widget);
    }
    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
    
    function form($instance) {
        if($instance) {
            $title = esc_attr($instance['title']);
        } else {
            $title = __('Statistics', 'cvtx');
        }
        
        echo('<p>');
        echo(' <label for="'.$this->get_field_id('title').'">'.__('Title:').'</label>');
        echo(' <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />');
        echo('</p>');
    }
}

// register RSS-Antrags-Widget
add_action('widgets_init', create_function('', 'register_widget("RSS_aeantrag_Widget");'));

/**
 * RSS-Antrags-Widget
 */
class RSS_aeantrag_Widget extends WP_Widget {
    function __construct() {
        parent::WP_Widget('RSS_aeantrag_Widget', __('RSS-Feed zu Änderungsanträgen', 'cvtx'), array('description' => __('Bietet einen Link zum RSS-Feed für neue Änderungsanträge eines spezifischen Antrags an.', 'cvtx')));
    }
    
    function widget($args, $instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        global $post;
        if (isset($post) && $post->post_type == 'cvtx_antrag') {
            echo($before_widget);
            if($title) echo($before_title.$title.$after_title);
            $post_title = '<strong>"'.get_the_title($post->ID).'"</strong>';
			$link = add_query_arg(array('post_type'            => 'cvtx_aeantrag',
                                        'cvtx_aeantrag_antrag' => $post->ID),
                                  get_feed_link('rss2'));
            $rss_url = '<a href="'.$link.'">'.__('RSS-Feed', 'cvtx').'</a>';
            printf(__('Immer auf dem Laufenden über %s bleiben? <p>Abbonier doch einfach diesen %s mit allen Änderungsanträgen!</p>', 'cvtx'), $post_title,$rss_url);
            echo($after_widget);
        }
    }
    
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
    
    function form($instance) {
        if ($instance) {
            $title = esc_attr($instance['title']);
        } else {
            $title = __('RSS-Feed');
        }
        
        echo('<p>');
        echo(' <label for="'.$this->get_field_id('title').'">'.__('Title:').'</label>');
        echo(' <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />');
        echo('</p>');
    }
}

/**
 * Cvtx dashboard widget
 */
function cvtx_dashboard_widget_function() {
    // show tops
    echo('<div class="table left">');
    echo(' <p class="sub">'.__('Published', 'cvtx').'</p>');
    echo(' <table><tbody>');
    cvtx_dashboard_widget_helper(array('publish'));
    echo(' </tbody></table>');
    echo('</div>');
    echo('<div class="table right">');
    echo(' <p class="sub">'.__('Pending', 'cvtx').'</p>');
    echo(' <table><tbody>');
    cvtx_dashboard_widget_helper(array('draft', 'pending'));
    echo(' </tbody></table>');
    echo('</div>');
    echo('<div class="more">');
    echo(' <p><a href="plugins.php?page=cvtx-config">'.__('Settings').'</a></p>');
    echo(__('<p>Fragen? Hier gibt\'s <a href="http://cvtx-project.org">Antworten</a>!</p>','cvtx'));
    echo('</div>');
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
    foreach (array_keys($cvtx_types) as $type) {
        $count = 0;
        foreach ($perms as $perm) {
            $count += wp_count_posts($type)->$perm;
        }
        
        switch($type) {
            case 'cvtx_top':
                $name = ($count == 1 ? __('TOP', 'cvtx') : __('TOPs', 'cvtx'));
                break;
            case 'cvtx_reader':
                $name = ($count == 1 ? __('Reader', 'cvtx') : __('Reader', 'cvtx'));
                break;
            case 'cvtx_antrag':
                $name = ($count == 1 ? __('Antrag', 'cvtx') : __('Anträge', 'cvtx'));
                break;
            case 'cvtx_aeantrag':
                $name = ($count == 1 ? __('Änderungsantrag', 'cvtx') : __('Änderungsanträge', 'cvtx'));
                break;
            case 'cvtx_application':
                $name = ($count == 1 ? __('application', 'cvtx') : __('applications', 'cvtx'));
                break;
        }
        
        echo('<tr>');
        echo('<td class="first b b-'.$type.'"><a href="edit.php?post_type='.$type.'">'.$count.'</a></td>');
        echo('<td class="t '.$type.'"><a href="edit.php?post_type='.$type.'" '.(in_array('draft', $perms) && $count > 0 ? 'class="pending"' : '').'>'.$name.'</a></td>');
        echo('</tr>');
    }
}

?>
