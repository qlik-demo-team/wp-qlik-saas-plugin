<?php
	require __DIR__ . '/vendor/autoload.php';
	
	use Firebase\JWT\JWT;

	function get_qs_token() {

		$tokenSettings = array(
			'host'							=> esc_attr( get_option('qs_host') ),
			'privateKey'					=> esc_attr( get_option('qs_privateKey') ),		
			'keyID'							=> esc_attr( get_option('qs_keyid') ),
		);

		$jwt = null;
		if (
			isset($tokenSettings['host']) && !empty($tokenSettings['host']) && 
			isset($tokenSettings['privateKey']) && !empty($tokenSettings['privateKey']) && 
			isset($tokenSettings['keyID']) && !empty($tokenSettings['keyID'])
		) {

			$issuedAt   = new DateTimeImmutable();
			$expire     = $issuedAt->modify('+30 minutes')->getTimestamp();

			$uuid = wp_generate_uuid4();

			$payload = [
					'iss'  						=> $tokenSettings['host'],
					"aud"						=> 'qlik.api/login/jwt-session',
					'iat'  						=> $issuedAt->getTimestamp(),
					'nbf'  						=> $issuedAt->getTimestamp(),
					'jti'						=> $uuid,
					'exp'						=> $expire,
					'sub'						=> $uuid,
					'subType'					=> 'user',
					'name'						=> 'Anon_' . $uuid,
					'email'						=> $uuid . '@anonymoususer.anon',
					'email_verified'			=> true,
					'groups'					=> ['anon-view'],
			];
			
			// Encode the array to a JWT string.
			JWT::$leeway = 30 * 60; // $leeway in seconds
	
			// encode($payload, $key, $alg = 'HS256', $keyId = null, $head = null)
			$jwt = JWT::encode(
				$payload,
				$tokenSettings['privateKey'],
				'RS256',
				$tokenSettings['keyID']
			);
		} else {
			$jwt = new WP_Error( 'error', 'Cannot Generate JWT token.', array( 'status' => 404 ) );;
		}
		
		return $jwt;		
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
