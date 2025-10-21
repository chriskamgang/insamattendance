@extends('master')

@section('title')
    Setting
@stop
@section('page_title')
    App Setting
@stop
@section('css')
    <link href="{{asset('admin/assets/vendors/select2/select2.min.css')}}" rel="stylesheet" />
@stop
@section('js')
    <script src="{{asset('admin/assets/vendors/select2/select2.min.js')}}"></script>
    <script>
        $(function() {
            'use strict'
            if ($(".timeZoneSelect2").length) {
                $(".timeZoneSelect2").select2();
            }
        });
    </script>
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
                    <h4 class="mb-4">App Setting</h4>
                    @if(checkUserRole())
                        <form id="leaveType_submit" class="forms-sample" action="#" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row" data-select2-id="select2-data-5-zgch">
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Check Password :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="check_password_yes" name="check_password" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['check_password'] == 1) checked @endif>
                                        <label class="form-check-label" for="check_password_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="check_password_no" name="check_password" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['check_password'] == 0) checked @endif>
                                        <label class="form-check-label" for="check_password_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('check_password') }}</span>
                                </div>
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Enable Notice :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_notice_yes" name="enable_notice" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['enable_notice'] == 1) checked @endif>
                                        <label class="form-check-label" for="enable_notice_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_notice_no" name="enable_notice" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['enable_notice'] == 0) checked @endif>
                                        <label class="form-check-label" for="enable_notice_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('enable_notice') }}</span>
                                </div>
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Enable Lunch in-out :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_lunch_in_out_yes" name="enable_lunch_in_out" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['enable_lunch_in_out'] == 1) checked @endif>
                                        <label class="form-check-label" for="enable_lunch_in_out_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_lunch_in_out_no" name="enable_lunch_in_out" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['enable_lunch_in_out'] == 0) checked @endif>
                                        <label class="form-check-label" for="enable_lunch_in_out_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('enable_lunch_in_out') }}</span>
                                </div>
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Enable Birthday :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_birthday_yes" name="enable_birthday" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['enable_birthday'] == 1) checked @endif>
                                        <label class="form-check-label" for="enable_birthday_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_birthday_no" name="enable_birthday" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['enable_birthday'] == 0) checked @endif>
                                        <label class="form-check-label" for="enable_birthday_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('enable_birthday') }}</span>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="birthday_message" class="form-label"> Birthday Message</label>
                                    <textarea id="birthday_message" name="birthday_message" class="form-control"
                                              required>{!! $_setting['birthday_message'] !!}</textarea>
                                    <span class="text-danger">{{ $errors->first('birthday_message') }}</span>
                                    <span class="text-danger">*Note: Write a message that will be visible in the home screen when an employee has birthday on the date. Placing #employee will make the text to override with the user's birthday.</span>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="banner_image" class="form-label">Dashboard Banner</label>
                                    <input type="file" id="banner_image" class="form-control" name="banner_image">
                                    @if($_setting['banner_image'] ?? false)
                                        <a href="{{ asset('admin/uploads/setting/'. $_setting['banner_image'])  }}"
                                           target="_blank">
                                            <img src="{{ asset('admin/uploads/setting/'. $_setting['banner_image'])  }}"
                                                 alt="{{ $_setting['banner_image'] }}" height="100px" width="100px">
                                        </a>
                                    @endif
                                    <span class="text-danger">{{ $errors->first('banner_image') }}</span>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="banner_url" class="form-label"> Dashboard Banner Url</label>
                                    <input type="url" id="banner_url" class="form-control" name="banner_url" value="{{ $_setting['banner_url'] }}" placeholder="Enter Dashboard Banner Url" >
                                    <span class="text-danger">{{ $errors->first('banner_url') }}</span>
                                </div>
                                <div class="col-xl-6 mb-3" data-select2-id="select2-data-3-lfv6">
                                    <label for="timezone" class="form-label"> Select Timezone</label>
                                    <select id="timezone" name="timezone" class="timeZoneSelect2 form-select" data-width="100%" required>
                                        <option>Select Timezone</option>
                                        @php $timezone = (($_setting ?? '')? $_setting['timezone'] : '') @endphp
                                        @foreach($timezoneArray as $timezoneKey => $timezoneValue)
                                            <option value="{{$timezoneKey}}" @if($timezone === $timezoneKey) selected @endif>{{ucfirst($timezoneValue)}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('user_id') }}</span>
                                </div>
                            </div>
                        </form>
                    @else
                        <form id="leaveType_submit" class="forms-sample" action="{{route('admin.setting.appSettingSave')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row" data-select2-id="select2-data-5-zgch">
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Check Password :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="check_password_yes" name="check_password" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['check_password'] == 1) checked @endif>
                                        <label class="form-check-label" for="check_password_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="check_password_no" name="check_password" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['check_password'] == 0) checked @endif>
                                        <label class="form-check-label" for="check_password_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('check_password') }}</span>
                                </div>
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Enable Notice :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_notice_yes" name="enable_notice" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['enable_notice'] == 1) checked @endif>
                                        <label class="form-check-label" for="enable_notice_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_notice_no" name="enable_notice" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['enable_notice'] == 0) checked @endif>
                                        <label class="form-check-label" for="enable_notice_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('enable_notice') }}</span>
                                </div>
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Enable Lunch in-out :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_lunch_in_out_yes" name="enable_lunch_in_out" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['enable_lunch_in_out'] == 1) checked @endif>
                                        <label class="form-check-label" for="enable_lunch_in_out_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_lunch_in_out_no" name="enable_lunch_in_out" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['enable_lunch_in_out'] == 0) checked @endif>
                                        <label class="form-check-label" for="enable_lunch_in_out_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('enable_lunch_in_out') }}</span>
                                </div>
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label"> Enable Birthday :</label>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_birthday_yes" name="enable_birthday" type="radio"
                                               class="form-check-input" value="1"
                                               @if($_setting['enable_birthday'] == 1) checked @endif>
                                        <label class="form-check-label" for="enable_birthday_yes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input id="enable_birthday_no" name="enable_birthday" type="radio"
                                               class="form-check-input" value="0"
                                               @if($_setting['enable_birthday'] == 0) checked @endif>
                                        <label class="form-check-label" for="enable_birthday_no">No</label>
                                    </div>
                                    <span class="text-danger">{{ $errors->first('enable_birthday') }}</span>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="birthday_message" class="form-label"> Birthday Message</label>
                                    <textarea id="birthday_message" name="birthday_message" class="form-control"
                                              required>{!! $_setting['birthday_message'] !!}</textarea>
                                    <span class="text-danger">{{ $errors->first('birthday_message') }}</span>
                                    <span class="text-danger">*Note: Write a message that will be visible in the home screen when an employee has birthday on the date. Placing #employee will make the text to override with the user's birthday.</span>
                                </div>
                                <div class="col-xl-6 mb-3" data-select2-id="select2-data-3-lfv6">
                                    <label for="timezone" class="form-label"> Select Timezone</label>
                                    <select id="timezone" name="timezone" class="timeZoneSelect2 form-select" data-width="100%" required>
                                        <option>Select Timezone</option>
                                        @php $timezone = (($_setting ?? '')? $_setting['timezone'] : '') @endphp
                                        @foreach($timezoneArray as $timezoneKey => $timezoneValue)
                                            <option value="{{$timezoneKey}}" @if($timezone === $timezoneKey) selected @endif>{{ucfirst($timezoneValue)}}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('user_id') }}</span>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="banner_image" class="form-label">Dashboard Banner</label>
                                    <input type="file" id="banner_image" class="form-control" name="banner_image">
                                    @if($_setting['banner_image'] ?? false)
                                        <a href="{{ asset('admin/uploads/setting/'. $_setting['banner_image'])  }}"
                                           target="_blank">
                                            <img src="{{ asset('admin/uploads/setting/'. $_setting['banner_image'])  }}"
                                                 alt="{{ $_setting['banner_image'] }}" height="100px" width="100px">
                                        </a>
                                    @endif
                                    <span class="text-danger">{{ $errors->first('banner_image') }}</span>
                                </div>
                                <div class="col-xl-6 mb-3">
                                    <label for="banner_url" class="form-label"> Dashboard Banner Url</label>
                                    <input type="url" id="banner_url" class="form-control" name="banner_url" value="{{ $_setting['banner_url'] }}" placeholder="Enter Dashboard Banner Url" >
                                    <span class="text-danger">{{ $errors->first('banner_url') }}</span>
                                </div>

                                <div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
