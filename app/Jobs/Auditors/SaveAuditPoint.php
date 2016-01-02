<?php

namespace App\Jobs\Auditors;

// change status
use App\Jobs\Job;

use App\Models\PointLog;
use App\Models\Auditor;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon, Auth;

class SaveAuditPoint extends Job implements SelfHandling
{
    protected $pointlog;

    public function __construct(PointLog $pointlog)
    {
        $this->pointlog                  = $pointlog;
    }

    public function handle()
    {
        if(is_null($this->pointlog->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                             = new JSend('success', (array)$this->pointlog);

        if((Auth::check() || (Auth::check() && Auth::user()->id != $this->pointlog->user_id)) && $this->pointlog->reference_id == 0)
        {
            $audit                          = new Auditor;

            $audit->fill([
                    'user_id'               => (Auth::check() ? Auth::user()->id : '0'),
                    'type'                  => 'point_added',
                    'ondate'                => Carbon::now()->format('Y-m-d H:i:s'),
                    'event'                 => 'Penambahan balin point sebesar '.$this->pointlog->amount.' untuk '.$this->pointlog->user->name,
                ]);

            $audit->table()->associate($this->pointlog);

            if(!$audit->save())
            {
                $result                         = new JSend('error', (array)$this->pointlog, $audit->getError());
            }
        }

        return $result;
    }
}
