<?php

namespace App\Jobs\Points;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Models\Transaction;

use App\Libraries\JSend;
use Validator;

class CountVoucherDiscount extends Job implements SelfHandling
{
    use DispatchesJobs;

    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction                  = $transaction;
    }

    public function handle()
    {
        $result                             = new JSend('success', (array)$this->transaction);
        
        $voucherrules                       = ['started_at' => 'before:now', 'expired_at' => 'after:now'];

        if($this->transaction->voucher()->count() && $this->transaction->status=='paid')
        {
            switch($this->transaction->voucher->type)
            {
                case 'debit_point' :
                    $result                                 = $this->dispatch(new DebitPoint($this->transaction, $this->transaction->voucher->value));
                break;
                default :
                break;
            }
        }
        elseif($this->transaction->voucher()->count())
        {
            $validator                      = Validator::make($this->transaction->voucher['attributes'], $voucherrules);

            if (!$validator->passes())
            {
                return new JSend('error', (array)$this->transaction, 'Voucher tidak dapat digunakan.');
            }

            switch($this->transaction->voucher->type)
            {
                case 'free_shipping_cost' :
                    $this->transaction->voucher_discount    = (!is_null($this->transaction->shipping_cost) ? $this->transaction->shipping_cost : 0);
                break;
                case 'referral' :
                    $result                                 = new JSend('error', (array)$this->transaction, 'Tidak dapat menggunakan kode referral sebagai voucher.');
                break;
                case 'debit_point' :
                    $result                                 = new JSend('success', (array)$this->transaction);
                break;
                default :
                    $result                                 = new JSend('error', (array)$this->transaction, 'Voucher tidak terdaftar.');
                break;
            }
        }

        return $result;
    }
}
