<?php

namespace App\Http\Services;

use App\Exceptions\SMException;
use App\Helper\Helper;
use App\Http\Repositories\CompanyDetailRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CompanyDetailServices
{
    private string $notFoundMessage = "Sorry! Company Details not found";
    private CompanyDetailRepository $companyDetailRepository;


    public function __construct()
    {
        $this->companyDetailRepository = new CompanyDetailRepository();
    }

    /**
     * @throws SMException
     */
    public function companyDetailUpdate($request)
    {
        $_companyDetail = $this->companyDetailRepository->getCompanyDetail();
        if ($_companyDetail) {
            $_image = $_companyDetail->image;
            if ($request->hasFile('image')) {
                Helper::unlinkUploadedFile($_companyDetail->image, "company");
                $_image = Helper::uploadFile(file: $request->image, file_folder_name: "company");
            }
            return $this->companyDetailRepository->update($_companyDetail, [
                'name' => $request->name,
                'primary_email' => $request->primary_email,
                'secondary_email' => $request->secondary_email,
                'primary_contact_no' => $request->primary_contact_no,
                'secondary_contact_no' => $request->secondary_contact_no,
                'address' => $request->address,
                'image' => $_image,
                'website_url' => $request->website_url,
            ]);
        }
        throw new SMException($this->notFoundMessage);
    }
    public function getCompanyDetail()
    {
        return $this->companyDetailRepository->getCompanyDetail();
    }

    /**
     * @throws SMException
     */
    public function getCompanyDetailApi(): array
    {
        $_companyDetail = $this->companyDetailRepository->getCompanyDetail();
        if ($_companyDetail) {
            return [
                'name' => $_companyDetail->name,
                'primary_email' => $_companyDetail->primary_email ?? "",
                'secondary_email' => $_companyDetail->secondary_email ?? "",
                'primary_contact_no' => $_companyDetail->primary_contact_no ?? "",
                'secondary_contact_no' => $_companyDetail->secondary_contact_no ?? "",
                'address' => $_companyDetail->address ?? "",
                'image' => $_companyDetail->image_path,
                'website_url' => $_companyDetail->website_url ?? "",
            ];
        }
        throw new SMException($this->notFoundMessage);
    }

    /**
     * @throws SMException
     */
    public function companyDetailUpdateApi($request)
    {
        $validator = $this->checkCompanyDetailValidation($request);
        if ($validator->fails()) {
            $validation_error = [];
            if ($validator->errors()->has('name')) {
                $validation_error['error']['name'] = $validator->errors()->first('name');
            }
            if ($validator->errors()->has('primary_email')) {
                $validation_error['error']['primary_email'] = $validator->errors()->first('primary_email');
            }
            if ($validator->errors()->has('primary_contact_no')) {
                $validation_error['error']['primary_contact_no'] = $validator->errors()->first('primary_contact_no');
            }
            if ($validator->errors()->has('address')) {
                $validation_error['error']['address'] = $validator->errors()->first('address');
            }
            return Helper::errorResponseAPI(message: "Invalid Validation", data: $validation_error);
        }

        $_companyDetail = $this->companyDetailRepository->getCompanyDetail();
        if ($_companyDetail) {
            $_image = $_companyDetail->image;
            if ($request->hasFile('image')) {
                Helper::unlinkUploadedFile($_companyDetail->image, "company");
                $_image = Helper::uploadFile(file: $request->image, file_folder_name: "company");
            }
            $this->companyDetailRepository->update($_companyDetail, [
                'name' => $request->name,
                'primary_email' => $request->primary_email,
                'secondary_email' => $request->secondary_email,
                'primary_contact_no' => $request->primary_contact_no,
                'secondary_contact_no' => $request->secondary_contact_no,
                'address' => $request->address,
                'image' => $_image,
                'website_url' => $request->website_url,
            ]);
            $_companyDetail = $this->companyDetailRepository->getCompanyDetail();
            $_return =  [
                'name' => $_companyDetail->name,
                'primary_email' => $_companyDetail->primary_email ?? "",
                'secondary_email' => $_companyDetail->secondary_email ?? "",
                'primary_contact_no' => $_companyDetail->primary_contact_no ?? "",
                'secondary_contact_no' => $_companyDetail->secondary_contact_no ?? "",
                'address' => $_companyDetail->address ?? "",
                'image' => $_companyDetail->image_path,
                'website_url' => $_companyDetail->website_url ?? "",
            ];
            return Helper::successResponseAPI(message: "Success", data: $_return);
        }
        throw new SMException($this->notFoundMessage);
    }

    private function checkCompanyDetailValidation($request)
    {
        return Validator::make($request->all(), [
            'name' => 'required|string',
            'primary_email' => 'required|string',
            'secondary_email' => 'nullable|email',
            'primary_contact_no' => 'required|numeric',
            'secondary_contact_no' => 'nullable|numeric',
            'address' => 'required|string',
            'website_url' => 'nullable|url',
        ]
        );
    }
}
