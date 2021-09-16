<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Project;
use App\Models\ProjectBid;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::all();
    }

    public function singleOrder(Order $order)
    {
        return $order;
    }

    public function workerOrders()
    {
        $worker = auth()->user();
        return $worker->orders;
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|numeric|min:1',
            'project_bid_id' => 'required|numeric|min:1',
            'worker_id' => 'required|numeric|min:1',

        ]);

        $bid = ProjectBid::find($request->project_bid_id);
        $project = Project::find($request->project_id);
        $worker = User::find($request->worker_id);

        $order = new Order();
        $order->bid()->associate($bid);
        $order->project()->associate($project);
        $order->worker()->associate($worker);

        $order->save();

        return response(['info' => 'Order created successfully!'], 200);

    }
}
