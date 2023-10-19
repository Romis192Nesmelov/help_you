<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;

trait HelperTrait
{
    public $validationPhone = 'regex:/^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/';
    public $validationBorn = 'required|regex:/^((\d){2}-(\d){2}-([1-2]\d\d\d))$/';
    public $validationPassword = 'required|min:3|max:20';
    public $validationPasswordConfirmed = 'required|confirmed|min:6|max:20';
    public $validationCode = 'required|regex:/^(([0-9]{2})\-([0-9]{2})-([0-9]{2}))$/';
    public $validationInteger = 'required|integer';
    public $validationNumeric = 'required|numeric';
    public $validationString = 'required|min:3|max:255';
    public $validationText = 'nullable|min:5|max:3000';
    public $validationLongText = 'required|min:5|max:50000';
//    public $validationColor = 'regex:/^(hsv\((\d+)\,\s(\d+)\%\,\s(\d+)\%\))$/';
//    public $validationSvg = 'required|mimes:svg|max:10';
    public $validationJpgAndPng = 'mimes:jpg,png|max:700';
    public $validationJpg = 'mimes:jpg|max:2000';
    public $validationPng = 'mimes:png|max:2000';
    public $validationDate = 'regex:/^(\d{2})\/(\d{2})\/(\d{4})$/';

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
