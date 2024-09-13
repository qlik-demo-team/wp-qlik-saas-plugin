<?php
	require __DIR__ . '/vendor/autoload.php';
	
	use Firebase\JWT\JWT;

	function get_qs_token() {

		$tokenSettings = array(
			'host'        => esc_attr( get_option('qs_host') ),
			'clientId'    => esc_attr( get_option('qs_client_id') ), 
			'clientSecret'=> esc_attr( get_option('qs_client_secret') ),
		);
	
		$tenant_FQDN = $tokenSettings['host'];
		$client_id = $tokenSettings['clientId'];
		$client_secret = $tokenSettings['clientSecret'];
	
		// Step 1: Get the client token
		$clientTokenResponse = wp_remote_post("https://$tenant_FQDN/oauth/token", array(
			'method'      => 'POST',
			'headers'     => array('Content-Type' => 'application/json'),
			'body'        => json_encode(array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'grant_type'    => 'client_credentials',
			)),
		));
	
		if (is_wp_error($clientTokenResponse) || wp_remote_retrieve_response_code($clientTokenResponse) !== 200) {
			return new WP_Error('error', 'Failed to get OAuth token.', array('status' => 500));
		}
	
		$clientToken = json_decode(wp_remote_retrieve_body($clientTokenResponse), true)['access_token'];
	
		// Step 2: Generate a random UUID for the user
		$randID = wp_generate_uuid4();
	
		// Step 3: Create a new user in Qlik
		$createUserResponse = wp_remote_post("https://$tenant_FQDN/api/v1/users", array(
			'method'      => 'POST',
			'headers'     => array(
				'Content-Type'  => 'application/json',
				'Authorization' => "Bearer $clientToken",
			),
			'body'        => json_encode(array(
				'name'         => "Anonymous_$randID",
				'subject'      => "ANON\\$randID",
				'assignedRoles'=> array(array('name' => 'EmbeddedAnalyticsUser')),
			)),
		));
	
		if (is_wp_error($createUserResponse) || wp_remote_retrieve_response_code($createUserResponse) !== 201) {
			return new WP_Error('error', 'Failed to create user.', array('status' => 500));
		}
	
		// Step 4: Impersonate the newly created user to get their token
		$impersonateResponse = wp_remote_post("https://$tenant_FQDN/oauth/token", array(
			'method'      => 'POST',
			'headers'     => array('Content-Type' => 'application/json'),
			'body'        => json_encode(array(
				'client_id'     => $client_id,
				'client_secret' => $client_secret,
				'grant_type'    => 'urn:qlik:oauth:user-impersonation',
				'user_lookup'   => array(
					'field' => 'subject',
					'value' => "ANON\\$randID",
				),
				'scope'        => 'user_default',
			)),
		));
	
		if (is_wp_error($impersonateResponse) || wp_remote_retrieve_response_code($impersonateResponse) !== 200) {
			return new WP_Error('error', 'Failed to get impersonated user OAuth token.', array('status' => 500));
		}
	
		$impersonatedToken = json_decode(wp_remote_retrieve_body($impersonateResponse), true)['access_token'];
	
		return $impersonatedToken;
	}
	
	
	function at_rest_init()
	{
		// route url: domain.com/wp-json/$namespace/$route
		$namespace = 'qs/v1';
		$route     = 'token';
	
		register_rest_route($namespace, $route, array(
			'methods'   => 'GET',
			'callback'  => 'get_qs_token',
			'permission_callback' => '__return_true'
		));
	}
	
	add_action('rest_api_init', 'at_rest_init');

?>
