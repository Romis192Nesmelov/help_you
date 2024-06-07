<?php

namespace App\Http\Controllers;
use App\Http\Requests\Feedback\FeedbackRequest;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Session;

class BaseController extends Controller
{
    use MessagesHelperTrait;

    protected array $data = [];
    protected string $activeMainMenu = '';
    protected string $activeLeftMenu = '';
//    protected string $activeSubMenu = '';

    public function index() :View
    {
        return $this->showView('home');
    }

    public function aboutUs(): View
    {
        $this->activeMainMenu = 'about';
        return $this->showView('about_us');
    }

    public function feedback(FeedbackRequest $request): JsonResponse
    {
        $this->sendMessage('feedback', env('MAIL_TO'), null, $request->validated());
        return response()->json(['message' => trans('mail.thanks_for_your_feedback')],200);
    }

    public function howDoesItWork($slug=null) :View
    {
        $this->activeMainMenu = 'how_does_it_work';
        if ($slug) {
            if (!in_array($slug,['who-needs-help','who-wants-to-help'])) abort(404);
            return $this->showView(str_replace('-','_',$slug));
        } else return $this->showView('how_does_it_work');
    }

    public function partners(Request $request) :View
    {
        $this->activeMainMenu = 'partners';
        if ($request->has('id')) {
            $this->data['partner'] = Partner::findOrFail($request->id);
            return $this->showView('partner');
        } else {
            $this->data['partners'] = Partner::all();
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
                    ['id' => 'my-messages', 'icon' => 'icon-bubbles4', 'key' => 'my_chats', 'name' => trans('account.my_chats'), 'url' => route('messages.chats')],
                    ['id' => 'my-subscriptions', 'icon' => 'icon-mail-read', 'key' => 'my_subscriptions', 'name' => trans('account.my_subscriptions'), 'url' => route('account.my_subscriptions')],
                    ['id' => 'my-orders', 'icon' => 'icon-drawer-out', 'key' => 'my_orders', 'name' => trans('account.my_orders'), 'url' => route('account.my_orders')],
                    ['id' => 'my-help', 'icon' => 'icon-lifebuoy', 'key' => 'my_help', 'name' => trans('account.my_help'), 'url' => route('account.my_help')],
                    ['id' => 'incentives', 'icon' => 'icon-gift', 'key' => 'incentives', 'name' => trans('account.incentives'), 'url' => route('account.incentives')]
                ],
                'activeMainMenu' => $this->activeMainMenu,
                'activeLeftMenu' => $this->activeLeftMenu,
            ]
        ));
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
//    protected function deleteSomething(Request $request, Model $model, $gate=false, $fileField=null): JsonResponse
//    {
//        $fields = $this->validate($request, ['id' => 'required|integer|exists:'.$model->getTable().',id']);
//        $itemModel = $model->find($fields['id']);
//        $this->authorize($gate, $itemModel);
//        if ($fileField) {
//            if (is_array($fileField)) {
//                foreach ($fileField as $field) {
//                    $this->deleteFile($itemModel[$field]);
//                }
//            } else $this->deleteFile($itemModel[$fileField]);
//        }
//        $itemModel->delete();
//        return response()->json([],200);
//    }
}
