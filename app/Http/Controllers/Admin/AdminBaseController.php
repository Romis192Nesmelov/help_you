<?php

namespace App\Http\Controllers\Admin;
use App\Events\Admin\AdminOrderEvent;
use App\Http\Controllers\HelperTrait;
use App\Http\Controllers\Controller;
//use App\Models\Seo;
use App\Models\AdminNotice;
use App\Models\Answer;
use App\Models\Ticket;
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
            'tickets' => [
                'key' => 'tickets',
                'icon' => 'icon-ticket',
                'hidden' => false,
            ],
            'answers' => [
                'key' => 'answers',
                'hidden' => true,
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
        array $width=[],
        Model|null $parentModel=null,
        Model|null $parentParentModel=null,
        string $rootKey=null
    ): View
    {
        $key = $model->getTable();
        $this->data['menu_key'] = $key;
        $this->data['singular_key'] = substr($key, 0, -1);
        $breadcrumbsParams = [];

        if (request('parent_id')) {
            $parentKey = $parentModel->getTable();

            if (request('parent_parent_id')) {

                $parentParentKey = $parentParentModel->getTable();
                $parentKeyFields = $this->getSelectedFields($parentParentModel);
                $breadcrumbsParams['parent_parent_id'] = request('parent_parent_id');
                $parentParent = $parentParentModel->query()->where('id',request('parent_parent_id'))->select($parentKeyFields)->first();

                $this->breadcrumbs[] = [
                    'key' => $this->menu[$rootKey]['key'],
                    'name' => trans('admin_menu.'.$rootKey),
                ];
                $this->data['menu_key'] = $rootKey;

                $this->breadcrumbs[] = [
                    'key' => $this->menu[$parentParentKey]['key'],
                    'params' => ['id' => request('parent_parent_id')],
                    'name' => is_array($parentKeyFields) ? getItemName($parentParent) : $parentParent[$parentKeyFields],
                ];

            } else {
                $this->breadcrumbs[] = [
                    'key' => $this->menu[$parentKey]['key'],
                    'name' => trans('admin_menu.'.$parentKey),
                ];
                $this->data['menu_key'] = $parentKey;
            }

            $parentKeyFields = $this->getSelectedFields($parentModel);
            $parent = $parentModel->query()->where('id',request('parent_id'))->select($parentKeyFields)->first();
            $breadcrumbsParams['parent_id'] = request('parent_id');
            $this->breadcrumbs[] = [
                'key' => $this->menu[$parentKey]['key'],
                'params' => ['id' => request('parent_id')],
                'name' => is_array($parentKeyFields) ? getItemName($parent) : $parent[$parentKeyFields],
            ];

        } else if (!$this->menu[$key]['hidden']) {
            $this->data['menu_key'] = $key;
            $this->breadcrumbs[] = $this->menu[$key];
        }

        if (request('id')) {
//            $this->data['metas'] = $this->metas;
            $this->data[$this->data['singular_key']] = $model->query()->where('id',request('id'))->with($width)->first();
            $breadcrumbsParams['id'] = $this->data[$this->data['singular_key']]->id;
            $this->breadcrumbs[] = [
                'key' => $this->menu[$key]['key'],
                'params' => $breadcrumbsParams,
                'name' => trans('admin.edit_'.$this->data['singular_key']).' id#'.$this->data[$this->data['singular_key']]->id,
            ];
            return $this->showView($this->data['singular_key']);
        } else if ($slug == 'add') {
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
                    ->with(['order.user','order.performers'])
                    ->select(['order_id'])
                    ->orderByDesc('created_at')
                    ->get(),
                'tickets' => Ticket::query()
                    ->where('read_admin',0)
                    ->with('user')
                    ->select('id','user_id','subject')
                    ->orderByDesc('created_at')
                    ->get(),
                'answers' => Answer::query()
                    ->where('read_admin',0)
                    ->with(['user','ticket'])
                    ->select('id','user_id','ticket_id')
                    ->orderByDesc('created_at')
                    ->get(),
            ]
        ));
    }

    private function getSelectedFields(Model $parentModel): string|array
    {
        if ($parentModel instanceof User) {
            $selectFields = ['name','family','phone','email'];
        } elseif ($parentModel instanceof Ticket) {
            $selectFields = 'subject';
        } else {
            $selectFields = 'name';
        }
        return $selectFields;
    }
}
