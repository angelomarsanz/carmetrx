<?php

namespace Reda\Integraciones\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CitiesRequested
{
    use Dispatchable, SerializesModels;

    public $state_id;

    /**
     * Create a new event instance.
     *
     * @param int $stateId
     * @return void
     */
    public function __construct($stateId)
    {
        $this->state_id = $stateId;
    }
}