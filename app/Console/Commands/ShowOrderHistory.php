<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowOrderHistory extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ShowOrderHistory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display Order History';
	
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

		date_default_timezone_set("Asia/Manila");
	
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		// Parse Order API
		$Orders = new \App\Http\Controllers\Orders;
		$results = $Orders->get();
		
		// Process results
		if($results) {
			$totalCollections = 0;
			$totalSales = 0;
			foreach($results as $result) {
				$res = (string)$result['value']->getBody();
				$res = json_decode($res);
				
				// re-construct output
				if($res) {
					$this->displayOutput($res);
				}
				
				// get total computations
				$totalCollections += (float)$res->total;
				$totalSales += (float)$res->transaction_fee + (float)$res->insurance_fee + (float)$res->shipping_fee;
			}
			
			// print total computations
			echo "total collections: {$totalCollections}\n";
			echo "total sales: {$totalSales}\n";
			
		}
		
    }
	/**
	 * Display response output
	 * @params $res
	 * @return
	 */
	public function displayOutput($res) 
	{
		// Tracking Number / Status
		echo "{$res->tracking_number} ({$res->status})\n";
		
		// Date History
		echo "  history:\n";
		
		// print dates
		$dates = (array)$res->tat;
		asort($dates);
		foreach($dates as $key => $val) {
			$d = date('Y-m-d H:i:s.u', $val);
			echo "    {$d}: {$key}\n";
		}
		
		// Breakdown
		echo "  breakdown:\n";
		echo "    subtotal: {$res->subtotal}\n";
		echo "    shipping: {$res->shipping}\n";
		echo "    tax: {$res->tax}\n";
		echo "    fee: {$res->fee}\n";
		echo "    insurance: {$res->insurance}\n";
		echo "    discount: {$res->discount}\n";
		echo "    total: {$res->total}\n";
		
		// Fees
		echo "  fees:\n";
		echo "    shipping fee: {$res->shipping_fee}\n";	
		echo "    insurance_fee fee: {$res->insurance_fee}\n";
		echo "    transaction_fee: {$res->transaction_fee}\n\n";

		return;
	}
	
}
