<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Admin Settings for Schema
 * Modeled after example 2: http://codex.wordpress.org/Creating_Options_Pages
 * 
 * @author Mark van Berkel
 */
class SchemaSettings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $license;
    
    const SCHEMA_ITEM_NAME = "schemawoocommerce";    

    /**
     * Start up
     */
    public function __construct()
    {
        $this->options = get_option( 'schema_option_name' );
        $this->license = get_option( 'schema_option_name_license' );
        $this->wc_status = get_option( 'schema_license_wc_status' );
        
        add_action( 'admin_init', array($this, 'admin_nag_handle'));
        add_action( 'admin_init', array( $this, 'page_init' ) );        
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        add_action( 'admin_notices', array($this, 'admin_nag_set'));         
        register_activation_hook( __FILE__, array($this, 'welcome_screen_activate'));
        add_action( 'admin_init', array($this, 'welcome_screen_do_activation_redirect'));
        add_action( 'admin_init', array($this, 'hunch_schema_activate_license'));

    }

    /**
     * 
     * @return type
     */
    public function welcome_screen_do_activation_redirect() {
        
        // Bail if no activation redirect
        if ( ! get_transient( '_welcome_screen_activation_redirect' ) ) {
            return;
        }

        // Delete the redirect transient
        delete_transient( '_welcome_screen_activation_redirect' );

        // Bail if activating from network, or bulk
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
            return;
        }

        // Redirect to schema app about page
        wp_safe_redirect( add_query_arg( array( 'page' => 'schema-app-setting', 'tab' => 'schema-app-welcome' ), admin_url( 'options-general.php' ) ) );        

    }
    /**
     * 
     */
    public function welcome_screen_activate() {
        set_transient( '_welcome_screen_activation_redirect', true, 30 );
    }


    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        
        // This page will be under "Settings"
        add_options_page(
            'Schema App Settings', 
            'Schema App', 
            'manage_options', 
            'schema-app-setting', 
            array( $this, 'create_admin_page' )
        );
        
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        ?>
        <div class="wrap">
            <h2>Schema App Settings</h2>
            <div></div>
            <section id="schema-app-welcome">
                <h3 class="nav-tab-wrapper">
                    <a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>options-general.php?page=schema-app-welcome">Quick Guide</a>
                    <a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=schema-app-setting">Settings</a>
                    <a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=schema-app-licenses">Licenses</a>
                </h3>
                <h4>Setup</h4>
                <ol>
                    <li>In Schema App Settings tab, register for a Graph ID and add the Graph ID. <em>Note: the Graph is not neccessary to make the plugin create default schema.org markup for pages and posts.</em> A Graph ID is necessary to integrate with Schema App for all your custom schema.org markup.</li>
                    <li>In Schema App Settings tab, add your publisher settings. </li>
                </ol>
                <h4>Semantic Search Strategy</h4>
                You can then employ a few different strategies to extend your structured data, schema.org, markup:
                <h5>Markup Key Content</h5>
                <ol>
                    <li>Make a list of your top webpages (e.g. 5-10% of your total pages). E.g. Your homepage, products, services, key articles or team members</li>
                    <li>For each view the page or post in edit mode then find the Schema App Structured Data box below the editor</li>
                    <li>Clicking on Update will bring you to the Schema App Editor where you customize your structured data markup</li>
                    <li>Fill out the type of Class and Properties that best represent the data on that webpage.</li>
                    <li>Data from Schema App is filled in automatically based on the URL</li>
                </ol>
                <h5>Determine which <a target="_blank" href="https://developers.google.com/structured-data/">Google Structured Data Features</a> you are eligible for.</h5>
                <ol>
                    <li>Match the features with your content</li>
                    <li>Read the guidelines in the recommendation</li>
                    <li>Go to the page that is eligible for the feature</li>
                    <li>Update the page content with required fields for the feature</li>
                </ol>
                <h4>Support & Service</h4>
                <p>Support for Schema App and Semantic Search strategy and implementation is provided with <a href="mailto:support@hunchmanifest.com">support@hunchmanifest.com</a>. For free users we make our best effort to respond to
                    each response thoughtfully. For better support we offer <a href="http://www.schemaapp.com/product-pricing/#premium-support" target="_blank">Premium Support</a> package for responses in 1 day. 
                </p>
            </section>
            <section id="schema-app-settings">
                <h3 class="nav-tab-wrapper">
                    <a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=schema-app-welcome">Quick Guide</a>
                    <a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>options-general.php?page=schema-app-setting">Settings</a>
                    <a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=schema-app-licenses">Licenses</a>
                </h3>
                <form method="post" action="options.php">
                <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'schema_option_group' );   
                    do_settings_sections( 'schema-app-setting' ); 
                    submit_button(); 
                ?>
                </form>
            </section>
            <!--
            <section id="schema-app-report"/>
            -->
            <?php 
            $license 	= (!empty($this->license['schema_license_wc'])) ? $this->license['schema_license_wc'] : false;
