<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Description of Article
 *
 * @author mark
 */
class HunchSchema_Page extends HunchSchema_Thing {
    
    /**
     * Add schema.org author information to creative works
     * Changes $this->schema array
     * @return type null
     */
    protected function addAuthor() {
        global $post;
        $name = get_the_author_meta('display_name', $post->post_author);
        if (!empty($name)) {
            $author = array(
                '@type' => 'Person',
                'name' => $name,
            );
            $this->schema['author'] = $author;
        }
    }

    /** 
     * Add schema.org publisher information to creative works
     * Changes $this->schema array
     * @return type null
     */
    protected function addPublisher() {
        $options = get_option('schema_option_name');
        
        if (isset($options['publisher_type'])) {

            // Basic publisher information
            $publisher = array(
                '@type' => $options['publisher_type'],
            );

            if ( isset($options['publisher_name']) ) {
                $publisher['name'] = $options['publisher_name'];
            }
            
            // Get Publisher Image Attributes
            if ( isset($options['publisher_image']) ) {
                global $wpdb;
                $pubimage = $wpdb->get_row( $wpdb->prepare ( 
                    "SELECT ID FROM $wpdb->posts WHERE guid = %s", 
                    $options['publisher_image']
                ));
                
                // Publisher image found, add it to schema
                if ( isset($pubimage) ) {
                    $imgProperty = ($options['publisher_type'] === "Person") ? "image" : "logo";
                    $imgAttributes = wp_get_attachment_image_src($pubimage->ID, "full");
                    $publisher[$imgProperty] = array(
                        "@type" => "ImageObject",
                        "url" => $options['publisher_image'],
                        "width" => $imgAttributes[1],
                        "height" => $imgAttributes[2]
                    );
                }
            }
            $this->schema['publisher'] = $publisher;

        }
    }
    
    /** 
     * Add schema.org MainEntityOfPage attribute to creative works
     * Changes $this->schema array
     * @return type null
     */
    protected function addMainEntity() {
        $this->schema['mainEntityOfPage'] = array(
            "@type" => "WebPage",
            "@id" => get_permalink()
        );
    }
    
    /**
     * Get Default Schema.org for Resource
     * 
     * @param type boolean
     * @return type string
     */
    public function getResource($pretty = false) {
        
        if (is_admin()) {
            $description = "DESCRIPTION IS EMPTY WHILE EDITING";
        } else {
            $description = wp_trim_excerpt();
        }
        
        $this->schema = array(
            '@context' => 'http://schema.org/',
            '@type' => "Article",
            'headline' => get_the_title(),
            'description' => $description,
            'datePublished' => get_the_date('Y-m-d'),
            'dateModified' => get_the_modified_date('Y-m-d'),
        );
        
        $this->addMainEntity();
        $this->addImage();
        $this->addAuthor();
        $this->addPublisher();
        
        return $this->toJson($pretty);
    }
}
