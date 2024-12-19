<?php

namespace App\Queries;

use App\Models\Center;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CenterDataTable
 */
class CenterDataTable
{
    /**
     * @return Builder
     */
    public function get()
    {
        return Center::where('is_active', '=', 1);
    }
}
