<?php

namespace App\ThunderID\EmploymentSystemV1\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Event for Product Viewed
 * 
 * @author cmooy
 */
class EmployeeCreated extends Event
{
    use SerializesModels;

    public $person;

    /**
     * Create a new event instance.
     *
     * @param  array  $person
     * @return void
     */
    public function __construct(array $person)
    {
        $this->person = $person;
    }
}