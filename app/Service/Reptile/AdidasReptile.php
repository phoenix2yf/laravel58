<?php

namespace App\Service\Reptile;

use App;
use App\Base\RabbitmqBase;
use Goutte\Client as GoutteClient;
use App\Models\Capture\GrabDataModel;

/**
* 
*/
class AdidasReptile
{
	
	function __construct()
	{
		$this->client = App::make('Goutte\Client');
		$this->grabUrl = 'https://www.adidas.com.cn/search?ci=194&cf=2-8%2C2-8&pr=-&fo=c2%2Cc2&pageSize=48&c=%E9%9E%8B%E7%B1%BB-%E9%9E%8B%E7%B1%BB&isSaleTop=false&pn=';
		$this->rabbitmq = new RabbitmqBase('grab_data');
	}

	public function grabList()
    {   
    	echo '开始抓取';
    	for ($i=1; $i <= 30; $i++) { 
    		$this->grabSiteUrl = $this->grabUrl. $i;
    		echo 'page='.$i.'....';
    		$this->handleGrab();
    		echo '抓取完成!'."\n";
    	}
    	echo '抓取结束';
    }

    private function handleGrab()
    {
    	try {
	        $crawler = $this->client->request('GET', $this->grabSiteUrl);
	        $crawler->filter('.product-list-grid .list-item')->each(function ($node) {
	            $title = $node->filter('.goods-title h2')->text();
	            $info = $node->filter('.goods-info span')->text();
	            $detailUrl = $node->filter('.pro-big-img-box a')->attr('href');
	            $img = $node->filter('.pro-big-img-box > a > img')->attr('data-img-src');
	            $price = $node->filter('.goods-price')->text();

	            $msg = [
	            	'site_name' => '阿迪达斯官网',
	            	'type_name' => 'training_men_heat_rdy',
	            	'capture_url' => $this->grabSiteUrl,
	            	'product_data' => [
	            		'title' => $title,
	            		'info' => $info,
	            		'detail_url' => $detailUrl,
	            		'img' => $img,
	            		'price' => str_replace('$', '', $price),
	            	],
	            	'create_at' => time(),
            	];
	            $this->rabbitmq->publishedMsg(json_encode($msg));
	        });
    	} catch (\Exception $e) {
    		echo $e->getMessage();
    	}
        
    }

    public function saveGrabData()
    {
    	$this->rabbitmq->basicConsume(function($msg){
    		$queueData = json_decode($msg->body, true);
    		$queueData['product_data'] = json_encode($queueData['product_data']);
    		GrabDataModel::insert($queueData);
    		RabbitmqBase::ack($msg);
    	});
    }
}