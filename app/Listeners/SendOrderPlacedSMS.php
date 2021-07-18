<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\SmsService;
use App\Services\SmsTemplateService;

class SendOrderPlacedSMS
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPlaced  $event
     * @return void
     */
    public function handle(OrderPlaced $event)
    {
        $event->orderData->load(['seller:id,email,phone,name']);
        $template_id = config("constants.SMS.TEMPLATE.ORDER_PLACED.ID");
        $sms = SmsTemplateService::orderPlaced($event->orderData->seller->name,$event->orderData->order_no,'shop.doondukan.com');
        SmsService::sendSms($event->orderData->seller->phone,$sms,$template_id);
    }
}
