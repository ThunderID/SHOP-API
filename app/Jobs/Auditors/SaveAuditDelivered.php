<?php

namespace App\Jobs\Auditors;

// change status
use App\Jobs\Job;

use App\Models\Transaction;
use App\Models\Auditor;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon, Auth;

class SaveAuditDelivered extends Job implements SelfHandling
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

        $datetrans                          = Carbon::now();

        $datepay                            = $this->transaction->shipment->updated_at;

        $difference                         = $datepay->diff($datetrans)->days;

        $audit                              = new Auditor;

        $audit->fill([
                'user_id'                   => (Auth::check() ? Auth::user()->id : '0'),
                'type'                      => 'transaction_delivered',
                'ondate'                    => Carbon::now()->format('Y-m-d H:i:s'),
                'event'                     => 'Pesanan Lengkap. Selisih waktu pengiriman hingga barang tiba : '.$difference.' hari',
            ]);

        $audit->table()->associate($this->transaction);

        if(!$audit->save())
        {
            $result                         = new JSend('error', (array)$this->transaction, $audit->getError());
        }

        return $result;
    }
}