//            $status 	= (!empty($this->license['schema_license_wc_status'])) ? $this->license['schema_license_wc_status'] : false;
            $status 	= $this->wc_status;
            ?>
            <section id="schema-app-license">   
                <h3 class="nav-tab-wrapper">
                    <a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=schema-app-welcome">Quick Guide</a>
                    <a class="nav-tab" href="<?php echo admin_url() ?>options-general.php?page=schema-app-setting">Settings</a>
                    <a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>options-general.php?page=schema-app-licenses">Licenses</a>
                </h3>
                <form method="post" action="options.php">

                    <?php 
                    settings_fields( 'schema_option_group_license' );   
                    do_settings_sections( 'schema-app-license' ); 
                    ?>
                    <table class="form-table">
                        <tbody>
                        <?php if (false !== $license) { ?>
                            <tr valign="top">	
                                <th scope="row" valign="top">
                                    <?php _e('Activate License'); ?>
                                </th>
                                <td>
                                    <?php if ($status !== false && $status == 'valid') { ?>
                                        <span style="color:green;"><?php _e('active'); ?></span>
                                    <?php } else {
                                        wp_nonce_field('schema_sample_nonce', 'schema_sample_nonce');
                                    ?>
                                    <input type="submit" class="button-secondary" name="schema_license_activate" value="<?php _e('Activate License'); ?>"/>
                                <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>	
                    <?php submit_button(); ?>
                </form>
            </section>
            
        </div>
        <?php
    }   

    /**
     * Register javascript for media upload (Publisher Logo)
     */
    public function admin_assets($hook) {
        
        if ( 'settings_page_schema-app-setting' != $hook ) {
            return;
        }
        // Javascript
        wp_enqueue_media(); 
        wp_enqueue_script('schema-admin-funcs', WP_PLUGIN_URL.'/schema-app-structured-data-for-schemaorg/js/schemaAdmin.js', array('jquery','media-editor'), '20160211');
        $tab = isset($_GET['tab']) ? $_GET['tab'] : 'schema-app-settings';
        wp_localize_script( 'schema-admin-funcs', 'schemaData', array(
            'tab' => $tab,
	));
        // CSS Styles
        wp_enqueue_style( 'schema-admin-style', WP_PLUGIN_URL.'/schema-app-structured-data-for-schemaorg/css/schemaStyle.css' );
        
    }
    
    /**
     * Register and add settings
     */
    public function page_init() {      
        
        ////
        // Schema App Settings Page
        register_setting(
            'schema_option_group', // Option group
            'schema_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        
        add_settings_section(
            'plugin_settings', // ID
            'Plugin Setup', // Title
            array( $this, 'print_section_plugin' ), // Callback
            'schema-app-setting' // Page
        );  
        add_settings_section(
            'publisher_settings', // ID
            'Publisher Settings', // Title
            array( $this, 'print_section_publisher' ), // Callback
            'schema-app-setting' // Page
        );  

        // Plugin Graph ID (account name)
        add_settings_field(
            'graph_uri', // ID
            'Graph ID', // Title 
            array( $this, 'graph_uri_callback' ), // Callback
            'schema-app-setting', // Page
            'plugin_settings' // Section           
        );     
        
        // Publisher Settings        
        add_settings_field(
            'publisher_type', // ID
            'Publisher Type', // Title 
            array( $this, 'publisher_type_callback' ), // Callback
            'schema-app-setting', // Page
            'publisher_settings' // Section           
        );      
        add_settings_field(
            'publisher_name', // ID
            'Publisher Name', // Title 
            array( $this, 'publisher_name_callback' ), // Callback
            'schema-app-setting', // Page
            'publisher_settings' // Section           
        );      
        add_settings_field(
            'publisher_image', // ID
            'Publisher Logo', // Title 
            array( $this, 'publisher_image_callback' ), // Callback
            'schema-app-setting', // Page
            'publisher_settings' // Section           
        );      
        
        //// Schema App License Page
        // License Information
        register_setting(
            'schema_option_group_license', // Option group
            'schema_option_name_license', // Option name
            array( $this, 'sanitize_license' ) // Sanitize
        );
        add_settings_section(
            'license_settings', // ID
            'License Settings', // Title
            array( $this, 'print_section_license' ), // Callback
            'schema-app-license' // Page
        );
        add_settings_field(
            'schema_license_wc',                    // ID
            'Schema App WooCommerce License',                   // Title 
            array( $this, 'schema_license_wc_callback' ),    // Callback
            'schema-app-license',                   // Page or Tab
            'license_settings'                      // Section           
        );     
 
        add_settings_field(
            'schema_license_wc_status',                    // ID
            'Schema App WooCommerce Status',                   // Title 
            array( $this, 'schema_license_wc_status_callback' ),    // Callback
            'schema-app-license',                   // Page or Tab
            'license_settings'                      // Section           
        );     
 
    }
    
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        
        // Schema App Settings tab
        if( isset( $input['graph_uri'] ) )
            $new_input['graph_uri'] = sanitize_text_field( $input['graph_uri'] );
        
        if( isset( $input['publisher_type'] ) && !empty($input['publisher_type']) )
            $new_input['publisher_type'] = sanitize_text_field( $input['publisher_type'] );
        if( isset( $input['publisher_name'] ) && !empty($input['publisher_name']) )
            $new_input['publisher_name'] = sanitize_text_field( $input['publisher_name'] );
        if( isset( $input['publisher_image'] ) && !empty($input['publisher_image']) )
            $new_input['publisher_image'] = sanitize_text_field( $input['publisher_image'] );
        
        return $new_input;
    }
    
    /**
     * sanitize_license
     * @param type $input
     * @return type
     */
    public function sanitize_license( $input ) {
        $new_input = array();

        // License tab
        if( isset( $input['schema_license_wc'] ) && !empty($input['schema_license_wc']) )
            $new_input['schema_license_wc'] = sanitize_text_field( $input['schema_license_wc'] );
        if( isset( $input['schema_license_wc_status'] ) && !empty($input['schema_license_wc_status']) )
            $new_input['schema_license_wc_status'] = sanitize_text_field( $input['schema_license_wc_status'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_plugin() {
        if (!isset( $this->options['graph_uri'] ) ) { 
            print '<h4><a style="color: #D44A44" target="_blank" href="http://www.schemaapp.com">Register with Schema App</a> to get your Graph ID</h4>';
        }
        print '<p>By default the Schema App Tools creates structured data for all pages and posts. To work best, specify the structured data for your content by registering on <a target="_blank" href="http://www.schemaapp.com">Schema App</a>. Get <a target="_blank" href="http://app.schemaapp.com/wordpress">your Graph ID</a> and start marking up your content with any schema.org class and property.</p>';       
    }
    
    /** 
     * Print the Section text
     */
    public function print_section_publisher() {
        print '<p>Publisher information is required for AMP Articles and used in Page and Post structured data.</p>';       
    }
    
    /** 
     * Print the Section text
     */
    public function print_section_license() {
        print '<p>License Information required for WooCommerce schema.org structured data.</p>';       
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function graph_uri_callback() {
        printf(
            '<input type="text" id="graph_uri" name="schema_option_name[graph_uri]" value="%s" class="regular-text" />',
            isset( $this->options['graph_uri'] ) ? esc_attr( $this->options['graph_uri']) : ''
        );
    }
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function publisher_type_callback() {
        $pubTypeSelect = 
            '<select type="text" id="publisher_type" name="schema_option_name[publisher_type]" class="regular-text" />' . 
            '<option value="">Please choose whether you are a company or person</option>';

        if ( isset( $this->options['publisher_type'] ) ) {
            if ( $this->options['publisher_type'] == "Organization" ) {
                $pubTypeSelect .= 
                    '<option value="Organization" selected="selected">Organization</option>' .
                    '<option value="Person">Person</option>';
            } else {
                $pubTypeSelect .= 
                    '<option value="Organization">Organization</option>' . 
                    '<option value="Person" selected="selected">Person</option>';
            }
        } else { 
            $pubTypeSelect .=
                '<option value="Organization">Organization</option>' .
                '<option value="Person">Person</option>';            
        }
        $pubTypeSelect .= '</select>';
        echo $pubTypeSelect;
        
    }
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function publisher_name_callback() {
        printf(
            '<input type="text" id="publisher_name" name="schema_option_name[publisher_name]" value="%s" class="regular-text" />',
            isset( $this->options['publisher_name'] ) ? esc_attr( $this->options['publisher_name']) : ''
        );
    }
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function publisher_image_callback() {
        $imageHtml = '<div class="uploader">';
        $imageVal = "";
        if ( isset( $this->options['publisher_image'] ) ) {
            $imageVal = esc_attr( $this->options['publisher_image']);
        }
        $imageHtml .= '<input id="publisher_image" name="schema_option_name[publisher_image]" value="'. $imageVal . '" type="text" />';        
        $imageHtml .= '<input id="publisher_image_button" class="button" name="publisher_image_button" type="text" value="Upload" />';
        $imageHtml .= '<ul style="list-style: inherit; padding-left: 25px;"><li>Logos should have a wide aspect ratio, not a square icon.</li>';
        $imageHtml .= '<li>Logos should be no wider than 600px, and no taller than 60px.</li>';
        $imageHtml .= '<li>Always retain the original aspect ratio of the logo when resizing. Ideally, logos are exactly 60px tall with width <= 600px. If maintaining a height of 60px would cause the width to exceed 600px, downscale the logo to exactly 600px wide and reduce the height accordingly below 60px to maintain the original aspect ratio.</li>';
        $imageHtml .= '</ul></div>';
        
        echo $imageHtml;

    }
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function schema_license_wc_callback() {
        printf(
            '<input type="text" id="schema_license_wc" name="schema_option_name_license[schema_license_wc]" value="%s" class="regular-text" />',
            isset( $this->license['schema_license_wc'] ) ? esc_attr( $this->license['schema_license_wc']) : ''
        );
    }
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function schema_license_wc_status_callback() {
        printf(
            '<input type="text" id="schema_license_wc_status" disabled="on" name="schema_option_name_license[schema_license_wc_status]" value="%s" class="regular-text" />',
            isset( $this->wc_status ) ? esc_attr( $this->wc_status ) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function title_callback() {
        printf(
            '<input type="text" id="title" name="schema_option_name[title]" value="%s" />',
            isset( $this->options['title'] ) ? esc_attr( $this->options['title']) : ''
        );
    }
    
    // meta box on post/page
    public function meta_box($data) {
        $value = $this->load_post_meta($data->ID);
        ?>
        <table id="dt-page-definition" width="100%" cellspacing="5px">
            <tr valign="top">
                <td style="width:20%;"><label for="dt-heading"><?php _e( 'Subtitle:', FB_DT_TEXTDOMAIN ); ?></label></td>
                <td><input type="text" id="dt-heading" name="dt-heading" class="heading form-input-tip" size="16" autocomplete="off" value="<?php echo $value['heading']; ?>" tabindex="6" style="width:99.5%"/></td>
            </tr>
            <tr valign="top">
                <td><label for="dt-additional-info"><?php _e( 'Additional information:', FB_DT_TEXTDOMAIN ); ?></label></td>
                <td><textarea cols="16" rows="5" id="dt-additional-info" name="dt-additional-info" class="additional-info form-input-tip code" size="20" autocomplete="off" tabindex="6" style="width:90%"/><?php echo wpautop( $value['additional-info'] ); ?></textarea>
                    <table id="post-status-info" cellspacing="0" style="line-height: 24px;">
                        <tbody>
                            <tr>
                                <td> </td>
                                <td> </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr valign="top">
                <td><label for="dt-listdata"><?php _e( 'Listdata:', FB_DT_TEXTDOMAIN ); ?></label></td>
                <td><textarea cols="16" rows="10" id="dt-listdata" name="dt-listdata" class="listdata form-input-tip" size="20" autocomplete="off" tabindex="6" style="width:99.5%"/><?php echo $value['listdata']; ?></textarea><br /><small><?php _e( 'One list per line', FB_DT_TEXTDOMAIN ) ?></small></td>
            </tr>
        </table>
        <?php
    }

    /**
     * Admin nags for Graph and Publisher Setting Setup
     */
    public function admin_nag_set() {
        if ( current_user_can( 'manage_options' ) ) {
            $graphNotice = isset( $this->options['schema_ignore_notice_graph'] ) ? esc_attr( $this->options['schema_ignore_notice_graph']) : '';
            $pubNotice = isset( $this->options['schema_ignore_notice_publisher'] ) ? esc_attr( $this->options['schema_ignore_notice_publisher']) : '';
            if (empty($this->options['graph_uri']) && $graphNotice !== '1') {            
                $hide_url = add_query_arg( 'schema_ignore_notice_graph', '0' );
                $message = "Setup Schema App Structured Data with <a href='/wp-admin/options-general.php?page=schema-app-setting'>Settings &#8594; Schema App</a> | <a id='hunch-schema-notice-dismiss' href='$hide_url'>Dismiss</a>";
                echo"<div class=\"update-nag hunch-schema-notice-dis\">$message</div>"; 
            } elseif (empty($this->options['publisher_type']) && $pubNotice !== '1') {
                $hide_url = add_query_arg( 'schema_ignore_notice_pub', '0' );
                $message = "Set Schema App Structured Data Publisher <a href='/wp-admin/options-general.php?page=schema-app-setting'>Settings &#8594; Schema App</a> | <a id='hunch-schema-notice-dismiss' href='$hide_url'>Dismiss</a>";
                echo"<div class=\"update-nag hunch-schema-notice-dis\">$message</div>"; 
            }
        }
    }
 
    /**
     * 
     */
    public function admin_nag_handle() {
        
        if ( isset($_GET['schema_ignore_notice_graph']) && '0' == $_GET['schema_ignore_notice_graph'] ) {
            $this->options['schema_ignore_notice_graph'] = '1';
            update_option('schema_option_name', $this->options);
        }
        
        if ( isset($_GET['schema_ignore_notice_pub']) && '0' == $_GET['schema_ignore_notice_pub'] ) {
            $this->options['schema_ignore_notice_publisher'] = '1';
            update_option('schema_option_name', $this->options);
        }

    }
    
    /**
     * Activate license listener, activation process
     */
    public function hunch_schema_activate_license() {
	// listen for our activate button to be clicked
	if( isset( $_POST['schema_license_activate'] ) ) {
            
            // run a quick security check 
            if( ! check_admin_referer( 'schema_sample_nonce', 'schema_sample_nonce' ) ) 	
                return; // get out if we didn't click the Activate button
            
            // retrieve the license from the database
            $license = trim( $_POST['schema_option_name_license']['schema_license_wc'] );

            // data to send in our API request
            $api_params = array( 
                'license' 	=> $license, 
                'item_name'     => urlencode( self::SCHEMA_ITEM_NAME ), // the name of our product
                'url'           => home_url()
            );
            
            $server = new SchemaServer();
            $licenseData = $server->activateLicense($api_params);

            // Check for no activation errors
            if ( $licenseData[0] == true ) {
                //Alter the options appropriately, should be part of the schema_option_name_license array
                update_option('schema_license_wc_status', 'Active' );
                add_settings_error(
                    'schema_wc_activation_err', esc_attr('settings_updated'), $licenseData[1], 'updated'
                );
            } else {
                // Report the errors
                update_option('schema_license_wc_status', 'Inactive');
                add_settings_error(
                    'schema_wc_activation_err', esc_attr('settings_updated'), $licenseData[1] . ". Visit <a target='_blank' href='http://app.schemaapp.com/licenses'>Schema App Licenses</a> for more information", 'error'
                );
            }

	}
    }

}