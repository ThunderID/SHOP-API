<?php

namespace App\Jobs;

use App\Libraries\JSend;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\Transaction;
use App\Models\StoreSetting;

class GenerateTransactionUniqNumber extends Job implements SelfHandling
{
    protected $transaction; 

    public function __construct(Transaction $transaction)
    {
        $this->transaction              = $transaction;
    }


    public function handle()
    {
        try
        {
            $result                     = new JSend('success', (array)$this->transaction);
            
            if(!is_null($this->transaction->unique_number))
            {
                $i                          = 0;
                $amount                     = true;

                while($amount)
                {
                    $prev_number            = Transaction::orderBy('id', 'DESC')->status('wait')->type('sell')->first();

                    $limit                  = StoreSetting::type('limit_unique_number')->ondate('now')->first();

                    if($prev_number['unique_number'] < $limit['value'])
                    {
                        $unique_number      = $i+ $prev_number['unique_number'] + 1;
                    }
                    else
                    {
                        $unique_number      = $i+ 1;
                    }

                    $amount                 = Transaction::amount($this->transaction->amount - $unique_number)->status('wait')->notid($this->transaction->id)->first();
                    $i                      = $i+1;
                }

                $this->transaction->unique_number    = $unique_number;

                $result                     = new JSend('success', (array)$this->transaction);
            }
        } 
        catch (Exception $e) 
        {
            $result                     = new JSend('fail', (array)$this->transaction, (array)$e);
        }  

        return $result;
    }
}
