<div class="row">
    <div class="col-lg-12 mb-3">
        <label for="title" class="form-label"> Title</label>
        <input type="text" id="title" class="form-control" name="title" value="{{ (($_notice ?? '')? $_notice->title :  old('title')) }}" placeholder="Enter Title" >
        <span class="text-danger">{{ $errors->first('title') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="start_date" class="form-label"> Start Date</label>
        <input type="date" id="start_date" class="form-control" name="start_date" value="{{ (($_notice ?? '')? $_notice->start_date :  old('start_date')) }}">
        <span class="text-danger">{{ $errors->first('start_date') }}</span>
    </div>
    <div class="col-lg-6 mb-3">
        <label for="end_date" class="form-label"> End Date</label>
        <input type="date" id="end_date" class="form-control" name="end_date" value="{{ (($_notice ?? '')? $_notice->end_date :  old('end_date')) }}">
        <span class="text-danger">{{ $errors->first('end_date') }}</span>
    </div>
    <div class="col-lg-12 mb-3">
        <label for="description" class="form-label"> Description</label>
        <textarea id="description" class="form-control" name="description">{!! (($_notice ?? '' )? $_notice->description :  old('description')) !!}</textarea>
        <span class="text-danger">{{ $errors->first('description') }}</span>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary"><i class="link-icon" data-feather="plus"></i> {{$btn}}</button>
    </div>
</div>
