<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Description of schema-editor
 * Inspired by http://codex.wordpress.org/Function_Reference/add_meta_box#Class
 *
 * @author Mark van Berkel
 */
class SchemaEditor {

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        
    }
    
    public function hunch_schema_edit() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));        
    }
    /**
     * Adds the meta box container.
     */
    public function add_meta_box($post_type) {
        $post_types = array('post', 'page');            //limit meta box to certain post types
        if (in_array($post_type, $post_types)) {
            add_meta_box(
                    'schema_meta_json_ld'
                    , __('Schema.org Structured Data', 'schema_textdomain')
                    , array($this, 'render_meta_box_content')
                    , $post_type
                    , 'advanced'
                    , 'high'
            );
        }
    }

    /**
     * Render Meta Box content.
     *
     * @param WP_Post $post The post object.
     */
    public function render_meta_box_content($post) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field('schema_inner_custom_box', 'schema_inner_custom_box_nonce');

        $server = new SchemaServer();
        // Use get_post_meta to retrieve an existing value from the database.       
        $jsonLd = $server->getResource(get_permalink(), true);
        
        if (empty($jsonLd)) {
            $postType = get_post_type();
            $schemaObj = HunchSchema_Thing::factory($postType);
            $jsonLd = $schemaObj->getResource(TRUE);
            $editlink = $server->createLink();
        } else {
            $editlink = $server->updateLink();
        }        
        $testlink = "https://developers.google.com/structured-data/testing-tool?url=" . urlencode(get_permalink());

        // Display the form, using the current value.
        echo '<label for="schema_new_field"><span class="inside">';
        _e('Post JSON-LD', 'schema_textdomain');
        echo '</span><a target="_blank" href="' . $editlink . '">Update</a>';
        echo '<a class="inside" target="_blank" href="' . $testlink . '">Test</a>';
        echo '</label> ';
        echo '<p><textarea disabled="" class="large-text metadesc" rows="6" id="schema_new_field" name="schema_new_field">';
        echo esc_attr($jsonLd);
        echo '</textarea>';
        if (isset($schemaObj)) {
            echo '<br/><strong>Note: </strong><span style="color: grey"><em>This is default markup. Extend this with Schema App Creator using Update linked above.</em></span>';
        }
        echo '</p>';
    }

}
