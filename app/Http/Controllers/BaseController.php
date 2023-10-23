<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class BaseController extends Controller
{
    protected array $data = [];
    protected string $activeMainMenu = '';
    protected string $activeLeftMenu = '';
    protected string $activeSubMenu = '';

    public function index() :View
    {
        return $this->showView('home');
    }

    public function partners(Request $request) :View
    {
        $this->activeMainMenu = 'partners';
        if ($request->has('id')) {
            $this->getItem('partner', new Partner(), $request->id);
            return $this->showView('partner');
        } else {
            $this->getItems('partners', new Partner());
            return $this->showView('partners');
        }
    }

    public function prevUrl(): JsonResponse
    {
        return response()->json(['url' => Session::get('prev_url')],200);
    }

    protected function showView($view) :View
    {
        return view($view, array_merge(
            $this->data,
            [
                'mainMenu' => ['about', 'how_does_it_work', 'partners',],
                'leftMenu' => [
                    ['icon' => 'icon-bubbles4', 'key' => 'messages'],
                    ['icon' => 'icon-mail-read', 'key' => 'my_subscriptions'],
                    ['icon' => 'icon-drawer-out', 'key' => 'my_orders'],
                    ['icon' => 'icon-lifebuoy', 'key' => 'my_help'],
                    ['icon' => 'icon-gift', 'key' => 'incentives']
                ],
                'activeMainMenu' => $this->activeMainMenu,
                'activeLeftMenu' => $this->activeLeftMenu,
                'activeSubMenu' => $this->activeSubMenu
            ]
        ));
    }

    protected function getItems(string $itemName, Model $model): void
    {
        $this->data[$itemName] = $model->where('active',1)->get();
    }

    protected function getItem(string $itemName, Model $model, $id): void
    {
        $this->data[$itemName] = $model->findOrFail($id);
        if (!$this->data[$itemName]->active) abort(404);
    }

    protected function processingImage(Request $request, array $fields, string $imageField, string $pathToSave, string $imageName): array
    {
        if ($request->hasFile($imageField)) {
            $imageName .= '.'.$request->file($imageField)->getClientOriginalExtension();
            $fields[$imageField] = $pathToSave.$imageName;
            $request->file($imageField)->move(base_path('public/'.$pathToSave), $imageName);
        }
        return $fields;
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function deleteSomething(Request $request, Model $model, $fileField=null): JsonResponse
    {
        $fields = $this->validate($request, ['id' => 'required|integer|exists:'.$model->getTable().',id']);
        $itemModel = $model->find($fields['id']);
        $this->authorize('owner', $itemModel);
        if ($fileField) {
            if (is_array($fileField)) {
                foreach ($fileField as $field) {
                    $this->deleteFile($itemModel[$field]);
                }
            } else $this->deleteFile($itemModel[$fileField]);
        }
        $itemModel->delete();
        return response()->json([],200);
    }

    protected function deleteFile($path): void
    {
        if (file_exists(base_path('public/'.$path))) unlink(base_path('public/'.$path));
    }
}
