<?php

namespace App\Jobs\Points;

// change status
use App\Jobs\Job;

use App\Models\Transaction;
use App\Models\PointLog;
use App\Models\QuotaLog;
use App\Models\StoreSetting;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

class AddQuotaForUpline extends Job implements SelfHandling
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction                  = $transaction;
    }

    public function handle()
    {
        if(is_null($this->transaction->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                             = new JSend('success', (array)$this->transaction);

        $upline                             = PointLog::userid($this->transaction->user_id)->referencetype('App\Models\User')->first();
        
        $quota                              = StoreSetting::type('downline_purchase_quota_bonus')->Ondate('now')->first();

        $whoisupline                        = 0;

        if($upline && $upline->reference()->count())
        {
            $whoisupline                    = $upline->reference->voucher->value;
        }

        if($upline && $quota && $whoisupline == 0)
        {
            $quotalog                       = new QuotaLog;

            $quotalog->fill([
                    'voucher_id'            => $upline->reference->voucher->id,
                    'amount'                => $quota->value,
                    'notes'                 => 'Bonus belanja '.$this->transaction->user->name.' nomor nota #'.$this->transaction->ref_number,
                ]);

            if(!$quotalog->save())
            {
                $result                     = new JSend('error', (array)$this->transaction, $quotalog->getError());
            }
        }

        return $result;
    }
}
