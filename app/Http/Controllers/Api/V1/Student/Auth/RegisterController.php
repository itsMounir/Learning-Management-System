<?php

namespace App\Http\Controllers\Api\V1\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\{
    User,
    Code
};
use App\Traits\{
    verifyCode,
    ExpierCode,
    createVerificationCode
};
use Illuminate\Support\Facades\Notification;
use App\Notifications\verfication_code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    use verifyCode,ExpierCode,createVerificationCode;

    public function create(RegisterRequest $request) {
        return DB::transaction(function () use ($request){

            $student = User::create($request->all());
            Auth::login($student);
            $verificationCode = $this->getOrCreateVerificationCode($student->email, 'cheack-email');
            Notification::route('mail',$student->email)
                ->notify(new verfication_code($student, $verificationCode));

            $token = $student->createToken('access_token')->plainTextToken;

            return response()->json([
                'message' => 'Code has been sent',
                'student'=>$student,
                'access_token' => $token
            ],201);
        });
    }


    public function resend(Request $request) {
        return DB::transaction(function () use ($request) {
            $student=Auth::user();
            $verificationCode = $this->getOrCreateVerificationCode($student->email, 'check-email');
            Notification::route('mail', $student->email)
                ->notify(new verfication_code($student, $verificationCode));
            return $this->sudResponse('Code has been resent');
        });
    }


    public function verify(Request $request){
        $request->validate([
            'verification_code'=>'required'
        ]);
        return $this->verifyCode($request['verification_code']);

    }

}