<?php

namespace App\Http\Controllers;

use App\Http\Requests\Settings\UpdateSettingRequest;
use App\Http\Requests\Settings\UserDeletionRequest;
use App\Jobs\DeleteUserJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;;

class UserSettingsController extends Controller
{
    public function destroy(UserDeletionRequest $request): JsonResponse 
    {
        $user = auth()->user();

        /* Let me know someone is deleting their account */
        Log::warning("{$user->email} is deleting their account... ID SANITY CHECK: {$request->user_id} === {$user->getKey()}");

        /* Log them out. */
        Auth::logout();

        /* Queue deletion. */
        DeleteUserJob::dispatch($user);

        /* Flash the user their account is being deleted. */
        session()->flash('success', "We're sorry to see you go {$user->name}. Be Well.");

        return response()->json([
            'success' => true,
            'redirect' => route('welcome')
        ], 200);
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        auth()->user()->preferences()->update([$request->setting_name => $request->setting_value]);

        return response()->json([
            'success' => true,
            'message' => "Successfully updated setting: {$request->setting_type}."
        ], 200);
    }
}
