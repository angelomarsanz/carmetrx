<?php

namespace Reda\Integraciones\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatesRequested
{
    use Dispatchable, SerializesModels;

    public $country_id;

    /**
     * Create a new event instance.
     *
     * @param int $countryId
     * @return void
     */
    public function __construct($countryId)
    {
        $this->country_id = $countryId;
    }
}