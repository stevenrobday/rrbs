<?php

namespace App\Controllers;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */

class BaseController extends Controller
{
	/**
	 * Instance of the main Request object.
	 *
	 * @var IncomingRequest|CLIRequest
	 */
	protected $request;

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];

	/**
	 * Constructor.
	 *
	 * @param RequestInterface  $request
	 * @param ResponseInterface $response
	 * @param LoggerInterface   $logger
	 */
	public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.: $this->session = \Config\Services::session();
	}

    protected function timeArray($start_time, $end_time): array
    {
        if ($start_time >= $end_time) {
            if ($end_time == 0) return range($start_time, 23);
            else {
                $arrayA = range($start_time, 23);
                $arrayB = range(0, $end_time - 1);
                return array_merge($arrayA, $arrayB);
            }
        }
        else return range($start_time, $end_time - 1);
    }

    protected function getReadableHour($hour)
    {
        switch ($hour)
        {
            case (0):
                return '12 AM';
            case (1):
                return '1 AM';
            case (2):
                return '2 AM';
            case (3):
                return '3 AM';
            case (4):
                return '4 AM';
            case (5):
                return '5 AM';
            case (6):
                return '6 AM';
            case (7):
                return '7 AM';
            case (8):
                return '8 AM';
            case (9):
                return '9 AM';
            case (10):
                return '10 AM';
            case (11):
                return '11 AM';
            case (12):
                return '12 PM';
            case (13):
                return '1 PM';
            case (14):
                return '2 PM';
            case (15):
                return '3 PM';
            case (16):
                return '4 PM';
            case (17):
                return '5 PM';
            case (18):
                return '6 PM';
            case (19):
                return '7 PM';
            case (20):
                return '8 PM';
            case (21):
                return '9 PM';
            case (22):
                return '10 PM';
            case (23):
                return '11 PM';
        }
    }

    protected function getVideoData($videoId)
    {
        $apiKey = getenv('API_KEY');

        $client = \Config\Services::curlrequest();
        $response = $client->request('GET', "https://www.googleapis.com/youtube/v3/videos?part=contentDetails,snippet,status&id={$videoId}&key=$apiKey");

        if ($response->getStatusCode() !== 200) $this->getVideoData($videoId);
        else {
            $body = $response->getBody();
            return json_decode($body, true);
        }
    }
}
