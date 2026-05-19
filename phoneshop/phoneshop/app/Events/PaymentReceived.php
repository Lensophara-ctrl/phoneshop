<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $amount;
    public $billNo;
    public $soundFile;

    public function __construct($soundFile, $amount, $billNo = null)
    {
        $this->soundFile = $soundFile;
        $this->amount = $amount;
        $this->billNo = $billNo;
    }

    public function broadcastOn()
    {
        return new Channel('payments');
    }

    public function broadcastAs()
    {
        return 'payment.received';
    }
}
