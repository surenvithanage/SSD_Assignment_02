<?php 
	// DB Configurations
	define('DB_HOST', 'localhost:3306');
	define('DB_UNAME', 'root');
	define('DB_PWD', '');
	define('DB_NAME', 'ssd_assignment2');
	define('DB_USER_TABLE', 'users');
	define('DB_POST_TABLE', 'user_posts');
	
	// Facebook API Configurations
	define('FB_APP_ID', 'xxx');
	define('FB_APP_SECRET', 'xxx');
	define('FB_REDIRECT_URL', 'xxx');
	define('FB_POST_LIMIT', 10);
	
	// Creating a new session if not session is available
	if(!session_id()){
		session_start();
	}
	
	require_once __DIR__ . '\facebook-php-graph-sdk/autoload.php';
	
	// Importing 
	use Facebook\Facebook;
	use Facebook\Exceptions\FacebookResponseException;
	use Facebook\Exceptions\FacebookSDKException;
	
	// Call Facebook API
	$fb = new Facebook(array(
		'app_id' => FB_APP_ID,
		'app_secret' => FB_APP_SECRET,
		'default_graph_version' => 'v3.2'
		));
	
	// Obtaining the redirect login helper
	$helper = $fb->getRedirectLoginHelper();
	
	// Retrieve Access Token
	try {
		if(isset($_SESSION['facebook_access_token'])) {
			$accessToken = $_SESSION['facebook_access_token'];
		}else{
			$accessToken = $helper->getAccessToken();
		}
	} catch(FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
?>