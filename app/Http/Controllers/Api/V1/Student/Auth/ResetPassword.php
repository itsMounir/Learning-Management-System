<?php

namespace App\Http\Controllers\Api\V1\Student\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{

    Code,
    User
};

use App\Traits\{
    Responses,
    ExpierCode,
    VerifyCodeForForgetPassword,


};
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\verfication_code;

class ResetPassword extends Controller{


    use ExpierCode,Responses,VerifyCodeForForgetPassword;

        public function verify(Request $request){
            $request->validate([
                'verification_code'=>'required'
            ]);
            return $this->verifyCode($request['verification_code']);

        }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $student = User::where('email', $request['email'])->get()->first();

        $student->password = bcrypt($request->password);
        $student->save();

        Code::where('email', $request->email)->delete();

        return $this->sudResponse('Your password has been reset !');
    }



}
