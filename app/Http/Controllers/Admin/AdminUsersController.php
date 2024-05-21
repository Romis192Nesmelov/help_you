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

    public User $user;

    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function users($slug=null): View
    {
        return $this->getSomething($this->user, $slug);
    }

    public function getUsers(): JsonResponse
    {
        return response()->json(
            $this->user::query()
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
        Request $request,
        ProcessingSpecialFields $processingSpecialFields,
        ProcessingImage $processingImage
    ): RedirectResponse
    {
        $validationArr = [
            'name' => 'nullable|max:255',
            'family' => 'nullable|max:255',
            'born' => $this->validationBorn,
            'phone' => $this->validationPhone,
            'email' => 'nullable|email|unique:users,email',
            'info_about' => $this->validationText
        ];
        $avatarPath = 'images/avatars/';
        if ($request->has('id')) {
            $validationArr['id'] = $this->validationUserId;
            $validationArr['email'] .= ','.$request->input('id');
            if ($request->input('password')) $validationArr['password'] = $this->validationPassword;
            $fields = $this->validate($request, $validationArr);
            $fields = $this->getUserSpecialFields($processingSpecialFields, $fields);
            $user = User::query()->where('id',$request->input('id'))->with('ratings')->first();
            if ($request->input('password')) $fields['password'] = bcrypt($fields['password']);
            $processingImage->handle($request, $fields, 'avatar', $avatarPath, 'avatar'.$user->id);
            $user->update($fields);
            broadcast(new AdminUserEvent('new_item',$user));
        } else {
            $validationArr['password'] = $this->validationPassword;
            $fields = $this->validate($request, $validationArr);
            $fields = $this->getUserSpecialFields($processingSpecialFields, $fields);
            $fields['password'] = bcrypt($fields['password']);
            $user = User::query()->create($fields);
            $processingImage->handle($request, [], 'avatar', $avatarPath, 'avatar'.$user->id);
            $user->update($fields);
            broadcast(new AdminUserEvent('change_item',$user));
        }
        $this->saveCompleteMessage();
        return redirect(route('admin.users'));
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
