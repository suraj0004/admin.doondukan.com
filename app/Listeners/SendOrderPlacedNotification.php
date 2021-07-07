<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;

class SendOrderPlacedNotification implements ShouldQueue
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
        $event->orderData->load(['seller','buyer']);
        if(!empty($event->orderData->seller->email)) {
            Mail::to($event->orderData->seller->email)->send(new OrderPlacedMail($event->orderData));
        }
    }
}
