<?php 

namespace App\Libraries;

use Exception;
use GuzzleHttp\Client;

/**
 * { API Class }
 * @author Budi
 * 
 * public functions :
 * 1. get() 							: get request API 
 * 2. post() 							: post request API 
 */

class API
{
	protected $domain			= 'http://apimanager';
	protected $port				= null;
	public $timeout				= 2;
	public $basic_url;

	public function __construct()
	{
		$this->basic_url 		= $this->domain;

		if(!is_null($this->port))
		{
			if(!empty($this->port))
			{
				$this->basic_url = $this->basic_url . ':' . $this->port;
			}
		}
	}


	/**
	 * { get }
	 *
	 * @param     
	 * 1. $url 								: destination url
	 *
	 * @return
	 * 1. API request
	 */
	public function get($url)
	{
		$client 				= new Client([
										'base_uri' => $this->basic_url,
									    'timeout'  => $this->timeout
									]);

		$response 				= $client->get($this->basic_url . $url , ['timeout' => $this->timeout]);
		$response->addHeader('Content-Type','application/json');

		$body 					= $response->getBody();

		return (string) $body;
	}


	/**
	 * { post }
	 *
	 * @param     
	 * 1. $url 								: destination url
	 * 2. $data 							: array set of data 
	 *
	 * @return
	 * 1. API request
	 */
	public function post($url, $data = [])
	{
		$client 				= new Client([
										'base_uri' => $this->basic_url,
									    'timeout'  => $this->timeout,
									]);

		$response 				= $client->post($this->basic_url . $url, ['body' => $data] , ['timeout' => $this->timeout] );
		$response->addHeader('Content-Type','application/json');

		$body 					= $response->getBody();

		return (string) $body;
	}

	/**
	 * { authorized }
	 *
	 * @param     
	 * 1. $url 								: destination url
	 * 2. $data 							: array set of data 
	 *
	 * @return
	 * 1. API request
	 */
	public function authorized($data = [])
	{
		return $this->post('/authorized/client', $data);
	}

	/**
	 * { CloseSession }
	 *
	 * @param     
	 * 1. $url 								: destination url
	 * 2. $data 							: array set of data 
	 *
	 * @return
	 * 1. API request
	 */
	public function CloseSession($queryString)
	{
		return $this->get('/close/session&'.$queryString);
	}

	/**
	 * { OauthMiddleware }
	 *
	 * @param     
	 * 1. $url 								: destination url
	 * 2. $data 							: array set of data 
	 *
	 * @return
	 * 1. API request
	 */
	public function OauthMiddleware($data = [])
	{
		return $this->post('/oauth/middleware', $data);
	}

	/**
	 * { OauthScopeMiddleware }
	 *
	 * @param     
	 * 1. $url 								: destination url
	 * 2. $data 							: array set of data 
	 *
	 * @return
	 * 1. API request
	 */
	public function OauthScopeMiddleware($data = [])
	{
		return $this->post('/oauth/scope/middleware', $data);
	}
}