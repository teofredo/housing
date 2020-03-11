<?php
namespace App\Services;

class CurlService
{
	public function __construct()
	{
		//
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
}