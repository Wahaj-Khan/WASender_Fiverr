<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Plan;
use App\Models\User;
use Hash;
use Str;
use Auth;
use App\Traits\Seo;
use Session;

$userEnteredOtp = null;
$userDBOtp = null;
class PricingController extends Controller
{

    use Seo;

    /**
     * Display a pricing page of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $faqs = Post::where('type', 'faq')->where('featured', 1)->where('lang', app()->getLocale())->with('excerpt')->latest()->get();
        $plans = Plan::where('status', 1)->latest()->get();

        $this->metadata('seo_pricing');

        return view('frontend.plans', compact('faqs', 'plans'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @param  Request 
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request, $id)
    {
        $plan = Plan::where('status', 1)->findorFail($id);

        $meta['title'] = $plan->title ?? '';
        $this->pageMetaData($meta);


        return view('frontend.register', compact('plan', 'request'));
    }
    /** 
     * @param  Request 
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function otp(Request $request)
    {
        $email = session('email');
        $id = session('plan_id');
        return view('frontend.otp-verification', compact('id', 'email'));
    }


    /**
     * register a user with plan
     *
     * @param  integer  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerPlan(Request $request, $id)
    {


        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $plan = Plan::where('status', 1)->findOrFail($id);

        $user = new User;
        $user->name        = $request->name;
        $user->email       = $request->email;
        $user->role        = 'user';
        $user->status      = 1;
        $user->plan        = json_encode($plan->data);
        $user->plan_id     = $plan->id;
        $user->will_expire = $plan->is_trial == 1 ? now()->addDays($plan->trial_days) : null;
        $user->authkey     = $this->generateAuthKey();
        $user->password    = Hash::make($request->password);
        $user->otp         = rand(100000, 999999);

        if ($user->save()) {
            session(['email' => $request->email, 'plan_id' => $plan->id]);
            return redirect('otp_verification_view');
        } else {
            //die;
            return redirect()->back()->withInput()->withErrors(['otp' => __('Error with sending OTP.')]);
        }
    }

    public function otp_verification(Request $request)
    {
        $userEnteredOtp = $request->otp;

        // Note: Getting user email from the session created when user signup
        $email = session('email');

        $userOTP = User::where('email', $email)->first();

        if ($userOTP && $userOTP->otp == $userEnteredOtp) {

            // Update 'email_verified_at' field to the current timestamp
            $userOTP->update(['email_verified_at' => now()]);
            Auth::login($userOTP);

            if ($userOTP->will_expire == null) {
                return redirect('user/subscription/' . $userOTP->plan_id);
            }

            Session::put('new-user', __('Let\'s create a WhatsApp device'));
            return redirect('/user/device/create');
        } else {

            return redirect()->back()->with('error', 'Invalid OTP');
        }
    }


    public function otp_resend(Request $request)
    {
        $email = session('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            // Generate a new OTP and save it to the user
            $newOtp = rand(100000, 999999);
            $user->otp = $newOtp;
            $user->save();

            // Flash a message to indicate successful OTP resend
            Session::flash('success', 'OTP has been resent successfully.');
        }

        // Redirect back to the OTP verification page
        return redirect()->back();
    }


    /**
     * generate auth key
     */
    public function generateAuthKey()
    {
        $rend = Str::random(50);
        $check = User::where('authkey', $rend)->first();

        if ($check == true) {
            $rend = $this->generateAuthKey();
        }
        return $rend;
    }
}
