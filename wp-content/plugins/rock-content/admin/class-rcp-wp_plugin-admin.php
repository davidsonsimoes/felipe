<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://rockcontent.com/
 * @since      1.0.0
 *
 * @package    Rcp_Wp_plugin
 * @subpackage Rcp_Wp_plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rcp_Wp_plugin
 * @subpackage Rcp_Wp_plugin/admin
 * @author     Rock Content <plugin@rockcontent.com>
 */
class Rcp_Wp_plugin_Admin {


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
	 * The platform endpoint
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plataform_endpoit The platform endpoint for integration.
	 */
	private $plataform_endpoit;

	/**
	 * @var string
	 */
	public $minimum_php_version;

	/**
	 * @var Rcp_Requirements
	 */
	public $requirements;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->minimum_php_version = "5.3.3";
		$this->plugin_name         = $plugin_name;
		$this->version             = $version;
		$this->plataform_endpoit   = "https://plataforma.rockcontent.com";
		$this->requirements        = new Rcp_Requirements( $this->minimum_php_version );
	}

	/**
	 * @since 1.0.0
	 */
	public function activation_success() {
		$class   = 'notice notice-success';
		$message = __( '<strong>Parabéns!</strong> Seu blog foi integrado com sucesso à plataforma Rock Content!',
			'sample-text-domain' );

		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}

	/**
	 * Shows "activation not finished" flash message
	 *
	 * @since 1.0.0
	 */
	public function activation_notice() {
		$class   = 'notice notice-error';
		$message = __( '<strong>Atenção!</strong> Você ainda não integrou sua plataforma Rock Content.',
			'sample-text-domain' );
		$url     = admin_url( 'admin.php?page=rcp_plugin_options' );
		$link    = "<a href='$url'>Clique aqui</a> para integrar.";

		printf( '<div class="%1$s"><p>%2$s %3$s</p></div>', $class, $message, $link );
	}

	public function requirements_notice() {
		$class = 'notice notice-error';

		if ( ! $this->requirements->valid_php_version() ) {
			$message = '<strong>Atenção!</strong> Você está utilizando a versão <strong>' . PHP_VERSION . '</strong> porém para integrar com a Rock Content ela precisa ser maior do que a versão <strong>' . $this->minimum_php_version . '</strong>';
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}

		if ( ! $this->requirements->open_ssl_enabled() ) {
			$message = '<strong>Atenção!</strong> Seu blog não possui a extensão <strong>OpenSSL</strong>. Para integrar com a plataforma Rock Content é necessário habilitar esta extensão.';
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
		}
	}


	/**
	 * Register the plugin menu link
	 *
	 * @since 1.0.0
	 */
	public function rcp_plugin_menu() {
		add_menu_page(
			'Rock Content',
			'Rock Content',
			'administrator',
			'rcp_plugin_options',
			array(
				$this,
				'rcp_plugin_display'
			),
			plugin_dir_url( __FILE__ ) . 'img/rockcontent.png'
		);
	}

	/**
	 * Settings page container
	 *
	 * @since 1.0.0
	 *
	 * @since 1.0.4
	 */
	public function rcp_plugin_display() {

		if ( isset( $_GET['rcp_disconnect'] ) && $_GET['rcp_disconnect'] == "1" ) {
			$this->disconnect();
		}

		?>
		<div class="wrap">
			<?php settings_errors(); ?>
			<img src="<?php echo plugin_dir_url( __FILE__ ) . 'img/logo-rockcontent.png' ?>" alt=""/>
			<hr/>
			<?php if ( ! $this->requirements->valid() ) {
				$this->requirements_notice();
			} else { ?>
				<form method="post" action="<?php echo $this->plataform_endpoit ?>/blogs/integrate">
					<?php settings_fields( 'rcp_integration_options' );
					do_settings_sections( 'rcp_integration_options' );


					if ( $this->integrated() ) {
						submit_button( "Parabéns! Integração conectada!", "success", "submit", true,
							array( "disabled" => true ) );
					} else {

						/**
						 * Fix bug in some WP that didnt flushed rules on activation
						 */
						flush_rewrite_rules();

						$this->rcp_integration_form();
					}


					?>
				</form>
			<?php } ?>

		</div>
		<?php
	}

	/**
	 * Checks if rcp_integrated_at is present
	 *
	 * @return bool
	 *
	 * @since 1.0.0
	 */
	public function integrated() {
		$integrated_at = get_option( "rcp_integrated_at" );

		return ! empty( $integrated_at );
	}

	public function rcp_integration_form() {
		$blog_url = get_bloginfo( 'url' );
		$token    = get_option( 'rcp_token' );
		?>

		<input type="hidden" name="open_ssl_enabled"
		       value="<?php echo $this->requirements->open_ssl_enabled() ? "true" : "false" ?>">
		<input type="hidden" name="php_version" value="<?php echo PHP_VERSION ?>">
		<input type="hidden" name="blog_url" value="<?php echo $blog_url ?>"/>
		<input type="hidden" name="token" value="<?php echo $token ?>"/>
		<input type="hidden" name="rcp_version" value="<?php echo $this->version ?>"/>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary"
			       value="Integrar com a plataforma rock content">
		</p>
		<?php
	}

	/**
	 * Register plugin options for settings page
	 *
	 * @since 1.0.0
	 */
	public function rcp_initialize_layout_options() {
		add_settings_section(
			'layout_settings_section',
			'Instalação',
			array( $this, 'rcp_integration_options_callback' ),
			'rcp_integration_options'
		);

		register_setting(
			'rcp_integration_options',
			'rcp_integration_options'
		);
	}

	/**
	 * Layout section text helper
	 *
	 * @since 1.0.0
	 */
	public function rcp_integration_options_callback() {
		echo '<p>Clique abaixo para iniciar a integração do seu blog com à plataforma Rock Content.</p>';
		echo '<hr/>';
	}

	/**
	 * Token field
	 *
	 * @param $args
	 *
	 * @since 1.0.0
	 */
	public function rcp_change_token_callback( $args ) {
		$defaults = array(
			'token' => get_option( 'rcp_token' )
		);
		$options  = wp_parse_args( get_option( 'rcp_integration_options' ), $defaults );
		$html     = '<input type="text" id="token" readonly size="35" value="' . sanitize_text_field( $options['token'] ) . '" />';
		$html .= '<label for="token"> ' . $args[0] . '</label>';
		echo $html;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return bool|string
	 */
	public function integrate() {
		$integrated_at = date( 'Y-m-d H:i:s' );
		if ( update_option( "rcp_integrated_at", $integrated_at ) ) {
			return $integrated_at;
		} else {
			return false;
		}
	}

	/**
	 * @since 1.0.0
	 */
	public function disconnect() {
		update_option( "rcp_integrated_at", "" );
		update_option( "rcp_activated_at", "" );
		update_option( "rcp_disconnected_at", date( 'Y-m-d H:i:s' ) );
	}

}
