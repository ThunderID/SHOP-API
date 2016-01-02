<?php

namespace App\Jobs\Auditors;

// change status
use App\Jobs\Job;

use App\Models\Price;
use App\Models\Auditor;

use App\Libraries\JSend;
use Illuminate\Contracts\Bus\SelfHandling;

use Carbon, Auth;

class SaveAuditPrice extends Job implements SelfHandling
{
    protected $price;

    public function __construct(Price $price)
    {
        $this->price                  = $price;
    }

    public function handle()
    {
        if(is_null($this->price->id))
        {
            throw new Exception('Sent variable must be object of a record.');
        }

        $result                             = new JSend('success', (array)$this->price);

        if(!Auth::check() || (Auth::check() && Auth::user()->id != $this->price->user_id))
        {
            $price                          = $this->price->price;

            if($this->price->promo_price!='' || $this->price->promo_price!=0)
            {
                $price                      = $this->price->promo_price;
            }

            $audit                          = new Auditor;

            $audit->fill([
                    'user_id'               => (Auth::check() ? Auth::user()->id : '0'),
                    'type'                  => 'price_changed',
                    'ondate'                => Carbon::now()->format('Y-m-d H:i:s'),
                    'event'                 => 'Perubahan harga produk '.$this->price->product->name.' menjadi '.$price,
                ]);
    
            $audit->table()->associate($this->price);

            if(!$audit->save())
            {
                $result                     = new JSend('error', (array)$this->price, $audit->getError());
            }
        }

        return $result;
    }
}
