<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Models\Order;
use Log;
use DB;

class ProcessFlashSaleOrder implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $unid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $unid = 0)
    {
        $this->order = $order;
        $this->unid = $this->order->id . '-' . rand() ;
        $this->onQueue('flashsale');
    }

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 86400;

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->unid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // DB::beginTransaction();
        // try {
        //     $product = $this->order->product;
        //     if ($product->units - $this->order->quantity >= 0) {
        //         DB::table('products')->update(['units' => $product->units - 1])
        //             ->where('id', $product->id);
        //         DB::table('orders')->update(['status' => Order::ORDER_ACCEPTED])
        //             ->where('id', $this->order->id);
        //     } else {
        //         DB::table('orders')->update(['status' => Order::ORDER_CANCELED])
        //             ->where('id', $this->order->id);
        //     }
        //     DB::commit();
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     throw new Exception($e->getMessage());
        // }
        Log::info('Order is processed ' . $this->order->id);
        usleep(1 * 1000 * 1000);
        Log::info('Order is processed completed ' . $this->order->id);
    }
}
