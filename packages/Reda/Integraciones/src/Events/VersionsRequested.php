<?php

namespace Reda\Integraciones\Events;

use Illuminate\Queue\SerializesModels;

class VersionsRequested
{
    use SerializesModels;

    public $model_id;

    public function __construct($model_id)
    {
        $this->model_id = $model_id;
    }
}