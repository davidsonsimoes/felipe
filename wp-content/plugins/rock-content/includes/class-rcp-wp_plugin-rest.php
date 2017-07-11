<?php

/**
 * This class has all code necessary to handle the endpoints defined in Rcp_Wp_plugin
 *
 * @since      1.0.0
 * @package    Rcp_Wp_plugin
 * @subpackage Rcp_Wp_plugin/includes
 * @author     Rock Content <plugin@rockcontent.com>
 */
class Rcp_Wp_plugin_Rest {

	/**
	 *
	 */
	public static $errors = array(
		/**
		 * Token errors
		 */
		"INVALID_TOKEN"          => "TK01",
		"TOKEN_NOT_PROVIDED"     => "TK02",
		/**
		 * Integration errors
		 */
		"INTEGRATION_FAILED"     => "IT01",
		/**
		 * Publish post errors
		 */
		"INVALID_POST_FIELDS"    => "PP01",
		"INVALID_WP_POST_FIELDS" => "PP02",
		/**
		 * List post errors
		 */
		"POST_STATUS_REQUIRED"   => "LP01",
		/**
		 * Find post errors
		 */
		"POST_ID_REQUIRED"       => "FP01",
		"POST_NOT_FOUND"         => "FP02"
	);

	/**
	 * List of endpoints used by this plugin
	 *
	 * @var array
	 */
	public $endpoints = array(
		"ACTIVATE"        => array( "method" => "post", "endpoint" => "rcp-activate-plugin" ),
		"PUBLISH_POST"    => array( "method" => "post", "endpoint" => "rcp-publish-content" ),
		"DISCONNECT"      => array( "method" => "get", "endpoint" => "rcp-disconnect-plugin" ),
		"LIST_POSTS"      => array( "method" => "get", "endpoint" => "rcp-list-posts" ),
		"LIST_CATEGORIES" => array( "method" => "get", "endpoint" => "rcp-list-categories" ),
		"LIST_USERS"      => array( "method" => "get", "endpoint" => "rcp-list-users" ),
		"FIND_POST"       => array( "method" => "get", "endpoint" => "rcp-find-post" ),
		"VERSION"         => array( "method" => "get", "endpoint" => "rcp-wp-version" )
	);

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->response    = new Rcp_Response();
		$this->admin       = new Rcp_Wp_plugin_Admin( $plugin_name, $version );
	}

	/**
	 * @since 1.0.0
	 */
	public function rcp_define_endpoints() {
		add_rewrite_endpoint( $this->endpoints["ACTIVATE"]["endpoint"], EP_ROOT );
		add_rewrite_endpoint( $this->endpoints["PUBLISH_POST"]["endpoint"], EP_ROOT );
		add_rewrite_endpoint( $this->endpoints["DISCONNECT"]["endpoint"], EP_ROOT );
		add_rewrite_endpoint( $this->endpoints["LIST_CATEGORIES"]["endpoint"], EP_ROOT );
		add_rewrite_endpoint( $this->endpoints["LIST_POSTS"]["endpoint"], EP_ROOT );
		add_rewrite_endpoint( $this->endpoints["LIST_USERS"]["endpoint"], EP_ROOT );
		add_rewrite_endpoint( $this->endpoints["FIND_POST"]["endpoint"], EP_ROOT );
		add_rewrite_endpoint( $this->endpoints["VERSION"]["endpoint"], EP_ROOT );
	}

	/**
	 * @since 1.0.0
	 */
	public function intercept_request() {
		global $wp_query;

		foreach ( $this->endpoints as $name => $endpoint ) {

			if ( isset( $wp_query->query_vars[ $endpoint["endpoint"] ] ) ) {

				$data = Rcp_Authentication::authenticate( $endpoint["method"] );

				$func = "handle_" . str_replace( "-", "_", $endpoint["endpoint"] ) . "_request";
				$this->$func( $data );

				exit;
			}
		}

		return;
	}

	/**
	 * @param $data
	 *
	 * @since 1.0.0
	 */
	public function handle_rcp_disconnect_plugin_request( $data ) {
		$this->admin->disconnect();

		Rcp_Response::respond_with( 200, array(
			"success" => "wordpress disconnected successfully"
		) );
	}

	/**
	 * @since 1.0.0
	 */
	public function handle_rcp_activate_plugin_request( $data = null ) {
		if ( $activated_at = $this->admin->integrate() ) {
			Rcp_Response::respond_with( 200, array(
				"success"      => "wordpress was successfully integrated",
				"activated_at" => $activated_at
			) );
		} else {
			Rcp_Response::respond_with( 500, array(
				"error_code" => self::$errors["INTEGRATION_FAILED"],
				"errors"     => array( "integration failed" )

			) );
		}
	}

	/**
	 * @since   1.0.0
	 */
	public function handle_rcp_publish_content_request( $data ) {

		$post = array(
			'post_title'   => sanitize_text_field( $data["post_title"] ),
			'post_content' => $data["post_content"],
			'post_status'  => sanitize_text_field( $data["post_status"] )
		);

		if ( isset( $data["terms"]["category"] ) ) {
			$post["post_category"] = $data["terms"]["category"];
		}

		$featured_image      = sanitize_text_field( $data["featured_image"] );
		$featured_image_name = sanitize_text_field( $data["featured_image_name"] );

		try {
			$this->validate_post_content_request( $post );

			$post_id = $this->publish_post( $post );

			if ( ! empty( $featured_image ) ) {
				$this->upload_featured_image( $featured_image, $post_id, $featured_image_name );
			}

			$post = $this->find_post( $post_id );

			Rcp_Response::respond_with( 200, $post );

		} catch ( Rcp_Wp_Exception $e ) {
			Rcp_Response::respond_with( $e->getCode(), $e->GetOptions() );
		}
	}

	/**
	 * @param $post
	 *
	 * @since 1.0.0
	 */
	private function validate_post_content_request( $post ) {
		$errors = array();
		if ( empty( $post["post_title"] ) ) {
			$errors["post_title"] = "post_title is required";
		}
		if ( empty( $post["post_content"] ) ) {
			$errors["post_content"] = "post_content is required";
		}
		if ( empty( $post["post_status"] ) ) {
			$errors["post_status"] = "post_status is required";
		}

		if ( ! empty( $errors ) ) {
			throw new Rcp_Wp_Exception( null, 403, null,
				array(
					"error_code" => self::$errors["INVALID_POST_FIELDS"],
					"errors"     => $errors
				) );
		}
	}

	/**
	 * @param array $post_attrs
	 *
	 * @return int|WP_Error
	 * @throws Rcp_Wp_Exception
	 *
	 * @since 1.0.0
	 */
	private function publish_post( $post_attrs = array() ) {

		$post_id = wp_insert_post( $post_attrs );

		if ( is_wp_error( $post_id ) ) {
			$errors = $post_id->get_error_messages();

			$errors["error_code"] = self::$errors["INVALID_WP_POST_FIELDS"];
			throw new Rcp_Wp_Exception( null, 403, null, $errors );
		}

		return $post_id;
	}

	/**
	 * @param $image_url
	 * @param $post_id
	 *
	 * @since 1.0.0
	 *
	 * @since 1.1.0
	 */
	private function upload_featured_image( $image_url, $post_id, $image_name = null ) {
		$image_url = $this->remove_query_strings( $image_url );
		$image_tag = @media_sideload_image( $image_url, $post_id, $image_name );
		$src       = $this->extract_image_src( $image_tag );
		$attach_id = $this->get_attatchment_id( $src );
		set_post_thumbnail( $post_id, $attach_id );
	}

	/**
	 * @param $image_tag
	 *
	 * @return mixed
	 *
	 * @since 1.1.0
	 */
	private function extract_image_src( $image_tag ) {
		$doc = new DOMDocument();
		$doc->loadHTML( $image_tag );
		$imageTag = $doc->getElementsByTagName( 'img' )->item( 0 );

		return $imageTag->getAttribute( 'src' );
	}

	/**
	 * @param $image_url
	 *
	 * @return mixed
	 *
	 * @since 1.1.0
	 */
	function get_attatchment_id( $image_url ) {
		global $wpdb;
		$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ) );

		return $attachment[0];
	}

	/**
	 * @param $url
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function remove_query_strings( $url ) {
		$pos = strpos( $url, "?" );

		if ( $pos !== false ) {
			$url = substr( $url, 0, $pos );
		}

		return $url;
	}

	/**
	 * @param $id
	 *
	 * @return array|bool
	 *
	 * @since 1.0.0
	 */
	private function find_post( $id ) {
		$post_object = get_post( $id );

		if ( ! $post_object ) {
			return false;
		}

		$post = get_object_vars( $post_object );
		$post = $this->parametrize_post_response( $post );

		return $post;
	}

	/**
	 * @param $post
	 *
	 * @since 1.0.0
	 */
	private function parametrize_post_response( $post ) {
		Rcp_Wp_plugin::_rename_arr_key( "ID", "post_id", $post );
		Rcp_Wp_plugin::_rename_arr_key( "guid", "link", $post );
		Rcp_Wp_plugin::_rename_arr_key( "post_author", "author", $post );

		$post["featured_image"] = wp_get_attachment_url( get_post_thumbnail_id( $post["post_id"] ) );
		$post["terms"]          = $this->get_post_categories( $post["post_id"] );


		return $post;
	}

	/**
	 * @param $post_id
	 *
	 * @since 1.0.0
	 */
	private function get_post_categories( $post_id ) {
		$post_categories = wp_get_post_categories( $post_id );
		$cats            = array();

		foreach ( $post_categories as $c ) {
			$cat    = get_category( $c );
			$cats[] = $this->parametrize_category( $cat );
		}

		return $cats;
	}

	/**
	 * @param $category
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	private function parametrize_category( $category ) {
		return array(
			"term_id"  => (int) $category->term_id,
			"name"     => $category->name,
			"slug"     => $category->slug,
			"taxonomy" => "category"
		);
	}

	/**
	 * @param $category
	 *
	 * @return array
	 *
	 * @since 1.0.1
	 */
	private function parametrize_user( $user ) {
		return array(
			"user_id"      => (int) $user->data->ID,
			"display_name" => $user->data->user_login,
			"email"        => $user->data->user_email,
			"roles"        => $user->roles
		);
	}

	/**
	 * @since 1.0.0
	 */
	public function handle_rcp_wp_version_request( $data = null ) {

		Rcp_Response::respond_with( 200, array(
			"software_version" => array(
				"value" => get_bloginfo( 'version' )
			),
			"rcp_version"      => array(
				"value" => $this->version
			)
		) );
	}

	/**
	 * @since 1.0.0
	 */
	public function handle_rcp_list_posts_request( $data = null ) {

		try {
			$this->validate_get_posts_request( $data );

			$posts = get_posts( $this->build_get_posts_params( $data ) );

			foreach ( $posts as $i => $post ) {
				$posts[ $i ] = $this->parametrize_post_response( get_object_vars( $post ) );
			}

			Rcp_Response::respond_with( 200, $posts );
		} catch ( Rcp_Wp_Exception $e ) {
			Rcp_Response::respond_with( $e->getCode(), $e->GetOptions() );
		}
	}

	/**
	 * @throws Rcp_Wp_Exception
	 *
	 * @since 1.0.0
	 */
	private function validate_get_posts_request( $data ) {
		$errors = array();

		if ( ! isset( $data['post_status'] ) ) {
			$errors[] = "post_status parameter is required";
		}

		if ( ! empty( $errors ) ) {
			throw new Rcp_Wp_Exception( null, 403, null, array(
				"error_code" => self::$errors["POST_STATUS_REQUIRED"],
				"errors"     => $errors
			) );
		}
	}

	/**
	 * @since 1.0.0
	 */
	private function build_get_posts_params( $data ) {
		$params = array();

		$params["posts_per_page"] = isset( $data["number"] ) ? intval( $data["number"] ) : 20;
		$params["offset"]         = isset( $data["offset"] ) ? intval( $data["offset"] ) : 0;
		$params["post_status"]    = isset( $data["post_status"] ) ? sanitize_text_field( $data["post_status"] ) : "publish";
		$params["post_type"]      = isset( $data["post_type"] ) ? sanitize_text_field( $data["post_type"] ) : "post";

		return $params;
	}

	/**
	 * @since 1.0.0
	 */
	public function handle_rcp_list_categories_request() {
		$categories = $this->get_filtered_categories();

		Rcp_Response::respond_with( 200, $categories );
	}

	/**
	 * @return array
	 *
	 * @since 1.0.0
	 */
	private function get_filtered_categories() {
		$categories = get_categories();
		$filtered   = array();

		foreach ( $categories as $i => $category ) {
			array_push( $filtered, $this->parametrize_category( $category ) );
		}

		return $filtered;
	}

	/**
	 * @since 1.0.0
	 */
	public function handle_rcp_list_users_request( $data = null ) {
		$users = $this->get_filtered_users();

		Rcp_Response::respond_with( 200, $users );
	}

	/**
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 * @updated 1.0.1
	 */
	private function get_filtered_users() {
		$users    = get_users();
		$filtered = array();

		foreach ( $users as $i => $user ) {
			array_push( $filtered, $this->parametrize_user( $user ) );
		}

		return $filtered;
	}

	/**
	 * @param null $data
	 *
	 * @since 1.0.0
	 */
	public function handle_rcp_find_post_request( $data = null ) {
		if ( empty( $data["post_id"] ) || ! intval( $data["post_id"] ) ) {
			return Rcp_Response::respond_with( 400, array(
				"error_code" => self::$errors["POST_STATUS_REQUIRED"],
				"errors"     => array( "Post ID is required" )
			) );
			exit;
		}

		$id = $data["post_id"];

		if ( $post = $this->find_post( $id ) ) {
			Rcp_Response::respond_with( 200, $post );
		} else {
			Rcp_Response::respond_with( 404, array(
				"error_code" => self::$errors["POST_NOT_FOUND"],
				"errors"     => array( "Post not found" )
			) );
		}
	}


}
