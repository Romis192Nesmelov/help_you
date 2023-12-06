<?php

namespace App\Http\Controllers;
use App\Models\Message;
use App\Models\MessageUser;
use App\Models\Order;
use http\Env\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
    public string $validationText = 'nullable|min:5|max:3000';
    public string $validationLongText = 'required|min:5|max:50000';
    public string $validationColor = 'regex:/^(hsv\((\d+)\,\s(\d+)\%\,\s(\d+)\%\))$/';
    public string $validationSvg = 'required|mimes:svg|max:10';
    public string $validationJpgAndPng = 'mimes:jpg,png|max:2000';
    public string $validationJpg = 'mimes:jpg|max:2000';
    public string $validationPng = 'mimes:png|max:2000';
    public string $validationDate = 'regex:/^(\d{2})\/(\d{2})\/(\d{4})$/';

//    public function getCutTableName(Model $item) :string
//    {
//        return substr($item->getTable(),0,-1);
//    }

//    public function sendMessage($email, $fields, $template, $pathToFile=null): void
//    {
//        Mail::send('emails.'.$template, ['fields' => $fields], function($message) use ($email, $fields, $pathToFile) {
//            $message->subject(trans('admin.message_from'));
//            $message->from(env('MAIL_TO'), 'Help you?');
//            $message->to($email);
//            if ($pathToFile) $message->attach($pathToFile);
//        });
//    }

//    public function saveCompleteMessage(): void
//    {
//        session()->flash('message', trans('content.save_complete'));
//    }

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

    public function newChatMessage(Order $order): void
    {
        if (!$order->messages->count()) {
            $message = Message::create([
                'body' => trans('content.new_chat_message'),
                'user_id' => 1,
                'order_id' => $order->id
            ]);
        }
        $this->setNewMessages($message);
    }

    public function setNewMessages(Message $message): void
    {
        if (Auth::id() != $message->order->user_id) {
            MessageUser::create([
                'message_id' => $message->id,
                'user_id' => $message->order->user_id,
                'order_id' => $message->order_id,
            ]);
        }
        foreach ($message->order->performers as $performer) {
            if (Auth::id() != $performer->id) {
                MessageUser::create([
                    'message_id' => $message->id,
                    'user_id' => $performer->id,
                    'order_id' => $message->order_id,
                ]);
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
}
