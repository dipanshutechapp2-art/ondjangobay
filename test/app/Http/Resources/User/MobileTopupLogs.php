<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileTopupLogs extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $statusInfo = [
            "success"       => 1,
            "pending"       => 2,
            "hold"          => 3,
            "rejected"      => 4,
            "waiting"       => 5,
            "failed"        => 6,
            "processing"    => 7,
        ];
        return [
            'id' => $this->id,
            'trx' => $this->trx_id,
            'transaction_type' => $this->type,
            'request_amount' => getAmount($this->request_amount,get_wallet_precision()).' '.topUpCurrency($this)['destination_currency'] ,
            'exchange_rate'  => topUpExchangeRate($this)['exchange_info'],
            'payable' => getAmount($this->payable,get_wallet_precision()).' '.topUpCurrency($this)['wallet_currency'],
            'topup_type' => $this->details->topup_type_name,
            'mobile_number' =>$this->details->mobile_number,
            'total_charge' => getAmount($this->charge->total_charge,get_wallet_precision()).' '.topUpCurrency($this)['wallet_currency'],
            'current_balance' => getAmount($this->available_balance,get_wallet_precision()).' '.topUpCurrency($this)['wallet_currency'],
            'status' => $this->stringStatus->value,
            'status_value' => $this->status,
            'date_time' => $this->created_at,
            'status_info' =>(object)$statusInfo,
            'rejection_reason' =>$this->reject_reason??"",
        ];
    }
}
