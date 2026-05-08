<?php

namespace Reda\Integraciones\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelsRequested
{
    use Dispatchable, SerializesModels;

    public $brand_id;

    public function __construct($brandId)
    {
        $this->brand_id = $brandId;
    }
}
