<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\OrderPlaced;
use App\Listeners\SendOrderPlacedNotification;
use App\Listeners\SendOrderPlacedSMS;

use App\Events\OrderConfirmed;
use App\Listeners\SendOrderConfirmedEmail;
use App\Listeners\SendOrderConfirmedSMS;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderPlaced::class => [
            SendOrderPlacedNotification::class,
            SendOrderPlacedSMS::class,
        ],
        OrderConfirmed::class => [
            SendOrderConfirmedEmail::class,
            SendOrderConfirmedSMS::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
