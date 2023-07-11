<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index');
$routes->cli('/videoCron/run/(:segment)', 'VideoCron::run/$1');
$routes->get('/video/playVideo', 'Video::playVideo');
$routes->post('/video/addVideo/(:segment)', 'Video::addVideo/$1');
$routes->patch('/video/deleteVideo', 'Video::deleteVideo');
$routes->patch('/video/updateSchedule', 'Video::updateSchedule');
$routes->patch('/video/updateTimes', 'Video::updateTimes');
$routes->get('/video/searchVideosUser/(:segment)', 'Video::searchVideosUser/$1');
$routes->get('/video/searchVideosUser', 'Video::searchVideosUser');
$routes->post('/schedule/updateTimezone', 'Schedule::updateTimezone');
$routes->post('/schedule/addUpdateSchedule/(:segment)', 'Schedule::addUpdateSchedule/$1');
$routes->delete('/schedule/deleteSchedule', 'Schedule::deleteSchedule');
$routes->patch('/schedule/updateRotation', 'Schedule::updateRotation');
$routes->get('/videos/(:segment)', 'Video::getVideos/$1');
$routes->get('/videos', 'Video::getVideos');
$routes->get('/videoLog/(:segment)', 'VideoLog::getVideoLog/$1');
$routes->get('/suggestVideos', 'Video::getSuggestedUserVideos');
$routes->get('/suggestedVideos/(:segment)', 'Video::getSuggestedAdminVideos/$1');
$routes->get('/suggestedVideos', 'Video::getSuggestedAdminVideos');
$routes->post('/updateSuggestedVideo', 'Video::updateSuggestedVideo');
$routes->get('/schedule', 'Schedule::getSchedule');
$routes->get('/categories/(:segment)', 'Categories::getCategories/$1');
$routes->get('/categories', 'Categories::getCategories');
$routes->get('/invite/(:segment)', 'Invite::getUsers/$1');
$routes->get('/invite', 'Invite::getUsers');
$routes->patch('/inviteUser', 'Invite::inviteUser');
$routes->get('/hiScores', 'HiScores::getScores');
$routes->get('/donate', 'Donate::index');
$routes->get('/signUp', 'SignUpController::index');
$routes->get('/signIn', 'SignInController::index');
$routes->post('/SignInController/loginAuth', 'SignInController::loginAuth');
$routes->post('SignUpController/store', 'SignUpController::store');
$routes->get('/signOut', 'SignInController::signOut');
$routes->get('/requestResetPassword', 'ResetPassword::index');
$routes->post('/submitResetPassword', 'ResetPassword::submit');
$routes->post('/user/addImage', 'User::addImage');
$routes->post('/user/addAbout', 'User::addAbout');
$routes->match(['get', 'post'], '/resetPassword/(:segment)/(:segment)/(:segment)', 'ResetPassword::resetPassword/$1/$2/$3');
$routes->get('/verifyEmail/(:segment)/(:segment)/(:segment)', 'VerifyEmail::index/$1/$2/$3');
$routes->get('/', 'Home::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
