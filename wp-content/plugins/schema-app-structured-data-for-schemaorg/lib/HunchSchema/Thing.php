<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Description of SchemaThing
 *
 * @author mark
 */
class HunchSchema_Thing {

    /**
     * Schema.org Array
     * 
     * @var type 
     */
    protected $schema;

    /**
     * Construuctor
     */
    public function __construct() {
        
    }

    public static function factory($postType) {
        // Check for specific Page Types
        if (is_search()) {
            $postType = "Search";
        } elseif (is_author()) {
            $postType = "Author";
        } elseif (is_category()) {
            $postType = "Category";
        }
        $class = "HunchSchema_" . $postType;
        if (class_exists($class)) {
            return new $class;
        }
    }

    /**
     * 
     */
    public function getResource($pretty = false) {
        // To override in child classes
    }

    /**
     * 
     * @param type $schema
     */
    protected function addImage() {
        if (has_post_thumbnail()) {
            $wpimage = wp_get_attachment_image_src(get_post_thumbnail_id(), 'single-post-thumbnail');
            $image = array(
                "@type" => "ImageObject",
                "url" => $wpimage[0],
                "height" => $wpimage[2],
                "width" => $wpimage[1]
            );
            $this->schema['image'] = $image;
        }
    }

    /**
     * Converts the schema information to JSON-LD
     * 
     * @return string
     */
    protected function toJson($pretty = false) {
        if (isset($this->schema)) {
            if ($pretty && strnatcmp(phpversion(), '5.4.0') >= 0) {
                $jsonLd = json_encode($this->schema, JSON_PRETTY_PRINT);
            } else {
                $jsonLd = json_encode($this->schema);
            }
            return $jsonLd;
        }
    }

}
