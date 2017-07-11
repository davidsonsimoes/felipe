<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Description of Author Page
 *
 * @author mark
 */
class HunchSchema_Author extends HunchSchema_Thing {
    
    public function __construct() {

    }
    
    public function getResource($pretty = false) {
        $this->schema = array(
            '@context' => 'http://schema.org/',
            '@type' => "ProfilePage",
            'headline' => get_the_title(),
            'description' => wp_trim_excerpt(),
            'datePublished' => get_the_date('Y-m-d'),
            'dateModified' => get_the_modified_date('Y-m-d')
        );

        $this->addImage();
        
        return $this->toJson($pretty);
    }
}
