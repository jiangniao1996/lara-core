<?php

namespace App\Http\Controllers;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;

class TestController extends BaseController
{
    public function index(Request $request)
    {

//        $hosts = ['https://elastic:rootroot@0.0.0.0:9201'];
        $hosts = ['https://localhost:9201'];

        $client = ClientBuilder::create()->setHosts(['http://192.168.31.96:9202'])
//            ->setBasicAuthentication("elastic", "rootroot")
//                ->setSSLVerification(false)
                ->build();

        $r = $client->create()->asArray();
        dd($r);
    }

}
