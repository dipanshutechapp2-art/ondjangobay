<?php

namespace App\Http\Resources\Merchant;

use App\Constants\PaymentGatewayConst;
use Illuminate\Http\Resources\Json\JsonResource;

class AddSubBalanceLogs extends JsonResource
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
            "success" =>      1,
            "pending" =>      2,
            "rejected" =>     3,
        ];
         if($this->attribute == PaymentGatewayConst::SEND){
            $field_type = 'deducted_amount';
            $operation_type = strtoupper("subtract") ;
         }else{
            $field_type = 'receive_amount';
            $operation_type = strtoupper("add");
         }
        return[
            'id' => $this->id,
            'trx' => $this->trx_id,
            'transaction_type' => $this->type,
            'operation_type' => $operation_type,
            'transaction_heading' => __("Balance Update From Admin")." (".$this->creator_wallet->currency->code.")",
            'request_amount' =>  get_transaction_numeric_attribute($this->attribute).getAmount($this->request_amount,get_wallet_precision()).' '.get_default_currency_code() ,
            'current_balance' => getAmount($this->available_balance,get_wallet_precision()).' '.get_default_currency_code(),
            $field_type => getAmount($this->payable,get_wallet_precision()).' '.get_default_currency_code(),
            'exchange_rate' => '1 ' .get_default_currency_code().' = '.getAmount($this->creator_wallet->currency->rate,get_wallet_precision()).' '.$this->creator_wallet->currency->code,
            'total_charge' => getAmount($this->charge->total_charge,get_wallet_precision()).' '.get_default_currency_code(),
            'remark' => $this->remark,
            'status' => $this->stringStatus->value,
            'date_time' => $this->created_at,
            'status_info' =>(object)$statusInfo,

        ];
    }
}
