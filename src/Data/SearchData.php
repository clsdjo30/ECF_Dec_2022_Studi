<?php

namespace App\Data;

use App\Entity\Partner;
use App\Entity\Subsidiary;

class SearchData
{
    /**
     * @var int
     */
    public int $page = 1;

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