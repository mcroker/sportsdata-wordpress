<?php
defined( 'ABSPATH' ) || exit;

function sd_add_settings_page() {
    add_options_page( 
        'SportsData plugin page', 
        'SportsData', 
        'manage_options', 
        'sportsdata-plugin', 
        'sd_render_plugin_settings_page' 
    );
}
add_action( 'admin_menu', 'sd_add_settings_page' );

function sd_render_plugin_settings_page() {
    ?>
    <h2>SportsData Plugin Settings</h2>
    <form action="options.php" method="post">
        <?php 
        settings_fields( 'sd_plugin_options' );
        do_settings_sections( 'sd_plugin' ); ?>
        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
    </form>
    <?php
}

function sd_register_settings() {
    register_setting( 'sd_plugin_options', 'sd_plugin_options', 'sd_plugin_options_validate' );

    add_settings_section( 'api_settings', 'API Settings', 'sd_plugin_section_text', 'sd_plugin' );
    add_settings_field( 'sd_plugin_setting_logo_url', 'LOGO URL', 'sd_plugin_setting_logo_url', 'sd_plugin', 'api_settings' );
    add_settings_field( 'sd_plugin_setting_api_url', 'API URL', 'sd_plugin_setting_api_url', 'sd_plugin', 'api_settings' );
    add_settings_field( 'sd_plugin_setting_api_key', 'API Key', 'sd_plugin_setting_api_key', 'sd_plugin', 'api_settings' );
}
add_action( 'admin_init', 'sd_register_settings' );

function sd_plugin_options_validate( $input ) {
    $newinput['api_key'] = trim( $input['api_key'] );
    $newinput['api_url'] = trim( $input['api_url'] );
    $newinput['logo_url'] = trim( $input['logo_url'] );
    return $newinput;
}

function sd_plugin_section_text() {
    echo '<p>Here you can set all the options for using the API</p>';
}

function sd_plugin_setting_api_key() {
    $options = get_option( 'sd_plugin_options' );
    echo "<input id='sd_plugin_setting_api_key' name='sd_plugin_options[api_key]' size=80 type='text' value='" . esc_attr( $options['api_key'] ?? '' ) . "' />";
}

function sd_plugin_setting_api_url() {
    $options = get_option( 'sd_plugin_options' );
    echo "<input id='sd_plugin_setting_api_url' name='sd_plugin_options[api_url]' size=80 type='text' value='" . esc_attr( $options['api_url'] ?? '' ) . "' />";
}

function sd_plugin_setting_logo_url() {
    $options = get_option( 'sd_plugin_options' );
    echo "<input id='sd_plugin_setting_logo_url' name='sd_plugin_options[logo_url]' size=80 type='text' value='" . esc_attr( $options['logo_url'] ?? '' ) . "' />";
}
