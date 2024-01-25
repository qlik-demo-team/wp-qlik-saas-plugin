<?php
	/*
	Plugin Name: Qlik Saas
	Plugin URI: https://github.com/yianni-ververis/qlik-saas-wordpress-plugin
	Description: A plugin to connect to Qlik Cloud tenant and embed sheets or objects. 
		- Unzip the plugin into your plugins directory
		- Activate from the admin panel
	Version: 2.0.0
	Author: oim@qlik.com
	License: MIT
	Text Domain: qlik-saas
	Domain Path: /
	*/
	require __DIR__ . '/auth.php';

	defined('ABSPATH') or die("No script kiddies please!"); //Block direct access to this php file

  	define( 'QLIK_SAAS_PLUGIN_VERSION', '2.0.0' );
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
		register_setting( 'qlik_saas-plugin-settings-group', 'qs_webintegrationid' );
		register_setting( 'qlik_saas-plugin-settings-group', 'qs_appid' );
		register_setting( 'qlik_saas-plugin-settings-group', 'qs_privatekey' );
		register_setting( 'qlik_saas-plugin-settings-group', 'qs_keyid' );
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
						<th scope="row"><?php esc_html_e('WebIntegrationID', 'qlik-saas'); ?>:</th>
						<td><input type="text" name="qs_webintegrationid" size="50" value="<?php echo esc_attr( get_option('qs_webintegrationid') ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('App ID', 'qlik-saas'); ?>:</th>
						<td><input type="text" name="qs_appid" size="50" value="<?php echo esc_attr( get_option('qs_appid') ); ?>" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Private Key for JWT auth', 'qlik-saas'); ?>:</th>
						<td><textarea name="qs_privatekey" cols="90" rows="20"><?php echo esc_attr( get_option('qs_privatekey') ); ?></textarea></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e('Key ID', 'qlik-saas'); ?>:</th>
						<td><input type="text" name="qs_keyid" size="50" value="<?php echo esc_attr( get_option('qs_keyid') ); ?>" /></td>
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
		wp_register_script( 'qlik-saas-enigma', 'https://www.unpkg.com/enigma.js@2.12.0/enigma.min.js', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-stardust', 'https://unpkg.com/@nebula.js/stardust@4.9.0', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snBarchart', 'https://unpkg.com/@nebula.js/sn-bar-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snLinechart', 'https://unpkg.com/@nebula.js/sn-line-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snSankeychart', 'https://unpkg.com/@nebula.js/sn-sankey-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snFunnelchart', 'https://unpkg.com/@nebula.js/sn-funnel-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snMekkochart', 'https://unpkg.com/@nebula.js/sn-mekko-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snTable', 'https://unpkg.com/@nebula.js/sn-table', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snGridchart', 'https://unpkg.com/@nebula.js/sn-grid-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snBulletchart', 'https://unpkg.com/@nebula.js/sn-bullet-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snKpi', 'https://unpkg.com/@nebula.js/sn-kpi', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snScatterplot', 'https://unpkg.com/@nebula.js/sn-scatter-plot', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snCombochart', 'https://unpkg.com/@nebula.js/sn-combo-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snDistributionplot', 'https://unpkg.com/@nebula.js/sn-distributionplot', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-snPiechart', 'https://unpkg.com/@nebula.js/sn-pie-chart', null, null, $in_footer = false );
		wp_register_script( 'qlik-saas-iframe-sheet-js', QLIK_SAAS_PLUGIN_PLUGIN_DIR . 'js/iframe-sheet.js', null, null,  $in_footer = true  );
		wp_register_script( 'qlik-saas-nebula', QLIK_SAAS_PLUGIN_PLUGIN_DIR . 'js/nebula.js', QLIK_SAAS_PLUGIN_VERSION, $in_footer = true );
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


	// qlik-saas-object - Embeds Nebula objects
	// [qlik-saas-object id="CSxZqS" height="200px"]
	function qlik_saas_object_func( $atts ) {
		$atts = shortcode_atts( array(
			'id' => '',
			'appid' => '',
			'height' => '500px',
			'width' => '100%',
		), $atts );

		wp_enqueue_script( 'qlik-saas-nebula' );
		wp_enqueue_script( 'qlik-saas-enigma' );
		wp_enqueue_script( 'qlik-saas-stardust' );
		wp_enqueue_script( 'qlik-saas-snBarchart' );
		wp_enqueue_script( 'qlik-saas-snLinechart' );
		wp_enqueue_script( 'qlik-saas-snSankeychart' );
		wp_enqueue_script( 'qlik-saas-snFunnelchart' );
		wp_enqueue_script( 'qlik-saas-snMekkochart' );
		wp_enqueue_script( 'qlik-saas-snTable' );
		wp_enqueue_script( 'qlik-saas-snGridchart' );
		wp_enqueue_script( 'qlik-saas-snBulletchart' );
		wp_enqueue_script( 'qlik-saas-snKpi' );
		wp_enqueue_script( 'qlik-saas-snScatterplot' );
		wp_enqueue_script( 'qlik-saas-snCombochart' );
		wp_enqueue_script( 'qlik-saas-snDistributionplot' );
		wp_enqueue_script( 'qlik-saas-snPiechart' );

		wp_enqueue_script( 'qlik-saas-nebula' );

		$settings = array(	
			'version'						=> QLIK_SAAS_PLUGIN_VERSION,
			'host'							=> esc_attr( get_option('qs_host') ),
			'webIntegrationID'				=> esc_attr( get_option('qs_webintegrationid') ),
			'appID'							=> !empty(get_option('qs_appid')) ? esc_attr( get_option('qs_appid') ) : '',
		);

		
		wp_localize_script( 'qlik-saas-nebula', 'settings', $settings );

		return "<div id=\"{$atts['id']}\" qlik-saas-object-id=\"{$atts['id']}\" app-id=\"{$atts['appid']}\" width=\"100%\" height=\"{$atts['height']}\" style=\"display: block; position: relative; width: {$atts['width']}; height: {$atts['height']};\" class=\"objects-containers\"></div>";
	}
	add_shortcode( 'qlik-saas-object', 'qlik_saas_object_func' );


	// qlik-saas-single - Injects an iframe based on Qlik Saas Single API
	// [qlik-saas-single-sheet id="kHgmg" height="600px"]
	function qlik_saas_single_sheet_func( $atts ) {

		$atts = shortcode_atts( array(
			'id' => '',
			'appid' => '',
			'height' => '1000px', 
		), $atts );
		
		global $wp_scripts;
		wp_enqueue_script( 'qlik-saas-iframe-sheet-js' );

		$settings = array(	
			'version'						=> QLIK_SAAS_PLUGIN_VERSION,
			'host'							=> esc_attr( get_option('qs_host') ),
			'webIntegrationID'				=> esc_attr( get_option('qs_webintegrationid') ),
			'appID'							=> !empty(get_option('qs_appid')) ? esc_attr( get_option('qs_appid') ) : '',
		);

		wp_localize_script( 'qlik-saas-iframe-sheet-js', 'qs_settings', $settings );
		return "<div qlik-saas-sheet-id=\"{$atts['id']}\" app-id=\"{$atts['appid']}\" width=\"100%\" height=\"{$atts['height']}\" class=\"sheet-container\" style=\"display: block; height: {$atts['height']};\"></div>";
	}
	add_shortcode( 'qlik-saas-single-sheet', 'qlik_saas_single_sheet_func' );


	// qlik-saas-multi-sheet - Embeds Multiple Sheets as Tabs
	// [qlik-saas-multi-sheet ids="5dcfd29c-6a0e-4c02-a512-58c39a597b4e,194342d8-2a6a-4596-bc6e-d23eb85f211c,5eabcb4b-f9a1-4a10-81cd-2a307d90a68f" titles="Overview,Risk Profiles,Comparison" height="1000" ]
	function qlik_saas_multi_sheet_func( $atts ) {
		$atts = shortcode_atts( array(
			'ids' => '',
			'titles' => '',
			'appid' => '',
			'height' => '1000px', 
		), $atts );
	
		global $wp_scripts;
		wp_enqueue_script( 'qlik-saas-iframe-sheet-js' );

		$settings = array(    
			'version'                       => QLIK_SAAS_PLUGIN_VERSION,
			'host'                          => esc_attr( get_option('qs_host') ),
			'webIntegrationID'              => esc_attr( get_option('qs_webintegrationid') ),
			'appID'                         => !empty(get_option('qs_appid')) ? esc_attr( get_option('qs_appid') ) : '',
		);
	
		wp_localize_script( 'qlik-saas-iframe-sheet-js', 'qs_settings', $settings );

		// Include the "Selections" object using its shortcode
		$selections_shortcode = '[qlik-saas-object id="selections" height="auto"]';
		$selections = do_shortcode($selections_shortcode);
	
		// Split the IDs and create tabs
		$ids = explode(',', $atts['ids']);
		$titles = explode(',', $atts['titles']); // Split titles by comma
		$tabs = '';
		$contents = '';
	
		foreach ($ids as $index => $id) {
			$title = isset($titles[$index]) ? $titles[$index] : "Sheet {$index}"; // Use custom title or default
        	$active_class = $index == 0 ? 'active' : '';
			$tabs .= "<li class=\"qlik-tab {$active_class}\" data-sheet-id=\"{$id}\">{$title}</li>";
			$contents .= "<div id=\"qlik-sheet-{$id}\" data-sheet-id=\"{$id}\" data-app-id=\"{$atts['appid']}\" data-height=\"{$atts['height']}\" class=\"qlik-sheet-content {$active_class}\" style=\"height: {$atts['height']};\"></div>";
		}
	
		return "<div class=\"qlik-tab-wrapper\">
					<ul class=\"qlik-tabs\">{$tabs}</ul>
					{$selections}
					<div class=\"qlik-sheets-container\">{$contents}</div>
				</div>";
	}
	add_shortcode( 'qlik-saas-multi-sheet', 'qlik_saas_multi_sheet_func' );


	// Uninstall the settings when the plugin is uninstalled
	function qlik_saas_uninstall() {
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_host' );
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_webintegrationid' );
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_appid' );
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_privatekey' );
		unregister_setting( 'qlik_saas-plugin-settings-group', 'qs_keyid' );
	}
	register_uninstall_hook(  __FILE__, 'qlik_saas_uninstall' );
?>
