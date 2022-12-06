<?php

namespace App\Http\Controllers\registration;

use PDO;
use App\Mail\ptoolzMail;
use PharIo\Manifest\Email;
use Illuminate\Http\Request;
use App\Models\users\customer;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\users\serviceProvider;
use App\Http\Controllers\emailController;
use Illuminate\Support\Facades\Validator;
use App\Models\order\eductional\academicpermission;

class userController extends Controller
{
    function __construct()
    {
        if (getRequestLanguage()  != 'en')
            App::setLocale(getRequestLanguage());
    }

    public function signup()
    {
        if (request()->account_type_id == 1) {
            $customer = new customer();
            return $customer->store();
        } elseif (request()->account_type_id == 2) {
            $sp = new serviceProvider();
            return $sp->store();
        }
    }


    function login(Request $request)
    {
        $validator = Validator::make(
            request()->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ],
            validationMessages()
        );



        if ($validator->fails()) {
            return apiresponse(false, 200, $validator->errors()->first());
        }
        // customer login
        // customer login
        if (Auth::guard('customer')->attempt(['email' => $request->email, 'password' => $request->password]))   // auth user after register
        {
            $user =  Auth('customer')->user();


            $token = $user->createToken('api-token')->plainTextToken;

            return apiresponse(true, 200, __('auth.success_proccess'), [
                'user' => $user,
                'token' => $token
            ]);
        }
        // sp login
        elseif (Auth::guard('sp')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user  = Auth('sp')->user();
            $user->academic_permission = academicpermission::where('user_id' , $user->id)->first();

            $token = $user->createToken('api-token')->plainTextToken;

            return apiresponse(true, 200, __('auth.success_proccess'), [
                'user' => $user,
                'token' => $token
            ]);
        }

