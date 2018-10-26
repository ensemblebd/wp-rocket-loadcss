<?php 


class WPRLCSettingsPage {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page( // title, menu, capability, slug, function
            'WP Rocket CSS Preload', 
            'WP Rocket CSS Preload', 
            'manage_options', 
            'wprlc-settings', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */ 
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'wprlc_settings' );
        ?>
        <div class="wrap">
            <h1>My Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wprlc_option_group' );
                do_settings_sections( 'wprlc-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {        
        register_setting(
            'wprlc_option_group', // Option group
            'wprlc_settings', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'wprlc_section_id', // ID
            'Plugin Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'wprlc-setting-admin' // Page
        );  

		
		
		
		
		
        add_settings_field(
            'inject_loadcss', // ID
            'Inject LoadCSS Library', // Title 
            array( $this, 'inject_loadcss_callback' ), // Callback
            'wprlc-setting-admin', // Page
            'wprlc_section_id' // Section           
        );      

        add_settings_field(
            'modify_output_buffer', 
            'Enable Output Buffer Modification', 
            array( $this, 'modify_output_buffer_callback' ), 
            'wprlc-setting-admin', 
            'wprlc_section_id'
        );
		
		add_settings_field(
            'buffer_override', 
            'Run Without WP Rocket', 
            array( $this, 'buffer_override_callback' ), 
            'wprlc-setting-admin', 
            'wprlc_section_id'
        ); 		
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['inject_loadcss'] ) )
            $new_input['inject_loadcss'] = absint( $input['inject_loadcss'] );

        if( isset( $input['modify_output_buffer'] ) )
            $new_input['modify_output_buffer'] = absint( $input['modify_output_buffer'] );
		
		if( isset( $input['buffer_override'] ) )
            $new_input['buffer_override'] = absint( $input['buffer_override'] );

        return $new_input;
    }
	
    public function print_section_info() {
        print '<h1>Please configure the plugin logistics below:</h1>';
    }

	
    public function inject_loadcss_callback() {
		$options = get_option( 'wprlc_settings' );
        printf(
            '<input style="display:inline-block;" type="checkbox" id="inject_loadcss" name="wprlc_settings[inject_loadcss]" value="1"' . checked( 1, $options['inject_loadcss'], false ) . ' />',
            isset( $this->options['inject_loadcss'] ) ? esc_attr( $this->options['inject_loadcss']) : ''
        );
		echo '<p style="display: inline-block;">Should the loadCSS polyfill be injected into the wp_head()?</p>';
		echo '<p style="margin-left: 22px;"><i>(this <b>must</b> be on <b>if</b> you don\'t already output it to the page via your theme), assuming you want to support all browsers</i></p>';
    }
    public function modify_output_buffer_callback() {
		$options = get_option( 'wprlc_settings' );
        printf(
            '<input style="display: inline-block;" type="checkbox" id="modify_output_buffer" name="wprlc_settings[modify_output_buffer]" value="1"' . checked( 1, $options['modify_output_buffer'], false ) . ' />',
            isset( $this->options['modify_output_buffer'] ) ? esc_attr( $this->options['modify_output_buffer']) : ''
        );
		echo '<p style="display: inline-block;">Should we process the output buffer, and replace stylesheet links?  (Intended Feature of plugin)</p>';
		echo '<p style="margin-left: 22px;"><b>Warning: <i>If you have not enabled WP Rocket\'s setting [Caching For Logged-In Users], then if you are logged-in, you will of course <u>not</u> see the effect.</i></b></p>';
    }
	public function buffer_override_callback() {
		$options = get_option( 'wprlc_settings' );
        printf(
            '<input style="display: inline-block;" type="checkbox" id="buffer_override" name="wprlc_settings[buffer_override]" value="1"' . checked( 1, $options['buffer_override'], false ) . ' />',
            isset( $this->options['buffer_override'] ) ? esc_attr( $this->options['buffer_override']) : ''
        );
		echo '<p style="display: inline-block;">With the previous enabled (Output Buffer Modification), should we also force execution when WP Rocket is non-existant or disabled?</p>';
		echo '<p style="margin-left: 22px;"><i>This plugin was designed for WP Rocket, but it also can work independently, should you so check this box.</p>';
		echo '<p style="margin-left: 22px;"><b>If the WP Rocket plugin is activated, this setting is ignored.</b></i></p>';
    }
}


