<?php

// register ReaderWidget
add_action('widgets_init', create_function('', 'register_widget("ReaderWidget");'));
/**
 * Reader-Widget
 */
class ReaderWidget extends WP_Widget {
    /** constructor */
    function __construct() {
        parent::WP_Widget('ReaderWidget', __('Reader list', 'cvtx'), array('description' => __('Show list of published readers.', 'cvtx')));
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
                if ($file = cvtx_get_file($post, 'pdf')) {
                    echo(the_title('<li><a href="'.$file.'" title="'.__('View PDF', 'cvtx').'" class="extern">', '</a></li>'));
                }
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
        echo(' <label for="'.$this->get_field_id('title').'">'.__('Title', 'cvtx').':</label> ');
        echo(' <input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" />');
        echo('</p>');
        echo('<p>');
        echo(' <label for="'.$this->get_field_id('description').'">'.__('Description', 'cvtx').':</label>');
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
        parent::WP_Widget('CountWidget', __('Statistics', 'cvtx'), array('description' => __('List with total count of published resolutions, amendments and applications.', 'cvtx')));
    }
    
    function widget($args,$instance) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        echo($before_widget);

        if($title) echo $before_title.$title.$after_title;
        $count_antraege    = wp_count_posts('cvtx_antrag')->publish;
        $count_aeantrag    = wp_count_posts('cvtx_aeantrag')->publish;
        $count_application = wp_count_posts('cvtx_application')->publish;
        
        echo(__('Online:', 'cvtx').'<p>');
        echo(' <strong>'.$count_antraege.'</strong> <em>'.($count_antraege == 1 ? __('resolution', 'cvtx') : __('resolutions', 'cvtx')).'</em><br/>');
        echo(' <strong>'.$count_aeantrag.'</strong> <em>'.($count_aeantrag == 1 ? __('amendment', 'cvtx') : __('amendments', 'cvtx')).'</em><br/>');
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
        echo(' <label for="'.$this->get_field_id('title').'">'.__('Title', 'cvtx').':</label>');
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
        parent::WP_Widget('RSS_aeantrag_Widget', __('RSS feed for amendments', 'cvtx'), array('description' => __('Offers a link to the RSS-Feed for new amendments to specific resolution.', 'cvtx')));
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
            $rss_url = '<a href="'.$link.'">'.__('RSS feed', 'cvtx').'</a>';
            printf(__('Stay updated about %s? <p>Sign up for %s with all amendments!</p>', 'cvtx'), $post_title, $rss_url);
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
            $title = __('RSS feed', 'cvtx');
        }
        
        echo('<p>');
        echo(' <label for="'.$this->get_field_id('title').'">'.__('Title', 'cvtx').':</label>');
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
    echo(' <p><a href="plugins.php?page=cvtx-config">'.__('Settings', 'cvtx').'</a></p>');
    echo(__('<p>Questions? Get some <a href="http://cvtx-project.org/">answers</a>!', 'cvtx'));
    echo('</div>');
} 

// Hook into the 'wp_dashboard_setup' action to register our other functions
add_action('wp_dashboard_setup', 'cvtx_add_dashboard_widgets');
/**
 * Create the function use in the action hook
 */
function cvtx_add_dashboard_widgets() {
    wp_add_dashboard_widget('cvtx_dashboard_widget', __('cvtx Agenda Plugin', 'cvtx'), 'cvtx_dashboard_widget_function');
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
                $name = ($count == 1 ? __('Agenda point', 'cvtx') : __('Agenda points', 'cvtx'));
                break;
            case 'cvtx_reader':
                $name = ($count == 1 ? __('Reader', 'cvtx') : __('Readers', 'cvtx'));
                break;
            case 'cvtx_antrag':
                $name = ($count == 1 ? __('Resolution', 'cvtx') : __('Resolutions', 'cvtx'));
                break;
            case 'cvtx_aeantrag':
                $name = ($count == 1 ? __('Amendment', 'cvtx') : __('Amendments', 'cvtx'));
                break;
            case 'cvtx_application':
                $name = ($count == 1 ? __('application', 'cvtx') : __('Applications', 'cvtx'));
                break;
        }
        
        echo('<tr>');
        echo('<td class="first b b-'.$type.'"><a href="edit.php?post_type='.$type.'">'.$count.'</a></td>');
        echo('<td class="t '.$type.'"><a href="edit.php?post_type='.$type.'" '.(in_array('draft', $perms) && $count > 0 ? 'class="pending"' : '').'>'.$name.'</a></td>');
        echo('</tr>');
    }
}

?>
