<?php

namespace App\Http\Controllers;

// require 'vendor/autoload.php';

use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use \Firebase\JWT\JWT;

class HomeController extends Controller
{
	public function verification(Request $request)
	{
		$key = '344'; //key要和签发的时候一样
		$page = '';

		$jwt = (String)$request->token;
		try {
	       		JWT::$leeway = 60;//当前时间减去60，把时间留点余地
	       		$decoded = JWT::decode($jwt, $key, ['HS256']); //HS256方式，这里要和签发的时候对应
	       		$arr = (array)$decoded;
				$page = $arr['data']->page;
	    	} catch(\Firebase\JWT\SignatureInvalidException $e) {  //签名不正确
	    		echo $e->getMessage();
	    	}catch(\Firebase\JWT\BeforeValidException $e) {  // 签名在某个时间点之后才能用
	    		echo $e->getMessage();
	    	}catch(\Firebase\JWT\ExpiredException $e) {  // token过期
	    		echo $e->getMessage();
	   	}catch(Exception $e) {  //其他错误
	    		echo $e->getMessage();
	    	}
	    //Firebase定义了多个 throw new，我们可以捕获多个catch来定义问题，catch加入自己的业务，比如token过期可以用当前Token刷新一个新Token
		
		return view('welcome', [
            'page' => $page
        ]);
	}
}