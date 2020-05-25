<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the 'welcome' class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// routes login register logout

    // routes per ottenere le view
$route['login']['get'] = 'auth/login';
$route['register']['get'] = 'auth/register';

    // routes per 'azione' : login / register / logout
$route['login']['post'] = 'auth/evaluateLogin';
$route['register']['post'] = 'auth/evaluateSignup';
$route['logout']['get'] = 'auth/logout';
// -------

// routes per upload dei post
$route['upload']['get'] = 'upload/index';
$route['upload/post']['post'] = 'upload/uploadPost';
// -------

// routes per add/remove Reactions
$route['api/reaction/add']['post'] = 'api/putReaction';
$route['api/reaction/delete']['post'] = 'api/deleteReaction';
$route['api/comment/add']['post'] = 'api/putComment';
$route['api/comment/delete']['post'] = 'api/deleteComment';
// -----------

// routes per Follow/Unfollow
$route['api/follow/(:num)']['post'] = 'api/putUserFollow/$1';
$route['api/unfollow/(:num)']['post'] = 'api/deleteUserFollow/$1';
$route['api/refusefollow/(:num)']['post'] = 'api/deleteUserFollow/$1/true';
$route['api/acceptfollow/(:num)']['post'] = 'api/putUserFollower/$1';
// ------------

// routes per ricerca utenti (api json)
$route['api/search/(:any)']['get'] = 'api/getResearchedUsers/$1';
// ------------

// routes per Settings
$route['user/settings']['get'] = 'userContr/settings';
$route['user/settings']['post'] = 'userContr/updateSettings';
// ------------

// routes per User
$route['user/(:any)']['get'] = 'userContr/index/$1';
    // routes per i dati sull'utente
$route['api/getuserinfo/(:num)']['get']= 'api/getUserProfileInfo/$1';
$route['api/getuserfollowers/(:num)']['get'] = 'api/getUserFollow/false/$1';
$route['api/getuserfollowing/(:num)']['get'] = 'api/getUserFollow/true/$1';
// ------------

// routes per Notifications
$route['/api/putnotificationasviewed']['post'] = 'api/putNotificationAsViewed';
$route['api/getnotifications'] = 'api/getNotifications';
// ------------

// routes per Post
$route['api/getuserposts/(:num)']['get'] ='api/getUserProfilePosts/$1';
$route['api/getusertaggedposts/(:num)']['get'] = 'api/getUserProfileTaggedPosts/$1';
$route['api/getusertaggedposts']['get'] = 'api/getUserProfileTaggedPosts';
$route['post/(:num)']['get'] = 'postContr/index/$1';
$route['api/getposthome']['get'] = 'api/getPostHome';
$route['post/remove']['post'] = 'api/removePost';
$route['tag/remove']['post'] = 'api/removeTag';
$route['api/getsinglepost/(:num)']['get'] = 'api/getSinglePost/$1';
$route['api/getexploreposts']['get'] = 'api/getExplorePosts';
// ----------

// explore (view)
$route['explore']['get'] = 'postContr/explore';
// ----------

//follow suggestions
$route['api/getfollowsuggestions']['get'] = 'api/getFollowSuggestions';
// ----------