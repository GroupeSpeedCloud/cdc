<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhitelistedEmail extends Model
{
    protected $fillable = ['email'];

    public static function isAllowed(string $email): bool
    {
        $superAdmin = strtolower(config('services.auth.super_admin', 'maxime.ponsart@groupe-speed.cloud'));
        if (strtolower($email) === $superAdmin) {
            return true;
        }

        return static::whereRaw('LOWER(email) = ?', [strtolower($email)])->exists();
    }
}
