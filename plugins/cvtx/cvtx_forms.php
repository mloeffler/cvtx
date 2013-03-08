<?php
/**
 * @package cvtx
 */


add_filter('mce_buttons', 'cvtx_mce_manage_buttons');
/**
 * Restrict first button row of the rich text editor
 *
 * @todo include 'formatselect'
 *
 * @param array $buttons rich edit buttons that are enabled
 */
function cvtx_mce_manage_buttons($buttons) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        return array('bold', 'italic', 'underline', 'strikethrough', 'ins', '|', 'bullist', 'numlist', '|', 'undo', 'redo', 'html', '|', 'formatselect');
    } else {
        return $buttons;
    }
}


add_filter('mce_buttons_2', 'cvtx_mce_manage_buttons_2');
/**
 * Restrict second button row of the rich text editor
 */
function cvtx_mce_manage_buttons_2($buttons) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        return array();
    } else {
        return $buttons;
    }
}


add_filter('tiny_mce_before_init', 'cvtx_mce_before_init');
/**
 * Restrict blockformats of the rich text editor
 */
function cvtx_mce_before_init($settings) {
    global $post;
    if ((isset($_REQUEST['post_type']) && ($_REQUEST['post_type'] == 'cvtx_antrag' || $_REQUEST['post_type'] == 'cvtx_aeantrag'))
     || (isset($post) && isset($post->post_type) && ($post->post_type == 'cvtx_antrag' || $post->post_type == 'cvtx_aeantrag'))) {
        $settings['theme_advanced_blockformats'] = __('Subsection', 'cvtx').'=h3; '.__('Subsubsection', 'cvtx').'=h4';
    }
    return $settings;
}


/**
 * Settings for the rich text editors
 */
function cvtx_tinymce_settings() {
    return array('theme_advanced_buttons1' => 'bold, italic, underline, strikethrough, ins, bullist, numlist, undo, redo, code, formatselect, fullscreen');
}


/**
 * Creates formular for creating antraege
 *
 * @param int $cvtx_antrag_top top of antrag if it has already been submitted
 * @param string $cvtx_antrag_title title if it has been already submitted
 * @param string $cvtx_antrag_text text of antrag if it has already been submitted
 * @param string $cvtx_antrag_steller antrag_steller if it have been already submitted
 * @param string $cvtx_antrag_email contact address if it has already been submitted
 * @param string $cvtx_antrag_phone phone number if it has already been submitted
 * @param string $cvtx_antrag_grund antragsbegruendung, if already submitted
 */
