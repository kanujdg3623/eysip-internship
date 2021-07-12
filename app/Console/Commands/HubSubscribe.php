<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use \Pubsubhubbub\Subscriber\Subscriber;

class HubSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hub:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to push notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $hub_url      = "http://pubsubhubbub.appspot.com";
		$callback_url = "http://media.e-yantra.org/youtube-notification";
		$s = new Subscriber($hub_url, $callback_url);
		$feed = "https://www.youtube.com/xml/feeds/videos.xml?channel_id=UCWfSeyt5dV39luJknVQhFzA";
		$s->subscribe($feed);
    }	
}
