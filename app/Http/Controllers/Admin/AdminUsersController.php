<?php

namespace App\Http\Controllers\Admin;
use App\Events\Admin\AdminUserEvent;
use App\Http\Controllers\HelperTrait;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

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
    public function editUser(Request $request): RedirectResponse
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
            $fields = $this->getSpecialFields($this->user, $validationArr, $fields);
            $user = User::where('id',$request->input('id'))->with('ratings')->first();
            if ($request->input('password')) $fields['password'] = bcrypt($fields['password']);
            $user->update($fields);
            $this->processingFiles($request, $user, 'avatar', $avatarPath, 'avatar'.$user->id);
            broadcast(new AdminUserEvent('new_item',$user));
        } else {
            $validationArr['password'] = $this->validationPassword;
            $fields = $this->validate($request, $validationArr);
            $fields = $this->getSpecialFields($this->user, $validationArr, $fields);
            $fields['password'] = bcrypt($fields['password']);
            $user = User::create($fields);
            $this->processingFiles($request, $user, 'avatar', $avatarPath, 'avatar'.$user->id);
            broadcast(new AdminUserEvent('change_item',$user));
        }
        $this->saveCompleteMessage();
        return redirect(route('admin.users'));
    }

    public function changeAvatar(Request $request): JsonResponse
    {
        return $this->changeSomeAvatar($request);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
//    public function deleteUser(Request $request): JsonResponse
//    {
//        return $this->deleteSomething($request, $this->user);
//    }
}
