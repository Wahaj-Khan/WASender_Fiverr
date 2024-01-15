<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Coupons;
use App\Models\Plan;
use App\Models\Postmeta;
use DB;
use Auth;
use Str;
class CoupounsController extends Controller
{
    public function __construct(){
         $this->middleware('permission:faq'); 
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coupons = Coupons::all();
        $plans = Plan::all();
        $languages = get_option('languages',true);

        return view('admin.coupoun.index',compact('coupons','languages','plans'));
    }

    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'coupon_code'  => 'required',
            'discount'    => 'required',
            'plan_id'    => 'required',
        ]);

        try {

           $coupon = new Coupons;
           $coupon->coupon_code = $request->coupon_code;
           $coupon->discount  = $request->discount;
           $coupon->plan_id  = $request->plan_id;
           $coupon->save();


        } catch (Throwable $th) {
            DB::rollback();

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'redirect' => route('admin.coupouns.index'),
            'message'  => __('Faq created successfully...')
        ]);  
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

        $request->validate([
            'question'  => 'required',
            'answer'    => 'required|max:500',
        ]);

        
       DB::beginTransaction();
        try {
            
           $post =  Post::findorFail($id);
           $post->title = $request->question;
           $post->slug  = $request->position ?? 'bottom';
           $post->type  = 'faq';
           $post->lang  = $request->language ?? 'en';
           $post->save();

           $post->excerpt()->update([
            'post_id' => $post->id,
            'key' => 'excerpt',
            'value' => $request->answer,
           ]);

            DB::commit();

        } catch (Throwable $th) {
            DB::rollback();

            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }

        return response()->json([
            'redirect' => route('admin.faq.index'),
            'message'  => __('Faq updated successfully...')
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::where('type','faq')->findorFail($id);
        $post->delete();

        return response()->json([
            'redirect' => route('admin.faq.index'),
            'message'  => __('Faq deleted successfully...')
        ]);
    }
}
