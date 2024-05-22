<?php

namespace App\Http\Controllers\Admin;
use App\Actions\DeleteFile;
use App\Actions\ProcessingImage;
use App\Events\Admin\AdminPartnerEvent;
use App\Http\Controllers\HelperTrait;
use App\Http\Requests\Admin\AdminEditPartnerRequest;
use App\Models\Partner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPartnersController extends AdminBaseController
{
    use HelperTrait;

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function partners(Partner $partners, $slug=null): View
    {
        return $this->getSomething($partners, $slug);
    }

    public function getPartners(Partner $partners): JsonResponse
    {
        return response()->json(
            $partners::query()
                ->filtered()
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10)
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editPartner(
        AdminEditPartnerRequest $request,
        ProcessingImage $processingImage
    ): RedirectResponse
    {
        $fields = $request->validated();
        $logoPath = 'images/partners/';

        if ($request->has('id')) {
            $partner = Partner::query()->where('id',$request->input('id'))->with('actions')->first();
            $processingImage->handle($request, $fields, 'logo', $logoPath, 'logo'.$partner->id);
            $partner->update($fields);
            broadcast(new AdminPartnerEvent('new_item',$partner));
        } else {
            $partner = Partner::query()->create($fields);
            $processingImage->handle($request, [], 'logo', $logoPath, 'logo'.$partner->id);
            $partner->update($fields);
            broadcast(new AdminPartnerEvent('change_item',$partner));
        }
        $this->saveCompleteMessage();
        return redirect(route('admin.partners'));
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deletePartner(
        Request $request,
        DeleteFile $deleteFile,
    ): JsonResponse
    {
        $this->validate($request, ['id' => 'required|exists:partners,id']);
        $partner = Partner::find($request->id);
        if ($partner->logo) $deleteFile->handle($partner->avatar);
        $partner->delete();
        return response()->json([],200);
    }
}
