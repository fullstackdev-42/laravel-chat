<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\Center;
use App\Models\Group;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use App\Repositories\GroupRepository;
use App\Rules\NoSpaceContaine;
use Exception;
use Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Redirect;
use Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /** @var AccountRepository */
    public $accountRepo;

    /** @var UserRepository */
    private $userRepository;

    /** @var GroupRepository */
    private $groupRepository;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/conversations';

    /**
     * Create a new controller instance.
     *
     * @param  AccountRepository  $accountRepository
     * @param  UserRepository  $userRepo
     */
    public function __construct(AccountRepository $accountRepository, UserRepository $userRepo, GroupRepository $groupRepo)
    {
        $this->accountRepo = $accountRepository;
        $this->userRepository = $userRepo;
        $this->groupRepository = $groupRepo;
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $data['name'] = htmlspecialchars($data['name']);

        return Validator::make($data, [
            'name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:30', 'confirmed', new NoSpaceContaine()],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     *
     * @throws Exception
     */
    protected function create(array $data)
    {
        $role = Role::find($data['role_id']);

        $role_name = $role->name;
        $center_id = $data['center_id'];

        $center = Center::find($center_id);
        $center_code = $center->code;
        $group = null;

        if($role_name == 'Center Admin') {
            $group_no_part = 'CA';
        } else {
            $group_id = $data['group_id'];
            $group = Group::find($group_id);
            $group_number  = $group->number;
            $group_no_part = str_pad($group_number, 2, '0', STR_PAD_LEFT);
        }

        $user_no_prefix = $center_code . '_' . $group_no_part . '_';

        $nextUserNo = $this->userRepository->getNextNumber($user_no_prefix);
        $user_no = $user_no_prefix . str_pad($nextUserNo, 4, '0', STR_PAD_LEFT);

        
        $user = User::create([
            'name' => htmlspecialchars($data['name']),
            'password' => Hash::make($data['password']),
            'age' => $data['age'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'is_active' => false,
            'user_no' => $user_no,
            'email_verified_at' => date('Y-m-d'),
        ]);

        $members = [];
        $members[] = $user->id;

        if($role_name == 'Center Admin') {
            // need to make this user as center admin
            $this->userRepository->assignCenters($user, ['center_id' => $data['center_id']] );
        } else if($role_name == 'Group Leader') {
            // need to make this user as group admin
            $this->userRepository->assignCenters($user, ['center_id' => $data['center_id']] );
            $this->groupRepository->addMembersToGroupInRegister($group, $members, true);
        } else if ($role_name == 'Member') {
            // need to add this member to group
            $this->userRepository->assignCenters($user, ['center_id' => $data['center_id']] );
            $this->groupRepository->addMembersToGroupInRegister($group, $members, false);
        }

        $this->userRepository->assignRoles($user, ['role_id' => $data['role_id']]);

        return $user;
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    

    public function showRegistrationForm()
    {
        $centers = Center::all();

        $centersWithGroups = [];

        foreach ($centers as $key => $center) {
            $groups = $center->groups;
            $groupData = [];
            foreach ($groups as $key => $group) {
                $groupData[] = array(
                    'id' => $group->id,
                    'number' => $group->number,
                    'name' => $group->name,
                );
            }

            $centersWithGroups[] = array(
                'id' => $center->id,
                'name' => $center->name,
                'code' => $center->code,
                'groups' => $groupData,
            );
        }
        
        $roles = Role::where('name', '!=', 'Admin')->orderByDesc('name')->get();
        return view('auth.register', compact('centersWithGroups', 'roles'));
    }
}
