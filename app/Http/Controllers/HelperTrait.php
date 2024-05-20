<?php

namespace App\Http\Controllers;
use App\Events\Admin\AdminOrderEvent;
use App\Events\Admin\AdminUserEvent;
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
use App\Models\OrderImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public $metas = [
        'meta_description' => ['name' => 'description', 'property' => false],
        'meta_keywords' => ['name' => 'keywords', 'property' => false],
        'meta_twitter_card' => ['name' => 'twitter:card', 'property' => false],
        'meta_twitter_size' => ['name' => 'twitter:size', 'property' => false],
        'meta_twitter_creator' => ['name' => 'twitter:creator', 'property' => false],
        'meta_og_url' => ['name' => false, 'property' => 'og:url'],
        'meta_og_type' => ['name' => false, 'property' => 'og:type'],
        'meta_og_title' => ['name' => false, 'property' => 'og:title'],
        'meta_og_description' => ['name' => false, 'property' => 'og:description'],
        'meta_og_image' => ['name' => false, 'property' => 'og:image'],
        'meta_robots' => ['name' => 'robots', 'property' => false],
        'meta_googlebot' => ['name' => 'googlebot', 'property' => false],
        'meta_google_site_verification' => ['name' => 'google-site-verification', 'property' => false],
    ];

    public function changeSomeAvatar(Request $request): JsonResponse
    {
        $validationArr = [
            'avatar' => 'required|'.$this->validationJpgAndPngSmall,
            'avatar_size' => 'nullable|integer',
            'avatar_position_x' => 'nullable',
            'avatar_position_y' => 'nullable',
        ];
        if ($request->has('id')) $validationArr['id'] = $this->validationUserId;

        $fields = $this->validate($request, $validationArr);

        $user = $request->has('id') ? User::find($request->id) : Auth::user();
        $fields['avatar_props'] = [];
        foreach (['size','position_x','position_y'] as $avatarProp) {
            $fieldProp = 'avatar_'.$avatarProp;
            $prop = $fields[$fieldProp];
            if ($prop) $fields['avatar_props']['background-'.str_replace('_','-',$avatarProp)] = $avatarProp == 'size' ? ((int)$prop).'%' : ((float)$prop);
            unset($fields[$fieldProp]);
        }
        $fields = $this->processingImage($request, $fields,'avatar', 'images/avatars/', 'avatar'.$user->id);
        $user->update($fields);
        broadcast(new AdminUserEvent('change_item',$user));
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    public function processingOrderImages(Request $request, Model $order)
    {
        for ($i=1;$i<=3;$i++) {
            $fieldName = 'photo'.$i;
            $imageFields = $this->processingImage($request, [], $fieldName, 'images/orders_images/', 'order'.$order->id.'_'.$i);
            if (count($imageFields)) {
                $orderImage = OrderImage::where('position',$i)->where('order_id',$order->id)->first();
                if ($orderImage) {
                    $orderImage->image = $imageFields[$fieldName];
                    $orderImage->save();
                } else {
                    OrderImage::create([
                        'position' => $i,
                        'image' => $imageFields[$fieldName],
                        'order_id' => $order->id
                    ]);
                }
            }
        }
    }

    public function removeOrderImage(Order $order, int $pos): JsonResponse
    {
        $fileName = 'images/orders_images/order'.$order->id.'_'.$pos.'.jpg';
        OrderImage::where('image',$fileName)->delete();
        $this->deleteFile($fileName);
        broadcast(new AdminOrderEvent('change_item', $order));
        return response()->json([],200);
    }

    public function processingImage(Request $request, array $fields, string $imageField, string $pathToSave, string $imageName): array
    {
        if ($request->hasFile($imageField)) {
            $imageName .= '.'.$request->file($imageField)->getClientOriginalExtension();
            $fields[$imageField] = $pathToSave.$imageName;
            $request->file($imageField)->move(base_path('public/'.$pathToSave), $imageName);
        }
        return $fields;
    }

    public function saveCompleteMessage(): void
    {
        session()->flash('message', trans('content.save_complete'));
    }

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
            $incentive = ActionUser::create([
                'action_id' => $actionId,
                'user_id' => $userId,
                'active' => 1
            ]);
            broadcast(new IncentivesEvent('new_incentive', $incentive, $userId));
            if ($user->email && $user->mail_notice) {
                $this->sendMessage('new_award_notice', $user->email, null, ['action' => $incentive->action]);
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
        $model->query()
            ->whereIn('order_id',Order::where('user_id',Auth::id())->pluck('id')->toArray())
            ->where('read',null)
            ->update(['read' => true]);
    }

    public function setReadUnreadUser(Model $model): void
    {
        $model->query()
            ->where('user_id',Auth::id())
            ->where('read',null)
            ->update(['read' => true]);
    }

    public function mailOrderNotice(Order $order, User $user, string $template): void
    {
        if ($user->email && $user->mail_notice) {
            $this->sendMessage($template, $user->email, null, ['order' => $order]);
        }
    }
}
