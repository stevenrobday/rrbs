<?php

namespace Config;

use App\Filters\AuthorizeAdmin;
use App\Filters\AuthorizeInvitedUser;
use App\Filters\AuthorizeUsers;
use App\Filters\CookieVerify;
use CodeIgniter\Filters\InvalidChars;
use App\Filters\SecureHeadersExt;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseConfig
{
	/**
	 * Configures aliases for Filter classes to
	 * make reading things nicer and simpler.
	 *
	 * @var array
	 */
	public $aliases = [
		'csrf'     => CSRF::class,
		'honeypot' => Honeypot::class,
        'authAdmin' => AuthorizeAdmin::class,
        'authInvitedUser' => AuthorizeInvitedUser::class,
        'authUsers' => AuthorizeUsers::class,
        'cookieVer' => CookieVerify::class,
        'invalidChars'  => InvalidChars::class,
        'secureHeaders' => SecureHeadersExt::class,
	];

	/**
	 * List of filter aliases that are always
	 * applied before and after every request.
	 *
	 * @var array
	 */
	public $globals = [
		'before' => [
            'honeypot',
            'csrf',
            'cookieVer',
            'invalidChars',
		],
		'after'  => [
			'honeypot',
            'secureHeaders',
		],
	];

	/**
	 * List of filter aliases that works on a
	 * particular HTTP method (GET, POST, etc.).
	 *
	 * Example:
	 * 'post' => ['csrf', 'throttle']
	 *
	 * @var array
	 */
	public $methods = [];

	/**
	 * List of filter aliases that should run on any
	 * before or after URI patterns.
	 *
	 * Example:
	 * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
	 *
	 * @var array
	 */
	public $filters = [
        'authAdmin' => ['before' => ['video/addVideo/admin', 'video/deleteVideo', 'video/updateSchedule', 'video/updateTimes', 'schedule','schedule/*', 'videos', 'videos/*', 'categories', 'categories/*', 'invite', 'invite/*', 'suggestedVideos', 'suggestedVideos/*', 'updateSuggestedVideo']],
        'authInvitedUser' => ['before' => ['video/addVideo/user', 'suggestVideos', 'video/searchVideosUser']],
        'authUsers' => ['before' => ['videoLog/*', 'user/*', 'hiScores', 'donate']]
    ];
}
