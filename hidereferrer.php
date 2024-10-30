<?php
/*
 * Plugin Name: The Official HideReferrer.com WP Plugin
 * Plugin URI: https://hidereferrer.com/plugin.php
 * Description: A configurable Wordpress Plugin to hide your referrer for external urls
 * Version: 1.0.0
 * Author: Simon Stapleton
 */

add_filter('plugin_action_links_hidereferrer/hidereferrer.php', 'synpro_hr_plugin_action_links');

function synpro_hr_plugin_action_links($links) {
    $plugin_links = array('<a href="' . admin_url('admin.php?page=synpro') . '">Settings</a>');
    return array_merge($plugin_links, $links);
}

add_action('wp_enqueue_scripts', 'synpro_hr_load_my_script');
add_action('admin_enqueue_scripts', 'synpro_hr_load_my_script');

function synpro_hr_load_my_script() {
    $uri = untrailingslashit(plugins_url('/', __FILE__));
    $options = get_option('synpro_hr_options');
    $referrer_link = 'hidereferrer.com/wp/?';
    $hide_mode_all = $options['hide_mode_all'];
    $hide_mode_post_page = $options['hide_mode_post_page'];
    $hide_mode_comments = $options['hide_mode_comments'];
    $hide_mode_all_comments_admin = $options['hide_mode_all_comments_admin'];
    $exceptions = $options['exceptions'];
    $exceptions = explode(',', $exceptions);


    echo "<script>var referrer_link='" . $referrer_link . "';  "
    . "var hide_mode_all='" . $hide_mode_all . "';    "
    . "var hide_mode_post_page='" . $hide_mode_post_page . "';    "
    . "var hide_mode_comments='" . $hide_mode_comments . "';    "
    . "var hide_mode_all_comments_admin='" . $hide_mode_all_comments_admin . "';    "
    . "var exceptions=" . json_encode($exceptions) . ";  "
    . "</script>";
    wp_enqueue_script('wc-hide-referrer', $uri . '/assets/app.js', array(), false, true);
}

function synpro_settings_init() {
    // register a new setting for "synpro" page
    register_setting('synpro', 'synpro_hr_options');

    // register a new section in the "synpro" page
    add_settings_section(
            'synpro_section_developers', __('Plugin Settings', 'synpro'), 'synpro_section_developers_cb', 'synpro'
    );

    // register a new field in the "synpro_section_developers" section, inside the "synpro" page
    add_settings_field('synpro_field_hide_mode_all', 'Hide All External Links', 'synpro_hide_mode_all_cb', 'synpro', 'synpro_section_developers');
    add_settings_field('synpro_field_hide_mode_post_page', 'Hide External Links From Post and Pages', 'synpro_hide_mode_post_page_cb', 'synpro', 'synpro_section_developers');
    add_settings_field('synpro_field_hide_mode_comments', 'Hide External Links In Comments', 'synpro_hide_mode_comments_cb', 'synpro', 'synpro_section_developers');
    add_settings_field('synpro_field_hide_mode_comments_admin', 'Hide External Links In Comments (wp-admin)', 'synpro_hide_mode_comments_admin_cb', 'synpro', 'synpro_section_developers');
    //add_settings_field('synpro_field_referrer_link', 'Referrer', 'synpro_referrer_link_cb', 'synpro', 'synpro_section_developers');
    add_settings_field('synpro_field_exceptions', 'Allow These Sites', 'synpro_exceptions_cb', 'synpro', 'synpro_section_developers');
}

/**
 * register our synpro_settings_init to the admin_init action hook
 */
add_action('admin_init', 'synpro_settings_init');

function synpro_section_developers_cb() {
    ?>
    <?php
}

function synpro_hide_mode_all_cb() {
    $options = get_option('synpro_hr_options');
    echo "<input type='checkbox' value=1 name='synpro_hr_options[hide_mode_all]' " . checked(1, $options['hide_mode_all'], false) . " />";
}

function synpro_hide_mode_post_page_cb() {
    $options = get_option('synpro_hr_options');
    echo "<input type='checkbox' value=1 name='synpro_hr_options[hide_mode_post_page]' " . checked(1, $options['hide_mode_post_page'], false) . " />";
}

function synpro_hide_mode_comments_cb() {
    $options = get_option('synpro_hr_options');
    echo "<input type='checkbox' value=1 name='synpro_hr_options[hide_mode_comments]' " . checked(1, $options['hide_mode_comments'], false) . " />";
}

function synpro_hide_mode_comments_admin_cb() {
    $options = get_option('synpro_hr_options');
    echo "<input type='checkbox' value=1 name='synpro_hr_options[hide_mode_all_comments_admin]' " . checked(1, $options['hide_mode_all_comments_admin'], false) . " />";
}

function synpro_exceptions_cb() {
    // get the value of the setting we've registered with register_setting()
    $options = get_option('synpro_hr_options');
    // output the field
    ?>
    <textarea name="synpro_hr_options[exceptions]"  style="width:50%" description="kkk"><?php echo!empty($options['exceptions']) ? $options['exceptions'] : get_home_url(); ?></textarea>
    </br><label style="width:50%;">Just enter sites address seperated by comma which you want to allow as direct link. These address will not be modified</label>
    <?php
}

/**
 * top level menu
 */
function synpro_hr_options_page() {
    // add top level menu page
    add_menu_page(
            'Hide Referrer', 'Hide Referrer', 'manage_options', 'synpro', 'synpro_hr_options_page_html'
    );
}

/**
 * register our synpro_hr_options_page to the admin_menu action hook
 */
add_action('admin_menu', 'synpro_hr_options_page');

/**
 * top level menu:
 * callback functions
 */
function synpro_hr_options_page_html() {
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // add error/update messages
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('synpro_messages', 'synpro_message', __('Settings Saved', 'synpro'), 'updated');
    }

    // show error/update messages
    settings_errors('synpro_messages');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "synpro"
            settings_fields('synpro');
            // output setting sections and their fields
            // (sections are registered for "synpro", each field is registered to a specific section)
            do_settings_sections('synpro');
            // output save settings button
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

/*
