<?php

namespace App\Http\Repositories;

use App\Helper\Helper;
use App\Models\CompanyDetails;
use Illuminate\Support\Facades\DB;

class CompanyDetailRepository
{
    private CompanyDetails $companyDetail;

    public function __construct()
    {
        $this->companyDetail = new CompanyDetails();
    }

    /**
     * @param $companyDetail
     * @param $data
     * @return mixed
     */
    public function update($companyDetail, $data): mixed
    {
        return DB::transaction(static function () use ($companyDetail, $data) {
            return $companyDetail->update($data);
        });
    }

    /**
     * @return mixed
     */
    public function getCompanyDetail(): mixed
    {
        return $this->companyDetail->first();
    }


}
