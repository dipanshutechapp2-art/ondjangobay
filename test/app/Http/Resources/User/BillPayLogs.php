<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class BillPayLogs extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
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
            'request_amount' => getAmount($this->request_amount,get_wallet_precision()).' '.billPayCurrency($this)['sender_currency'],
            'payable' => getAmount($this->payable,get_wallet_precision()).' '.billPayCurrency($this)['wallet_currency'],
            'bill_type' =>$this->details->bill_type_name,
            'bill_month' =>$this->details->bill_month??"",
            'bill_number' =>$this->details->bill_number,
            'total_charge' => getAmount($this->charge->total_charge,get_wallet_precision()).' '.billPayCurrency($this)['wallet_currency'],
            'current_balance' => getAmount($this->available_balance,get_wallet_precision()).' '.billPayCurrency($this)['wallet_currency'],
            'status' => $this->stringStatus->value,
            'date_time' => $this->created_at,
            'status_info' =>(object)$statusInfo,
            'status_value' => $this->status,
            'rejection_reason' =>$this->reject_reason??"",

        ];

    }
}
