<?php

namespace App\Http\Controllers;

use App\Models\LandCertificate;
use App\Models\Partitions;
use Illuminate\Http\Request;

class GovernmentController extends Controller
{
    public function getLand(){
        $data = [];
        $message = "listed successfully";
        $statusCdoe = 200;
        $data = LandCertificate::get()->map(function($certificate){
            return [
                "id" => $certificate->feature_id,
                "type" => "Feature",
                "properties" => [],
                "geometry" => [
                    "coordinates" => [$certificate->coordinate()->get()->map(function($coord){
                        return[
                            $coord->lat,$coord->lng
                        ];
                    })],
                    "type" => $certificate->feature_type,
                ],
            ];
        });
        return apiResponse($data, $message, $statusCdoe);
    }

    public function getPartitions(){
        $data = [];
        $message = "listed successfully";
        $statusCdoe = 200;
        $data = Partitions::get()->map(function($partition){
            if($partition->feature_type == "Polygon"){
                if($partition->coordinate_lenth == NULL){
                    return [
                        "id" => $partition->feature_id,
                        "type" => "Feature",
                        "properties" => [],
                        "geometry" => [
                            "coordinates" => [$partition->coordinate()->get()->map(function($coord){
                                return[
                                    $coord->lat,$coord->lng
                                ];
                            })],
                            "type" => $partition->feature_type,
                        ],
                    ];
                }else{
                    $coordinates = [];
                    for ($i=0; $i < $partition->coordinate_lenth ; $i++) { 
                        $coordinates[$i] = $partition->coordinate()->where(["array_position" => $i])->get()->map(function($coord){
                            return[
                                $coord->lat,$coord->lng
                            ];
                        });
                    }
                    return [
                        "id" => "",
                        "type" => "Feature",
                        "properties" => [],
                        "geometry" => [
                            "coordinates" => $coordinates,
                            "type" => $partition->feature_type,
                        ],
                    ];
                }

            }else{

            }
            return [
                "id" => $partition->feature_id,
                "type" => "Feature",
                "properties" => [],
                "geometry" => [
                    "coordinates" => [$partition->coordinate()->get()->map(function($coord){
                        return[
                            $coord->lat,$coord->lng
                        ];
                    })],
                    "type" => $partition->feature_type,
                ],
                // "area" => $certificate->area,
                // "serial_no" => $certificate->serial_no,
                // "user" => $certificate->user
            ];
        });
        return apiResponse($data, $message, $statusCdoe);
    }
}
