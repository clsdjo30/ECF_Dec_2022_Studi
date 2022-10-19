<?php

namespace App\Data;

use App\Entity\Partner;
use App\Entity\Subsidiary;

class SearchData
{
    /**
     * @var null | string
     */
    public ?string $q = null;

    /**
     * @var Subsidiary[]
     */
    public array $subsidiaries = [];

    /**
     * @var bool|null
     */
    public ?bool $active = false;

    /**
     * @var bool|null
     */
    public ?bool $close = false;

}