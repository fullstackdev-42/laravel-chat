<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Livewire\Component;

class SearchUsers extends Component
{
    public $users = [];

    public $myContactIds = [];

    public $searchTerm;

    public $male;

    public $female;

    protected $listeners = ['clearSearchUsers' => 'clearSearchUsers'];

    /**
     * @param  array  $ids
     */
    public function setMyContactIds($ids)
    {
        $this->myContactIds = $ids;
    }

    /**
     * @return array
     */
    public function getMyContactIds()
    {
        return $this->myContactIds;
    }

    /**
     * initialize variables
     *
     * @param $myContactIds
     * @param $blockUserIds
     */
    public function mount($myContactIds, $blockUserIds)
    {
        $userIds = array_unique(array_merge($blockUserIds, array_keys($blockUserIds)));
        $userIds = array_unique(array_merge($userIds, $myContactIds));
        $this->setMyContactIds($userIds);
    }

    /**
     * @return Application|Factory|View
     */
    public function render()
    {
        $this->searchUsers();

        return view('livewire.search-users');
    }

    public function clearSearchUsers()
    {
        $this->male = false;
        $this->female = false;
        $this->searchTerm = '';

        $this->searchUsers();
    }

    /**
     * search users and apply filters
     */
    public function searchUsers()
    {
        $currentUser = getLoggedInUser();
        $myRole = $currentUser->role_name;

        $male = $this->male;
        $female = $this->female;
        if ($this->male && $this->female) {
            $male = false;
            $female = false;
        }

        $query = User::with(['roles', 'centers', 'groups'])
            ->whereNotIn('id', $this->getMyContactIds())
            ->where('id', '!=', getLoggedInUserId())
            ->when($male, function ($query) {
                return $query->where('gender', '=', User::MALE);
            })
            ->when($female, function ($query) {
                return $query->where('gender', '=', User::FEMALE);
            })
            ->when($this->searchTerm, function ($query) {
                return $query->where(function ($q) {
                    $q->whereRaw('name LIKE ?', ['%'.strtolower($this->searchTerm).'%']);
                });
            })
            ->orderBy('name', 'asc')
            ->select(['id', 'is_online', 'gender', 'photo_url', 'name', 'email', 'user_no']);

        if ($myRole == 'Center Admin') {
            $clonedQuery = clone $query;
            // get all users in center
            $centers = $currentUser->centers->pluck('id');

            $centerUsers = $query->whereHas('centers', function ($query) use ($centers) {
                $query->whereIn('center_id', $centers);
            })->get();

            // get other center admins and super admin
            $centerAdminsAndSuperAdmin = $clonedQuery->whereHas('roles', function ($query) {
                $query->whereIn('name', ['Center Admin', 'Admin']);
            })->get();


            $users = $centerAdminsAndSuperAdmin->merge($centerUsers);
        } else if ($myRole == 'Group Leader' ) {
            $clonedQuery = clone $query;
            // retrive the center admins and group leaders in the same center
            $centers = $currentUser->centers->pluck('id');            
            $centerAdminAndGroupLeaders = $query->whereHas('roles', function ($query) {
                $query->whereIn('name', ['Center Admin', 'Group Leader']);
            })->whereHas('centers', function ($query) use ($centers) {
                $query->whereIn('center_id', $centers);
            })->get();

            // actually group leader can't see the groups page
            $groups = $currentUser->groups->pluck('id');

            $groupMembers = $clonedQuery->whereHas('groups', function ($query) use ($groups) {
                $query->whereIn('group_id', $groups);
            })->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['Admin', 'Center Admin']);
            })->get();

            $users = $centerAdminAndGroupLeaders->merge($groupMembers);
        } else if($myRole == 'Member') {
            // only can see same group members
            $groups = $currentUser->groups->pluck('id');
            $users = $query->whereHas('groups', function ($query) use ($groups) {
                $query->whereIn('group_id', $groups);
            })->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['Admin', 'Center Admin']);
            });
            // member can see group leader, but can't access center admin and super admin
            $users = $users->get();
        } else if($myRole == 'Admin') {
            $users = $query->get();
        }

        $this->users = $users;
    }
}
