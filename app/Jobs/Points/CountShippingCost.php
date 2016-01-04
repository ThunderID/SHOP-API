<?php

namespace App\Jobs\Points;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StoreSetting;

use App\Libraries\JSend;


class CountShippingCost extends Job implements SelfHandling
{

    Protected $transactiondetail;
    Protected $shipping_cost;

    public function __construct($transactiondetail, $shipping_cost)
    {
        $this->transactiondetail                = $transactiondetail;
        $this->shipping_cost                    = $shipping_cost;
    }

    public function handle()
    {
        $qty                                    = 0;
        foreach ($this->transactiondetail as $key => $value) 
        {
            $qty                                = $qty + $value['quantity'];
        }

        $default                                = StoreSetting::type('item_for_one_package')->ondate('now')->orderby('created_at', 'desc')->first();

        if(!$default)
        {
            return new JSend('error', (array)$this->transactiondetail, 'Tidak ada batas kirim minimum.');
        }

        $shipping_cost                          = $this->shipping_cost * ceil($qty/$default->value);

        return new JSend('success', ['shipping_cost' => $shipping_cost]);
    }
}
