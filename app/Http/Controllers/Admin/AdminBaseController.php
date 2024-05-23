<?php

namespace App\Http\Controllers\Admin;
use App\Events\Admin\AdminOrderEvent;
use App\Http\Controllers\HelperTrait;
use App\Http\Controllers\Controller;
//use App\Models\Seo;
use App\Models\AdminNotice;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class AdminBaseController extends Controller
{
    use HelperTrait;

    protected array $data = [];
    protected array $breadcrumbs = [];
    protected array $menu;

    public function __construct()
    {
        $this->menu = [
            'home' => [
                'key' => 'home',
                'icon' => 'icon-home2',
                'hidden' => false,
            ],
            'users' => [
                'key' => 'users',
                'icon' => 'icon-users',
                'hidden' => false,
            ],
            'orders' => [
                'key' => 'orders',
                'icon' => 'icon-map',
                'hidden' => false,
            ],
            'partners' => [
                'key' => 'partners',
                'icon' => 'icon-users4',
                'hidden' => false,
            ],
            'actions' => [
                'key' => 'actions',
                'icon' => 'icon-trophy3',
                'hidden' => false,
            ],
        ];
        $this->breadcrumbs[] = $this->menu['home'];
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function home(): View
    {
        $this->data['menu_key'] = 'home';
        return $this->showView('home');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function getSomething(
        Model $model,
        string|null $slug=null,
        Model|null $parentModel=null,
        string|null $parentRelation=null
    ): View
    {
        $key = $model->getTable();
        $this->data['menu_key'] = $key;
        $this->data['singular_key'] = substr($key, 0, -1);

        if (request('parent_id')) {
            $isParentModelUser = $parentModel instanceof User;
            $selectFields = $isParentModelUser ? ['name','family','phone','email'] : ['name'];
            $this->data['parent'] = $parentModel->query()->where('id',request('parent_id'))->select($selectFields)->first();
            $this->data['menu_key'] = $this->data['parent_key'];
            $this->breadcrumbs[] = [
                'key' => $this->menu[$this->data['parent_key']]['key'],
                'name' => trans('admin_menu.'.$this->data['parent_key']),
            ];
            $this->breadcrumbs[] = [
                'key' => $this->menu[$this->data['parent_key']]['key'],
                'params' => ['id' => request('parent_id')],
                'name' => $isParentModelUser ? getItemName($this->data['parent']) : $this->data['parent']->name,
            ];
        } else if (!$this->menu[$key]['hidden']) {
            $this->data['menu_key'] = $key;
            $this->breadcrumbs[] = $this->menu[$key];
        }

        $breadcrumbsParams = [];
        if ($parentModel) $breadcrumbsParams['parent_id'] = request('parent_id');

        if (request('id')) {
//            $this->data['metas'] = $this->metas;
            $this->data[$this->data['singular_key']] = $model->findOrFail(request('id'));
            $breadcrumbsParams['id'] = $this->data[$this->data['singular_key']]->id;
            $this->breadcrumbs[] = [
                'key' => $this->menu[$key]['key'],
                'params' => $breadcrumbsParams,
                'name' => trans('admin.edit_'.$this->data['singular_key']).' id#'.$this->data[$this->data['singular_key']]->id,
            ];
            return $this->showView($this->data['singular_key']);
        } else if ($slug && $slug == 'add') {
//            $this->data['metas'] = $this->metas;
            $breadcrumbsParams['slug'] = 'add';
            $this->breadcrumbs[] = [
                'key' => $this->menu[$key]['key'],
                'params' => $breadcrumbsParams,
                'name' => trans('admin.adding_'.$this->data['singular_key']),
            ];
            return $this->showView($this->data['singular_key']);
        } else {
//            $this->data[$key] = $model->orderBy('id', 'desc')->get();
            return $this->showView($key);
        }
    }

//    /**
//     * @throws \Illuminate\Validation\ValidationException
//     */
//    protected function getSeo(Request $request, Model $model): array
//    {
//        $seoFields = [];
//        if (in_array('seo_id',$model->getFillable())) {
//            $seoFields = $this->validate($request, $this->getValidationSeo());
//            $seoExistFlag = false;
//            foreach ($seoFields as $field => $val) {
//                if ($val) {
//                    $seoExistFlag = true;
//                    break;
//                }
//            }
//            if (!$seoExistFlag) $seoFields = [];
//        }
//        return $seoFields;
//    }

    protected function showView($view): View
    {
        return view('admin.'.$view, array_merge(
            $this->data,
            [
                'breadcrumbs' => $this->breadcrumbs,
                'menu' => $this->menu,
                'notices' => AdminNotice::query()
                    ->where('read',null)
                    ->with('order.user')
                    ->select(['order_id'])
                    ->orderByDesc('created_at')
                    ->get()
            ]
        ));
    }
}
