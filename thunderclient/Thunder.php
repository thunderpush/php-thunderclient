<?php

class Thunder
{
	protected static $API_VERSION = '1.0.0';
	protected static $API_URL = '/api/%s/%s/%s/';

	protected $apikey;
	protected $apisecret;
	protected $host;

	public function __construct($apikey, $apisecret, $host, $port=80)
	{
		$this->apikey = $apikey;
		$this->apisecret = $apisecret;
		$this->host = sprintf('http://%s:%d', $host, $port);
	}

	protected function make_url($command)
	{
		$arguments = array_slice(func_get_args(), 1);


		$url = sprintf(self::$API_URL, self::$API_VERSION, $this->apikey, 
			$command);

		if ($arguments)
		{
			$url .= implode('/', $arguments) . '/';;
		}

		return $url;
	}

	protected function make_request($method, $url, $data=NULL)
	{
		$ch = curl_init();

		// let curl_exec return the response
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// set up the URL of the request
		curl_setopt($ch, CURLOPT_URL, $this->host . $url);

		// set up the headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'X-Thunder-Secret-Key: ' . $this->apisecret,
		));

		// set the request method
		switch ($method)
		{
			case 'GET':
				// do nothing, GET is the default request method
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
				break;
			default:
				die('Unsupported request method.');
				return;
		}

		$response = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($status == 200)
		{
			if ($response)
			{
				$response = json_decode($response, true);
			}
		}
		else
		{
			$response = array();
		}

		curl_close($ch);

		return array('status' => $status, 'data' => $response);
	}

	// builds function response based on the API response
	protected function build_response($response, $field=NULL)
	{
		if ($response['status'] == 200)
		{
			return $response['data'][$field];
		}
		else if (is_null($field) && $response['status'] == 204)
		{
			return true;
		}
		else
		{
			return NULL;
		}
	}

	public function get_user_count()
	{
		$response = $this->make_request('GET', $this->make_url('users'));
		return $this->build_response($response, 'count');
	}

	public function get_users_in_channel($channel)
	{
		$response = $this->make_request('GET', 
			$this->make_url('channels', $channel));
		return $this->build_response($response, 'users');
	}

	public function send_message_to_user($userid, $message)
	{
		$response = $this->make_request('POST', 
			$this->make_url('users', $userid), $message);
		return $this->build_response($response, 'count');
	}

	public function send_message_to_channel($channel, $message)
	{
		$response = $this->make_request('POST', 
			$this->make_url('channels', $channel), $message);
		return $this->build_response($response, 'count');
	}

	public function is_user_online($userid)
	{
		$response = $this->make_request('GET', 
			$this->make_url('users', $userid));
		return $this->build_response($response, 'online');
	}

	public function disconnect_user($userid)
	{
		$response = $this->make_request('DELETE', 
			$this->make_url('users', $userid));
		return $this->build_response($response);
	}
}