        return apiresponse(false, 200, __('auth.failed_login'));
    }



    public function sendCode($type)
    {

        if ($type == 'change_password') {

            // email validation
            $validator = Validator::make(request()->all(),['email' => 'required|email', ],validationMessages());
            if($validator->fails())
            return apiresponse(false,200,$validator->errors()->first());


            // email exist

            if (customer::where("email", request()->email)->count() > 0)   // auth user after register
            $user =  customer::where("email", request()->email)->first();
           elseif (serviceProvider::where("email", request()->email)->count() > 0)
             $user  = serviceProvider::where("email", request()->email)->first();
           else
            return apiResponse(false, 200, __('auth.email_not_found'));


            // send code
            if ($user->emailcode()->where('type', 2)->count() == 0) {
                $user->emailcode()->create([
                    'code' => random_int(10, 99) . rand(10, 99),
                    'type' => '2',
                ]);
            }

            $userCode = $user->emailcode()->where('type', 2)->first();
            $userCode->code = random_int(10, 99) . random_int(10, 99);
            $userCode->save();

            $params = [
                'type' => 'password_change',
                'title' => 'change password',
                'body' => 'change your password with this code',
                'code' => $userCode->code,
                'to' => $user->email
            ];
            $email = new emailController();
            $email->Send($params);
            return apiresponse(true, 200, __('auth.code_sent_email'));
     }


        elseif ($type == 'account_verification') {
  // email sent
            if(isset(request()->email)){
                if (customer::where("email", request()->email)->count() > 0)   // auth user after register
                $user =  customer::where("email", request()->email)->first();
               elseif (serviceProvider::where("email", request()->email)->count() > 0)
                 $user  = serviceProvider::where("email", request()->email)->first();
               else
                return apiResponse(false, 200, __('auth.email_not_found'));


                $useremailCode = $user->emailcode()->where('type', 1)->first();
                $useremailCode->code =  random_int(10, 99) . rand(10, 99);
                $useremailCode->save();
                $params = [
                    'type' => 'email_verification',
                    'title' => 'email verification',
                    'body' => ' verificate your email with this code',
                    'code' => $useremailCode->code,
                    'to' => $user->email
                ];
                $email = new emailController();
                $email->Send($params);
            }


        if(isset(request()->mobile))
    {

        if (customer::where("mobile", request()->mobile)->count() > 0)   // auth user after register
        $user =  customer::where("mobile", request()->mobile)->first();
       elseif (serviceProvider::where("mobile", request()->mobile)->count() > 0)
         $user  = serviceProvider::where("mobile", request()->mobile)->first();
       else
       return apiresponse(false,200,__('auth.mobile_not_found'));


       $userMobileCode = $user->mobileCode()->where('type', 1)->first();
       $userMobileCode->code = 1234;
       $userMobileCode->save();

    }

            // send mobile msg
            return apiresponse(true, 200, __('auth.verification_code'));

        }
    }



    public function checkCode($type)
    {


        $email = request()->email;

        $validator = Validator::make(request()->all(),
        [
           'email' => 'required|email',
        ],
        validationMessages()
        );

        if($validator->fails())
        return apiresponse(false,200,$validator->errors()->first());

        if (customer::where("email", request()->email)->count() > 0)   // auth user after register
            $user =  customer::where("email", request()->email)->first();


        elseif (serviceProvider::where("email", $email)->count() > 0)
            $user  = serviceProvider::where("email", $email)->first();
        else
            return apiResponse(false,200, __('auth.email_not_found'));


        if ($type == 'change_password') {


            $userCode = $user->emailcode()->where('type', 2)->first();

            if ($userCode->code == request()->code) {
                return apiresponse(true, 200, __('auth.correct_code'), null);
            }
            return apiresponse(false, 200,  __('auth.incorrect_code'), null);
        }



        elseif ($type == 'account_verification') {

            if($user->mobile  != request()->mobile)
            return apiresponse(false,200,__('auth.incorrect_code'));


            $useremailCode = $user->emailcode()->where('type', 1)->first();
            $usermobileCode = $user->mobilecode()->where('type', 1)->first();
            if ($useremailCode->code == request()->email_code  && $usermobileCode->code == request()->mobile_code) {
                $user->activity = 1;
                $user->save();
             $token = $user->createToken('api-token')->plainTextToken;
            return apiresponse(true, 200, __('auth.account_verification_true'), [
                'user' => $user,
                'token' => $token
            ]);
            }
            return apiresponse(false, 200, __('auth.incorrect_code'), null);
        }

    }




    public function change_password()
    {
        $email = request()->email;
        $password = request()->password;
        $password = request()->password;
        $code = request()->code;

        $validator = Validator::make(request()->all(),
        [
           'email' => 'required|email',
           'password'=>'required|same:confirm_password',
           'confirm_password'=>'required|same:password'
        ],
        validationMessages()
        );

        if($validator->fails())
        return apiresponse(false,200,$validator->errors()->first());

        $email = request()->email;

        if (customer::where("email", request()->email)->count() > 0)   // auth user after register
            $user =  customer::where("email", request()->email)->first();


        elseif (serviceProvider::where("email", $email)->count() > 0)
            $user  = serviceProvider::where("email", $email)->first();
        else
            return apiresponse(false, 200, "unAuthorized", null);


        $userCode = $user->emailcode()->where('type', 2)->first();

        if ($userCode->code == request()->code) {
            $user->password  = Hash::make($password);
            $user->save();
            return apiresponse(true, 200, __('auth.password_changed'), null);
        }
        return apiresponse(false, 200, __('auth.unAuthorized'), null);
    }








    public function mobileLogin(){
        $mobile = request()->mobile;


        if (customer::where("mobile", request()->mobile)->count() > 0)   // auth user after register
            $user =  customer::where("mobile", request()->mobile)->first();

        elseif (serviceProvider::where("mobile", $mobile)->count() > 0)
            $user  = serviceProvider::where("mobile", $mobile)->first();

        else
        return apiresponse(false, 200, __('auth.mobile_not_found'), null);

        $userMobileCode = $user->mobileCode()->where('type', 1)->first();

        if(request()->code == $userMobileCode->code)
        {
            $token = $user->createToken('api-token')->plainTextToken;
              return apiresponse(true, 200, __('auth.success_proccess'), [
                'user' => $user,
                'token' => $token
            ]);
        }

     return apiresponse(false, 200, __('auth.incorrect_code'), null);

    }




    public function sendMobile(){


        $validator = Validator::make(request()->all(),
        [
           'mobile' => 'required',
        ],
        validationMessages()
        );

        if($validator->fails())
        return apiresponse(false,200,$validator->errors()->first());

        $mobile = request()->mobile;


        if (customer::where("mobile", $mobile)->count() > 0)   // auth user after register
            $user =  customer::where("mobile", $mobile)->first();

        elseif (serviceProvider::where("mobile", $mobile)->count() > 0)
            $user  = serviceProvider::where("mobile", $mobile)->first();

        else
        return apiresponse(false, 200, __('auth.mobile_not_found'), null);

        $userMobileCode = $user->mobileCode()->where('type', 1)->first();

        $userMobileCode->code = 1234;
        $userMobileCode->save();

         //sendCode



      return apiresponse(true, 200, __('auth.login_code_sent'), null);

 }


public function account_verification($user){

    $params = [
        'type' => 'email_verification',
        'title' => 'email verification',
        'body' => ' verificate your email with this code',
        'code' => $user->useremailCode->code,
        'to' => $user->email
    ];
    $email = new emailController();
    $email->Send($params);


    // send mobile msg

    return apiresponse(true, 200, __('auth.verification_code'));

}

public function logout()
{
$user = Auth('sanctum')->user();
$tokenId =  $user->currentAccessToken()->id;
$user->tokens()->where('id', $tokenId)->delete();
return apiresponse(true, 200,"logged out");
//
}



}
