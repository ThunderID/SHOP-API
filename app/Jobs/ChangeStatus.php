<?php

namespace App\Jobs;

// change status
use App\Jobs\Job;

use App\Models\Transaction;
use App\Models\TransactionLog;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

class ChangeStatus extends Job implements SelfHandling
{
    protected $transaction;
    protected $status;
    protected $notes;

    public function __construct(Transaction $transaction, $status, $notes = '')
    {
        $this->transaction                  = $transaction;
        $this->status                       = $status;
        $this->notes                        = $notes;
    }

    public function handle()
    {
        if(is_null($this->transaction->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                             = new JSend('success', (array)$this->transaction);

        if($this->transaction->status == $this->status)
        {
            return new JSend('error', (array)$this->transaction, 'Tidak ada perubahan status.');
        }

        $translog                           = new TransactionLog;

        $translog->fill([
                'transaction_id'            => $this->transaction->id,
                'status'                    => $this->status,
                'notes'                     => $this->notes,
                'changed_at'                => date('Y-m-d H:i:s'),
            ]);

        if(!$translog->save())
        {
            $result                         = new JSend('error', (array)$this->transaction, $translog->getError());
        }
        else
        {
            $result                         = new JSend('success', (array)$translog->transaction);
        }

        return $result;
    }
}
