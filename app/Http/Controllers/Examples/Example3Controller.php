<?php
namespace App\Http\Controllers\Examples;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Enum\UserProfileFormEnum;

class Example3Controller extends Controller
{
    public function update(UserProfileFormEnum $frm, Request $request)
    {
        // Add the check by indicating after the point of the [Option] field
        Gate::authorize('example3.update.'.$frm->value);

        $user = Auth::user();
        switch ($frm)
        {
            case(UserProfileFormEnum::Name):

                if($request->name) $user->fill( $request->only(['name']) );

            break;
            case(UserProfileFormEnum::Password):

                if($request->password) $user->password = Hash::make($request->password);

            break;
            case(UserProfileFormEnum::Email):

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                ]);
                if ($validator->fails()) abort('403', $validator->messages());

                $user->email = $request->email;

            break;
        }

        return Response::json($user->save(), 200);
    }
}
