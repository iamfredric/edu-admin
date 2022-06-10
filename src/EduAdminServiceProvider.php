<?php

namespace Iamfredric\EduAdmin;

use Illuminate\Support\ServiceProvider;

class EduAdminServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $username = config('services.edu-admin.username');
        $password = config('services.edu-admin.password');
        if (! empty($username) && ! empty($password)) {
            Client::setCredentials($username, $password);
        }
    }
}
