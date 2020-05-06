<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Services\{
    FractalService,
    ErrorResponse,
    AuthApiService
};

use Illuminate\Support\Carbon;
use App\Traits\ApiQueryBuilder;

class Controller extends BaseController
{
    use AuthorizesRequests, 
        DispatchesJobs, 
        ValidatesRequests, 
        ApiQueryBuilder;

    protected $fractal;
    
    protected $authApiService;
    
    private $authUser;
    
    private $vConstraints = [];

    public function __construct(FractalService $fractal)
    {
    	$this->fractal = $fractal;

        $this->initForResource();
        
        $this->authApiService = new AuthApiService;
    }

    private function initForResource()
    {
        try {
            if(!$this->model || !$this->transformer) {
                return;
            }

            //model
        	$model = $this->model;
        	$this->model = new $model;
        	if(!$this->model instanceof Model) {
        		throw new \Exception("Class {$model} must return an instance of Illuminate\Database\Eloquent\Model;");
        	}

        	//transformer
        	$transformer = $this->transformer;
        	$this->transformer = new $transformer;

        } catch(\Exception $e) {
            // throw $e;
        }
    }

    public function index($id=null, Request $request)
    {
        try {
            if(!$this->model || !$this->transformer) {
                throw new \Exception('controller requires model and transformer definition');
            }

        	$includes = $request->get('_includes');

            // if request is calling special function
            if($_function = $request->get('_function')) {
                $_function = '_get' . $_function;
                return $this->$_function($id, $request);
            }

    		if(!$id) {
                $resource = $this->buildQuery($request);
                if(!$resource) {
                    throw new \Exception('resource returned an empty result');
                }

                if($resource instanceof \Illuminate\Database\Eloquent\Collection) {
                    return $this->fractal
                        ->collection($resource, $this->transformer)
                        ->includes($includes)
                        ->get();
                } 
    		} else {
                $resource = $this->model->find($id);
            }

    		return $this->fractal
    			->item($resource, $this->transformer)
    			->includes($includes)
    			->get();

        } catch(\Exception $e) {}

        throw $e;
    }
    
    public function post(Request $request)
    {
        try {
            // if request is calling special function
            if($_function = $request->get('_function')) {
                $_function = '_post' . $_function;
                return $this->$_function($id, $request);
            }

            $data = $request->all();
            
            $validator = $this->validator ?? null;
            if($validator) {
                $this->validator = new $validator;
                $this->validator
                    ->setConstraints($this->vConstraints)
                    ->validate($data);
            } 
            
            $resource = $this->model->create($data);
            $resource = $this->fractal->item($resource, $this->transformer)->get();
            
            return response($resource);
            
        } catch(\Exception $e) {}
        
        $errorResponse = new ErrorResponse($e);
        
        return $errorResponse->toJson();   
    }
    
    public function requestToken(
        $grantType='client_credentials', 
        array $data=[]
    ) {
        try {
            $data = array_merge($data, [
                'grant_type' => $grantType ?? 'password',
            ]);
            
            $token = $this->authApiService
                ->setReqData($data)
                ->getToken();
            
            if(isset($token->error)) {
                throw new \Exception('Unauthenticated');
            }
                
            if($data['grant_type'] == 'password') {
                $this->authUser = $this->authApiService->getUserByAccessToken($token->access_token);
            }
            
            \App\Models\AccessToken::create([
                'user_id' => $this->authUser->user_id ?? null,
                'access_token' => $token->access_token,
                'refresh_token' => $token->refresh_token ?? null,
                'expired_at' => Carbon::createFromTimestamp(time() + $token->expires_in)
            ]);
            
            return $token;
            
        } catch(\Exception $e) {}
        
        throw $e;
    }
    
    protected function getAuthUser()
    {
        return $this->authUser;
    }
    
    /**
    * set validator constraints
    */
    protected function setVConstraints(array $constraints=[])
    {
        $this->vConstraints = $constraints;
        return $this;
    }

    protected function getModel()
    {
        return $this->model;
    }
}