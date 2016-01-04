<?php

namespace App\Jobs\Points;

use App\Jobs\Job;
use App\Models\User;
use App\Models\Voucher;
use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;

class GenerateRefferalCode extends Job implements SelfHandling
{
    use DispatchesJobs;

    protected $user;

    public function __construct(User $user)
    {
        $this->user             			= $user;
    }

    public function handle()
    {
        // checking
		$letters 							= 'abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        //check is user doesnt have refferal code yet
        if(!is_null($this->user->referral_code))
        {
            $result							= new JSend('error', (array)$this->user, $this->user->name.' sudah memiliki referral code');
        }
        else
        {
            do
            {
    			$names							= explode(' ', $this->user->name);
    			$fnames 						= [];
    			$lnames 						= [];
    			$lostcode 						= [];
            	if(isset($names[0]))
            	{
    				$fname 						= str_split($names[0]);

    				foreach ($fname as $key => $value) 
    				{
    					if($key <= 2)
    					{
    						$fnames[$key]		= $value;
    					}
    				}
            	}

            	if(count($fnames) < 3)
            	{
            		foreach (range((count($fnames)-1), 2) as $key) 
            		{
            			$fnames[$key] 			= substr(str_shuffle($letters), 0, 1);
            		}
            	}

            	if(isset($names[count($names)-1]))
            	{
    				$lname 						= str_split($names[count($names)-1]);
    				foreach ($lname as $key => $value) 
    				{
    					if($key <= 2)
    					{
    						$lnames[$key]		= $value;
    					}
    				}
            	}

            	if(count($lnames) < 3)
            	{
            		foreach (range((count($lnames)-1), 2) as $key) 
            		{
            			$lnames[$key] 			= substr(str_shuffle($letters), 0, 1);
            		}
            	}

            	foreach (range(0, 1) as $key) 
        		{
        			$lostcode[$key] 			= substr(str_shuffle($letters), 0, 1);
        		}

        		$lcode 							= implode('', $lnames);
        		$fcode 							= implode('', $fnames);
        		$locode 						= implode('', $lostcode);

    			$referral_code 		            = strtolower($fcode.$lcode.$locode);
 
                $referral                       = User::referralcode($fcode.$lcode.$locode)->first();
            }
            while($referral);

			$result         				     = new JSend('success', ['referral' => $referral_code]);
        }

        return $result;
    }
}