<?php

namespace App\Http\Controllers;

use App\Models\Partitions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartitionsController extends Controller
{
    public function show($id){
            $data = [];
            $message = "listed successfully";
            $statusCdoe = 200;
            $user = Auth::user();
            $partition = Partitions::where(["user_id" => $user->id, "id" => $id])->first();
            $coordinates = [];
            if($partition->feature_type == "Polygon"){
                if($partition->coordinate_lenth == NULL){
                    $coordinates = $partition->coordinate()->get()->map(function($coord){
                        return[
                            floatval($coord->lat),floatval($coord->lng)
                        ];
                    });
                }else{
                    for ($i=0; $i < $partition->coordinate_lenth ; $i++) { 
                        $coordinates[$i] = $partition->coordinate()->where(["array_position" => $i])->get()->map(function($coord){
                            return[
                                floatval($coord->lat),floatval($coord->lng)
                            ];
                        });
                    }
                }

            }else{

            }
            $data = [
                "id" => $partition->feature_id,
                "type" => "Feature",
                "properties" => [],
                "geometry" => [
                    "coordinates" => $coordinates,
                    "type" => $partition->feature_type,
                ],
                "area" => $partition->area,

            ];
     return apiResponse($data, $message, $statusCdoe);
    }
}
