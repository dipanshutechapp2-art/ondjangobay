<?php

namespace Database\Seeders\Admin;

use App\Models\VirtualCardApi;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VirtualApiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $virtual_card_apis = array(
            array('admin_id' => '1','image' => 'seeder/virtual-card.png','card_details' => 'This card is property of QRPay, Wonderland. Misuse is criminal offence. If found, please return to QRPay or to the nearest bank.','config' => '{"flutterwave_secret_key":"FLWSECK_TEST-SANDBOXDEMOKEY-X","flutterwave_url":"https:\/\/api.flutterwave.com\/v3","sudo_api_key":"eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfaWQiOiI2NTY3MDBiYmQxNTQwNzYyMzA1ZWUyNjMiLCJlbWFpbEFkZHJlc3MiOiJ1c2VyM0BhcHBkZXZzLm5ldCIsImp0aSI6IjY1NjcwMTc3ZDE1NDA3NjIzMDVlZWIxNyIsIm1lbWJlcnNoaXAiOnsiX2lkIjoiNjU2NzAwYmJkMTU0MDc2MjMwNWVlMjY2IiwiYnVzaW5lc3MiOnsiX2lkIjoiNjU2NzAwYmJkMTU0MDc2MjMwNWVlMjYxIiwibmFtZSI6IkFwcERldnMiLCJpc0FwcHJvdmVkIjpmYWxzZX0sInVzZXIiOiI2NTY3MDBiYmQxNTQwNzYyMzA1ZWUyNjMiLCJyb2xlIjoiQVBJS2V5In0sImlhdCI6MTcwMTI0OTM5OSwiZXhwIjoxNzMyODA2OTk5fQ.oB0i1Hn_MMLM3tZpbAEqU6YlDIqtk_yJT25EGhE021E","sudo_vault_id":"tntbuyt0v9u","sudo_url":"https:\/\/api.sandbox.sudo.cards","sudo_mode":"sandbox","stripe_public_key":"pk_test_51NjGM4K6kUt0AggqD10PfWJcB8NxJmDhDptSqXPpX2d4Xcj7KtXxIrw1zRgK4jI5SIm9ZB7JIhmeYjcTkF7eL8pc00TgiPUGg5","stripe_secret_key":"sk_test_51NjGM4K6kUt0Aggqfejd1Xiixa6HEjQXJNljEwt9QQPOTWoyylaIAhccSBGxWBnvDGw0fptTvGWXJ5kBO7tdpLNG00v5cWHt96","stripe_url":"https:\/\/api.stripe.com\/v1","stripe_mode":"sandbox","strowallet_public_key":"R67MNEPQV2ABQW9HDD7JQFXQ2AJMMY","strowallet_secret_key":"AOC963E385FORPRRCXQJ698C1Q953B","strowallet_url":"https:\/\/strowallet.com\/api\/bitvcard\/","strowallet_mode":"sandbox","cardyfie_public_key":"pub_tNRjVnabfDYNrlnT9pA3TAgS2QKbsk2KmDSesu64GQCVfz5mPkCccTjFOtGeOoz3Un6CH9anfxsKxmTVtvy9y2riu8oimmMwxiUhcdKuCKZDCueE3qCdXUcp","cardyfie_secret_key":"sec_hJeg1ie1viG1nTs9ej8ZeJM9BZRpQx8rPJ9OtdZy6esfuDz1vn1Ev4zT9Zy1RA9M2uh6SMyjpdS1WUqFpp2UxuWOBAag2naWnYKAHCRZDxwkONF3gpciaMdX","cardyfie_sandbox_url":"https:\/\/core.cardyfie.com\/api\/sandbox\/v1","cardyfie_production_url":"https:\/\/core.cardyfie.com\/api\/production\/v1","cardyfie_webhook_secret":"w_sec_6pdJhloUi21SECG3wqVMNZcQaxmEAQmcApoxgn3nfDomx2DEtZVvYf7YXVs1ezpve6qnrxtjHDvZ9EcOTr3vdbTtMG6royDS3s9p6mdjqv7MphbvKMTFWivb","cardyfie_universal_card_issues_fee":"3.00","cardyfie_platinum_card_issues_fee":"5.00","cardyfie_card_deposit_fixed_fee":"1.00","cardyfie_card_deposit_percent_fee":"1","cardyfie_card_withdraw_fixed_fee":"1.00","cardyfie_card_withdraw_percent_fee":"1","cardyfie_card_maintenance_fixed_fee":"0.00","cardyfie_min_limit":"5.00","cardyfie_max_limit":"500.00","cardyfie_daily_limit":"5000.00","cardyfie_monthly_limit":"10000.00","cardyfie_mode":"sandbox","name":"cardyfie"}','created_at' => now(),'updated_at' => now())
          );
        VirtualCardApi::insert($virtual_card_apis);
    }
}
