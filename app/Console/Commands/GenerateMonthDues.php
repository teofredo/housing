<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\{
    MonthlyDueService,
    PaymentService,
    ErrorResponse,
    ProcessService
};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateMonthDues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:month-dues {process_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly dues to accounts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(
        MonthlyDueService $monthDueService,
        PaymentService $paymentService
    ) {
        try {
            $processId = $this->argument('process_id');
            $process = ProcessService::ins()->find($processId);
            if(!$process) {
                throw new \Exception('process not found');
            }

            if($process->name != 'generate-month-dues') {
                throw new \Exception('invalid process');   
            }

            if(in_array($process->status, ['processing', 'done'])) {
                $this->info("{$process->status}");
                return;
            }

            $dueDate = Carbon::parse($process->due_date);

            $process->status = 'processing';
            $process->save();
            $this->info('processing');

            DB::beginTransaction();

            sleep(30);
            $monthDueService->generateMonthDue($dueDate);
            $paymentService->initPayments($dueDate);

            DB::commit();

            //done
            $process->status = 'done';
            $process->save();
            $this->info('done');

            return;

        } catch(\Exception $e) {}

        DB::rollBack();

        //failed
        if($process) {
            $process->status = 'failed';
            $process->save();
        }

        $this->info($e->getMessage());
    }
}
