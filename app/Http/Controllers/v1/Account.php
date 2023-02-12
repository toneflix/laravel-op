<?php

namespace App\Http\Controllers\v1;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\v1\User\UserResource;
use App\Http\Resources\v1\WalletCollection;
use App\Traits\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use ToneflixCode\LaravelFileable\Media;

class Account extends Controller
{
    use Meta;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return (new UserResource(Auth::user()))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function wallet()
    {
        return (new WalletCollection(Auth::user()->wallet_transactions()->orderByDesc('id')->paginate()))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $field = null)
    {
        $user = Auth::user();
        $set = $request->set;
        unset($request->set);

        if ($set === 'settings') {
            $this->validate($request, ['settings' => ['required', 'array']]);
            $user->settings = $request->settings;
            $message = __('Account settings updated');
        } elseif ($set === 'status_message') {
            $this->validate($request, ['status_message' => ['required', 'string', new WordLimit(5, ['>' => 5, '<' => 3])]]);
            $user->status_message = $request->status_message;
            $message = __('Status message successfully updated');
        } else {
            $phone_val = stripos($request->phone, '+') !== false ? 'phone:AUTO,NG' : 'phone:'.$this->ipInfo('country');
            $this->validate($request, [
                'firstname' => ['required', 'string', 'max:255'],
                'lastname' => ['required', 'string', 'max:255'],
                'phone' => ['required', $phone_val, 'max:255', Rule::unique('users')->ignore($user->id)],
                'about' => ['nullable', 'string', 'max:155'],
                'address' => ['nullable', 'string', 'max:255'],
            ], [], [
                'phone' => 'Phone Number',
            ]);

            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->about = $request->about;
            $user->phone = $request->phone;
            $user->address = $request->address;
            $message = __('Your profile has been successfully updated');
        }

        $user->save();

        return (new UserResource($user))->additional([
            'message' => $message,
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Update the user profile picture.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateProfilePicture(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'image' => ['required', 'image', 'mimes:png,jpg', 'max:1024'],
        ], [
            'image.required' => 'You did not select an image for upload',
        ]);

        if ($validator->fails()) {
            return $this->buildResponse([
                'message' => 'Your input has a few errors',
                'status' => 'error',
                'status_code' => HttpStatus::UNPROCESSABLE_ENTITY,
                'errors' => $validator->errors(),
            ]);
        }
        $user->image = (new Media)->save('avatar', 'image', $user->image);
        $user->updated_at = \Carbon\Carbon::now();
        $user->save();

        return (new UserResource($user))->additional([
            'message' => 'Your profile picture has been changed successfully',
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }

    /**
     * Update the user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->buildResponse([
                'message' => 'Your input has a few errors',
                'status' => 'error',
                'status_code' => HttpStatus::UNPROCESSABLE_ENTITY,
                'errors' => ['current_password' => ['Your current password is not correct.']],
            ]);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->buildResponse([
                'message' => 'Your input has a few errors',
                'status' => 'error',
                'status_code' => HttpStatus::UNPROCESSABLE_ENTITY,
                'errors' => $validator->errors(),
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return (new UserResource($user))->additional([
            'message' => 'Your password has been successfully updated',
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}
