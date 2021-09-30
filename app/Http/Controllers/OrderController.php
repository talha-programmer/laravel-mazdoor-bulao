<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Job;
use App\Models\JobBid;
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
            'job_id' => 'required|numeric|min:1',
            'job_bid_id' => 'required|numeric|min:1',
            'worker_id' => 'required|numeric|min:1',

        ]);

        $bid = JobBid::find($request->job_bid_id);
        $job = Job::find($request->job_id);
        $worker = User::find($request->worker_id);

        $order = new Order();
        $order->bid()->associate($bid);
        $order->job()->associate($job);
        $order->worker()->associate($worker);

        $order->save();

        return response(['info' => 'Order created successfully!'], 200);

    }
}
