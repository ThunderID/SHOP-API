<?php 

namespace App\Models\Traits\Calculations;

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
     * @param model transaction, amount
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
}