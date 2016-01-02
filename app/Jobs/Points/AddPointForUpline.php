<?php

namespace App\Jobs\Points;

// change status
use App\Jobs\Job;

use App\Models\Transaction;
use App\Models\PointLog;
use App\Models\StoreSetting;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

class AddPointForUpline extends Job implements SelfHandling
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

        $point                              = StoreSetting::type('downline_purchase_bonus')->Ondate('now')->first();

        $expired                            = StoreSetting::type('downline_purchase_bonus_expired')->Ondate('now')->first();


        $whoisupline                        = 0;

        if($upline && $upline->reference()->count())
        {
            $whoisupline                    = $upline->reference->voucher->value;
        }
        

        if($upline && $point && $expired  && $whoisupline == 0)
        {
            $pointlog                       = new PointLog;

            $pointlog->fill([
                    'user_id'               => $upline->reference_id,
                    'amount'                => $point->value,
                    'expired_at'            => date('Y-m-d H:i:s', strtotime($this->transaction->transact_at.' '.$expired->value)),
                    'notes'                 => 'Bonus belanja '.$this->transaction->user->name
                ]);

            $pointlog->reference()->associate($this->transaction);

            if(!$pointlog->save())
            {
                $result                     = new JSend('error', (array)$this->transaction, $pointlog->getError());
            }
        }

        return $result;
    }
}
