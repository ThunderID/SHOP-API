<?php

namespace App\Jobs\Points;

//to count how much point cuts for trs
use App\Jobs\Job;

use App\Models\Transaction;
use App\Models\PointLog;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

class CreditPoint extends Job implements SelfHandling
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

        //cek all  in debit active point
        $points                             = PointLog::userid($this->transaction->user_id)->onactive('now')->debit(true)->get();

        //count leftover active point
        $sumpoints                          = PointLog::userid($this->transaction->user_id)->onactive('now')->sum('amount');

        $idx                                = 0;
        $currentamount                      = 0;
        $transactionamount                  = $this->transaction->amount;

        while($transactionamount <= $this->transaction->amount && $points && isset($points[$idx]) && $transactionamount > 0)
        {
            //count left over point per debit to credit
            $currentamount                  = $points[$idx]['amount'];

            foreach($points[$idx]->pointlogs as $key => $value)
            {
                $currentamount              = $currentamount + $value['amount'];
            }

            //if leftover more than 0
            if($currentamount > 0 && $currentamount >= $transactionamount)
            {
                $camount                    = 0 - $transactionamount;
            }
            else
            {
                $camount                    = 0 - $currentamount;
            }

            if($currentamount > 0)
            {
                $point                      = new PointLog;
                $point->fill([
                        'user_id'           => $points[$idx]->user_id,
                        'point_log_id'      => $points[$idx]->id,
                        'amount'            => $camount,
                        'expired_at'        => $points[$idx]->expired_at,
                        'notes'             => 'Pembayaran Belanja #'.$this->transaction->ref_number,
                    ]);

                $point->reference()->associate($this->transaction);

                if(!$point->save())
                {
                    return new JSend('error', (array)$this->transaction, $point->getError());
                }

                $transactionamount           = $transactionamount + $camount;
            }

            $idx++;
        }

        $result                         = new JSend('success', (array)$this->transaction);
        
        return $result;
    }
}
