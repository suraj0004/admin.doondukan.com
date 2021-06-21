<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\SmsService;
use App\Services\SmsTemplateService;

class SendOrderConfirmedSMS
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
     * @param  OrderConfirmed  $event
     * @return void
     */
    public function handle(OrderConfirmed $event)
    {
        $template_id = config("constants.SMS.TEMPLATE.ORDER_CONFIRMED.ID");
        $sms = SmsTemplateService::orderConfirmed(
            $event->order->buyer->name,
            $event->order->seller->name,
            $event->order->order_no
        );
        SmsService::sendSms($event->order->buyer->phone, $sms, $template_id);
    }
}
