<?php

namespace App\Jobs\Points;

// check all quantity
use App\Jobs\Job;

use App\Models\User;
use App\Models\QuotaLog;
use App\Models\StoreSetting;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

class AddQuotaRegistration extends Job implements SelfHandling
{
    protected $user;
    protected $voucher;

    public function __construct(User $user, $voucher)
    {
        $this->user                  = $user;
        $this->voucher               = $voucher;
    }

    public function handle()
    {
        if(is_null($this->user->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                     = new JSend('success', (array)$this->user);

        $quota 						= StoreSetting::type('first_quota')->Ondate('now')->first();

        if(!$quota)
        {
	        $result					= new JSend('error', (array)$this->user, 'Tidak dapat melakukan registrasi saat ini.');
        }
        else
        {
        	$newquota 				= new QuotaLog;
        	$newquota->fill([
        		'voucher_id'		=> $this->voucher['id'],
				'amount'			=> $quota->value,
				'notes'				=> 'Hadiah registrasi',
        		]);

        	if(!$newquota->save())
        	{
                $result             = new JSend('error', (array)$this->user, $newquota->getError());
            }
        }

        return $result;
    }
}
