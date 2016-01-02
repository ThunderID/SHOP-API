<?php

namespace App\Jobs\Auditors;

// change status
use App\Jobs\Job;

use App\Models\QuotaLog;
use App\Models\Auditor;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon, Auth;

class SaveAuditQuota extends Job implements SelfHandling
{
    protected $quota;

    public function __construct(QuotaLog $quota)
    {
        $this->quota                  = $quota;
    }

    public function handle()
    {
        if(is_null($this->quota->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                             = new JSend('success', (array)$this->quota);

        if($this->quota->voucher()->count())
        {
            $audit                          = new Auditor;

            $audit->fill([
                    'user_id'               => (Auth::check() ? Auth::user()->id : '0'),
                    'type'                  => 'quota_added',
                    'ondate'                => Carbon::now()->format('Y-m-d H:i:s'),
                    'event'                 => 'Penambahan quota sebesar '.$this->quota->amount.' voucher '.$this->quota->voucher->code,
                ]);

            $audit->table()->associate($this->quota->voucher);

            if(!$audit->save())
            {
                $result                     = new JSend('error', (array)$this->quota, $audit->getError());
            }
        }

        return $result;
    }
}
