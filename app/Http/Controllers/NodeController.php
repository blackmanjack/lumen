<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\Node;
use Illuminate\Http\Request;

class NodeController extends Controller
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
        $data = new Node();
        $data->id_hardware = $request->id_hardware;
        $data->name = $request->name;
        $data->location = $request->location;
        $save = $data->save();

        if($save){
            $message = "Success add new node";
            return response()->json($message, 201);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
    }

    public function showAll()
    {
        $data = Node::all();
        $response=
            // 'data'=> $data
            $data
        ;
        return response($response);
    }

    public function showDetailData($id)
    {
        $data = Node::where('id', $id)->first();
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
        $data = Node::find($id);
        // dd($request->all());
        // return response($request);
        $update = $data->update([
            'name'=> $request->name,
            'location'=> $request->location,
        ]);

         if($update){
            $message = "Success edit node";
            return response()->json($message, 200);
        }else{
            $message = "Empty Request Body";
            return response()->json($message, 400);
        }
        return response($response);
    }

    public function delete($id)
    {
        $data = Node::find($id);
        $delete = $data->delete();

        if($delete){
            $message = "Success delete node, id: $id";
            return response()->json($message, 200);
        }else{
            $message = "Parameter is Invalid";
            return response()->json($message, 404);
        }
        // return response($response);
    }
    //
}
