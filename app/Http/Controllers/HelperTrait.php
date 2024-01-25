<?php

namespace App\Http\Controllers;
use App\Events\NotificationEvent;
use App\Jobs\SendMessage;
use App\Models\InformingOrder;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\Order;
use App\Models\ReadRemovedPerformer;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

trait HelperTrait
{
    public string $validationPhone = 'regex:/^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/';
    public string $validationBorn = 'required|regex:/^((\d){2}-(\d){2}-([1-2]\d\d\d))$/';
    public string $validationPassword = 'required|min:3|max:20';
    public string $validationPasswordConfirmed = 'required|confirmed|min:6|max:20';
    public string $validationCode = 'required|regex:/^(([0-9]{2})\-([0-9]{2})-([0-9]{2}))$/';
    public string $validationInteger = 'required|integer';
    public string $validationNumeric = 'required|numeric';
    public string $validationString = 'required|min:3|max:255';
    public string $validationText = 'nullable|min:1|max:3000';
    public string $validationLongText = 'required|min:5|max:50000';
    public string $validationColor = 'regex:/^(hsv\((\d+)\,\s(\d+)\%\,\s(\d+)\%\))$/';
    public string $validationSvg = 'required|mimes:svg|max:10';
    public string $validationJpgAndPng = 'mimes:jpg,png|max:2000';
    public string $validationJpgAndPngSmall = 'mimes:jpg,png|max:300';
    public string $validationJpg = 'mimes:jpg|max:2000';
    public string $validationPng = 'mimes:png|max:2000';
    public string $validationDate = 'regex:/^(\d{2})\/(\d{2})\/(\d{4})$/';
    public string $validationOrderId = 'required|exists:orders,id';
    public string $validationUserId = 'required|exists:users,id';

//    public function saveCompleteMessage(): void
//    {
//        session()->flash('message', trans('content.save_complete'));
//    }

    public function processingSpecialField($fields, $specFieldName): array
    {
        if (isset($fields[$specFieldName]) && $fields[$specFieldName] == 'on') $fields[$specFieldName] = 1;
        return $fields;
    }

    public function generatingCode(): string
    {
        return rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9).'-'.rand(0,9).rand(0,9);
    }

    public function unifyPhone($phone): string
    {
        return '+7'.substr($phone,2);
    }

    public function getSessionKey(FormRequest $request): string
    {
        return $request->has('id') && (int)$request->input('id') ? 'edit'.$request->id.'_steps' : 'steps';
    }

    public function checkOrdersInProgress(): void
    {
        $ordersInProgress = Order::where('status',1)->get();
        foreach ($ordersInProgress as $order) {
            $checkingTime = $order->updated_at->timestamp + (60 * 60 * 24 * 7);
            if (
                time() >= $checkingTime &&
                (!$order->lastInformingOrder->count() || $order->lastInformingOrder[0]->created_at->timestamp >= $checkingTime)
            ) {
                $this->chatMessage($order, trans('content.to_over_order'));
                InformingOrder::create([
                    'message' => trans('content.to_over_order'),
                    'order_id' => $order->id
                ]);
            }
        }
    }

    public function chatMessage(Order $order, string $message): void
    {
        if (!$order->messages->count()) {
            $message = Message::create([
                'body' => $message,
                'user_id' => 1,
                'order_id' => $order->id
            ]);
            $this->setNewMessages($message);
        }
    }

    public function setNewMessages(Message $message): void
    {
        if (Auth::id() != $message->order->user_id) {
            MessageUser::create([
                'message_id' => $message->id,
                'user_id' => $message->order->user_id,
                'order_id' => $message->order_id,
            ]);
            broadcast(new NotificationEvent('new_message', $message->order, $message->order->user_id));
            $this->mailNotice($message->order, $message->order->userCredentials, 'new_message_notice');
        }
        foreach ($message->order->performers as $performer) {
            if (Auth::id() != $performer->id) {
                MessageUser::create([
                    'message_id' => $message->id,
                    'user_id' => $performer->id,
                    'order_id' => $message->order_id,
                ]);
                broadcast(new NotificationEvent('new_message', $message->order, $performer->id));
                $this->mailNotice($message->order, $performer, 'new_message_notice');
            }
        }
    }

    public function sendSms($phone, $text)
    {
        $data = array(
            'user_name' => env('MOIZVONKI_USER_NAME'),
            'api_key' => env('MOIZVONKI_API_KEY'),
            'action' => 'calls.send_sms',
            'to' => $phone,
            'text' => $text
        );

        $fields = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://apollomotors.moizvonki.ru/api/v1');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json','Content-Length:'.mb_strlen($fields,'UTF-8')]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return json_decode(curl_exec($ch));
    }

    public function sendMessage(string $template, string $mailTo, string|null $cc, array $fields, string|null $pathToFile=null)
    {
        dispatch(new SendMessage($template, $mailTo, null, $fields));
    }

    public function setReadUnread(Model $model): void
    {
        $model->query()->whereIn('order_id',Order::where('user_id',Auth::id())->pluck('id')->toArray())->update(['read' => true]);
    }

    public function setReadUnreadRemovedPerformers(): void
    {
        ReadRemovedPerformer::where('user_id',Auth::id())->update(['read' => true]);
    }

    private function mailNotice(Order $order, User $user, string $template): void
    {
        if ($user->email && $user->mail_notice) {
            $this->sendMessage($template, $user->email, null, ['order' => $order]);
        }
    }
}
