<?php

namespace App\Jobs\Points;

// change status
use App\Jobs\Job;

use App\Models\Voucher;
use App\Models\QuotaLog;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

class CreditQuota extends Job implements SelfHandling
{
    protected $voucher;
    protected $status;

    public function __construct(Voucher $voucher, $message)
    {
        $this->voucher                      = $voucher;
        $this->message                      = $message;
    }

    public function handle()
    {
        if(is_null($this->voucher->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        if($this->voucher->quota - 1 < 0)
        {
            return new JSend('error', (array)$this->voucher, 'Voucher tidak dapat digunakan.');
        }

        $result                             = new JSend('success', (array)$this->voucher);

        // $prevquota                          = QuotaLog::notes($this->message)->first();

        // if(!$prevquota)
        // {
            $quotalog                       = new QuotaLog;

            $quotalog->fill([
                    'voucher_id'            => $this->voucher->id,
                    'amount'                => -1,
                    'notes'                 => $this->message,
                ]);

            if(!$quotalog->save())
            {
                $result                         = new JSend('error', (array)$this->voucher, $quotalog->getError());
            }
        // }

        return $result;
    }
}
