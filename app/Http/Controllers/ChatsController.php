<?php

namespace App\Http\Controllers;

use App\Actions\ProcessingImage;
use App\Actions\SetReadUnread;
use App\Events\ChatMessageEvent;
use App\Http\Requests\Chats\ChatRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use App\Http\Requests\Chats\MessageRequest;
use App\Models\MessageKeyword;
use App\Models\MessageUser;
use App\Models\Order;
use App\Models\OrderUser;
use App\Models\ReadPerformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatsController extends BaseController
{
    use MessagesHelperTrait;

    private array $keywords;

    public function __construct()
    {
        $this->keywords = MessageKeyword::pluck('phrase')->toArray();
    }

    public function chats(): View
    {
        $this->data['active_left_menu'] = 'my_chats';
        return $this->showView('chats');
    }

    public function chatsMyOrders(): JsonResponse
    {
        return response()->json([
            'orders' => Order::query()
                ->where('status',1)
                ->where('user_id',Auth::id())
                ->whereIn('id',OrderUser::groupBy('order_id')->pluck('order_id')->toArray())
                ->with('user.ratings')
                ->with('performers.ratings')
                ->with('orderType')
                ->orderByDesc('created_at')
                ->paginate(4)
        ],200);
    }

    public function chatsPerformer(): JsonResponse
    {
        return response()->json([
            'orders' => Order::query()
                ->where('status',1)
                ->whereIn('id',OrderUser::where('user_id',Auth::id())->pluck('order_id')->toArray())
                ->with('user.ratings')
                ->with('performers.ratings')
                ->with('orderType')
                ->orderByDesc('created_at')
                ->paginate(4)
        ],200);
    }

    public function chat(ChatRequest $request, SetReadUnread $actionSetReadUnread): View
    {
        $this->data['active_left_menu'] = 'my_chats';
        $this->data['order'] = Order::query()
            ->with('user.ratings')
            ->with('performers.ratings')
            ->with('orderType')
            ->with('subType')
            ->with('images')
            ->with('messages.user')
            ->where('id',$request->input('id'))
            ->first();
        if ($this->data['order']->status != 1) abort (403);
        $actionSetReadUnread->handle(new ReadPerformer());
		$this->setReadAllMessagesInChatForUser($this->data['order']->id);
        return $this->showView('chat');
    }

    public function newMessage(
        MessageRequest $request,
        ProcessingImage $processingImage
    ): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $pathToImage = 'images/messages_images/';
            do {
                $imageName = Str::random(32);
            } while (file_exists(base_path('public/'.$pathToImage.$imageName.'.'.$request->file('image')->getClientOriginalExtension())));
            $data = $processingImage->handle($request, $data,'image', $pathToImage, $imageName);
        }

        $message = Message::create($data);
        $this->setNewMessages($message);

        broadcast(new ChatMessageEvent($message));

        foreach ($this->keywords as $phrase) {
            if (preg_match('/'.$phrase.'/ui',$message->body))
                $this->checkAndSendInforming($message->order, trans('content.to_over_order'), (60 * 60 * 24));
        }

        return response()->json(MessageResource::make($message)->resolve(), 200);
//        return response()->json(['data' => $data], 200);
    }

    public function readMessage(ChatRequest $request): JsonResponse
    {
        $this->setReadAllMessagesInChatForUser($request->input('id'));
        return response()->json([],200);
    }

    public function getUnreadMessages(): JsonResponse
    {
        $unreadMessagesCounters = [];
        if ($unreadMessages = MessageUser::where('user_id',Auth::id())->where('read',null)->get()) {
            $orderIds = [];
            foreach ($unreadMessages as $unreadMessage) {
                $key = array_search($unreadMessage->order_id,$orderIds);
                if ($key === false) {
                    $orderIds[] = $unreadMessage->order_id;
                    $unreadMessagesCounters[] = ['order' => $unreadMessage->order, 'count' => 1];
                } else $unreadMessagesCounters[$key]['count']++;
            }
        }
        return response()->json(['unread' => $unreadMessagesCounters],200);
    }

    private function setReadAllMessagesInChatForUser(int $orderId): void
    {
        MessageUser::where('order_id',$orderId)->where('user_id',Auth::id())->where('read',null)->update(['read' => true]);
    }
}
