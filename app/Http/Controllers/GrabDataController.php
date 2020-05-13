<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Capture\GrabDataModel;
use Illuminate\Support\Facades\Redis;

class GrabDataController extends Controller
{
    /**

     * @api {get} /getGrabData Request grab information

     * @apiName getGrabData

     * @apiGroup grab

     *

     * @apiParam {Number} id Users unique ID.

     *

     * @apiSuccess {String} firstname Firstname of the User.

     * @apiSuccess {String} lastname  Lastname of the User.

     */

    public function getGrabData()
    {
        $this->redis = new \Redis;
        $this->redis->connect('127.0.0.1', 6379, 7*24*3600);
        $this->redis->auth('yufei1234');
        $a = $this->redis->setex('user_1',600,2);
        var_dump($a);exit;
        // $ret = GrabDataModel::all();
        // var_dump($ret->toArray());
        //$ret = Redis::get('name');
        //Redis::publish('test-channel', json_encode(['foo' => '飞啊飞啊发饿撒哦粉撒飞洒啊发俄双方打算发的啊似懂非懂爱上发']));
    }   
}
