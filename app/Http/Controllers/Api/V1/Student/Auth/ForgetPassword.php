<?php

namespace App\Http\Controllers\Api\V1\Student\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{

    Code,
    User
};
use App\Traits\{
    createVerificationCode,
    Responses

};
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use Carbon\Carbon;

use Illuminate\Support\Facades\Notification;
use App\Notifications\verfication_code;
class ForgetPassword extends Controller{


use createVerificationCode,Responses;

public function forgetPassword(Request $request)
{
    $validated = $request->validate([
        'email' => 'required',
    ]);

    $student = User::where('email', $validated['email'])->firstOrFail();
    $verificationCode = $this->getOrCreateVerificationCode($validated['email'],'forget-password');
    Notification::route('mail', $validated['email'])
                ->notify(new verfication_code($student, $verificationCode));

    return $this->sudResponse('Code has been sent.');
}








}
