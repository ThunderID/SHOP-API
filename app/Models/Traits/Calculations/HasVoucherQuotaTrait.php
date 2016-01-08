<?php 

namespace App\Models\Traits\Calculations;

/**
 * Function to do calculations
 *
 * @author cmooy
 */
trait HasVoucherQuotaTrait 
{
    /**
     * Do credit quota of voucher.
     *
     * @param model voucher, message
     * @return boolean
     */
    public function CreditQuota($voucher, $message)
    {
		$quotalog                       = new App\Models\QuotaLog;

		$quotalog->fill([
		        'voucher_id'            => $voucher->id,
		        'amount'                => -1,
		        'notes'                 => $message,
		    ]);

		if(!$quotalog->save())
		{
			$this->errors	 			= $quotalog->getError();
			
			return false;
		}

		return true;
    }
}