<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Center;
use App\Models\Group;
use App\Queries\GroupDataTable;
use App\Repositories\GroupRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;
use Auth;

class GroupController extends AppBaseController
{
    /** @var GroupRepository */
    private $groupRepository;

    public function __construct(GroupRepository $groupRepo)
    {
        $this->groupRepository = $groupRepo;
    }

    /**
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // return Datatables::of((new GroupDataTable())->get())->make(true);
            $user = Auth::user();

            if($user->hasRole('Admin')) {
                $groups = Group::with('center')->get();
            } else if ($user->hasRole('Center Admin')) {
                $centers = $user->centers->pluck('id');
                $groups = Group::whereIn('center_id', $centers)->with('center')->get();
            } else if ($user->hasRole('Group Leader')) {
                // actually group leader can't see the groups page
                $groups = $user->groups;
            } else {
                $group = [];
            }
            
            return DataTables::of($groups)->make(true);
        }

        return view('groups.index');
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        $user = Auth::user();

        $centers = $this->groupRepository->getAvailableCenters($user);
        return view('groups.create', compact('centers'));
    }

    /**
     * @param  CreateGroupRequest  $request
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function store(CreateGroupRequest $request)
    {
        $input = $request->all();

        $input['group_type'] = ($input['group_type'] == '1') ? Group::TYPE_OPEN : Group::TYPE_CLOSE;
        $input['privacy'] = ($input['privacy'] == '1') ? Group::PRIVACY_PUBLIC : Group::PRIVACY_PRIVATE;
        $input['created_by'] = getLoggedInUserId();
        $input['users'] = [];

        $center = Center::find($input['center_id']);

        $nextNumber = $this->groupRepository->getNextNumber($input['center_id']);
        
        $input['name'] = $center->code . "_" . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        $input['number'] = $nextNumber;

        $this->groupRepository->store($input);
        Flash::success('Group saved successfully.');

        return redirect()->route('group.index');
    }

    /**
     * @param  Group  $group
     * @return Application|Factory|View
     */
    public function show(Group $group)
    {
        return redirect()->back();
//        return \view('groups.show',compact('group'));
    }

    /**
     * @param  Group  $group
     * @return Application|Factory|View
     */
    public function edit(Group $group)
    {
        $user = Auth::user();
        $centers = $this->groupRepository->getAvailableCenters($user);

        return view('groups.edit', compact('group', 'centers'));
    }

    /**
     * @param  Group  $group
     * @param  UpdateGroupRequest  $request
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function update(Group $group, UpdateGroupRequest $request)
    {
        $this->groupRepository->update($request->all(), $group);
        Flash::success('Group updated successfully.');

        return redirect()->route('groups.index');
    }

    /**
     * @param  Group  $group
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Group $group)
    {
        $this->groupRepository->delete($group->id);

        return $this->sendSuccess('Group deleted successfully');
    }
}
