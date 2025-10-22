@extends('master')

@section('title')
    Company Detail
@stop
@section('page_title')
    Details
@stop
@section('js')
    <script src="{{ asset('assets/js/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/validation/companyDetail.js') }}"></script>
    <script src="{{ asset('assets/numerical_checker.js') }}"></script>
@stop
@section('content')
    @if( checkUserRole())
        <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
                <marquee direction="left">
                    Edits cannot be done in demo version. Please purchase the full code to unlock these features
                </marquee>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Company Detail Setup</h4>
                    @if(checkUserRole())
                        <form id="companyDetail_submit" class="forms-sample" action="#" >
                        <div class="row">
                            <div class="col-lg-12 mb-3">
                                <label for="name" class="form-label"> Company Name</label>
                                <input type="text" id="name" class="form-control" name="name"
                                       value="{{ (($_companyDetail ?? '')? $_companyDetail->name :  old('name')) }}"
                                       placeholder="Enter Company Name">
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="primary_email" class="form-label"> Primary Email</label>
                                <input type="email" id="primary_email" class="form-control" name="primary_email"
                                       value="{{ (($_companyDetail ?? '')? $_companyDetail->primary_email :  old('primary_email')) }}"
                                       placeholder="Enter Primary Email">
                                <span class="text-danger">{{ $errors->first('primary_email') }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="secondary_email" class="form-label"> Secondary Email</label>
                                <input type="email" id="secondary_email" class="form-control" name="secondary_email"
                                       value="{{ (($_companyDetail ?? '')? $_companyDetail->secondary_email :  old('secondary_email')) }}"
                                       placeholder="Enter Secondary Email">
                                <span class="text-danger">{{ $errors->first('secondary_email') }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="primary_contact_no" class="form-label"> Primary Contact No</label>
                                <input type="number" id="primary_contact_no" class="form-control numeric"
                                       name="primary_contact_no"
                                       value="{{ (($_companyDetail ?? '')? $_companyDetail->primary_contact_no :  old('primary_contact_no')) }}"
                                       placeholder="Enter Primary Contact No">
                                <span class="text-danger">{{ $errors->first('primary_contact_no') }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="secondary_contact_no" class="form-label"> Secondary Contact no</label>
                                <input type="number" id="secondary_contact_no" class="form-control numeric"
                                       name="secondary_contact_no"
                                       value="{{ (($_companyDetail ?? '')? $_companyDetail->secondary_contact_no :  old('secondary_contact_no')) }}"
                                       placeholder="Enter Secondary Contact no">
                                <span class="text-danger">{{ $errors->first('secondary_contact_no') }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="address" class="form-label"> Address</label>
                                <input type="text" id="address" class="form-control" name="address"
                                       value="{{ (($_companyDetail ?? '')? $_companyDetail->address :  old('address')) }}"
                                       placeholder="Enter Address">
                                <span class="text-danger">{{ $errors->first('address') }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="website_url" class="form-label"> Website Url</label>
                                <input type="url" id="website_url" class="form-control" name="website_url"
                                       value="{{ (($_companyDetail ?? '')? $_companyDetail->website_url :  old('website_url')) }}"
                                       placeholder="Enter Website Url">
                                <span class="text-danger">{{ $errors->first('website_url') }}</span>
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" id="image" class="form-control" name="image">
                                @if($_companyDetail ?? false)
                                    <a href="{{ $_companyDetail->image_path  }}" target="_blank">
                                        <img src="{{ $_companyDetail->image_path  }}" alt="{{ $_companyDetail->title }}"
                                             height="100px" width="100px">
                                    </a>
                                @endif
                                <span class="text-danger">{{ $errors->first('image') }}</span>
                            </div>
                        </div>
                        </form>
                    @else
                        <form id="companyDetail_submit" class="forms-sample"
                              action="{{ route('admin.companyDetail.companyDetailUpdate') }}" method="post"
                              enctype="multipart/form-data">
                            <input type="hidden" name="_method" value="put"/>
                            @csrf
                            <div class="row">
                                <div class="col-lg-12 mb-3">
                                    <label for="name" class="form-label"> Company Name</label>
                                    <input type="text" id="name" class="form-control" name="name"
                                           value="{{ (($_companyDetail ?? '')? $_companyDetail->name :  old('name')) }}"
                                           placeholder="Enter Company Name">
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label for="primary_email" class="form-label"> Primary Email</label>
                                    <input type="email" id="primary_email" class="form-control" name="primary_email"
                                           value="{{ (($_companyDetail ?? '')? $_companyDetail->primary_email :  old('primary_email')) }}"
                                           placeholder="Enter Primary Email">
                                    <span class="text-danger">{{ $errors->first('primary_email') }}</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label for="secondary_email" class="form-label"> Secondary Email</label>
                                    <input type="email" id="secondary_email" class="form-control" name="secondary_email"
                                           value="{{ (($_companyDetail ?? '')? $_companyDetail->secondary_email :  old('secondary_email')) }}"
                                           placeholder="Enter Secondary Email">
                                    <span class="text-danger">{{ $errors->first('secondary_email') }}</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label for="primary_contact_no" class="form-label"> Primary Contact No</label>
                                    <input type="number" id="primary_contact_no" class="form-control numeric"
                                           name="primary_contact_no"
                                           value="{{ (($_companyDetail ?? '')? $_companyDetail->primary_contact_no :  old('primary_contact_no')) }}"
                                           placeholder="Enter Primary Contact No">
                                    <span class="text-danger">{{ $errors->first('primary_contact_no') }}</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label for="secondary_contact_no" class="form-label"> Secondary Contact no</label>
                                    <input type="number" id="secondary_contact_no" class="form-control numeric"
                                           name="secondary_contact_no"
                                           value="{{ (($_companyDetail ?? '')? $_companyDetail->secondary_contact_no :  old('secondary_contact_no')) }}"
                                           placeholder="Enter Secondary Contact no">
                                    <span class="text-danger">{{ $errors->first('secondary_contact_no') }}</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label for="address" class="form-label"> Address</label>
                                    <input type="text" id="address" class="form-control" name="address"
                                           value="{{ (($_companyDetail ?? '')? $_companyDetail->address :  old('address')) }}"
                                           placeholder="Enter Address">
                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label for="website_url" class="form-label"> Website Url</label>
                                    <input type="url" id="website_url" class="form-control" name="website_url"
                                           value="{{ (($_companyDetail ?? '')? $_companyDetail->website_url :  old('website_url')) }}"
                                           placeholder="Enter Website Url">
                                    <span class="text-danger">{{ $errors->first('website_url') }}</span>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label for="image" class="form-label">Upload Image</label>
                                    <input type="file" id="image" class="form-control" name="image">
                                    @if($_companyDetail ?? false)
                                        <a href="{{ $_companyDetail->image_path  }}" target="_blank">
                                            <img src="{{ $_companyDetail->image_path  }}" alt="{{ $_companyDetail->title }}"
                                                 height="100px" width="100px">
                                        </a>
                                    @endif
                                    <span class="text-danger">{{ $errors->first('image') }}</span>
                                </div>
                                <div class="text-left">
                                    <button type="submit" class="btn btn-primary"><i class="link-icon"
                                                                                     data-feather="plus"></i> Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
