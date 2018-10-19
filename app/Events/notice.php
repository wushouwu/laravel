<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
class notice implements ShouldBroadcast
{
    use SerializesModels;

    public $notice;

    /**
     * 创建一个新的事件实例
     *
     * @return void
     */
    public function __construct(\App\Notice $notice)
    {
        $this->notice = $notice->toArray();
        $this->notice['role']=empty($notice->role)?explode(',',$notice->role):[];
        $this->notice['dept']=empty($notice->dept)?explode(',',$notice->dept):[];
        $this->notice['user']=empty($notice->user)?explode(',',$notice->user):[];
    }

    /**
     * 指定事件在哪些频道上进行广播
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return [new Channel('notice')];
    }
}