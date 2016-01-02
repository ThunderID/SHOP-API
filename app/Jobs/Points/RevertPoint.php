<?php

namespace App\Jobs\Points;

//to revert point used for shop
use App\Jobs\Job;

use App\Models\Transaction;
use App\Models\PointLog;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

class RevertPoint extends Job implements SelfHandling
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

        foreach ($this->transaction->pointlogs as $key => $value) 
        {
            if($value->amount < 0)
            {
                $point                      = new PointLog;
                $point->fill([
                        'user_id'           => $value->user_id,
                        'point_log_id'      => $value->id,
                        'amount'            => 0 - $value->amount,
                        'expired_at'        => $value->expired_at,
                        'notes'             => 'Revert Belanja #'.$this->transaction->ref_number,
                    ]);
        
                $point->reference()->associate($this->transaction);

                if(!$point->save())
                {
                    return new JSend('error', (array)$this->transaction, $point->getError());
                }
            }
        }


        $result                         = new JSend('success', (array)$this->transaction);
        
        return $result;
    }
}
