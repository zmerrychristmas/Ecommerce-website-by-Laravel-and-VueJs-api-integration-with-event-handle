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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Events\OrderShipmentStatusUpdated;

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
        $message = '';
        DB::beginTransaction();
        try {
            $product = $this->order->product;
            if ($product->units - $this->order->quantity >= 0) {
                DB::table('products')->where('id', $product->id)->update(['units' => $product->units - 1]);
                DB::table('orders')->where('id', $this->order->id)->update(['status' => Order::ORDER_ACCEPTED]);
                $message = 'ORDER_ACCEPTED';
            } else {
                DB::table('orders')->where('id', $this->order->id)->update(['status' => Order::ORDER_CANCELED]);
                $message = 'ORDER_CANCELED';                    
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $message = 'ORDER_ERROR';                    
            throw new Exception($e->getMessage());
        }
        Log::info('Order is processed ' . $this->order->id);
        //usleep(1 * 1000 * 1000);
        $this->order = Order::find($this->order->id);
        OrderShipmentStatusUpdated::dispatch($this->order, $message);
        Log::info('Order is processed completed ' . $this->order  . ' status: ' . $this->order->status);
    }
}
