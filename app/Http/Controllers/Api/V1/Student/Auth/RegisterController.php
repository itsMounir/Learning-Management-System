<?php

namespace App\Http\Controllers\Api\V1\Student\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Resources\StudentResource;
use App\Models\{
    User,
    Code
};
use App\Models\Wallet;
use App\Traits\{
    VerifyCodeForRegister,
    ExpierCode,
    createVerificationCode
};
use App\Traits\Media;
use Illuminate\Support\Facades\Notification;
use App\Notifications\verfication_code;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{



    use VerifyCodeForRegister, ExpierCode, createVerificationCode, Media;



    public function create(RegisterRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $student = User::create(
                array_merge(
                    $request->all(),
                    ['status' => 'active']
                )
            );

            Auth::login($student);

            $verificationCode = $this->getOrCreateVerificationCode($student->email, 'cheack-email');

            if ($request->hasFile('image')) {
                $request_image = $request->file('image');
                $image_name = $this->setMediaName([$request_image], 'Students')[0];

                $student->image()->create(['name' => $image_name]);
                $this->saveMedia([$request_image], [$image_name], 'public');
            }

            Wallet::create(['user_id' => $student->id]);


            Notification::route('mail', $student->email)
                ->notify(new verfication_code($student, $verificationCode));

            $student->assignRole('student');

            $token = $student->createToken('access_token')->plainTextToken;

            return response()->json([
                'message' => 'Code has been sent',
                'student' => new StudentResource($student),
                'access_token' => $token
            ], 200);
        });
    }


    public function resend(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $student = Auth::user();
            $verificationCode = $this->getOrCreateVerificationCode($student->email, 'check-email');
            Notification::route('mail', $student->email)
                ->notify(new verfication_code($student, $verificationCode));
            return $this->sudResponse('Code has been resent');
        });
    }


    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required'
        ]);
        return $this->verifyCode($request['verification_code']);

    }

}
