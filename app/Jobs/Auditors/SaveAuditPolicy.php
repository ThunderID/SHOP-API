<?php

namespace App\Jobs\Auditors;

// change status
use App\Jobs\Job;

use App\Models\StoreSetting;
use App\Models\Auditor;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon, Auth;

class SaveAuditPolicy extends Job implements SelfHandling
{
    protected $store;

    public function __construct(StoreSetting $store)
    {
        $this->store                  = $store;
    }

    public function handle()
    {
        if(is_null($this->store->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                             = new JSend('success', (array)$this->store);

        if(in_array($this->store->type, ['expired_cart', 'expired_paid', 'expired_shipped', 'expired_point', 'referral_royalty', 'invitation_royalty', 'limit_unique_number', 'expired_link_duration', 'first_quota', 'downline_purchase_bonus', 'downline_purchase_bonus_expired', 'downline_purchase_quota_bonus', 'voucher_point_expired']))
        {
            $audit                          = new Auditor;

            $audit->fill([
                    'user_id'               => (Auth::check() ? Auth::user()->id : '0'),
                    'type'                  => 'policy_changed',
                    'ondate'                => Carbon::now()->format('Y-m-d H:i:s'),
                    'event'                 => 'Perubahan Policy  '.str_replace('_', ' ', $this->store->type).' menjadi '.$this->store->value,
                ]);
    
            $audit->table()->associate($this->store);

            if(!$audit->save())
            {
                $result                         = new JSend('error', (array)$this->store, $audit->getError());
            }
        }

        return $result;
    }
}
