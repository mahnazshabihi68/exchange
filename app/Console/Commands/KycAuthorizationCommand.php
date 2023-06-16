<?php

namespace App\Console\Commands;

use App\Models\Kyc;
use App\Services\interfaces\IKycService;
use Illuminate\Console\Command;

class KycAuthorizationCommand extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kyc:authorization';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kyc Authorization';

    private IKycService $kycService;
    private Kyc $kyc;

    public function __construct(IKycService $kycService, Kyc $kyc)
    {
        parent::__construct();
        $this->kyc = $kyc;
        $this->kycService = $kycService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kycList = $this->kyc::whereStatus('pending')->get();

        foreach ($kycList as $kyc) {
            $kyc['clientToken'] = $kyc['authorization_kyc'];
            $this->kycService->getAuthorizationKycInfo($kyc);
        }

        $this->info($this->signature . ' has been lunched successfully at ' . now());
    }
}
