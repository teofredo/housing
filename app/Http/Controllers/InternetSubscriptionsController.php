<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternetSubscription;
use App\Transformers\InternetSubscriptionTransformer;
use App\Validators\InternetSubscriptionValidator;
use App\Services\{
	InternetSubscriptionService,
	ErrorResponse
};
use Carbon\Carbon;

class InternetSubscriptionsController extends Controller
{
    protected $model = InternetSubscription::class;
    protected $transformer = InternetSubscriptionTransformer::class;
    protected $validator = InternetSubscriptionValidator::class;
}
