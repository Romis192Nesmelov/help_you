<?php

namespace App\Http\Controllers;

use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\View\View;

class BaseController extends Controller
{
    use HelperTrait;

    protected array $data = [];
    protected array $menu = [];
    protected string $activeMenu = '';
    protected string $activeSubMenu = '';

    public function __construct()
    {
        $this->menu = [
            'about' =>              ['href' => true],
            'how_does_it_work' =>   ['href' => true],
            'for_partners' =>       ['href' => true],
        ];
    }

    public function index() :View
    {
        return $this->showView('home');
    }

    public function map() :View
    {
        return $this->showView('map');
    }

    protected function setSeo($seo): void
    {
        if ($seo) {
            foreach (['title', 'keywords', 'description'] as $item) {
                $this->data[$item] = $seo[$item];
            }
        }
    }

    protected function showView($view) :View
    {
        return view($view, array_merge(
            $this->data,
            [
                'menu' => $this->menu,
                'activeMenu' => $this->activeMenu,
                'activeSubMenu' => $this->activeSubMenu
            ]
        ));
    }

    protected function getItem(string $itemName, Model $model, $slug)
    {
        $this->data[$itemName] = $model->where('slug',$slug)->where('active',1)->first();
        if (!$this->data[$itemName]) abort(404, trans('404'));
    }
}
