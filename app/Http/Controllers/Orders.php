<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class Orders extends Controller
{

	/**
     * @var class
     */
    protected $client;

	/**
     * Variables
     */
    protected $constants;
	
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->constants = Config::get('constants.order_history_api');
		$this->client = new Client(['base_uri' => $this->constants['base_uri']]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function get()
    {
		
		
        $urlStr = $this->constants['version'] . "/" . $this->constants['category'] . "/";
		
		// Initiate non-blocking request
		$promises = [];
		foreach($this->constants['tracking_numbers'] as $tracking_number) {
			$promises[] = $this->client->getAsync($urlStr . $tracking_number);
		}
		
		// Wait on all of the requests to complete. Throws a ConnectException
		// if any of the requests fail
		$results = Promise\unwrap($promises);

		// Wait for the requests to complete, even if some of them fail
		$results = Promise\settle($promises)->wait();
		
		return $results;
		
    }

}