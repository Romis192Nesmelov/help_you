<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TicketsController extends BaseController
{
    use HelperTrait;

    public function tickets() :View
    {
        $this->data['active_left_menu'] = 'tickets';
        if (request()->has('id')) {

        } else return $this->showView('tickets');
    }

    public function getTickets(): JsonResponse
    {
//        return response()->json([
//            'news_subscriptions' => ReadOrder::query()
//                ->whereIn('subscription_id',Subscription::query()->default()->pluck('id')->toArray())
//                ->where('read',null)
//                ->with('order.user')
//                ->get(),
//            'news_performers' => ReadPerformer::query()
//                ->whereIn('order_id',Order::where('user_id',Auth::id())->pluck('id')->toArray())
//                ->where('read',null)
//                ->with('order')
//                ->with('user')
//                ->get(),
//            'news_removed_performers' => ReadRemovedPerformer::query()
//                ->where('user_id', Auth::id())
//                ->where('read', null)
//                ->with('order')
//                ->get(),
//            'news_status_orders' => ReadStatusOrder::query()
//                ->whereIn('order_id',Order::where('user_id',Auth::id())->pluck('id')->toArray())
//                ->where('read',null)
//                ->with('order')
//                ->get(),
//            'news_incentive' => ActionUser::query()
//                ->where('user_id', Auth::id())
//                ->where('read', null)
//                ->where('active', 1)
//                ->with('action')
//                ->get(),
//        ]);
    }
}
