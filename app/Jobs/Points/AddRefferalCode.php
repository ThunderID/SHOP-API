<?php

namespace App\Jobs\Points;

// check all quantity
use App\Jobs\Job;

use App\Models\User;
use App\Models\Referral;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;

class AddRefferalCode extends Job implements SelfHandling
{
    use DispatchesJobs;

    protected $user;

    public function __construct(User $user)
    {
        $this->user							= $user;
    }

    public function handle()
    {
        if(is_null($this->user->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result								= $this->dispatch(new GenerateRefferalCode($this->user));

		if($result->getStatus()=='success')
        {
	    	$newvoucher						= new Referral;
	    	$newvoucher->fill([
	    		'user_id'					=> $this->user->id,
				'code'		    			=> $result->getData()['referral'],
				'type'						=> 'referral',
				'value'						=> 0,
	            'started_at'				=> null,
				'expired_at'				=> null,
	    		]);

            if(!$newvoucher->save())
            {
                $result                     = new JSend('error', (array)$this->user, $newvoucher->getError());
            }
            else
            {
                $result                     = new JSend('success', (array)$newvoucher['attributes']);
            }
        }


        return $result;
    }
}
