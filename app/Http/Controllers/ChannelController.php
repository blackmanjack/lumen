<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Channel;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(Request $request)
    {
        $data = new Channel();
        $data->value = $request->value;
        $save = $data->save();

        if($save){
            $message = "Success add new Channel";
            return response()->json($message, 201);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 400);
        }
    }

    public function showAll()
    {
        $response = Channel::all();
        return response($response);
    }

    public function showDetailData($id)
    {
        $data = Channel::where('id', $id)->first();
        if($data){
            return response()->json($data, 200);
        }
        else{
            $message = "Not found";
            return response()->json($message, 404);
            }
    }

    // public function update(Request $request, $id)
    // {
    //     $data = Channel::find($id);
    //     // dd($request->all());
    //     // return response($request);
    //     $update = $data->update([
    //         'name'=> $request->name,
    //         'unit'=> $request->unit,
    //     ]);

    //      if($update){
    //         $message = "Success edit Channel";
    //         return response()->json($message, 200);
    //     }else{
    //         $message = "Empty Request Body";
    //         return response()->json($message, 400);
    //     }
    //     return response($response);
    // }

    // public function delete($id)
    // {
    //     $data = Channel::find($id);
    //     $delete = $data->delete();

    //     if($delete){
    //         $message = "Success delete Channel, id: $id";
    //         return response()->json($message, 200);
    //     }else{
    //         $message = "Parameter is Invalid";
    //         return response()->json($message, 404);
    //     }
    //     // return response($response);
    // }
    // //
}
