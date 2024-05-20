<?php

namespace App\Http\Controllers\Admin;
use App\Events\Admin\AdminOrderEvent;
use App\Http\Controllers\HelperTrait;
use App\Http\Requests\Order\DelOrderImageRequest;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrdersController extends AdminBaseController
{
    use HelperTrait;

    public Order $order;

    public function __construct(Order $order)
    {
        parent::__construct();
        $this->order = $order;
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function orders($slug=null): View
    {
        $this->data['users'] = User::select(['id','name','family'])->get();
        $this->data['types'] = OrderType::with(['subtypes'])->get();
        return $this->getSomething($this->order, $slug);
    }

    public function getOrders(): JsonResponse
    {
        return response()->json([
            'orders' => $this->order::query()
                ->filtered()
                ->with(['user.ratings'])
                ->with(['orderType'])
                ->orderBy(request('field') ?? 'id',request('direction') ?? 'desc')
                ->paginate(request('show_by') ?? 10),
            'users' => User::query()->select(['id','name','family'])->get(),
            'types' => OrderType::query()->select(['id','name'])->get(),
        ]);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function editOrder(Request $request): RedirectResponse
    {
        $order = $this->editSomething (
            $request,
            $this->order,
            [
                'user_id' => $this->validationUserId,
                'order_type_id' => 'required|exists:order_types,id',
                'subtype_id' => 'nullable|exists:subtypes,id',
                'name' => 'required|min:3|max:50',
                'need_performers' => 'required|min:1|max:20',
                'address' => $this->validationString,
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'description_short' => 'nullable|string|min:5|max:200',
                'description_full' => 'nullable|string|min:5|max:1000',
                'status' => 'nullable|max:3'
            ],
        );

        $this->processingOrderImages($request, $order);
        broadcast(new AdminOrderEvent('change_item', $order));

        if ($request->status != 3) {
            $order->adminNotice->read = 1;
            $order->adminNotice->save();
        }

        $this->saveCompleteMessage();
        return redirect(route('admin.orders'));
    }

    public function deleteOrderImage(DelOrderImageRequest $request): JsonResponse
    {
        $order = Order::find($request->id);
        return $this->removeOrderImage($order, $request->pos);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function deleteOrder(Request $request): JsonResponse
    {
        return $this->deleteSomething($request, $this->order);
    }
}
