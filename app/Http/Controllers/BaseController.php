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
            $this->data['partners'] = Partner::where('active',1)->get();
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
                'mainMenu' => [
                    ['key' => 'about', 'name' => trans('menu.about'), 'url' => route('about')],
                    ['key' => 'how_does_it_work', 'name' => trans('menu.how_does_it_work'), 'url' => route('how_does_it_work')],
                    ['key' => 'partners', 'name' => trans('menu.partners'), 'url' => route('partners')]
                ],
                'leftMenu' => [
                    ['id' => 'my-messages', 'icon' => 'icon-bubbles4', 'key' => 'account.my_chats', 'name' => trans('account.my_chats'), 'url' => route('messages.chats')],
                    ['id' => 'my-subscriptions', 'icon' => 'icon-mail-read', 'key' => 'account.my_subscriptions', 'name' => trans('account.my_subscriptions'), 'url' => route('account.my_subscriptions')],
                    ['id' => 'my-orders', 'icon' => 'icon-drawer-out', 'key' => 'account.my_orders', 'name' => trans('account.my_orders'), 'url' => route('account.my_orders')],
                    ['id' => 'my-help', 'icon' => 'icon-lifebuoy', 'key' => 'account.my_help', 'name' => trans('account.my_help'), 'url' => route('account.my_help')],
                    ['id' => 'incentives', 'icon' => 'icon-gift', 'key' => 'account.incentives', 'name' => trans('account.incentives'), 'url' => route('account.incentives')]
                ],
                'activeMainMenu' => $this->activeMainMenu,
                'activeLeftMenu' => $this->activeLeftMenu,
            ]
        ));
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
    protected function deleteSomething(Request $request, Model $model, $gate=false, $fileField=null): JsonResponse
    {
        $fields = $this->validate($request, ['id' => 'required|integer|exists:'.$model->getTable().',id']);
        $itemModel = $model->find($fields['id']);
        $this->authorize($gate, $itemModel);
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
