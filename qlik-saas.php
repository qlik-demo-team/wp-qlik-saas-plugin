<?php
	/*
	Plugin Name: Qlik Saas
	Plugin URI: https://github.com/qlik-demo-team/wp-qlik-saas-plugin
	Description: A plugin to connect to Qlik Cloud tenant and embed sheets or objects. 
		- Unzip the plugin into your plugins directory
		- Activate from the admin panel
	Version: 2.1.0
	Author: oim@qlik.com
	License: MIT
	Text Domain: qlik-saas
	Domain Path: /
	*/
	require __DIR__ . '/auth.php';

	defined('ABSPATH') or die("No script kiddies please!"); //Block direct access to this php file

  	define( 'QLIK_SAAS_PLUGIN_VERSION', '2.1.0' );
    define( 'QLIK_SAAS_PLUGIN_MINIMUM_WP_VERSION', '5.1' );
	define( 'QLIK_SAAS_PLUGIN_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
	
	add_action('admin_menu', 'qlik_saas_plugin_menu');
	function qlik_saas_plugin_menu() {
		add_menu_page( esc_attr__('Qlik Saas Plugin Settings', 'qlik-saas'), 'Qlik Saas', 'administrator', 'qlik_saas_plugin_settings', 'qlik_saas_plugin_settings_page', plugin_dir_url( __FILE__ ) . 'assets/qlik.png', null );
	}
	
	// Create the options to be saved in the Database
	add_action( 'admin_init', 'qlik_saas_plugin_settings' );	
	function qlik_saas_plugin_settings() {
		register_setting( 'qlik_saas-plugin-settings-group', 'qs_host' );
		register_setting( 'qlik_saas-plugin-settings-group', 'qs_client_id' );
		register_setting( 'qlik_saas-plugin-settings-group', 'qs_client_secret' );
	}

	// Create the Admin Setting Page
	function qlik_saas_plugin_settings_page() {
		?>
		<div class="wrap">
			<h2><?php esc_html__('Qlik Saas Plugin Settings', 'qlik-saas'); ?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'qlik_saas-plugin-settings-group' ); ?>
				<?php do_settings_sections( 'qlik_saas-plugin-settings-group' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Host', 'qlik-saas'); ?>:</th>
						<td><input type="text" name="qs_host" size="50" value="<?php echo esc_attr( get_option('qs_host') ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Client ID', 'qlik-saas'); ?>:</th>
						<td><input type="text" name="qs_client_id" size="50" value="<?php echo esc_attr( get_option('qs_client_id') ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Client Secret', 'qlik-saas'); ?>:</th>
						<td><input type="text" name="qs_client_secret" size="50" value="<?php echo esc_attr( get_option('qs_client_secret') ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row">&nbsp;</th>
						<td><?php submit_button(); ?></td>
					</tr>
				</table>
				
				<div style="border-top:1px solid #ccc;padding-top:35px;"><a href="https://www.qlik.com/us/"><img src="<?php echo QLIK_SAAS_PLUGIN_PLUGIN_DIR . "/assets/QlikLogo-RGB.png"?>" width="140px"></a></div>
			</form>
		</div>
		<?php
	}

  	// Register Javascript Files
	add_action( 'wp_enqueue_scripts', 'qlik_saas_enqueued_assets', 20 );
	function qlik_saas_enqueued_assets() {
		wp_register_script( 'qlik-saas-embed-init', QLIK_SAAS_PLUGIN_PLUGIN_DIR . 'js/qlik-embed.js', QLIK_SAAS_PLUGIN_VERSION, $in_footer = true );
	}


  function qlik_saas_enqueue_styles() {
    wp_enqueue_style( 'qlik-saas-tabs-style', QLIK_SAAS_PLUGIN_PLUGIN_DIR . 'css/qlik-saas.css' );
	}
	add_action( 'wp_enqueue_scripts', 'qlik_saas_enqueue_styles' );

	function qs_register_csrf_variable(){ 
	?>
		<script type="text/javascript">
			var qs_csrf = false;
			var qs_identity = `${Date.now().toString()}_ANON`;
		</script>
	<?php
	}
	add_action ( 'wp_head', 'qs_register_csrf_variable' );


	function qlik_embed_app( $atts ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'appid' => '',
			'sheetid' => '',
			'height' => '1000px',
			'width' => '100%',
		), $atts );
	
		wp_enqueue_script( 'qlik-saas-embed-init');
	
		$output = "
			<script crossorigin='anonymous' type='application/javascript'
				src='https://cdn.jsdelivr.net/npm/@qlik/embed-web-components'
				data-host='" . esc_attr(get_option('qs_host')) . "'
				data-client-id='" . esc_attr(get_option('qs_client_id')) . "'
				data-get-access-token='getAccessToken'
				data-auth-type='Oauth2'>
			</script>
			<div style='width: {$atts['width']}; height: {$atts['height']};'>
				<qlik-embed ui='classic/app' 
							app-id='{$atts['appid']}' 
							sheet-id='{$atts['sheetid']}'>
				</qlik-embed>
			</div>";
		
		return $output;
	}
	add_shortcode( 'qlik-embed-app', 'qlik_embed_app' );

	// TO DO: add qlik-embed-object  ui/chart
	function qlik_embed_object( $atts ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'appid' => '',
			'objectid' => '',
			'height' => '600px',
			'width' => '100%',
		), $atts );
	
		wp_enqueue_script( 'qlik-saas-embed-init');
	
		$output = "
			<script crossorigin='anonymous' type='application/javascript'
				src='https://cdn.jsdelivr.net/npm/@qlik/embed-web-components'
				data-host='" . esc_attr(get_option('qs_host')) . "'
				data-client-id='" . esc_attr(get_option('qs_client_id')) . "'
				data-get-access-token='getAccessToken'
				data-auth-type='Oauth2'>
			</script>
			<div style='width: {$atts['width']}; height: {$atts['height']};'>
				<qlik-embed ui='analytics/chart' 
							app-id='{$atts['appid']}' 
							object-id='{$atts['objectid']}'>
				</qlik-embed>
			</div>";
		
		return $output;
	}
	add_shortcode( 'qlik-embed-object', 'qlik_embed_object' );

	// TO DO: add qlik-embed-selections ui/selections
	function qlik_embed_selections( $atts ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'appid' => '',
			'height' => '200px',
			'width' => '100%',
		), $atts );
	
		wp_enqueue_script( 'qlik-saas-embed-init');
	
		$output = "
			<script crossorigin='anonymous' type='application/javascript'
				src='https://cdn.jsdelivr.net/npm/@qlik/embed-web-components'
				data-host='" . esc_attr(get_option('qs_host')) . "'
				data-client-id='" . esc_attr(get_option('qs_client_id')) . "'
				data-get-access-token='getAccessToken'
				data-auth-type='Oauth2'>
			</script>
			<div style='width: {$atts['width']}; height: {$atts['height']};'>
				<qlik-embed ui='analytics/selections' 
							app-id='{$atts['appid']}'>
				</qlik-embed>
			</div>";
		
		return $output;
	}
	add_shortcode( 'qlik-embed-selections', 'qlik_embed_selections' );


	// Uninstall the settings when the plugin is uninstalled
	function qlik_saas_uninstall() {
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_host' );
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_privateKey' );
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_keyid' );
	}
	register_uninstall_hook(  __FILE__, 'qlik_saas_uninstall' );
?>
