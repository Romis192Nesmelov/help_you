<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageEvent;
use App\Http\Requests\Chats\ChatRequest;
use App\Http\Requests\Chats\ReadMessageRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use App\Http\Requests\Chats\MessageRequest;
use App\Models\MessageUser;
use App\Models\Order;
use App\Models\OrderUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatsController extends BaseController
{
    use HelperTrait;

    public function chats(): View
    {
        $this->data['active_left_menu'] = 'messages.chats';
        $this->data['chats'] = [
            'my_orders' => Order::where('approved',1)->where('status','>',0)->where('user_id',Auth::id())->whereIn('id',OrderUser::groupBy('order_id')->pluck('order_id')->toArray())->get(),
            'im_performer' => Order::where('approved',1)->where('status','>',0)->whereIn('id',OrderUser::where('user_id',Auth::id())->pluck('order_id')->toArray())->get()
        ];
        return $this->showView('chats');
    }

    public function chat(ChatRequest $request): View
    {
        $this->data['active_left_menu'] = 'messages.chats';
        $this->data['order'] = Order::find($request->input('order_id'));
        if ($unreadMessages = MessageUser::where('order_id',$this->data['order']->id)->where('user_id',Auth::id())->where('read',null)->get()) {
            foreach ($unreadMessages as $unreadMessage) {
                $unreadMessage->read = true;
                $unreadMessage->save();
            }
        }
        return $this->showView('chat');
    }

    public function newMessage(MessageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $message = Message::create($data);

        $this->setNewMessages($message);

        broadcast(new ChatMessageEvent($message));
        return response()->json(MessageResource::make($message)->resolve(), 200);
    }

    public function readMessage(ReadMessageRequest $request): JsonResponse
    {
        if ($unreadMessage = MessageUser::where('message_id', $request->input('message_id'))->where('user_id',Auth::id())->first()) {
            $unreadMessage->read = true;
            $unreadMessage->save();
        }
        return response()->json([],200);
    }

    public function getUnreadMessages(): JsonResponse
    {
        $unreadMessagesCounters = [];
        if ($unreadMessages = MessageUser::where('user_id',Auth::id())->where('read',0)->orderBy('order_id')->get()) {
            foreach ($unreadMessages as $unreadMessage) {
                if (!isset($unreadMessagesCounters['order'.$unreadMessage->order_id])) {
                    $unreadMessagesCounters['order'.$unreadMessage->order_id] = 1;
                } else $unreadMessagesCounters['order'.$unreadMessage->order_id]++;
            }
        }
        return response()->json(['unread' => $unreadMessagesCounters],200);
    }
}
