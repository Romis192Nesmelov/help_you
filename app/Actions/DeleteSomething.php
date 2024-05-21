<?php

namespace App\Actions;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Model;

class DeleteSomething
{
    public function handle(Request $request, DeleteFile $deleteFile, Model $model): JsonResponse
    {
        $request->validate(['id' => 'required|integer|exists:'.$model->getTable().',id']);
        $table = $model->find($request->input('id'));

        if (isset($table->avatar)) {
            $deleteFile->handle($table->avatar);
        } elseif (isset($table->images)) {
            foreach ($table->images as $image) {
                $deleteFile->handle($image->image);
            }
        }
        $table->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }
}
