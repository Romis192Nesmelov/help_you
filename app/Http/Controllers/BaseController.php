<?php

namespace App\Http\Controllers;

//use App\Models\City;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class BaseController extends Controller
{
    use HelperTrait;

    protected array $data = [];
    protected array $menu = [];
    protected string $activeMenu = '';
    protected string $activeSubMenu = '';

    public function index() :View|RedirectResponse
    {
        if (!Auth::guest() && (!Auth::user()->name || !Auth::user()->family || !Auth::user()->born || !Auth::user()->email))
            return redirect(route('account'));
        else return $this->showView('home');
    }

    public function partners(Request $request) :View
    {
        $this->activeMenu = 'for_partners';
        if ($request->has('id')) {
            $this->getItem('partner', new Partner(), $request->id);
            return $this->showView('partner');
        } else {
            $this->getItems('partners', new Partner());
            return $this->showView('partners');
        }
    }

//    public function map() :View
//    {
//        $this->data['cities'] = City::all();
//        return $this->showView('map');
//    }

//    protected function setSeo($seo): void
//    {
//        if ($seo) {
//            foreach (['title', 'keywords', 'description'] as $item) {
//                $this->data[$item] = $seo[$item];
//            }
//        }
//    }

    protected function showView($view) :View
    {
        return view($view, array_merge(
            $this->data,
            [
                'menu' => [
                    'about' =>              ['href' => true],
                    'how_does_it_work' =>   ['href' => true],
                    'for_partners' =>       ['href' => true],
                ],
                'activeMenu' => $this->activeMenu,
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
}
