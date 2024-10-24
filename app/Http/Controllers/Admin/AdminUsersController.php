<?php

namespace App\Http\Controllers\Admin;
use App\Actions\ChangeAvatar;
use App\Actions\DeleteFile;
use App\Actions\DeleteOrder;
use App\Actions\ProcessingImage;
use App\Events\Admin\AdminOrderEvent;
use App\Events\Admin\AdminUserEvent;
use App\Events\NotificationEvent;
use App\Events\OrderEvent;
use App\Http\Controllers\FieldsHelperTrait;
use App\Http\Controllers\HelperTrait;
use App\Http\Controllers\MessagesHelperTrait;
use App\Http\Requests\Account\ChangeAvatarRequest;
use App\Http\Requests\Admin\AdminEditUserRequest;
use App\Models\OrderUser;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

//use Illuminate\Validation\Rules\Password;

class AdminUsersController extends AdminBaseController
{
    use HelperTrait;
    use FieldsHelperTrait;
    use MessagesHelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function users(User $users, $slug=null): View
    {
        return $this->getSomething($users, $slug, ['ratings']);
    }

    public function getUsers(User $users): JsonResponse
    {
        return response()->json([
            'items' => $users::query()
                ->filtered()
                ->with('ratings')
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10)
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editUser(
        AdminEditUserRequest $request,
        ProcessingImage $processingImage
    ): JsonResponse
    {
        $fields = $request->validated();

        if ($request->has('id')) {
            $user = User::query()->where('id',$request->input('id'))->with('ratings')->first();
            if ($request->input('password')) $fields['password'] = bcrypt($fields['password']);
            $user->update($fields);
            $user->refresh();
            /** @var USER $user */
            if ($user->wasChanged()) broadcast(new AdminUserEvent('change_item',$user));
        } else {
            $fields['password'] = bcrypt($fields['password']);
            $user = User::query()->create($fields);
            $fields = $processingImage->handle($request, [], 'avatar', 'images/avatars/', 'avatar'.$user->id);
            $user->update($fields);
            $user->refresh();
            /** @var USER $user */
            broadcast(new AdminUserEvent('new_item',$user));
        }
//        $this->saveCompleteMessage();
//        return redirect()->back();
        return response()->json(['message' => trans('content.save_complete')],200);
    }

    public function changeAvatar(
        ChangeAvatarRequest $request,
        ProcessingImage $processingImage,
        ChangeAvatar $changeAvatar
    ): JsonResponse
    {
        return $changeAvatar->handle($request, $processingImage);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteUser(
        Request $request,
        DeleteOrder $deleteOrder,
        DeleteFile $deleteFile,
    ): JsonResponse
    {
        $this->validate($request, ['id' => $this->validationUserId]);
        $user = User::where('id',$request->id)->select('id')->with('orders')->first();

        if ($user->orders->count()) {
            foreach ($user->orders as $order) {
                $deleteOrder->handle($order, $deleteFile);
            }
        }

        $ordersUser = OrderUser::query()->where('user_id',$user->id)->with('order')->get();
        foreach ($ordersUser as $orderUser) {
            if ($orderUser->order->status != 2) {
                $orderUser->order->status = 2;
                $orderUser->order->save();
                $orderUser->load($this->orderLoadFields);

                broadcast(new NotificationEvent('new_order_status', $orderUser, $orderUser->user_id));
                broadcast(new OrderEvent('new_order_status', $orderUser));
                broadcast(new AdminOrderEvent('change_item', $orderUser));

                $this->mailOrderNotice($orderUser, $orderUser->userCredentials, 'new_order_status_notice');
            }
        }

        if ($user->avatar) $deleteFile->handle($user->avatar);
        Subscription::query()->where('user_id',$user->id)->delete();
        Subscription::query()->where('subscriber_id',$user->id)->delete();

        broadcast(new AdminUserEvent('del_item', $user));

        $user->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }
}
