<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="title" class="form-label"> Title</label>
        <input type="text" id="title" class="form-control" name="title" value="{{ (($_shift ?? '')? $_shift->title :  old('title')) }}" placeholder="Enter Title" >
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="type" class="form-label"> Shift Type</label>
        <select id="type" name="type" class="form-select">
            <option >Select Shift Type</option>
            @php $typeData = (($_shift ?? old('type'))? $_shift->type : '') @endphp
            @foreach($shiftType as $type)
                <option value="{{$type}}" @if($typeData === $type) selected @endif>{{ucfirst($type)}}</option>
            @endforeach
        </select>
        <span class="text-danger">{{ $errors->first('type') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="start" class="form-label"> Opening Time</label>
        <input type="time" id="start" class="form-control" name="start" value="{{ (($_shift ?? '')? $_shift->start :  old('start')) }}" >
        <span class="text-danger">{{ $errors->first('start') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="end" class="form-label"> Closing Time</label>
        <input type="time" id="end" class="form-control" name="end" value="{{ (($_shift ?? '')? $_shift->end :  old('end')) }}" >
        <span class="text-danger">{{ $errors->first('end') }}</span>
    </div>


    <div class="col-lg-12 mb-3">
        @php $is_early_check_in = (($_shift ?? '')? $_shift->is_early_check_in : '') @endphp
        <span class="form-check form-switch">
            <input id="is_early_check_in" type="checkbox" name="is_early_check_in" value="1" class="form-check-input change-status-toggle" @if($is_early_check_in) checked @endif>
            <label for="is_early_check_in" class="form-label"> Early Check In</label>
        </span>
    </div>
    <div class="col-lg-6 mb-3" id="before_start_div" @if($is_early_check_in) style="display: none" @endif>
        <label for="before_start" class="form-label"> Can check in before (in minute)</label>
        <input type="number" id="before_start" class="form-control numeric" name="before_start" value="{{ (($_shift ?? '')? $_shift->before_start :  old('before_start')) }}" placeholder="Enter Can check in before (in minute)" >
        <span class="text-danger">{{ $errors->first('before_start') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="after_start" class="form-label"> Can check in after (in minute)</label>
        <input type="number" id="after_start" class="form-control numeric" name="after_start" value="{{ (($_shift ?? '')? $_shift->after_start :  old('after_start')) }}" placeholder="Enter Can check in after (in minute)" >
        <span class="text-danger">{{ $errors->first('after_start') }}</span>
    </div>
    <div class="col-lg-12 mb-3">
        @php $is_early_check_out = (($_shift ?? '')? $_shift->is_early_check_out : '') @endphp
        <span class="form-check form-switch">
            <input id="is_early_check_out" type="checkbox" name="is_early_check_out" value="1" class="form-check-input change-status-toggle" @if($is_early_check_out) checked @endif>
            <label for="is_early_check_out" class="form-label"> Early Check out</label>
        </span>
    </div>
    <div class="col-lg-6 mb-3" id="before_end_div" @if($is_early_check_out) style="display: none" @endif>
        <label for="before_end" class="form-label"> Can check out before (in minute)</label>
        <input type="number" id="before_end" class="form-control numeric" name="before_end" value="{{ (($_shift ?? '')? $_shift->before_end :  old('before_end')) }}" placeholder="Enter Can check out before (in minute)" >
        <span class="text-danger">{{ $errors->first('before_end') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="after_end" class="form-label"> Can check out after (in minute)</label>
        <input type="number" id="after_end" class="form-control numeric" name="after_end" value="{{ (($_shift ?? '')? $_shift->after_end :  old('after_end')) }}" placeholder="Enter Can check out after (in minute)" >
        <span class="text-danger">{{ $errors->first('after_end') }}</span>
    </div>

    <div class="col-lg-12 mb-3">
        @php $includes_saturday = (($_shift ?? '')? $_shift->includes_saturday : '') @endphp
        <span class="form-check form-switch">
            <input id="includes_saturday" type="checkbox" name="includes_saturday" value="1" class="form-check-input change-status-toggle" @if($includes_saturday) checked @endif>
            <label for="includes_saturday" class="form-label"> Inclure Samedi (Lundi-Samedi)</label>
        </span>
    </div>
    <div class="col-lg-6 mb-3" id="saturday_end_div" @if(!$includes_saturday) style="display: none" @endif>
        <label for="saturday_end_time" class="form-label"> Heure de fin Samedi</label>
        <input type="time" id="saturday_end_time" class="form-control" name="saturday_end_time" value="{{ (($_shift ?? '')? $_shift->saturday_end_time :  old('saturday_end_time')) }}" placeholder="Ex: 12:00">
        <span class="text-danger">{{ $errors->first('saturday_end_time') }}</span>
        <small class="text-muted">Heure de sortie pour le samedi (généralement 12:00)</small>
    </div>

    <div class="text-center">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
    </div>
</div>
