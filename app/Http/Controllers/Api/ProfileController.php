<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected ProfileService $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->middleware('auth:sanctum');
        $this->profileService = $profileService;
    }

    public function show(Request $request)
    {
        return response()->json(
            $this->profileService->getProfile($request->user())
        );
    }
}
