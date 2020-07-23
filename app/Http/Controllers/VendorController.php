<?php

namespace App\Http\Controllers;

use App\Http\Resources\VendorResource;
use App\Http\Resources\OrderResource;
use App\Vendor;
use App\Order;
use App\DetailOrder;
use DB;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // Search Restaurant and get menu
        $validator = Validator::make($request->all(), [
            'tag' => 'required|max:128'
        ],
        [
            'tag.required' => 'Restaurant name is required',
        ]);
        if ($validator->fails()) {
            return response(['response'=> $validator->messages()], 422);
        }else {
            $data = VendorResource::collection(Vendor::where('name', 'Like', '%'.$request->tag.'%')->get());
            if (count($data) == 0) {
                return response('Restaurant Not Found!', 404);
            }else {
                return response($data, Response::HTTP_OK);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Save order from user
        $this->validate($request,
            [
                'vendor_id' => 'required',
                'tag_id' => 'required',
                'qty' => 'required',
                'note' => 'max:255',
            ],
            [
                'vendor_id.required' => 'Please choose a restaurant',
                'tag_id.required' => 'Please choose a menu',
                'qty.required' => 'Qty is required',
            ]
        );
        DB::transaction(function () use ($request) {
            $order = New Order;
            $order->vendor_id = $request->vendor_id;
            $order->total_qty = array_sum($request->qty);
            $order->save();
            for ($i=0; $i < count($request->tag_id); $i++) { 
                $d_order = New DetailOrder;
                $d_order->order_id = $order->id; 
                $d_order->tag_id = $request->tag_id[$i];
                $d_order->qty = $request->qty[$i];
                $d_order->note = $request->note[$i];
                $d_order->save();
            }
        });
        return response('Success', Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get Order from user
        $data = Order::select('vendor_id','total_qty')->find($id);
        $data->detail_order = DetailOrder::select('order_id','tag_id','qty','note')->where('order_id',$id)->get();
        return response($data, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Update order from user
        $this->validate($request,
            [
                'vendor_id' => 'required',
                'tag_id' => 'required',
                'qty' => 'required',
                'note' => 'max:255',
            ],
            [
                'vendor_id.required' => 'Please choose a restaurant',
                'tag_id.required' => 'Please choose a menu',
                'qty.required' => 'Qty is required',
            ]
        );
        DB::transaction(function () use ($request,$id) {
            $order = Order::find($id);
            $order->vendor_id = $request->vendor_id;
            $order->total_qty = array_sum($request->qty);
            $order->save();
            DetailOrder::where('order_id',$id)->delete();
            for ($i=0; $i < count($request->tag_id); $i++) { 
                $d_order = New DetailOrder;
                $d_order->order_id = $id; 
                $d_order->tag_id = $request->tag_id[$i];
                $d_order->qty = $request->qty[$i];
                $d_order->note = $request->note[$i];
                $d_order->save();
            }
        });
        return response('Success', Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Delete order from user
        DB::transaction(function () use ($id) {
            $order = Order::find($id)->delete();
            DetailOrder::where('order_id',$id)->delete();
        });
        return response('Success', Response::HTTP_OK);
    }
}
