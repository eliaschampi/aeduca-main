<?php

namespace App\Repositories;

use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfileRepository
{
    public function fetchByDni(string $dni): ?Profile
    {
        return Profile::find($dni);
    }

    public function store(string $dni, string $image): string
    {
        $password = Str::random(10);
        $profile = new Profile();
        $profile->person_dni = $dni;
        $profile->image = $image;
        $profile->password = Hash::make($password);
        $profile->original_password = $password;
        $profile->last_logout = now();
        $profile->save();
        return $profile->original_password;
    }

    public function changeImage(Profile $profile, string $fileName): bool
    {
        $profile->image = $fileName;
        return $profile->save();
    }

    public function update(string $dni): string
    {
        $profile = $this->fetchByDni($dni);
        $profile->password = Hash::make($profile->original_password);
        $profile->save();
        return $profile->original_password;
    }

    public function destroy(string $dni): bool
    {
        return Profile::find($dni)->delete();
    }
}
