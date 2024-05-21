<?php

namespace App\Actions;

use App\Events\Admin\AdminUserEvent;
use App\Http\Controllers\HelperTrait;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ChangeAvatar
{
    use HelperTrait;

    public function handle(Request $request, ProcessingImage $processingImage): JsonResponse
    {
        $fields = $request->validated();
        $user = $request->has('id') ? User::find($request->id) : Auth::user();
        $fields['avatar_props'] = [];
        foreach (['size','position_x','position_y'] as $avatarProp) {
            $fieldProp = 'avatar_'.$avatarProp;
            $prop = $fields[$fieldProp];
            if ($prop) $fields['avatar_props']['background-'.str_replace('_','-',$avatarProp)] = $avatarProp == 'size' ? ((int)$prop).'%' : ((float)$prop);
            unset($fields[$fieldProp]);
        }
        $fields = $processingImage->handle($request, $fields,'avatar', 'images/avatars/', 'avatar'.$user->id);
        $user->update($fields);
        broadcast(new AdminUserEvent('change_item',$user));
        return response()->json(['message' => trans('content.save_complete')],200);
    }
}
