<?php

namespace App\Http\Controllers;

use App\Enums\BidStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Job;
use App\Models\JobBid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        return Order::all();
    }

    public function singleOrder(Order $order)
    {
        return $order;
    }

    public function sellingOrders()
    {
        $worker = auth()->user();
        $orders =  $worker->sellingOrders()->with(['job', 'bid', 'buyer'])->get();
        $response = [
            'selling_orders' => $orders,
        ];
        return response($response, 200);
    }

    public function startOrder(Request $request)
    {
        $request->validate([
            'job_id' => 'required|numeric|min:1',
            'job_bid_id' => 'required|numeric|min:1',
        ]);

        $bid = JobBid::find($request->job_bid_id);
        $job = Job::find($request->job_id);
        $worker = $bid->offeredBy;

        $order = new Order();
        $order->bid()->associate($bid);
        $order->job()->associate($job);
        $order->worker()->associate($worker);
        $order->buyer()->associate($job->postedBy);
        $order->status = OrderStatus::Started;
        $order->save();

        $bid->status = BidStatus::Accepted;
        $bid->save();

        return response(['info' => 'Order created successfully!'], 200);

    }

    /**
     * Orders on which currently logged-in user has hired other workers
    */
    public function buyingOrders()
    {
        $user = auth()->user();
        $buyingOrders = $user->buyingOrders()->with(['job' , 'worker', 'bid'])->get();
        $response = [
            'buying_orders' => $buyingOrders,
        ];

        return response($response, 200);
    }

    /**
    * Called when a buyer mark the order as complete
     */
    public function completeOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric|min:1',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Ending time will be only added here if the buyer directly mark
        // the order as complete without any request from seller
        if($order->status == OrderStatus::Started) {
            $order->ending_time = Carbon::now()->timestamp;
        }
        $order->status = OrderStatus::Completed;
        $order->save();

        return response(['status' => 'Order completed successfully!'], 200);
    }

    /**
     * Called when a seller create an order completion request
     * buyer will mark the order as completed, finally
     */
    public function completionRequest(Request $request)
    {
        $request->validate([
            'order_id' => 'required|numeric|min:1',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->status = OrderStatus::RequestedForCompletion;
        $order->ending_time = Carbon::now()->timestamp;
        $order->save();

        return response(['status' => 'Order marked as completed from buyer!'], 200);
    }
}
