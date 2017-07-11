<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       https://rockcontent.com/
 * @since      1.0.0
 * @package    Rcp_Wp_plugin
 * @subpackage Rcp_Wp_plugin/includes
 * @author     Rock Content <plugin@rockcontent.com>
 */
class Rcp_Wp_plugin_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		self::store_deactivation_date();
	}

	/**
	 * @since 1.0.0
	 */
	public static function store_deactivation_date() {
		update_option ("rcp_deactivated_at", date( 'Y-m-d H:i:s' ) );
	}
}