function cvtx_create_antrag_form($cvtx_antrag_top = 0,  $cvtx_antrag_title = '', $cvtx_antrag_text = '', $cvtx_antrag_steller = '', $cvtx_antrag_email = '', $cvtx_antrag_phone = '', $cvtx_antrag_grund = '', $show_recaptcha = true) {
    ?>
    <form id="create_antrag_form" class="cvtx_antrag_form cvtx_form" method="post" action="">
     <?php echo(wp_nonce_field('cvtx_form_create_antrag', 'cvtx_form_create_antrag_submitted')); ?>
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Title', 'cvtx'); ?></h3></div>
      <div class="form-item">
       <label for="cvtx_antrag_title"><?php _e('Title', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><br/>
       <input type="text" id="cvtx_antrag_title" name="cvtx_antrag_title" class="required" value="<?php echo($cvtx_antrag_title); ?>" size="80" /><br />
      </div>
      <div class="form-item">
       <label for="cvtx_antrag_top"><?php _e('Agenda point', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><br />
       <?php echo(cvtx_dropdown_tops($cvtx_antrag_top, __('No agenda created', 'cvtx'))); ?><br />
      </div><br/>
     </fieldset>
     
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Author(s)', 'cvtx'); ?></h3></div>
      <div class="form-item">
       <label for="cvtx_antrag_steller"><?php _e('Author(s)', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><br/>
       <textarea id="cvtx_antrag_steller" name="cvtx_antrag_steller" class="required" size="100%" cols="60" rows="5" /><?php echo($cvtx_antrag_steller); ?></textarea><br/>
      </div>
      <div class="form-item">
       <label for="cvtx_antrag_email"><?php _e('E-mail address', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label> (<?php _e('will not be published', 'cvtx'); ?>)<br/>
       <input type="text" id="cvtx_antrag_email" name="cvtx_antrag_email" class="required" value="<?php echo($cvtx_antrag_email); ?>" size="70" /><br/>
      </div>
      <div class="form-item">
       <label for="cvtx_antrag_phone"><?php _e('Mobile number', 'cvtx'); ?>: <?php echo (get_option('cvtx_phone_required', true) == true ? '<span class="form-required" title="'.__('This field is mandatory', 'cvtx').'">*</span></label> ('.__('will not be published', 'cvtx').')' : '</label>'); ?> <br/>
       <input type="text" id="cvtx_antrag_phone" name="cvtx_antrag_phone" <?php echo (get_option('cvtx_phone_required', true) ? 'class="required"' : ''); ?> value="<?php echo($cvtx_antrag_phone); ?>" size="70" /><br/>
      </div>
      <?php
      $cvtx_privacy_message = get_option('cvtx_privacy_message');
      if (!empty($cvtx_privacy_message)) { ?>
          <p class="form-item"><small><?php echo($cvtx_privacy_message); ?></small></p>
      <?php } ?>
      <br />
     </fieldset>
     
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Text', 'cvtx'); ?></h3></div>
      <div class="form-item">
       <label for="cvtx_antrag_text"><?php _e('Text', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><br/>
       <?php
       if (is_plugin_active('html-purified/html-purified.php')) {
           wp_editor($cvtx_antrag_text, 'cvtx_antrag_text', array('media_buttons' => false,
                                                                  'textarea_name' => 'cvtx_antrag_text',
                                                                  'tinymce'       => cvtx_tinymce_settings(),
                                                                  'quicktags'     => false,
                                                                  'teeny'         => false));
       } else { ?>
           <textarea id="cvtx_antrag_text" name="cvtx_antrag_text" class="required tinymce_data" size="100%" cols="60" rows="20" /><?php echo($cvtx_antrag_text); ?></textarea><br/>
       <?php } ?>
      </div>
      <div class="form-item">
       <label for="cvtx_antrag_grund"><?php _e('Explanation', 'cvtx'); ?>:</label><br/>
       <?php
       if (is_plugin_active('html-purified/html-purified.php')) {
           wp_editor($cvtx_antrag_grund, 'cvtx_antrag_grund', array('media_buttons' => false,
                                                                    'textarea_name' => 'cvtx_antrag_grund',
                                                                    'tinymce'       => cvtx_tinymce_settings(),
                                                                    'quicktags'     => false,
                                                                    'teeny'         => false));
       } else { ?>
       <textarea id="cvtx_antrag_grund" name="cvtx_antrag_grund" size="100%" cols="60" rows="10" /><?php echo($cvtx_antrag_grund); ?></textarea><br/>
       <?php } ?>
      </div><br/>
     </fieldset>
     
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Submit', 'cvtx'); ?></h3></div>
      <?php
      // embed reCaptcha
      if (is_plugin_active('wp-recaptcha/wp-recaptcha.php') && $show_recaptcha) {
              $ropt = get_option('recaptcha_options');
      ?>
          <div class="form-item">
           <?php echo(recaptcha_get_html($ropt['public_key'])); ?>
          </div>
      <?php } ?>
      <div class="form-item">
       <input type="submit" id="cvtx_antrag_submit" class="submit" value="<?php _e('Submit resolution', 'cvtx'); ?>">
      </div></br>
     </fieldset>
    </form>
    <?php
}


/**
 * Method which evaluates input of antrags-creation form and saves it to the wordpress database
 */
function cvtx_submit_antrag($show_recaptcha = true) {
    // Request Variables, if already submitted, set corresponding variables to '' else
    $cvtx_antrag_title   = (!empty($_POST['cvtx_antrag_title'])   ? trim($_POST['cvtx_antrag_title'])   : '');
    $cvtx_antrag_steller = (!empty($_POST['cvtx_antrag_steller']) ? trim($_POST['cvtx_antrag_steller']) : '');
    $cvtx_antrag_email   = (!empty($_POST['cvtx_antrag_email'])   ? trim($_POST['cvtx_antrag_email'])   : '');
    $cvtx_antrag_phone   = (!empty($_POST['cvtx_antrag_phone'])   ? trim($_POST['cvtx_antrag_phone'])   : '');
    $cvtx_antrag_top     = (!empty($_POST['cvtx_antrag_top'])     ? trim($_POST['cvtx_antrag_top'])     : '');
    $cvtx_antrag_text    = (!empty($_POST['cvtx_antrag_text'])    ? trim($_POST['cvtx_antrag_text'])    : '');
    $cvtx_antrag_grund   = (!empty($_POST['cvtx_antrag_grund'])   ? trim($_POST['cvtx_antrag_grund'])   : '');

    // Check whether the form has been submitted and the wp_nonce for security reasons
    if (isset($_POST['cvtx_form_create_antrag_submitted'] ) && wp_verify_nonce($_POST['cvtx_form_create_antrag_submitted'], 'cvtx_form_create_antrag') ){
        if (is_plugin_active('wp-recaptcha/wp-recaptcha.php')) {
            $ropt = get_option('recaptcha_options');
            $resp = recaptcha_check_answer($ropt['private_key'],
                                           $_SERVER['REMOTE_ADDR'],
                                           $_POST['recaptcha_challenge_field'],
                                           $_POST['recaptcha_response_field']);
            if (!$resp->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                echo('<p id="message" class="error">'.__('Wrong captcha. Please try again.', 'cvtx').'</p>');
            }
        }
        if (!is_plugin_active('wp-recaptcha/wp-recaptcha.php') || $resp->is_valid) {
            // check whether the required fields have been submitted
            if(!empty($cvtx_antrag_title) && !empty($cvtx_antrag_text) && !empty($cvtx_antrag_steller) && !empty($cvtx_antrag_email)) {
                // Apply content filters
                $cvtx_antrag_text  = apply_filters('the_content', $cvtx_antrag_text);
                $cvtx_antrag_grund = apply_filters('the_content', $cvtx_antrag_grund);
                
                // create an array which holds all data about the antrag
                $antrag_data = array(
                    'post_title'          => $cvtx_antrag_title,
                    'post_content'        => $cvtx_antrag_text,
                    'cvtx_antrag_steller' => $cvtx_antrag_steller,
                    'cvtx_antrag_email'   => $cvtx_antrag_email,
                    'cvtx_antrag_phone'   => $cvtx_antrag_phone,
                    'cvtx_antrag_top'     => $cvtx_antrag_top,
                    'cvtx_antrag_grund'   => $cvtx_antrag_grund,
                    'post_status'         => 'pending',
                    'post_author'         => get_option('cvtx_anon_user'),
                    'post_type'           => 'cvtx_antrag');
                
                // submit the post
                if ($antrag_id = wp_insert_post($antrag_data)) {
                    echo('<p id="message" class="success">'.__('The resolution has been created but it is not published yet.', 'cvtx').'</p>');
                    $erstellt = true;
                } else {
                    echo('<p id="message" class="error">'.__('Resolution could not be saved. God knows why.', 'cvtx').'</p>');
                }
            }
            // return error-message because some required fields have not been submitted
            else {
                echo('<p id="message" class="error">'.__('The amendment could not be saved because some mandatory fields '
                    .'(marked by <span class="form-required" title="This field is mandatory">*</span>) are empty.', 'cvtx'));
            }
        }
    }
    
    // nothing has been submitted yet -> include creation form
    if (!isset($erstellt)) {
        cvtx_create_antrag_form($cvtx_antrag_top, $cvtx_antrag_title, $cvtx_antrag_text, $cvtx_antrag_steller,
                                $cvtx_antrag_email, $cvtx_antrag_phone, $cvtx_antrag_grund, $show_recaptcha);
    }
}


/**
 * Creates formular for creating ae_antraege
 *
 * @param int $cvtx_aeantrag_antrag antrag to which the ae_antrag is dedicated
 * @param string $cvtx_aeantrag_zeile zeile if it has been already submitted
 * @param string $cvtx_aeantrag_text text of aeantrag if it has already been submitted
 * @param string $cvtx_aeantrag_steller aeantrag_steller if it have been already submitted
 * @param string $cvtx_aeantrag_email email of antragsteller if it have been already submitted
 * @param string $cvtx_aeantrag_phone phone number of antragsteller if it have been already submitted
 * @param string $cvtx_aeantrag_grund aeantragsbegruendung, if already submitted
 */
function cvtx_create_aeantrag_form($cvtx_aeantrag_antrag = 0, $cvtx_aeantrag_zeile  = '', $cvtx_aeantrag_text  = '', $cvtx_aeantrag_steller = '', $cvtx_aeantrag_email  = '', $cvtx_aeantrag_phone = '', $cvtx_aeantrag_grund = '', $show_recaptcha = true) {
    ?>
    <form id="create_aeantrag_form" class="cvtx_antrag_form cvtx_form" method="post" action="">
     <?php echo(wp_nonce_field('cvtx_form_create_aeantrag', 'cvtx_form_create_aeantrag_submitted')); ?>
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Line', 'cvtx'); ?></h3></div>
      <div class="form-item">
       <label for="cvtx_aeantrag_zeile"><?php _e('Line', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><br/>
       <input type="text" id="cvtx_aeantrag_zeile" name="cvtx_aeantrag_zeile" class="required" value="<?php echo($cvtx_aeantrag_zeile); ?>" size="4" /><br>
      </div><br/>
     </fieldset>
     
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Author(s)', 'cvtx'); ?></h3></div>
       <div class="form-item">
          <label for="cvtx_aeantrag_steller"><?php _e('Author(s)', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><br/>
          <textarea id="cvtx_aeantrag_steller" name="cvtx_aeantrag_steller" class="required" size="100%" cols="50" rows="5" /><?php echo($cvtx_aeantrag_steller); ?></textarea><br/>
       </div>
       <div class="form-item">
          <label for="cvtx_aeantrag_email"><?php _e('e-mail address', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label> (<?php _e('will not be published', 'cvtx'); ?>)<br/>
          <input type="text" id="cvtx_aeantrag_email" name="cvtx_aeantrag_email" class="required" value="<?php echo($cvtx_aeantrag_email); ?>" size="70" /><br/>
       </div>
       <div class="form-item">
         <label for="cvtx_aeantrag_phone"><?php _e('Mobile number', 'cvtx'); ?>: <?php echo (get_option('cvtx_phone_required', true) == true ? '<span class="form-required" title="'.__('This field is mandatory', 'cvtx').'">*</span></label> ('.__('will not be published', 'cvtx').')' : '</label>'); ?> <br/>
         <input type="text" id="cvtx_aeantrag_phone" name="cvtx_aeantrag_phone" <?php echo (get_option('cvtx_phone_required', true) ? 'class="required"' : ''); ?> value="<?php echo($cvtx_aeantrag_phone); ?>" size="70" /><br/>
       </div>
       <?php
       $cvtx_privacy_message = get_option('cvtx_privacy_message');
       if (!empty($cvtx_privacy_message)) { ?>
           <p class="form-item"><small><?php echo($cvtx_privacy_message); ?></small></p>
       <?php } ?>
     </fieldset>
     
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Text', 'cvtx'); ?></h3></div>
      <input type="hidden" id="cvtx_aeantrag_antrag" name="cvtx_aeantrag_antrag" value="<?php echo($cvtx_aeantrag_antrag); ?>"/>
      <div class="form-item">
       <label for="cvtx_aeantrag_text"><?php _e('Text', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><br/>
       <?php
       if (is_plugin_active('html-purified/html-purified.php')) {
           wp_editor($cvtx_aeantrag_text, 'cvtx_aeantrag_text', array('media_buttons' => false,
                                                                      'textarea_name' => 'cvtx_aeantrag_text',
                                                                      'tinymce'       => cvtx_tinymce_settings(),
                                                                      'quicktags'     => false,
                                                                      'teeny'         => false));
       } else { ?>
           <textarea id="cvtx_aeantrag_text" name="cvtx_aeantrag_text" class="required" size="100%" cols="60" rows="20" /><?php echo($cvtx_aeantrag_text); ?></textarea><br/>
       <?php } ?>
      </div>
      <div class="form-item">
       <label for="cvtx_aeantrag_grund"><?php _e('Explanation', 'cvtx'); ?>:</label><br/>
       <?php
       if (is_plugin_active('html-purified/html-purified.php')) {
           wp_editor($cvtx_aeantrag_grund, 'cvtx_aeantrag_grund', array('media_buttons' => false,
                                                                        'textarea_name' => 'cvtx_aeantrag_grund',
                                                                        'tinymce'       => cvtx_tinymce_settings(),
                                                                        'quicktags'     => false,
                                                                        'teeny'         => false));
       } else { ?>
           <textarea id="cvtx_aeantrag_grund" name="cvtx_aeantrag_grund" size="100%" cols="60" rows="10" /><?php echo($cvtx_aeantrag_grund); ?></textarea><br/>
       <?php } ?>
      </div><br/>
     </fieldset>
     
     <fieldset class="fieldset">
      <div class="legend"><h3><?php _e('Submit', 'cvtx'); ?></h3></div>
      <?php
      // embed reCaptcha
      if (is_plugin_active('wp-recaptcha/wp-recaptcha.php') && $show_recaptcha) {
          $ropt = get_option('recaptcha_options'); ?>
          <div class="form-item">
           <?php echo(recaptcha_get_html($ropt['public_key'])); ?>
          </div>
      <?php } ?>
      <div class="form-item">
       <input type="submit" id="cvtx_aeantrag_submit" class="submit" value="<?php _e('Submit amendment', 'cvtx'); ?>">
      </div><br/>
     </fieldset>
    </form>
    <?php
}


/**
 * Method which evaluates the input of an ae_antrags_creation-form and saves it to the wordpress database
 */
function cvtx_submit_aeantrag($cvtx_aeantrag_antrag = 0, $show_recaptcha = true) {
    $cvtx_aeantrag_zeile   = (!empty($_POST['cvtx_aeantrag_zeile'])   ? trim($_POST['cvtx_aeantrag_zeile'])   : '');
    $cvtx_aeantrag_steller = (!empty($_POST['cvtx_aeantrag_steller']) ? trim($_POST['cvtx_aeantrag_steller']) : '');
    $cvtx_aeantrag_email   = (!empty($_POST['cvtx_aeantrag_email'])   ? trim($_POST['cvtx_aeantrag_email'])   : '');
    $cvtx_aeantrag_phone   = (!empty($_POST['cvtx_aeantrag_phone'])   ? trim($_POST['cvtx_aeantrag_phone'])   : '');
    $cvtx_aeantrag_text    = (!empty($_POST['cvtx_aeantrag_text'])    ? trim($_POST['cvtx_aeantrag_text'])    : '');
    $cvtx_aeantrag_grund   = (!empty($_POST['cvtx_aeantrag_grund'])   ? trim($_POST['cvtx_aeantrag_grund'])   : '');
    
    if (isset($_POST['cvtx_form_create_aeantrag_submitted']) && $cvtx_aeantrag_antrag != 0
     && wp_verify_nonce($_POST['cvtx_form_create_aeantrag_submitted'], 'cvtx_form_create_aeantrag')) {
        if (is_plugin_active('wp-recaptcha/wp-recaptcha.php')) {
            $ropt = get_option('recaptcha_options');
            $resp = recaptcha_check_answer($ropt['private_key'],
                                           $_SERVER['REMOTE_ADDR'],
                                           $_POST['recaptcha_challenge_field'],
                                           $_POST['recaptcha_response_field']);
            if (!$resp->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                echo('<p id="message" class="error">'.__('Wrong captcha. Please try again.', 'cvtx').'</p>');
            }
        }
        if (!is_plugin_active('wp-recaptcha/wp-recaptcha.php') || $resp->is_valid) {
            // check whethter the required fields have been set
            if (!empty($cvtx_aeantrag_zeile) && !empty($cvtx_aeantrag_text) && !empty($cvtx_aeantrag_steller)
             && !empty($cvtx_aeantrag_antrag) && !empty($cvtx_aeantrag_email)) {
                // Apply content filters
                $cvtx_aeantrag_text  = apply_filters('the_content', $cvtx_aeantrag_text);
                $cvtx_aeantrag_grund = apply_filters('the_content', $cvtx_aeantrag_grund);
                
                // create an array which holds all data about the amendment
                $aeantrag_data = array(
                    'cvtx_aeantrag_steller' => $cvtx_aeantrag_steller,
                    'cvtx_aeantrag_antrag'  => $cvtx_aeantrag_antrag,
                    'cvtx_aeantrag_grund'   => $cvtx_aeantrag_grund,
                    'cvtx_aeantrag_zeile'   => $cvtx_aeantrag_zeile,
                    'cvtx_aeantrag_email'   => $cvtx_aeantrag_email,
                    'cvtx_aeantrag_phone'   => $cvtx_aeantrag_phone,
                    'post_status'           => 'pending',
                    'post_author'           => get_option('cvtx_anon_user'),
                    'post_content'          => $cvtx_aeantrag_text,
                    'post_type'             => 'cvtx_aeantrag',
                );
                
                // submit the post!
                if($antrag_id = wp_insert_post($aeantrag_data)) {
                    echo('<p id="message" class="success">'.__('The amendment has been created but it is not published yet.', 'cvtx').'</p>');
                    $erstellt = true;
                } else {
                    echo('<p id="message" class="error">'.__('Amendment could not be saved. '
                        .'Please dance around the table and try again with the computer in a '
                        .'different position.', 'cvtx').'</p>');
                }
            } else {
                echo('<p id="message" class="error">'.__('The amendment could not be saved '
                    .'because some mandatory fields (marked by <span class="form-required" '
                    .'title="This field is mandatory">*</span>) are empty.', 'cvtx').'</p>');
            }
        }
    }
    
    if (!isset($erstellt)) {
        cvtx_create_aeantrag_form($cvtx_aeantrag_antrag, $cvtx_aeantrag_zeile, $cvtx_aeantrag_text, $cvtx_aeantrag_steller,
                                  $cvtx_aeantrag_email, $cvtx_aeantrag_phone, $cvtx_aeantrag_grund, $show_recaptcha);
    }
}


/**
 * Creates form for creating applications
 *
 */
function cvtx_create_application_form($cvtx_application_prename,
                                      $cvtx_application_surname,
                                      $cvtx_application_photo,
                                      $cvtx_application_top,
                                      $cvtx_application_text,
                                      $cvtx_application_cv,
                                      $show_recaptcha = true) {
    global $cvtx_allowed_image_types;
    ?>
    <form id="create_application_form" class="cvtx_application_form cvtx_form" method="post" action="" enctype="multipart/form-data">
        <?php echo(wp_nonce_field('cvtx_form_create_application', 'cvtx_form_create_application_submitted')); ?>
        <!-- Name -->
        <fieldset class="fieldset">
            <div class="legend"><h3><?php _e('Personal Data', 'cvtx'); ?></h3></div>
            <div class="form-item">
                <label for="cvtx_application_prename"><?php _e('First Name', 'cvtx'); ?>: <span class="form-required" title="'.__('This field is mandatory', 'cvtx').'">*</span></label>
                <input type="text" id="cvtx_application_prename" name="cvtx_application_prename" class="required" value="<?php echo($cvtx_application_prename); ?>" size="70" /><br/>
            </div>
            <div class="form-item">
                <label for="cvtx_application_surname"><?php _e('Family Name', 'cvtx'); ?>: <span class="form-required" title="'__('This field is mandatory', 'cvtx').'">*</span></label>
                <input type="text" id="cvtx_application_surname" name="cvtx_application_surname" class="required" value="<?php echo($cvtx_application_surname); ?>" size="70" /><br/>
            </div>
            <div class="form-item">
                <label for="cvtx_application_photo"><?php _e('Photo', 'cvtx'); ?></label>:
                <input type="file" name="cvtx_application_photo" id="cvt_application_photo" />
                <p><small>
                    <?php
                    $max_image_size           = get_option('cvtx_max_image_size');
                    echo(__('Allowed file endings: ','cvtx'));
                    $i = 0;
                    foreach($cvtx_allowed_image_types as $ending => $type) {
                        echo '<span class="ending">'.$ending.'</span>';
                        if($i++ != count($cvtx_allowed_image_types)-1) {
                            echo ', ';
                        }
                    }
                    echo('. '.__('Max. file size: ','cvtx').$max_image_size.' KB');
                    ?>
                </small></p>
            </div>
        </fieldset>
        
        <fieldset class="fieldset">
            <div class="legend"><h3><?php _e('Applying for', 'cvtx'); ?></h3></div>
            <div class="form-item">
                <label for="cvtx_application_top"><?php _e('Agenda point', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label>
                <?php echo(cvtx_dropdown_tops($cvtx_application_top, __('No agenda points enabled to applications.', 'cvtx').'.', '', true)); ?><br />
            </div>
        </fieldset>
                
        <fieldset class="fieldset">
            <div class="legend"><h3><?php _e('Application', 'cvtx'); ?></h3></div>
            <div class="form-item">
                <label for="cvtx_application_text"><?php _e('Application text', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><p/>
                <?php if (is_plugin_active('html-purified/html-purified.php')) {
                    wp_editor($cvtx_application_text, 'cvtx_application_text', array('media_buttons' => false,
                        'textarea_name' => 'cvtx_application_text',
                        'tinymce'       => cvtx_tinymce_settings(),
                        'quicktags'     => false,
                        'teeny'         => false));
                    } else { ?>
                        <textarea id="cvtx_application_text" name="cvtx_application_text" size="100%" cols="60" rows="10" /><?php echo($cvtx_application_text); ?></textarea><br/>
                <?php } ?>
            </div>
            <div class="form-item">
                <label for="cvtx_application_cv"><?php _e('Life career', 'cvtx'); ?>: <span class="form-required" title="<?php _e('This field is mandatory', 'cvtx'); ?>">*</span></label><p/>
                <?php if (is_plugin_active('html-purified/html-purified.php')) {
                    wp_editor($cvtx_application_cv, 'cvtx_application_cv', array('media_buttons' => false,
                        'textarea_name' => 'cvtx_application_cv',
                        'tinymce'       => cvtx_tinymce_settings(),
                        'quicktags'     => false,
                        'teeny'         => false));
                    } else { ?>
                        <textarea id="cvtx_application_cv" name="cvtx_application_cv" size="100%" cols="60" rows="10" /><?php echo($cvtx_application_cv); ?></textarea><br/>
                <?php } ?>
            </div>
        </fieldset>
        
        <fieldset class="fieldset">
            <div class="legend"><h3><?php _e('Submit', 'cvtx'); ?></h3></div>
            <?php
            // embed reCaptcha
            if (is_plugin_active('wp-recaptcha/wp-recaptcha.php') && $show_recaptcha) {
                $ropt = get_option('recaptcha_options'); ?>
                <div class="form-item">
                    <?php echo(recaptcha_get_html($ropt['public_key'])); ?>
                </div>
            <?php } ?>
            <div class="form-item">
                <input type="submit" id="cvtx_application_submit" class="submit" value="<?php _e('Submit application', 'cvtx'); ?>">
            </div><br/>
        </fieldset>
    </form>
    <?php
}


/**
 * Method which evaluates the input of an ae_antrags_creation-form and saves it to the wordpress database
 */
function cvtx_submit_application($show_recaptcha = true) {
    include_once ABSPATH . 'wp-admin/includes/media.php';
    include_once ABSPATH . 'wp-admin/includes/file.php';
    include_once ABSPATH . 'wp-admin/includes/image.php';
    $cvtx_application_photo   = (!empty($_FILES['cvtx_application_photo']) ? $_FILES['cvtx_application_photo']   : '');
    $cvtx_application_prename = (!empty($_POST['cvtx_application_prename']) ? trim($_POST['cvtx_application_prename']) : '');
    $cvtx_application_surname = (!empty($_POST['cvtx_application_surname']) ? trim($_POST['cvtx_application_surname'])   : '');
    $cvtx_application_top     = (!empty($_POST['cvtx_application_top']) ? trim($_POST['cvtx_application_top'])   : '');
    $cvtx_application_text    = (!empty($_POST['cvtx_application_text']) ? trim($_POST['cvtx_application_text'])    : '');
    $cvtx_application_cv      = (!empty($_POST['cvtx_application_cv']) ? trim($_POST['cvtx_application_cv'])   : '');
    $max_image_size           = get_option('cvtx_max_image_size');
    global $cvtx_allowed_image_types;
    
    if (isset($_POST['cvtx_form_create_application_submitted']) && wp_verify_nonce($_POST['cvtx_form_create_application_submitted'], 'cvtx_form_create_application')) {
        if (is_plugin_active('wp-recaptcha/wp-recaptcha.php')) {
            $ropt = get_option('recaptcha_options');
            $resp = recaptcha_check_answer($ropt['private_key'],
                                           $_SERVER['REMOTE_ADDR'],
                                           $_POST['recaptcha_challenge_field'],
                                           $_POST['recaptcha_response_field']);
            if (!$resp->is_valid) {
                // What happens when the CAPTCHA was entered incorrectly
                echo('<p id="message" class="error">'.__('Wrong captcha. Please try again.', 'cvtx').'</p>');
            }
        }
        if (!is_plugin_active('wp-recaptcha/wp-recaptcha.php') || $resp->is_valid) {
            // check whethter the required fields have been set
            if (!empty($cvtx_application_prename) && !empty($cvtx_application_surname) && !empty($cvtx_application_top)
             && !empty($cvtx_application_text) && !empty($cvtx_application_cv)) {
                if(!$cvtx_application_photo || $_FILES['cvtx_application_photo']['size'] < $max_image_size*1000) {
                    $proceed = true;
                    if($cvtx_application_photo) {
                        $validate = wp_check_filetype_and_ext($_FILES['cvtx_application_photo']['tmp_name'], 
                                                              basename($_FILES['cvtx_application_photo']['name']));
                        if(!in_array($validate['type'], $cvtx_allowed_image_types)) {
                            $proceed = false;
                        }
                    }
                    if($proceed) {
                        // Apply content filters
                        $cvtx_application_text  = apply_filters('the_content', $cvtx_application_text);
                        $cvtx_appliction_cv = apply_filters('the_content', $cvtx_application_cv);
                        // create an array which holds all data about the application
                        $application_data = array(
                            'post_title'               => $cvtx_application_prename.' '.$cvtx_application_surname,
                            'cvtx_application_prename' => $cvtx_application_prename,
                            'cvtx_application_surname' => $cvtx_application_surname,
                            'cvtx_application_photo'   => $cvtx_application_photo,
                            'cvtx_application_top'     => $cvtx_application_top,
                            'cvtx_application_cv'      => $cvtx_application_cv,
                            'post_status'              => 'pending',
                            'post_author'              => get_option('cvtx_anon_user'),
                            'post_content'             => $cvtx_application_text,
                            'post_type'                => 'cvtx_application',
                        );
                        
                        // submit the post!
                        if($application_id = wp_insert_post($application_data)) {
                            echo('<p id="message" class="success">'.__('The application has been created but it is not published yet.', 'cvtx').'</p>');
                            $erstellt = true;
                        } else {
                            echo('<p id="message" class="error">'.__('Application could not be saved. '
                                .'Please dance around the table and try again with the computer in a '
                                .'different position.', 'cvtx').'</p>');
                        }
                    }
                    else {
                        echo('<p id="message" class="error">'.__('Please upload an image of one of the following file-types: ','cvtx').implode(', ', $cvtx_allowed_image_types));
                    }
                }
                else {
                    echo('<p id="message" class="error">'.__('The attached image is larger than the allowed size', 'cvtx').' ('.$max_image_size.' KB). '.__('Please check and upload again.', 'cvtx').$_FILES['cvtx_application_photo']['size']);
                }
            } else {
                echo('<p id="message" class="error">'.__('The application could not be saved '
                    .'because some mandatory fields (marked by <span class="form-required" '
                    .'title="This field is mandatory">*</span>) are empty.', 'cvtx').'</p>');
            }
        }
    }
    
    if (!isset($erstellt)) {
        cvtx_create_application_form($cvtx_application_prename,$cvtx_application_surname,$cvtx_application_photo,$cvtx_application_top,$cvtx_application_text,$cvtx_application_cv,$show_recaptcha);
    }
}
?>