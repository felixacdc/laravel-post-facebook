<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
$fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
// Or in PHP >= 5.5
$fb = app(SammyK\LaravelFacebookSdk\LaravelFacebookSdk::class);

Route::get('/', function () {
    return view('welcome');
});

// Generate a login URL
Route::get('/facebook/login', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
{
    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email',
				  'user_location',
				  'user_birthday',
				  'publish_actions',
				  'publish_pages',
				  'manage_pages',
				  'public_profile'];
				  
	$loginUrl = $helper->getLoginUrl('http://laravel-facebook.app/facebook/callback', $permissions);

	echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
});

// Endpoint that is redirected to after an authentication attempt
Route::get('/facebook/callback', function(SammyK\LaravelFacebookSdk\LaravelFacebookSdk $fb)
{
    $helper = $fb->getRedirectLoginHelper();

    try {
        $accessToken = $helper->getAccessToken();
        $linkData = [
            'link' => 'http://go.reu.gt',
            'message' => "Hola Mundo",
        ];
        $fb->post('/feed', $linkData, $accessToken);
        return redirect('/')->with('message', 'Successfully logged in with Facebook');
        
    } catch (Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
    }
});