<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );
/**
 * Description of BlogPosting
 *
 * @author mark
 */
class HunchSchema_Post extends HunchSchema_Page {

    public function getResource($pretty = false) {
        parent::getResource($pretty);
        $this->schema['@type'] = "BlogPosting";

        // Get the Categories
        $categories = get_the_category();
        if (count($categories) > 0) {
            foreach ($categories AS $category) {
                $categoryNames[] = $category->name;
            }
            $this->schema['about'] = $categoryNames;
        }
                
        return $this->toJson($pretty);

    }
}
