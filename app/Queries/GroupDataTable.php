<?php

namespace App\Queries;

use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Auth;

/**
 * Class CenterDataTable
 */
class GroupDataTable
{
    /**
     * @return Builder
     */
    public function get()
    {
        return Group::with('center');
    }
}
