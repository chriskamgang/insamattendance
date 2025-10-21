<div class="row">
    <div class="col-lg-12 mb-3">
        <label for="title" class="form-label"> Title</label>
        <input type="text" id="title" class="form-control" name="title" value="{{ (($_leaveType ?? '')? $_leaveType->title :  old('title')) }}" placeholder="Enter Title" >
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
    </div>
</div>
