<?php
//initialize facebook sdk
require_once ('src/Facebook/autoload.php');
session_start();
$fb = new Facebook\Facebook([
 'app_id' => '397258797864685',
 'app_secret' => '557ef1ed685d220454564df3ba0d1fe4',
 'default_graph_version' => 'v5.0',
]);
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email']; // optional
try {
if (isset($_SESSION['facebook_access_token'])) {
$accessToken = $_SESSION['facebook_access_token'];
} else {
  $accessToken = $helper->getAccessToken();
}
} catch(Facebook\Exceptions\facebookResponseException $e) {
// When Graph returns an error
echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
// When validation fails or other local issues
echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}
if (isset($accessToken)) {
if (isset($_SESSION['facebook_access_token'])) {
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
} else {
// getting short-lived access token
$_SESSION['facebook_access_token'] = (string) $accessToken;
  // OAuth 2.0 client handler
$oAuth2Client = $fb->getOAuth2Client();
// Exchanges a short-lived access token for a long-lived one
$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
// setting default access token to be used in script
$fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
}
// redirect the user to the profile page if it has "code" GET variable
if (isset($_GET['code'])) {
	 $code = $_GET['code'];
     $_SESSION['code'] = $code;
// header('Location: profile.php');
}
// getting basic info about user
try {
$profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
$requestPicture = $fb->get('/me/picture?redirect=false&height=200'); //getting user picture
$picture = $requestPicture->getGraphUser();
$profile = $profile_request->getGraphUser();
$fbid = $profile->getProperty('id');           // To Get Facebook ID
$fbfullname = $profile->getProperty('name');   // To Get Facebook full name
$fbemail = $profile->getProperty('email');    //  To Get Facebook email
$fbpic = "<img src='".$picture['url']."' class='img-rounded'/>";
# save the user nformation in session variable
$_SESSION['fb_id'] = $fbid.'</br>';
$_SESSION['fb_name'] = $fbfullname.'</br>';
$_SESSION['fb_email'] = $fbemail.'</br>';
$_SESSION['fb_pic'] = $fbpic.'</br>';
$_SESSION['fb_id'] = $fbid.'</br>';
} catch(Facebook\Exceptions\FacebookResponseException $e) {
// When Graph returns an error
echo 'Graph returned an error: ' . $e->getMessage();
session_destroy();
// redirecting user back to app login page
// header("Location: ./");
exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
// When validation fails or other local issues
echo 'Facebook SDK returned an error: ' . $e->getMessage();
exit;
}
} else {
// replace your website URL same as added in the developers.Facebook.com/apps e.g. if you used http instead of https and you used            
$loginUrl = $helper->getLoginUrl('https://localhost/fb-login/', $permissions);
echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
}
?>

<?php if(isset($_SESSION['fb_id'])) {?>
        <div class = "container">
           <div class = "jumbotron">
              <h1>Hello <?php echo $_SESSION['fb_name']; ?></h1>
              <p>Welcome to Cloudways</p>
           </div>
              <ul class = "nav nav-list">
                 <h4>Image</h4>
                 <li><?php echo $_SESSION['fb_pic']?></li>
                 <h4>Facebook ID</h4>
                 <li><?php echo  $_SESSION['fb_id']; ?></li>
                 <h4>Facebook fullname</h4>
                 <li><?php echo $_SESSION['fb_name']; ?></li>
                 <h4>Facebook Email</h4>
                 <li><?php echo $_SESSION['fb_email']; ?></li>
				         <h4>Facebook Code</h4>
                 <li><?php echo $_SESSION['code']; ?></li>
              </ul>
          </div>
<?php } ?>
