<?php

namespace App\Http\Controllers;

// require '/vendor/autoload.php';
use Illuminate\Support\Facades\DB;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CloudVisionController extends Controller
{
    public function getVisionData()
    {
        $images = DB::table('vision')->get();
        // dd($db);
        return view('vision', ['images' => $images]);
    }

    public function getCloudVision(Request $request){
        $images = (array)$request->images;
        $client = new ImageAnnotatorClient();
        $dataArray = [];
        foreach($images as $image){
            $file = file_get_contents($image);
            $response = $client->labelDetection($file);
            $labels = $response->getLabelAnnotations();

            if ($labels) {
                $keyword = '';
                foreach ($labels as $label) {
                    $keyword = $keyword . $label->getDescription() . PHP_EOL . ',';
                }

                if($keyword != ''){
                    $keyword = substr($keyword,0,-1);
                    $data = [
                        'image' => (String)$image, 'keyword' => (String)$keyword, 
                    ];
            
                    array_push($dataArray, $data);
                }
                
            } else {
                $message = $image . '此圖並無特徵';
                return $message;
            }

        }

        try {
            $db = DB::table('vision')->insert($dataArray);

        } catch (Exception $e) {
            $message = '新增失敗!';
            return $message;
        }

        $client->close();
        return $message = '新增成功!';
    }
}