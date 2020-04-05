<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;
use App\Transformers\BlockTransformer;
use App\Validators\BlockValidator;

class BlocksController extends Controller
{
    protected $model = Block::class;
    protected $transformer = BlockTransformer::class;
    protected $validator = BlockValidator::class;
    
    public function index($id=null, Request $request)
    {
    	return parent::index($id, $request);
    }
}
