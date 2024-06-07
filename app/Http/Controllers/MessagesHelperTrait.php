<?php

namespace App\Http\Controllers;
use App\Actions\StripPhone;
use App\Actions\UnifyPhone;
use App\Events\ChatMessageEvent;
use App\Events\IncentivesEvent;
use App\Events\NotificationEvent;
use App\Jobs\SendMessage;
use App\Models\Action;
use App\Models\ActionUser;
use App\Models\InformingOrder;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use GreenSMS\GreenSMS;

trait MessagesHelperTrait
{
    public function checkOrdersInProgress(): void
    {
        $dayToCheck = Carbon::now()->subDays(7);
        $ordersInProgress = Order::where('status',1)->whereDate('created_at','<=',$dayToCheck)->get();
        foreach ($ordersInProgress as $order) {
            $this->checkAndSendInforming($order, trans('content.to_over_order'), 0);
        }
    }

    public function checkAndSendInforming(Order $order, string $message, int $checkingTime): void
    {
        $lastMessage = InformingOrder::where('message',$message)->where('order_id',$order->id)->orderBy('created_at','desc')->first();
        if (!$lastMessage || time() >= $lastMessage->created_at->timestamp + $checkingTime) {
            $this->chatMessage($order, $message);
            InformingOrder::create([
                'message' => $message,
                'order_id' => $order->id
            ]);
        }
    }

    public function checkRegistrationAward(): void
    {
        $usersIds = User::query()
            ->where('active',1)
            ->where('created_at','<=', Carbon::now()->subDay())
            ->pluck('id');

        foreach ($usersIds as $userId) {
            $this->setIncentive(2, $userId);
        }
    }

    public function chatMessage(Order $order, string $message): void
    {
        $message = Message::create([
            'body' => $message,
            'user_id' => 1,
            'order_id' => $order->id
        ]);
        broadcast(new ChatMessageEvent($message));
        $this->setNewMessages($message);
    }

    public function setNewMessages(Message $message): void
    {
        if (!Auth::check() || Auth::id() != $message->order->user_id) {
            MessageUser::create([
                'message_id' => $message->id,
                'user_id' => $message->order->user_id,
                'order_id' => $message->order_id,
            ]);
            broadcast(new NotificationEvent('new_message', $message->order, $message->order->user_id));
            $this->mailOrderNotice($message->order, $message->order->userCredentials, 'new_message_notice');
        }

        foreach ($message->order->performers as $performer) {
            if (!Auth::check() || Auth::id() != $performer->id) {
                MessageUser::create([
                    'message_id' => $message->id,
                    'user_id' => $performer->id,
                    'order_id' => $message->order_id,
                ]);
                broadcast(new NotificationEvent('new_message', $message->order, $performer->id));
                $this->mailOrderNotice($message->order, $performer, 'new_message_notice');
            }
        }
    }

    public function setIncentive(int $actionRating, int $userId): void {
        $actionId = Action::query()
            ->where('rating',$actionRating)
            ->where('start','<=',Carbon::now())
            ->where('end','>=',Carbon::now()->addDays(7))
            ->pluck('id')
            ->first();

        $alreadyAwarded = false;
        $user = User::find($userId);

        foreach ($user->incentives as $incentive) {
            if ($incentive->rating == $actionRating) {
                $alreadyAwarded = true;
                break;
            }
        }

        if ($actionId && !$alreadyAwarded) {
            $this->createNewIncentive($user, $actionId);
        }
    }

    /**
     * @throws \Exception
     */
    public function sendSms(string $phone, string $text): void
    {
        $stripPhone = new StripPhone();
        $client = new GreenSMS(['user' => env('GREENSMS_LOGIN'), 'pass' => env('GREENSMS_PASSWORD')]);
        $response = $client->sms->send(['to' => $stripPhone->handle($phone), 'txt' => $text]);
//        return $response->request_id;
    }

    public function sendMessage(string $template, string $mailTo, string|null $cc, array $fields, string|null $pathToFile=null)
    {
        dispatch(new SendMessage($template, $mailTo, $cc, $fields));
    }

    public function mailOrderNotice(Order $order, User $user, string $template): void
    {
        if ($user->email && $user->mail_notice) {
            $this->sendMessage($template, $user->email, null, ['order' => $order]);
        }
    }

    public function createNewIncentive(User $user, int $actionId): void
    {
        $incentive = ActionUser::create([
            'action_id' => $actionId,
            'user_id' => $user->id,
            'active' => 1
        ]);
        broadcast(new IncentivesEvent('new_incentive', $incentive, $user->id));
        if ($user->email && $user->mail_notice) {
            $this->sendMessage('new_award_notice', $user->email, null, ['action' => $incentive->action]);
        }
    }
}
