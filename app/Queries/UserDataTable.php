<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 */

namespace App\Queries;

use App\Models\User;
use App\Models\Group;
use Illuminate\Database\Eloquent\Builder;
use Auth;



/**
 * Class UserDataTable.
 */
class UserDataTable
{
    /**
     * @param  array  $input
     * @return Builder
     */
    public function get($input = [])
    {
        $currentUser = Auth::user();

        $users = User::with(['roles', 'centers', 'groups']);
        $users->when(
            isset($input['filter_user']),
            function (Builder $q) use ($input) {
                if ($input['filter_user'] == User::FILTER_ARCHIVE) {
                    $q->onlyTrashed();
                }
                if ($input['filter_user'] == User::FILTER_ALL) {
                    $q->withTrashed();
                }
                if ($input['filter_user'] == User::FILTER_ACTIVE) {
                    $q->where('is_active', '=', 1);
                }
                if ($input['filter_user'] == User::FILTER_INACTIVE) {
                    $q->where('is_active', '=', 0);
                }
            })
        // ->when(isset($input['privacy_filter']), function (Builder $q) use ($input) {
        //     if ($input['privacy_filter'] == User::PRIVACY_FILTER_PUBLIC) {
        //         $q->where('privacy', '=', 1);
        //     }
        //     if ($input['privacy_filter'] == User::PRIVACY_FILTER_PRIVATE) {
        //         $q->where('privacy', '=', 0);
        //     }
        // })
        ->where('id', '!=', getLoggedInUserId())->where('is_super_admin', '=', 0);

        if ($currentUser->hasRole('Center Admin')) {
            $centers = $currentUser->centers->pluck('id');

            $users->whereHas('centers', function ($query) use ($centers) {
                $query->whereIn('center_id', $centers);
            });
            
        } else if ($currentUser->hasRole('Group Leader')) {
            // actually group leader can't see the groups page
            $groups = $currentUser->groups->pluck('id');

            $users->whereHas('groups', function ($query) use ($groups) {
                $query->whereIn('group_id', $groups);
            });
        } else {
            // do nothing here
            // all users for super admin - no filter here
        }

        $users = $users->select([
            'photo_url', 'id', 'name', 'phone', 'privacy', 'is_active', 'is_super_admin', 'deleted_at',
            'email_verified_at', 'user_no', 'type',
        ]);

        return $users;
    }
}
