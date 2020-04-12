<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\{
    MonthlyDueService,
    PaymentService,
    ErrorResponse,
    ProcessService
};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateMonthDues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:month-dues {due_date}';

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
            $dueDate = Carbon::parse($this->argument('due_date'));
            if(!$dueDate->isValid()) {
                throw new \Exception('invalid due_date');
            }

            $process = ProcessService::ins()->first([
                'name' => 'generate-monthly-dues',
                'due_date' => $dueDate
            ]);

            if($process && in_array($process->status, ['processing', 'done'])) {
                $this->info("{$process->status}");
                return;
            }

            //processing
            $process = ProcessService::ins()
                ->getModel()
                ->updateOrCreate(
                    ['name' => 'generate-month-dues', 'due_date' => $dueDate],
                    ['status' => 'processing']
                );

            $this->info('processing');

            DB::beginTransaction();

            $monthDueService
                ->checkGeneratorLock()
                ->setDueDate($dueDate)
                ->generateWaterBill()
                ->generateInternetFee()
                ->generateOtherCharges()
                ->generatePreviousBalance()
                ->generatePenaltyForNonPayment()
                ->generateAdjustments();

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
        ProcessService::ins()
            ->getModel()
            ->where([
                'name' => 'generate-monthly-dues',
                'due_date' => $dueDate
            ])
            ->update(['status' => 'failed']);

        $this->info($e->getMessage());
    }
}
