<?php

namespace App\Http\Controllers;

// require 'vendor/autoload.php';

use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Google\Cloud\Vision\VisionClient;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CloudVisionController extends Controller
{
    public function getCloudVision(){
        // $client = new ImageAnnotatorClient();
        $vision = new VisionClient();

        // $path = 'https://i.imgur.com/l8yNat5.jpg';
        $familyPhotoResource = fopen('C:/Users/coolg/Downloads/12.jpg', 'r');

        $image = $vision->image($familyPhotoResource, [
            'FACE_DETECTION'
        ]);

        $result = $vision->annotate($image);
        dd($result);
        // $image = file_get_contents($path);
        // $response = $client->faceDetection($image);
        // $faces = $response->getFaceAnnotations();

        // Annotate an image, detecting faces.
        // $annotation = $client->annotateImage(
        //     fopen('http://127.0.0.1:8000/images/clock-bg-sm.png', 'r'),
        //     [Type::FACE_DETECTION]
        // );

        dd($faces);

        // Determine if the detected faces have headwear.
        // foreach ($annotation->getFaceAnnotations() as $faceAnnotation) {
        //     $likelihood = Likelihood::name($faceAnnotation->getHeadwearLikelihood());
        //     echo "Likelihood of headwear: $likelihood" . PHP_EOL;
        // }
    }
}