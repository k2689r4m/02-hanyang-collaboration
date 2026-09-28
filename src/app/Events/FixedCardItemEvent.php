<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Item;

class FixedCardItemEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $channelId;
    public $item;
    public $type;
    public $cardId;
    public $itemsNum;
    /////
    public $channelType;

    public function __construct($item, $cardId, $itemsNum, $channelId, $type, $channelType)
    {
        $this->channelId = $channelId;
        $this->type = $type; //'create'
        $this->item = $item;
        $this->cardId = $cardId;
        $this->itemsNum = $itemsNum;
        /////
        $this->channelType = $channelType;
    }

//    public function __construct(Item $item, $cardId, $itemsNum, $classObjectId, $type)
//    {
//        $this->classObjectId = $classObjectId;
//        $this->type = $type; //'create'
//        $this->item = $item;
//        $this->cardId = $cardId;
//        $this->itemsNum = $itemsNum;
//    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel($this->channelType.'_'.$this->channelId);
    }

    public function broadcastAs()
    {
        return 'fixedCardItemEvent.'.$this->type;
    }
}
