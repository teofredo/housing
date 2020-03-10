<?php
namespace App\Services;

use Illuminate\Support\Arr;

class AuthApiService
{
	public function __construct()
	{
		$this->authUrl = env('AUTH_URL');
		
		$this->reqData = [
			'grant_type' => 'client_credentials',
			'client_id' => env('CLIENT_ID'),
			'client_secret' => env('CLIENT_SECRET'),
			'scope' => '*'
		];
		
		$this->http = new \GuzzleHttp\Client;
	}
	
	public function getRequestToken()
	{
		$reqData = $this->getReqDataByGrantType();
		
		if(!$reqData) {
			throw new \Exception('invalid request data');
		}
		
		$response = $this->http->post("{$this->authUrl}/oauth/token", [
			'form_params' => $reqData
		]);
		
		if(!$response) {
			return [];
		}
		
		return $response->getBody();
	}
	
	private function getReqDataByGrantType()
	{
		$grantType = $this->reqData['grant_type'];
		
		$commonKeys = ['grant_type', 'client_id', 'client_secret', 'scope'];
		
		if($grantType === 'client_credentials') {
			return Arr::only($this->reqData, $commonKeys);
		}
		
		if($grantType === 'password') {
			return Arr::only($this->reqData, array_merge($commonKeys, ['username', 'password']));
		}
		
		return;
	}
	
	public function setReqData(array $data)
	{
		$this->reqData = array_merge($this->reqData, $data);
		return $this;
	}
}