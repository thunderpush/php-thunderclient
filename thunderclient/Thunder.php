<?php

/*
 * This file is part of the Thunderpush package.
 *
 * (c) Krzysztof Jagiełło <https://github.com/kjagiello>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Guzzle\Http\Client;

class Thunder
{
	const API_VERSION = '1.0.0';
	const API_URL = '/api/%s/%s/%s/';

	protected $apikey;
	protected $apisecret;
	protected $host;

	/**
	 * @var $client Guzzle\Http\Client Guzzle client
	 */
	protected $client;

	public function __construct($apikey, $apisecret, $host, $port = 80, $https = false)
	{
		$this->apikey = $apikey;
		$this->apisecret = $apisecret;
		$this->client = new Client(($https === true ? 'https://' : 'http://') . $host . ':' . $port);

		$this->client->setDefaultOption('headers', array(
			'Content-Type' => 'application/json',
			'X-Thunder-Secret-Key' => $this->apisecret
		));
	}

	protected function make_url($command)
	{
		$arguments = array_slice(func_get_args(), 1);

		$url = sprintf(self::API_URL, self::API_VERSION, $this->apikey, 
			$command);

		if ($arguments) {
			$url .= implode('/', $arguments) . '/';;
		}

		return $url;
	}

	protected function make_request($method, $url, $data = NULL)
	{
		$url = call_user_func_array(array($this, 'make_url'), $url);

		// set the request method
		switch ($method) {
			case 'GET':
				// do nothing, GET is the default request method
				$request = $this->client->get($url);
				break;
			case 'POST':
				$request = $this->client->post($url, array(), json_encode($data));
				break;
			case 'DELETE':
				$request = $this->client->delete($url);
				break;
			default:
				throw new \UnsupportedMethodException('Unsupported request method: ' . $method);
				return;
		}

		$return = array(
			'data' => array(),
			'status' => 500
		);
		try {
			$response = $request->send();
			$return['data'] = $response->json();
			$return['status'] = $response->getStatusCode();
		}
		catch(\RequestException $e) {
			$return['status'] = $e->getStatusCode();
			$return['exception'] = $e;
		}
		catch(\Exception $e) {
			$return['exception'] = $e;
		}

		return $return;
	}

	// builds function response based on the API response
	protected function build_response($response, $field = NULL) {
		if ($response['status'] == 200) {
			return $response['data'][$field];
		}
		else if (is_null($field) && $response['status'] == 204) {
			return true;
		}
		else {
			return NULL;
		}
	}

	public function get_user_count()
	{
		$response = $this->make_request('GET', array('users'));
		return $this->build_response($response, 'count');
	}

	public function get_users_in_channel($channel)
	{
		$response = $this->make_request('GET', array('channels', $channel));
		return $this->build_response($response, 'users');
	}

	public function send_message_to_user($userid, $message)
	{
		$response = $this->make_request('POST', array('users', $userid), $message);
		return $this->build_response($response, 'count');
	}

	public function send_message_to_channel($channel, $message)
	{
		$response = $this->make_request('POST', array('channels', $channel), $message);
		return $this->build_response($response, 'count');
	}

	public function is_user_online($userid)
	{
		$response = $this->make_request('GET', array('users', $userid));
		return $this->build_response($response, 'online');
	}

	public function disconnect_user($userid)
	{
		$response = $this->make_request('DELETE', array('users', $userid));
		return $this->build_response($response);
	}
}

/** Exceptions **/

class UnsupportedMethodException extends \Exception {}