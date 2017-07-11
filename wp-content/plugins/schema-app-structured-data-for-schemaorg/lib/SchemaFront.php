<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Description of schema-editor
 *
 * @author Mark van Berkel
 */
class SchemaFront {

    /**
     * Hook into the appropriate actions when the class is constructed.
     */
    public function __construct() {
        
    }

    /**
     * hunch_schema_add is called to lookup schema.org or add default markup 
     */
    public function hunch_schema_add() {
        $server = new SchemaServer();
        $jsonLd = $server->getResource();
        if ($jsonLd === "") {
            $postType = get_post_type();
            $schemaObj = HunchSchema_Thing::factory($postType);
            if ( isset( $schemaObj )) {
                $jsonLd = $schemaObj->getResource();
            }
        }
        if ( $jsonLd !== "" ) {
            echo "<script type='application/ld+json'>" . $jsonLd . "</script>";
        }     
    }
}
