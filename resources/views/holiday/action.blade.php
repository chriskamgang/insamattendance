<div class="row">
    <div class="col-lg-6 mb-3">
        <label for="title" class="form-label"> Title</label>
        <input type="text" id="title" class="form-control" name="title" value="{{ (($_holiday ?? '')? $_holiday->title :  old('title')) }}" placeholder="Enter Title" >
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="date" class="form-label"> Date</label>
        <input type="date" id="date" class="form-control" name="date" value="{{ (($_holiday ?? '')? $_holiday->date :  old('date')) }}">
        <span class="text-danger">{{ $errors->first('date') }}</span>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
    </div>
</div>
