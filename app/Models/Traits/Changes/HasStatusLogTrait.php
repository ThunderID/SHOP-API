<?php 

namespace App\Models\Changes\Calculations;

/**
 * Function to do change transaction log status
 *
 * @author cmooy
 */
trait HasStatusLogTrait 
{
    /**
     * change transaction log (status) of transaction
     * 
     * @param model of transaction, status, notes
     * @return boolean, error message saved to models
     */ 
    public function ChangeStatus($transaction, $status, $notes) 
    {
        $logs                   = new TransactionLog;
        $params                 =   [
                                        'transaction_id'    => $transaction['id'],
                                        'status'            => $status,
                                        'notes'             => $notes,
                                        'changed_at'        => Carbon::now()->format('Y-m-d H:i:s'),
                                    ];

        $logs->fill($params);

        if($logs->save())
        {
            return true;
        }
        else
        {
            $this->errors   = $logs->getError();
            
            return false;
        }
    }
}