<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCenterRequest;
use App\Http\Requests\UpdateCenterRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Center;
use App\Queries\CenterDataTable;
use App\Repositories\CenterRepository;
use DataTables;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class CenterController extends AppBaseController
{
    /** @var CenterRepository */
    private $centerRepository;

    public function __construct(CenterRepository $centerRepo)
    {
        $this->centerRepository = $centerRepo;
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
            return Datatables::of((new CenterDataTable())->get())->make(true);
        }

        return view('centers.index');
    }

    /**
     * @return Factory|View
     */
    public function create()
    {
        return view('centers.create');
    }

    /**
     * @param  CreateCenterRequest  $request
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function store(CreateCenterRequest $request)
    {
        $input = $request->all();
        $this->centerRepository->storeCenter($input);
        Flash::success('Center saved successfully.');

        return redirect()->route('centers.index');
    }

    /**
     * @param  Center  $center
     * @return Application|Factory|View
     */
    public function show(Center $center)
    {
        return redirect()->back();
//        return \view('centers.show',compact('center'));
    }

    /**
     * @param  Center  $center
     * @return Application|Factory|View
     */
    public function edit(Center $center)
    {
        return view('centers.edit', compact('center'));
    }

    /**
     * @param  Center  $center
     * @param  UpdateCenterRequest  $request
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function update(Center $center, UpdateCenterRequest $request)
    {
        $this->centerRepository->updateCenter($request->all(), $center);
        Flash::success('Center updated successfully.');

        return redirect()->route('centers.index');
    }

    /**
     * @param  Center  $center
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Center $center)
    {
        if ($center->groups->count() > 0) {
            return $this->sendError('There are groups in this center.');
        }
        $this->centerRepository->delete($center->id);

        return $this->sendSuccess('Center deleted successfully');
    }

    public function getCenterGroups(Request $request)
    {
        $id = $request->input('id');
        $groups = $this->centerRepository->getCenterGroups($id);
        return $this->sendData($groups);
    }
}
