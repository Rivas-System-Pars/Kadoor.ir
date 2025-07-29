<?php

namespace App\Listeners\OrderCreated;

use App\Events\OrderCreated;

class ChangePrices
{
    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {
        if (!$event->order->hasPhysicalProduct()) {
            return;
        }

        foreach ($event->order->items as $item) {
            $price = $item->get_price;

            if ($price) {
                $price->createChange($price->price, $price->discount);
            }
        }
    }
}
