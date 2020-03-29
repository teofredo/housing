<?php
namespace App\Services;

use Illuminate\Support\Arr;
use App\Traits\CurlApi;

class AuthApiService
{
	use CurlApi;
	
	private $oauthUrl;
	
	private $resourceUrl;
	
	public function __construct()
	{
		$this->oauthUrl = env('OAUTH_URL');
		
		$this->resourceUrl = env('RESOURCE_URL');
		
		$this->reqData = [
			'grant_type' => 'client_credentials',
			'client_id' => env('CLIENT_ID'),
			'client_secret' => env('CLIENT_SECRET'),
			'scope' => '*'
		];
	}
	
	public function getToken()
	{
		$reqData = $this->getReqDataByGrantType();
		
		if(!$reqData) {
			throw new \Exception('invalid request data');
		}
		
		return $this->httpPost("{$this->oauthUrl}/oauth/token", $reqData);
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