<?php 

namespace App\Models\Traits\Calculations;

use App\Models\PointLog;
use App\Models\StoreSetting;

/**
 * Function to do calculations for payment
 *
 * @author cmooy
 */
trait HasPaidTrait 
{
    /**
     * Check payment.
     *
     * @param $model (of transaction), $amount
     * @return boolean
     */
    public function CheckPaid($transaction, $amount)
    {
        if($amount == $transaction->bills)
        {
        	return true;
        }
        elseif($amount > $transaction->bills)
        {
        	$this->errors 					= "Pembayaran berlebih sebesar ".($amount - $transaction->bills);
 
        	return false;
        }
        elseif($amount < $transaction->bills)
        {
        	$this->errors 					= "Pembayaran kurang sebesar ".($transaction->bills - $amount);

        	return false;
        }

		return true;
    }

    /**
     * Revert payment.
     *
     * @param model transaction
     * @return boolean
     */
    public function RevertPoint($transaction)
    {
        foreach ($transaction->pointlogs as $key => $value) 
        {
            if($value->amount < 0)
            {
                $point                      = new PointLog;
                $point->fill([
                        'user_id'           => $value->user_id,
                        'point_log_id'      => $value->id,
                        'amount'            => 0 - $value->amount,
                        'expired_at'        => $value->expired_at,
                        'notes'             => 'Revert Belanja #'.$transaction->ref_number,
                    ]);
        
                $point->reference()->associate($transaction);

                if(!$point->save())
                {
                    $this->errors                   = $point->getError();
                    
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Add quota for upline.
     *
     * @param model transaction
     * @return boolean
     */
    public function AddQuotaForUpline($transaction)
    {
        $upline                             = PointLog::userid($transaction->user_id)->referencetype('App\Models\User')->first();
        
        $quota                              = StoreSetting::type('downline_purchase_quota_bonus')->Ondate('now')->first();

        $whoisupline                        = 0;

        if($upline && $upline->reference()->count())
        {
            $whoisupline                    = $upline->reference->voucher->value;
        }

        if($upline && $quota && $whoisupline == 0)
        {
            $quotalog                       = new QuotaLog;

            $quotalog->fill([
                    'voucher_id'            => $upline->reference->voucher->id,
                    'amount'                => $quota->value,
                    'notes'                 => 'Bonus belanja '.$transaction->user->name.' nomor nota #'.$transaction->ref_number,
                ]);

            if(!$quotalog->save())
            {
                $this->errors               = $quotalog->getError();
                    
                return false;
            }
        }

        return true;
    }

    /**
     * Add point for upline.
     *
     * @param model transaction
     * @return boolean
     */
    public function AddPointForUpline($transaction)
    {
        $upline                             = PointLog::userid($transaction->user_id)->referencetype('App\Models\User')->first();

        $point                              = StoreSetting::type('downline_purchase_bonus')->Ondate('now')->first();

        $expired                            = StoreSetting::type('downline_purchase_bonus_expired')->Ondate('now')->first();


        $whoisupline                        = 0;

        if($upline && $upline->reference()->count())
        {
            $whoisupline                    = $upline->reference->voucher->value;
        }
        

        if($upline && $point && $expired  && $whoisupline == 0)
        {
            $pointlog                       = new PointLog;

            $pointlog->fill([
                    'user_id'               => $upline->reference_id,
                    'amount'                => $point->value,
                    'expired_at'            => date('Y-m-d H:i:s', strtotime($transaction->transact_at.' '.$expired->value)),
                    'notes'                 => 'Bonus belanja '.$transaction->user->name
                ]);

            $pointlog->reference()->associate($transaction);

            if(!$pointlog->save())
            {
                $this->errors               = $pointlog->getError();

                return false;
            }
        }

        return true;
    }

    /**
     * Credit point.
     *
     * @param model transaction
     * @return boolean
     */
    public function CreditPoint($transaction)
    {
        //cek all  in debit active point
        $points                             = PointLog::userid($transaction->user_id)->onactive('now')->debit(true)->get();

        //count leftover active point
        $sumpoints                          = PointLog::userid($transaction->user_id)->onactive('now')->sum('amount');

        $idx                                = 0;
        $currentamount                      = 0;
        $transactionamount                  = $transaction->amount;

        while($transactionamount <= $transaction->amount && $points && isset($points[$idx]) && $transactionamount > 0)
        {
            //count left over point per debit to credit
            $currentamount                  = $points[$idx]['amount'];

            foreach($points[$idx]->pointlogs as $key => $value)
            {
                $currentamount              = $currentamount + $value['amount'];
            }

            //if leftover more than 0
            if($currentamount > 0 && $currentamount >= $transactionamount)
            {
                $camount                    = 0 - $transactionamount;
            }
            else
            {
                $camount                    = 0 - $currentamount;
            }

            if($currentamount > 0)
            {
                $point                      = new PointLog;
                $point->fill([
                        'user_id'           => $points[$idx]->user_id,
                        'point_log_id'      => $points[$idx]->id,
                        'amount'            => $camount,
                        'expired_at'        => $points[$idx]->expired_at,
                        'notes'             => 'Pembayaran Belanja #'.$transaction->ref_number,
                    ]);

                $point->reference()->associate($transaction);

                if(!$point->save())
                {
                    $this->errors               = $point->getError();
                    
                    return false;
                }

                $transactionamount           = $transactionamount + $camount;
            }

            $idx++;
        }

        return true;
    }
}