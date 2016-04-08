<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\ThunderID\EmploymentSystemV1\Events\EmployeeCreated' => [
            'App\ThunderID\EmploymentSystemV1\Listeners\SendActivationMail',
        ],
    ];
}
