<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController as BaseController;
use App\Http\Resources\UserStatusResource;
use App\Models\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserStatusController extends BaseController
{
    /**
     * Display all user status from storage
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $userStatus = UserStatus::all();

        return $this->sendResponse(UserStatusResource::collection($userStatus), 'Hooray, User Status Retrieved with success');
    }

    /**
     * Store a new User Status resource to storage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'user_id'  => 'required',
            'status'   => 'required',
            'position' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Sorry, some data is not valid', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $userStatus = UserStatus::create($inputData);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Sorry, there is some issue on storing the data', $exception->getMessage());
        }

        return $this->sendResponse(new UserStatusResource($userStatus), 'Yeaay, User Status successfully stored');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $userStatus = UserStatus::find($id);

        if (!is_object($userStatus) || is_null($userStatus)) {
            return $this->sendError('Sorry, User Status not found');
        }

        return $this->sendResponse(new UserStatusResource($userStatus), 'Yeaay, User Status found');
    }

    /**
     * @param Request $request
     * @param UserStatus $userStatus
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, UserStatus $userStatus)
    {
        $inputData = $request->all();

        $validator = Validator::make($inputData, [
            'user_id'  => 'required',
            'status'   => 'required',
            'position' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Sorry, some data is not valid', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $userStatus->user_id  = $inputData['user_id'];
            $userStatus->status   = $inputData['status'];
            $userStatus->position = $inputData['position'];
            $userStatus->save();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Sorry, there is some issue on updating the data', $exception->getMessage());
        }

        return $this->sendResponse(new UserStatusResource($userStatus), 'Yeaay, User Status successfully updated');
    }

    /**
     * @param UserStatus $userStatus
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UserStatus $userStatus)
    {
        $userStatus->delete();

        return $this->sendResponse([], 'User Status deleted successfully.');
    }
}
