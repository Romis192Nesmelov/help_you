<?php

namespace App\Http\Controllers\Admin;
use App\Actions\ChangeAvatar;
use App\Actions\DeleteFile;
use App\Actions\DeleteOrder;
use App\Actions\ProcessingImage;
use App\Actions\ProcessingSpecialFields;
use App\Events\Admin\AdminUserEvent;
use App\Http\Controllers\HelperTrait;
use App\Http\Requests\Account\ChangeAvatarRequest;
use App\Http\Requests\Admin\AdminEditUserRequest;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use JetBrains\PhpStorm\Pure;

//use Illuminate\Validation\Rules\Password;

class AdminUsersController extends AdminBaseController
{
    use HelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function users(User $users, $slug=null): View
    {
        return $this->getSomething($users, $slug);
    }

    public function getUsers(User $users): JsonResponse
    {
        return response()->json(
            $users::query()
                ->filtered()
                ->with('ratings')
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10)
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editUser(
        AdminEditUserRequest $request,
        ProcessingSpecialFields $processingSpecialFields,
        ProcessingImage $processingImage
    ): RedirectResponse
    {
        $fields = $request->validated();

        if ($request->has('id')) {
            $fields = $this->getUserSpecialFields($processingSpecialFields, $fields);
            $user = User::query()->where('id',$request->input('id'))->with('ratings')->first();
            if ($request->input('password')) $fields['password'] = bcrypt($fields['password']);
            $user->update($fields);
            $user->refresh();
            /** @var USER $user */
            broadcast(new AdminUserEvent('new_item',$user));
        } else {
            $fields = $this->getUserSpecialFields($processingSpecialFields, $fields);
            $fields['password'] = bcrypt($fields['password']);
            $user = User::query()->create($fields);
            $fields = $processingImage->handle($request, [], 'avatar', 'images/avatars/', 'avatar'.$user->id);
            $user->update($fields);
            $user->refresh();
            /** @var USER $user */
            broadcast(new AdminUserEvent('change_item',$user));
        }
        $this->saveCompleteMessage();
        return redirect()->back();
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
        if ($user->avatar) $deleteFile->handle($user->avatar);
        Subscription::query()->where('user_id',$user->id)->delete();
        Subscription::query()->where('subscriber_id',$user->id)->delete();

        broadcast(new AdminUserEvent('del_item', $user));

        $user->delete();
        return response()->json([],200);
    }

    #[Pure] private function getUserSpecialFields(ProcessingSpecialFields $processingSpecialFields, array $fields): array
    {
        foreach (['mail_notice','active','admin'] as $field) {
            $fields = $processingSpecialFields->handle($fields, $field);
        }
        return $fields;
    }
}
