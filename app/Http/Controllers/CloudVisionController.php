<?php

namespace App\Http\Controllers;

// require '/vendor/autoload.php';
use Illuminate\Support\Facades\DB;
use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\ImageContext;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;
use Intervention\Image\ImageManagerStatic as Image;
use JamesDordoy\LaravelVueDatatable\Http\Resources\DataTableCollectionResource;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CloudVisionController extends Controller
{
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
        $poImage = $request->po_image;
        $imageName = $poImage->getClientOriginalName();
        $imagePath = $poImage->store("uploads", 'public');
        $storageImagePath = public_path("storage/{$imagePath}");
        $image = Image::make($storageImagePath)->resize(300, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($storageImagePath, 60);
        $image->save();

        $imageAnnotator = new ImageAnnotatorClient();
        $imageContext = new ImageContext();
        $imageContext->setLanguageHints(['en','zh-Hant']);
        $dataArray = [];
        $keyword = '';
        
        try {
            $file = @file_get_contents($storageImagePath);
            $imageData = $imageAnnotator->labelDetection($file, [
                'imageContext' => $imageContext
            ]);
            $labelDetection = $imageData->getLabelAnnotations();
            $labelData = $imageData->getLabelAnnotations();
            if($labelData != null){
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
            // dd($textData);
            $textCount = $textData;
            if($textData != null){
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
                $path = 
                $dbData = [
                    'image' => (String)'storage/'.$imagePath, 'keyword' => (String)$keyword, 
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
        return view('vision');
    }
}