<?php

namespace App\Features\Checkout\Messages;

use App\Classes\Messages\AbstractMessageHandler;
use App\Features\Checkout\Handlers\ListDeliveryMethodsHandler;


class OrderPrepareChangeMessageHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->user->state->order->change = $this->text;
        $this->user->save();

        $handler = new ListDeliveryMethodsHandler($this->user, $this->api);
        $handler->make($this->update, []);

        $this->user->state->message_handler = null;
        $this->user->save();
    }
}


