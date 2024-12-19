<?php

namespace App\Repositories;

use App\Models\Center;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class CenterRepository
 *
 * @version November 12, 2019, 11:13 am UTC
 */
class CenterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'code',
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Center::class;
    }

    /**
     * @param $input
     * @return Center
     *
     * @throws \Exception
     */
    public function storeCenter($input)
    {
        try {
            DB::beginTransaction();
            /** @var Center $center */
            $center = Center::create([
                'name' => $input['name'],
                'code' => $input['code'],
                'is_active' => 1,
                'remark' => $input['remark'],
            ]);

            DB::commit();

            return $center;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @param $input
     * @param $center
     * @return Center
     *
     * @throws \Exception
     */
    public function updateCenter($input, $center)
    {
        try {
            DB::beginTransaction();
            /** @var Center $center */
            $center->update($input);
            DB::commit();

            return $center;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function getCenterGroups($id)
    {
        $center = Center::find($id);
        $data = [];

        if($center) {
            $groups = $center->groups;

            foreach ($groups as $key => $group) {
                $data[] = array(
                    'id' => $group->id,
                    'name' => $group->name,
                    'number' => $group->number,
                );
            }

            return $data;
        } else {
            return [];
        }
    }
}
