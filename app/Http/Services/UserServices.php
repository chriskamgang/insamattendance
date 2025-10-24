<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Enums\EDateFormat;
use App\Http\Repositories\UserRepository;
use App\Http\Resources\FaceIdsResource;
use App\Http\Resources\UserEmployeeResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Illuminate\Validation\Rule;

class UserServices
{
    private string $notFoundMessage = "Sorry! User not found";
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getList($request): mixed
    {
        $search = $request->all();
        return $this->userRepository->findALl($search);
    }
    public function getAdminList(): mixed
    {
        return $this->userRepository->findALlAdmin();
    }

    /**
     * @throws SMException
     */
    public function getUser($user_id)
    {
        $_user = $this->userRepository->find($user_id);
        if ($_user) {
            return $_user;
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function updatePassword($user_id, $request)
    {
        $data = $request->all();
        $_user = $this->userRepository->find($user_id);
        if ($_user) {
            if (isset($data['password'])) {
                checkCreated();
                return $this->userRepository->update($_user, [
                    'password' => Helper::passwordHashing($data['password']),
                ]);
            }
            throw new SMException("Password is required");

        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function updateAppPassword($user_id, $request)
    {
        $data = $request->all();
        $_user = $this->userRepository->find($user_id);
        if ($_user) {
            if (isset($data['app_password'])) {
                Helper::checkUrl();
                return $this->userRepository->update($_user, [
                    'app_password' => $data['app_password'],
                ]);
            }
            throw new SMException("App Password is required");
        }
        throw new SMException($this->notFoundMessage);
    }

    public function checkUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function userLogin($request)
    {
        $validator = $this->checkLoginValidation($request);
        if ($validator->fails()) {
            $validation_error = [];
            if ($validator->errors()->has('email')) {
                $validation_error['error']['email'] = $validator->errors()->first('email');
            }
            if ($validator->errors()->has('password')) {
                $validation_error['error']['password'] = $validator->errors()->first('password');
            }
            return Helper::errorResponseAPI(message: "Invalid Validation", data: $validation_error);
        }

        $customerWithEmail = $this->userRepository->getUserByEmail($request->email);

        if ($customerWithEmail) {
            $data = $request->all();
            $check_password = Helper::checkPassword($data['password'], $customerWithEmail->password);
            if ($check_password) {
                $credential = [
                    'email' => $data['email'],
                    'password' => Helper::getSaltedPassword($data['password'])
                ];

                if ($this->getAttempt($credential)) {
                    $_user = Auth::user();
                    $return_response = [
                        'token' => $_user->createToken('AppName')->accessToken,
                        'app_password' => $_user->app_password,
                    ];
                    checkCreated();
                    return Helper::successResponseAPI(message: "Success", data: $return_response);
                }
            } else {
                return Helper::errorResponseAPI(message: "Password is Incorrect", code: ResponseAlias::HTTP_UNAUTHORIZED, status: ResponseAlias::HTTP_UNAUTHORIZED);
            }
        }
        return Helper::errorResponseAPI(message: "Email does not matched.", code: ResponseAlias::HTTP_UNAUTHORIZED, status: ResponseAlias::HTTP_UNAUTHORIZED);

    }

    private function checkLoginValidation($request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|max:255|min:6',
        ], [
                'email.required' => 'Email is required',
                'password.required' => 'Password field is required.',
                'password.min' => 'Password must be more than 6 character Please try again',
                'password.max' => 'Password must be less than 255 character  Please try again',
            ]
        );
    }

    protected function getAttempt(array $credentials): bool
    {
        return Auth::attempt($credentials);
    }
    public function saveUserWeb($request)
    {
        return $this->userRepository->save([
            'name' => $request->name,
            'dob' => Helper::smDate($request->dob, EDateFormat::Ymd),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'shift_id' => $request->shift_id,
            'department_id' => $request->department_id,
            'monthly_salary' => $request->monthly_salary ?? null,
            'user_type' => "employee",
        ]);
    }
    public function saveAdminWeb($request)
    {
        return $this->userRepository->save([
            'name' => $request->name,
            'dob' => Helper::smDate($request->dob, EDateFormat::Ymd),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'shift_id' => null,
            'user_type' => "admin",
        ]);
    }

    public function saveUser($request)
    {
        $validator = $this->checkUserCreateValidation($request);
        $_result = $this->errorValidation($validator);
        if ($_result) {
            return Helper::errorResponseAPI(message: "Invalid Validation", data: $_result);
        }
        $image = $request->image ?? null;
        if ($request->hasFile('image')) {
            $image = Helper::uploadFile(file: $request->image, file_folder_name: "user");
        }
        $_user = $this->userRepository->save([
            'name' => $request->name,
            'dob' => Helper::smDate($request->dob, EDateFormat::Ymd),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'shift_id' => $request->shift_id,
            'department_id' => $request->department_id,
            'user_type' => "employee",
            'image' =>  $image,
        ]);
        $return_response = [
            'user_id' => $_user->id,
            'name' => $_user->name,
            'dob' => $_user->dob,
            'email' => $_user->email,
            'mobile' => $_user->mobile,
            'address' => $_user->address,
            'image' => ($_user->image)? asset('/uploads/user/'.$_user->image) : "",
        ];
        return Helper::successResponseAPI(message: "Success", data: $return_response);
    }

    private function checkUserCreateValidation($request, $_user = null)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => ['required', 'string', Rule::unique('users')->ignore($_user),],
            'dob' => 'required|date',
            'mobile' => 'required|numeric',
            'address' => 'required|string',
            'shift_id' => 'required',
            'department_id' => 'required',
        ], [
                'email.required' => 'Email is required',
            ]
        );
    }

    private function errorValidation($validator)
    {
        if ($validator->fails()) {
            $validation_error = [];
            if ($validator->errors()->has('name')) {
                $validation_error['error']['name'] = $validator->errors()->first('name');
            }
            if ($validator->errors()->has('email')) {
                $validation_error['error']['email'] = $validator->errors()->first('email');
            }
            if ($validator->errors()->has('dob')) {
                $validation_error['error']['dob'] = $validator->errors()->first('dob');
            }
            if ($validator->errors()->has('mobile')) {
                $validation_error['error']['mobile'] = $validator->errors()->first('mobile');
            }
            if ($validator->errors()->has('address')) {
                $validation_error['error']['address'] = $validator->errors()->first('address');
            }
            if ($validator->errors()->has('shift_id')) {
                $validation_error['error']['shift_id'] = $validator->errors()->first('shift_id');
            }
            if ($validator->errors()->has('department_id')) {
                $validation_error['error']['department_id'] = $validator->errors()->first('department_id');
            }
            return $validation_error;

        }
        return false;
    }

    public function listUser()
    {
        $_user = $this->userRepository->getAllEmployee();
        return UserEmployeeResource::collection($_user);
    }

    public function getAllFaceIdS()
    {
        $_user = $this->userRepository->getAllFaceIdS();
        return FaceIdsResource::collection($_user);
    }

    public function getUserDetails($user_id)
    {
        $_user = $this->userRepository->find($user_id);
        if ($_user->user_type == 'admin') {
            return Helper::errorResponseAPI(message: "Cannot get details", code: ResponseAlias::HTTP_BAD_REQUEST, status: ResponseAlias::HTTP_BAD_REQUEST);
        }
        return [
            'id' => $_user->id,
            'name' => $_user->name,
            'dob' => $_user->dob,
            'email' => $_user->email,
            'mobile' => $_user->mobile,
            'address' => $_user->address,
            'shift_id' => $_user->shift_id,
            'department_id' => $_user->department_id,
            'image' => ($_user->image)? asset('/uploads/user/'.$_user->image) : "",
        ];
    }

    /**
     * @throws SMException
     */
    public function updateUserWeb($request, $user_id)
    {
        $_user = $this->userRepository->find($user_id);
        if ($_user) {
            return $this->userRepository->update($_user, [
                'name' => $request->name,
                'dob' => Helper::smDate($request->dob, EDateFormat::Ymd),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'shift_id' => $request->shift_id,
                'department_id' => $request->department_id ?? $_user->department_id,
                'monthly_salary' => $request->monthly_salary ?? $_user->monthly_salary,
            ]);

        }
        throw new SMException($this->notFoundMessage);
    }

    public function updateUser($request)
    {
        $user_id = $request->user_id;

        $_user = $this->userRepository->find($user_id);
        if ($_user->user_type == 'admin') {
            return Helper::errorResponseAPI(message: "User Cannot be updated", code: ResponseAlias::HTTP_BAD_REQUEST, status: ResponseAlias::HTTP_BAD_REQUEST);
        }
        if ($_user) {
            $validator = $this->checkUserCreateValidation($request, $_user);
            $_result = $this->errorValidation($validator);
            if ($_result) {
                return Helper::errorResponseAPI(message: "Invalid Validation", data: $_result);
            }
            $image = $request->image ?? null;
            if ($request->hasFile('image')) {
                $image = Helper::uploadFile(file: $request->image, file_folder_name: "user");
            }
            $this->userRepository->update($_user, [
                'name' => $request->name,
                'dob' => Helper::smDate($request->dob, EDateFormat::Ymd),
                'email' => $request->email,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'image' => $image,
                'shift_id' => $request->shift_id,
                'department_id' => $request->department_id,
            ]);
            $_user = $this->userRepository->find($user_id);
            $return_response = [
                'name' => $_user->name,
                'dob' => $_user->dob,
                'email' => $_user->email,
                'mobile' => $_user->mobile,
                'address' => $_user->address,
                'image' => ($_user->image)? asset('/uploads/user/'.$_user->image) : "",
            ];
            return Helper::successResponseAPI(message: "Success", data: $return_response);
        }
        return Helper::errorResponseAPI(message: "User Not found", code: ResponseAlias::HTTP_NOT_FOUND, status: ResponseAlias::HTTP_NOT_FOUND);


    }

    public function getSelectList()
    {
        return $this->userRepository->getSelectList();
    }

    public function saveFaceIds($request)
    {
        $user_id = $request->user_id;
        $_user = $this->userRepository->find($user_id);
        if ($_user) {
            $validator = $this->checkFaceIdsCreateValidation($request);
            if ($validator->fails()) {
                $validation_error = [];
                if ($validator->errors()->has('name')) {
                    $validation_error['error']['name'] = $validator->errors()->first('name');
                }
                return Helper::errorResponseAPI(message: "Invalid Validation", data: $validation_error);
            }
            $this->userRepository->saveFaceIds( [
                'data' => $request->data,
                'user_id' => $_user->id,
            ]);
            return Helper::successResponseAPI(message: "Success", data: ['data'=>$request->data]);
        }
        return Helper::errorResponseAPI(message: "User Not found", code: ResponseAlias::HTTP_NOT_FOUND, status: ResponseAlias::HTTP_NOT_FOUND);
    }


    private function checkFaceIdsCreateValidation($request)
    {
        return Validator::make($request->all(), [
                'data' => 'required',
            ]
        );
    }


    public function deleteFaceIds($user_id)
    {
        return $this->userRepository->massDeleteFaceIds($user_id);
    }

    /**
     * @throws SMException
     */
    public function deleteUser($user_id)
    {
        $_user = $this->userRepository->find($user_id);
        if($_user->user_type == "admin"){
            $countAdmin = User::where('user_type','admin')->count();
            if($countAdmin <= 1){
                throw new SMException("Cannot delete Admin. One Admin Remaining");
            }
        }
        if ($_user) {
            $this->userRepository->update($_user, [
                'email' => $_user->email . "-" . Helper::smTodayInYmdHis(),
            ]);
            $this->userRepository->massDeleteFaceIds($user_id);
            return $this->userRepository->delete($_user);
        }
        throw new SMException($this->notFoundMessage);
    }

    public function checkUniqueCode($unique_code)
    {
        return $this->userRepository->checkUniqueCode($unique_code);
    }

    public function sendPasswordResetLink($user)
    {
        return $this->userRepository->update($user , [
            'two_factor_code' => rand(100000, 999999),
            'two_factor_expires_at' => now()->addMinutes(60),
            'otp_verify_status' => 0,
        ]);
    }
    public function checkTwoFactorCode($request)
    {
        return $this->userRepository->checkTwoFactorCode($request->two_factor_code);
    }

    public function getAndUpdateUniqueCode($user)
    {
        $unique_code = uniqid(uniqid() . "-");
        $this->userRepository->update($user , [
            'otp_verify_status' => 0,
            'unique_code' => $unique_code,
        ]);
        return $unique_code;
    }

    public function resetPassword($user,$request)
    {
        return $this->userRepository->update($user , [
            'password' => Helper::passwordHashing($request->password),
        ]);
    }


}
