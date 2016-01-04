<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\Transaction;

class GenerateTransactionRefNumber extends Job implements SelfHandling
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction                      = $transaction;
    }

    public function handle()
    {
        if(is_null($this->transaction->id) || $this->transaction->ref_number=='0000000000')
        {
            if($this->transaction->type=='sell' && in_array($this->transaction->status, ['na', 'cart', 'abandoned']))
            {
                $this->transaction->ref_number  = '0000000000';
            }
            else
            {
                $prefix                         = $this->transaction->type[0].date("ym");

                $latest_transaction             = Transaction::select('ref_number')
                                                    ->refnumber($prefix)
                                                    ->status(['wait', 'paid', 'packed', 'shipping', 'delivered', 'canceled'])
                                                    ->orderBy('ref_number', 'DESC')
                                                    ->first();

                if(date('Y')=='2015')
                {
                    if(empty($latest_transaction))
                    {
                        $number                     = 47;
                    }
                    else
                    {
                        $number                     = 1 + (int)substr($latest_transaction['ref_number'],6);
                    }
                }
                else
                {
                    if(empty($latest_transaction))
                    {
                        $number                     = 1;
                    }
                    else
                    {
                        $number                     = 1 + (int)substr($latest_transaction['ref_number'],6);
                    }
                }


                $ref_number                     = str_pad($number,4,"0",STR_PAD_LEFT);

                $this->transaction->ref_number  = $prefix . $ref_number;
            }
        }

        return new JSend('success', (array)$this->transaction);
    }
}
