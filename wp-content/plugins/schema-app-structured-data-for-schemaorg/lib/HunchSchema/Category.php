<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Description of Category
 *
 * @author mark
 */
class HunchSchema_Category extends HunchSchema_Thing {

    public function getResource($pretty = false) {
        $this->schema = array(
            '@context' => 'http://schema.org/',
            '@type' => "CollectionPage",
            'headline' => get_the_title(),
            'description' => wp_trim_excerpt()
        );
        
        return $this->toJson($pretty);
    }
}
