<?php

namespace App\Http\Controllers;

// require '/vendor/autoload.php';
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\ImageContext;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;
use Google\Cloud\Storage\StorageClient;
use Intervention\Image\ImageManagerStatic as Image;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;
use \Firebase\JWT\JWT;

class CloudVisionController extends Controller
{   
    public function getVisionDefaultData(){
        $key = '344'; //key
		$time = time(); //当前时间
       	$token = [
        	'iss' => 'http://www.helloweba.net', //签发者 可选
           	'aud' => 'http://www.helloweba.net', //接收该JWT的一方，可选
           	'iat' => $time, //签发时间
           	'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
           	'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' => [ //自定义信息，不要定义敏感信息
             	'page' => 'vision',
            ]
        ];
        
        return view('vision', [
            'token' => '/?token=' . (JWT::encode($token, $key)),
        ]);
    }

    public function getVisionDataTable(Request $request)
    {
        $db = DB::table('vision');
        $length = $request->input('length');
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValue = $request->input('search');
        $dataArray = [];
        $visionData = $db->get()->toArray();
        foreach($visionData as $data){
            $array = [];
            $array['image'] = $data->image;
            $array['keyword'] = $data->keyword;
            $array['editId'] = $data->id;
            array_push($dataArray, (object)$array);
        }

        $collection = collect($dataArray);

        if($searchValue != ''){
            $collection->search(function ($item, $key) {
                return $item->$key == $searchValue;
            });
        }
        
        if($sortBy != '' && $orderBy != ''){
            if($orderBy == 'asc'){
                $collection = $collection->sortBy($sortBy);
            }else if($orderBy == 'desc'){
                $collection = $collection->sortByDesc($sortBy);
            }
        }

        if($length != ''){
            $collection = $collection->forPage(1, $length);
        }

        return new DataTableCollectionResource($collection->values()->all());
    }

    public function saveCloudVision(Request $request){
        //先做圖片調整大小後暫存
        $poImage = $request->po_image;
        $file_name = uniqid().'.'.$poImage->getClientOriginalExtension();
        $file_path = public_path('images');
        if (!is_dir($file_path)){
            mkdir($file_path);
        }
        $thumbnail_file_path = $file_path . '\\' .$file_name;
        $image = Image::make($poImage)->resize(300, null, function ($constraint) {$constraint->aspectRatio();})->save($thumbnail_file_path, 60);
        //再從本機的storage的圖片上傳
        $storage = new StorageClient([
            'projectId' => 'useVision'
        ]);
        $uploadFile = fopen($thumbnail_file_path , 'r');
        $bucketName = 'vision-save-image';
        $bucket = $storage->bucket($bucketName);
        $imageName = $poImage->getClientOriginalName();
        
        $bucket->upload($uploadFile, [
            'name' => $imageName
        ]);

        $imageAnnotator = new ImageAnnotatorClient();
        $imageContext = new ImageContext();
        $imageContext->setLanguageHints(['en','zh-Hant']);
        $dataArray = [];
        $keyword = '';
        $googleStroageIamge = 'https://storage.googleapis.com/' . $bucket->name() . '/' .$imageName;
        try {
            $file = @file_get_contents($googleStroageIamge);
            $imageData = $imageAnnotator->labelDetection($file, [
                'imageContext' => $imageContext
            ]);
            $labelDetection = $imageData->getLabelAnnotations();
            $labelData = $imageData->getLabelAnnotations();
            if(count($labelData) > 0){
                foreach($labelData as $data){
                    $labels = explode("\n",$data->getDescription());
                    if(count($labels) > 1){
                        foreach($labels as $label){
                            $keyword = $keyword . $label . ',';
                        }
                    }else{
                        $keyword = $keyword . $data->getDescription() . ',';
                    }
                }
            }

            $textDetection = $imageAnnotator->textDetection($file, [
                'imageContext' => $imageContext
            ]);
            $textData = $textDetection->getTextAnnotations();
            if(count($textData) > 0){
                $texts = explode("\n", $textData[0]->getDescription());
                if(count($texts) > 1){
                    foreach($texts as $text){
                        $keyword = $keyword . $text . ',';
                    }
                }else{
                    $keyword = $keyword . $textData->getDescription() . ',';
                }
            }
            
            if($keyword != ''){
                $keyword = substr($keyword,0,-1);
                $dbData = [
                    'image' => (String)$googleStroageIamge, 'keyword' => (String)$keyword, 
                ];
                
                array_push($dataArray, $dbData);
            } else {
                $message = $image . '此圖並無特徵';
                return $message;
            }

            if(count($dataArray) > 0){
                $db = DB::table('vision')->insert($dataArray);
            }

        } catch (Exception $e) {
            $message = '新增失敗!';
            return $message;
        }

        $imageAnnotator->close();

        $key = '344'; //key
		$time = time(); //当前时间
       	$token = [
        	'iss' => 'http://www.helloweba.net', //签发者 可选
           	'aud' => 'http://www.helloweba.net', //接收该JWT的一方，可选
           	'iat' => $time, //签发时间
           	'nbf' => $time , //(Not Before)：某个时间点后才能访问，比如设置time+30，表示当前时间30秒后才能使用
           	'exp' => $time+7200, //过期时间,这里设置2个小时
            'data' => [ //自定义信息，不要定义敏感信息
             	'page' => 'vision',
            ]
        ];

        return view('vision', [
            'token' => '/?token=' . (JWT::encode($token, $key)),
        ]);
    }
}