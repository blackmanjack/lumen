<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Sensor;
use Illuminate\Http\Request;

class SensorController extends Controller
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
        $data = new Sensor();
        $data->name = $request->name;
        $data->unit = $request->unit;
        $save = $data->save();

        if($save){
            $message = "Success add new sensor";
            return response()->json($message, 201);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Sensor::all();
        $response=
            // 'data'=> $data
            $data
        ;
        return response($response);
    }

    public function showDetailData($id)
    {
        $data = Sensor::where('id', $id)->first();
        if($data){
            return response()->json($data, 200);
        }
        else{
            $message = "Not found";
            return response()->json($message, 404);
            }
    }

    public function update(Request $request, $id)
    {
        $data = Sensor::find($id);
        // dd($request->all());
        // return response($request);
        $update = $data->update([
            'name'=> $request->name,
            'unit'=> $request->unit,
        ]);

         if($update){
            $message = "Success edit Sensor";
            return response()->json($message, 200);
        }else{
            $message = "Empty Request Body";
            return response()->json($message, 400);
        }
        return response($response);
    }

    public function delete($id)
    {
        $data = Sensor::find($id);
        $delete = $data->delete();

        if($delete){
            $message = "Success delete Sensor, id: $id";
            return response()->json($message, 200);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
        // return response($response);
    }
    //
}
