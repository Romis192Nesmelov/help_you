<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Http\FormRequest;

trait HelperTrait
{
    public string $validationName = 'required|min:3|max:50';
    public string $validationPhone = 'regex:/^((\+)?(\d)(\s)?(\()?[0-9]{3}(\))?(\s)?([0-9]{3})(\-)?([0-9]{2})(\-)?([0-9]{2}))$/';
    public string $validationBorn = 'regex:/^((\d){2}-(\d){2}-([1-2]\d\d\d))$/';
    public string $validationPassword = 'required|min:3|max:20';
    public string $validationCode = 'required|regex:/^(([0-9]{2})\-([0-9]{2})-([0-9]{2}))$/';
    public string $validationInteger = 'required|integer';
    public string $validationNumeric = 'required|numeric';
    public string $validationString = 'required|min:3|max:255';
    public string $validationText = 'nullable|min:1|max:3000';
    public string $validationLongText = 'nullable|min:5|max:50000';
//    public string $validationColor = 'regex:/^(hsv\((\d+)\,\s(\d+)\%\,\s(\d+)\%\))$/';
//    public string $validationSvg = 'required|mimes:svg|max:10';
    public string $validationJpgAndPng = 'mimes:jpg,png|max:2000';
    public string $validationJpgAndPngSmall = 'mimes:jpg,png|max:300';
    public string $validationJpgAndPngAndSvgSmall = 'mimes:jpg,png,svg|max:300';
//    public string $validationJpg = 'mimes:jpg|max:2000';
//    public string $validationPng = 'mimes:png|max:2000';
    public string $validationDate = 'regex:/^(\d{2})\/(\d{2})\/(\d{4})$/';
    public string $validationOrderId = 'required|exists:orders,id';
    public string $validationUserId = 'required|integer|exists:users,id';

    public $metas = [
        'meta_description' => ['name' => 'description', 'property' => false],
        'meta_keywords' => ['name' => 'keywords', 'property' => false],
        'meta_twitter_card' => ['name' => 'twitter:card', 'property' => false],
        'meta_twitter_size' => ['name' => 'twitter:size', 'property' => false],
        'meta_twitter_creator' => ['name' => 'twitter:creator', 'property' => false],
        'meta_og_url' => ['name' => false, 'property' => 'og:url'],
        'meta_og_type' => ['name' => false, 'property' => 'og:type'],
        'meta_og_title' => ['name' => false, 'property' => 'og:title'],
        'meta_og_description' => ['name' => false, 'property' => 'og:description'],
        'meta_og_image' => ['name' => false, 'property' => 'og:image'],
        'meta_robots' => ['name' => 'robots', 'property' => false],
        'meta_googlebot' => ['name' => 'googlebot', 'property' => false],
        'meta_google_site_verification' => ['name' => 'google-site-verification', 'property' => false],
    ];

    public function saveCompleteMessage(): void
    {
        session()->flash('message', trans('content.save_complete'));
    }

    public function getSessionKey(FormRequest $request): string
    {
        return $request->has('id') && (int)$request->input('id') ? 'edit'.$request->id.'_steps' : 'steps';
    }
}
