<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\HelperTrait;
use App\Http\Controllers\Controller;
//use App\Models\Seo;
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

    protected function getSomething(
        Model $model,
        string|null $slug=null,
        Model|null $parentModel=null,
        string|null $parentRelation=null
    ): View
    {
        $key = $model->getTable();
        if (request('parent_id')) {
            $parentItem = $parentModel->findOrFail(request('parent_id'));
            $this->data['parent'] = $parentModel->find(request('parent_id'));

            if ($parentRelation) {
                $this->data['menu_key'] = $this->data['parent_key'];
                $this->data['current_sub_item'] = $parentItem[$parentRelation]->id;
                $this->breadcrumbs[] = [
                    'key' => $this->menu[$this->data['parent_key']]['key'],
                    'name' => trans('admin_menu.'.$this->data['parent_key']),
                ];
                $this->breadcrumbs[] = [
                    'key' => $this->menu[$this->data['parent_key']]['key'],
                    'params' => ['id' => $parentItem[$parentRelation]->id],
                    'name' => $parentItem[$parentRelation]->name ?? $parentItem[$parentRelation]->head,
                ];
                $this->breadcrumbs[] = [
                    'key' => $this->menu[$this->data['near_parent_key']]['key'],
                    'params' => ['id' => $parentItem->id, 'parent_id' => $parentItem[$parentRelation]->id],
                    'name' => $parentItem->name ?? $parentItem->head,
                ];
            } else {
                $this->data['menu_key'] = $this->data['parent_key'];
                $this->breadcrumbs[] = [
                    'key' => $this->menu[$this->data['parent_key']]['key'],
                    'name' => trans('admin_menu.'.$this->data['parent_key']),
                ];
                $this->breadcrumbs[] = [
                    'key' => $this->menu[$this->data['parent_key']]['key'],
                    'params' => ['id' => $parentItem->id],
                    'name' => $parentItem->name ?? $parentItem->head,
                ];
            }
        } else if (!$this->menu[$key]['hidden']) {
            $this->data['menu_key'] = $key;
            $this->breadcrumbs[] = $this->menu[$key];
        }

        $this->data['singular_key'] = substr($key, 0, -1);

        $breadcrumbsParams = [];
        if ($parentModel) $breadcrumbsParams['parent_id'] = $parentItem->id;

        if (request('id')) {
//            $this->data['metas'] = $this->metas;
            $this->data[$this->data['singular_key']] = $model->findOrFail(request('id'));
            $breadcrumbsParams['id'] = $this->data[$this->data['singular_key']]->id;
            $this->breadcrumbs[] = [
                'key' => $this->menu[$key]['key'],
                'params' => $breadcrumbsParams,
                'name' => trans('admin.edit_'.$this->data['singular_key']),
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

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function editSomething (
        Request $request,
        Model $model,
        array $validationArr,
        string $pathToImages = null,
        string $imageName = null
    ): Model
    {
        if ($request->has('id')) {
            $validationArr['id'] = 'required|integer|exists:'.$model->getTable().',id';
            if ($imageName) $validationArr['image'] = 'nullable|'.$validationArr['image'];
            $fields = $this->validate($request, $validationArr);
//            $seoFields = $this->getSeo($request, $model);
            $fields = $this->getSpecialFields($model, $validationArr, $fields);
            $model = $model->find($request->input('id'));
            $model->update($fields);
        } else {
            if ($imageName) $validationArr['image'] = 'required|'.$validationArr['image'];
            $fields = $this->validate($request, $validationArr);
//            $seoFields = $this->getSeo($request, $model);
            $fields = $this->getSpecialFields($model, $validationArr, $fields);
            $model = $model->create($fields);
        }

//        if (count($seoFields)) {
//            if (!isset($model->seo)) {
//                $seo = Seo::create($seoFields);
//                $model->update(['seo_id' => $seo->id]);
//            } else {
//                $model->seo->update($seoFields);
//            }
//        }

        $this->processingFiles($request, $model, 'image', $pathToImages, $imageName.$model->id);
        $this->saveCompleteMessage();
        return $model;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function deleteSomething(Request $request, Model $model): JsonResponse
    {
        $this->validate($request, ['id' => 'required|integer|exists:'.$model->getTable().',id']);
        $table = $model->find($request->input('id'));

        if (isset($table->avatar)) {
            $this->deleteFile($table->avatar);
        } elseif (isset($table->images)) {
            foreach ($table->images as $image) {
                $this->deleteFile($image->image);
            }
        }
        $table->delete();
        return response()->json(['message' => trans('admin.delete_complete')],200);
    }

    protected function getSpecialFields(Model $model, array $validationArr, array $fields): array
    {
        foreach (['mail_notice','active','admin'] as $field) {
            if (in_array($field, $model->getFillable())) $fields[$field] = request($field) ? 1 : 0;
        }
        if (in_array('date',$model->getFillable()) && array_key_exists('date',$validationArr)) $fields['date'] = $this->convertTimestamp(request('date'));
        return $fields;
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

    protected function convertTimestamp($time): int
    {
        $time = explode('/', $time);
        return strtotime($time[1].'/'.$time[0].'/'.$time[2]);
    }

    protected function processingFiles(Request $request, Model $model, string $fileField, string|null $pathToFile=null, string|null $fileName=null): void
    {
        if ($pathToFile && $request->hasFile($fileField)) {
            if ($model[$fileField]) $this->deleteFile($model[$fileField]);
            $fileName .= $model->id.'.'.$request->file($fileField)->getClientOriginalExtension();
            $model[$fileField] = $pathToFile.$fileName;
            $model->save();
            $request->file($fileField)->move(base_path('public/'.$pathToFile), $fileName);
        }
    }

    protected function showView($view): View
    {
        return view('admin.'.$view, array_merge(
            $this->data,
            [
                'breadcrumbs' => $this->breadcrumbs,
                'menu' => $this->menu
            ]
        ));
    }

    protected function deleteFile($path): void
    {
        if ($path && file_exists(base_path('public/'.$path))) unlink(base_path('public/'.$path));
    }
}
