<?php

namespace App\Services;

use App\Models\User;

class ProfileService
{
    /**
     * Get full user profile with balances and assets
     */
    public function getProfile(User $user): array
    {
        $user->load('assets');

        return [
            'user' => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'balance' => $user->balance,
            ],
            'assets' => $user->assets->map(function ($asset) {
                return [
                    'symbol'        => $asset->symbol,
                    'amount'        => $asset->amount,
                    'locked_amount' => $asset->locked_amount,
                ];
            })->values(),
        ];
    }
}
