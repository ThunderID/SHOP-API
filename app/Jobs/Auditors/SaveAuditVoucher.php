<?php

namespace App\Jobs\Auditors;

// change status
use App\Jobs\Job;

use App\Models\Voucher;
use App\Models\Auditor;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon, Auth;

class SaveAuditVoucher extends Job implements SelfHandling
{
    protected $voucher;

    public function __construct(Voucher $voucher)
    {
        $this->voucher                  = $voucher;
    }

    public function handle()
    {
        if(is_null($this->voucher->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                         = new JSend('success', (array)$this->voucher);

        $audit                          = new Auditor;

        $audit->fill([
                'user_id'               => (Auth::check() ? Auth::user()->id : '0'),
                'type'                  => 'voucher_added',
                'ondate'                => Carbon::now()->format('Y-m-d H:i:s'),
                'event'                 => 'Pembuatan Voucher '.str_replace('_', ' ', $this->voucher->type).' sebesar '.$this->voucher->value,
            ]);

        $audit->table()->associate($this->voucher);

        if(!$audit->save())
        {
            $result                     = new JSend('error', (array)$this->voucher, $audit->getError());
        }

        return $result;
    }
}
