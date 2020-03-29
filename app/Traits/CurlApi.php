<?php
namespace App\Traits;

trait CurlApi
{
	private $headers = [
		'Accept: application/json',
		'ContentType: application/json'
	];
	
	private $data = [];
	
	public function withHeaders(array $headers=[])
	{
		$this->headers = $headers;
		return $this;
	}
	
	public function addHeaders($header)
	{
		if($header) {
			$this->headers[] = $header;
		}
		
		return $this;
	}
	
	public function defaultHeaders()
	{
		$this->headers = [
			'Accept: application/json',
			'ContentType: application/json'		
		];
	}
	
	public function setData(array $data=[])
	{
		$this->data = $data;
		return $this;
	}
	
	public function request($endpoint, $method='GET')
	{
		$httpMethod = 'http' . (($method === 'GET') ? 'Get' : 'Post');
		
		if(!method_exists($this, $httpMethod)) {
			return;
		}
		
		return $this->$httpMethod($endpoint, $this->data, $this->headers);
	}
	
	public function httpPost($endpoint, array $request, array $headers = [])
	{
		$ch = curl_init($endpoint);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if(!empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		$response = curl_exec($ch);
		
		if($response === false) {
			$response = json_encode([
				'error' => [
					'code' => curl_errno($ch),
					'message' => curl_error($ch)
				]
			]);
		} 
		
		curl_close($ch);
		
		return $response;
	}
	
	public function httpGet($endpoint, array $request, array $headers = [])
	{
		$queryString = http_build_query($request);
		$endpoint .= $queryString ? "?{$queryString}" : '';
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $endpoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		if(!empty($headers)) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
		
		$response = curl_exec($ch);
		
		if($response === false) {
			$response = json_encode([
				'error' => [
					'code' => curl_errno($ch),
					'message' => curl_error($ch)
				]
			]);
		} 
		
		curl_close($ch);
		
		return $response;
	}
}