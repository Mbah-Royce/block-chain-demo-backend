<?php

namespace App\Http\Controllers;

use App\Http\Requests\PartitionTitleTransRequest;
use App\Models\LandCertificate;
use App\Models\User;
use Illuminate\Http\Request;

class PartionTitleTransactionController extends Controller
{
    public function create(PartitionTitleTransRequest $request)
    {
        $data = [];
        $message = "Transaction Successful";
        $statusCode = 201;
        $userSender = User::where("public_key", $request->sender_pub)->first();
        $userReciever = User::where("public_key", $request->reciever_pub)->first();
        $landCetificate = LandCertificate::where(['serial_no' => $request->serial_no, 'user_id' => auth()->user()->id])->first();


        /*** create the partition for reciever **/
        $reciverPartition = $landCetificate->partition()->create([
            'area' => $request->reciever_feature_area,
            'user_id' => $userReciever->id,
            'feature_type' => $request->reciever_feature_type,
            'feature_id' => $request->reciever_feature_id,
            'coordinate_lenth' => $request->coordinate_length
        ]);

        $cor = $request->reciever_feature_coordinates;
        foreach ($cor as $coordinate) {
            $reciverPartition->coordinate()->create([
                'lat' => $coordinate[0],
                'lng' => $coordinate[1]
            ]);
        }

        /*** create the partition for sender **/
        $senderPartition = $landCetificate->partition()->create([
            'area' => $request->sender_feature_area,
            'user_id' => $userSender->id,
            'feature_type' => $request->sender_feature_type,
            'coordinate_lenth' => $request->sender_feature_coordinate_length
        ]);
        $cor = $request->sender_feature_coordinates;
        if ($senderPartition->feature_type == 'MultiPolygon') {
            for ($i = 0; $i < $request->sender_feature_coordinate_length; $i++) {
                foreach ($cor[$i] as $coordinate) {
                    foreach($coordinate as $value){
                        $senderPartition->coordinate()->create([
                            'lat' => $value[0],
                            'lng' => $value[1],
                            'array_position' => $i
                        ]);
                    }
                }
            }
        } else {
            for ($i = 0; $i < $request->sender_feature_coordinate_length; $i++) {
                    foreach ($cor[$i] as $key => $value) {
                        $senderPartition->coordinate()->create([
                            'lat' => $value[0],
                            'lng' => $value[1],
                            'array_position' => $i
                        ]);
                    }
                
            }
        }

        $landCetificate->update([
            'partitioned' => true
        ]);

        $data['reciver'] = $reciverPartition;
        $data['sender'] = $senderPartition;

        $transaction = createTransaction(
            $request->reciever_pub,
            $request->sender_pub,
            $landCetificate->id,
            $request->serial_no,
            $request->signature,
            $request->reciever_feature_area,"
            Partition-Title"
        );
        createBlock($transaction->id,
        $transaction->area,
        $transaction->reciever,
        $transaction->sender,
        $transaction->signature,
        $userSender,
        $userReciever);

        return apiResponse($data, $message, $statusCode);
    }
}
