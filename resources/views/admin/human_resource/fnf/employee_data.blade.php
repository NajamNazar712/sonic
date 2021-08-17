<h3 class="text-center"><strong>Employee Details</strong></h3>
<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">
                Trax ID:
            </label>
            <input type="text" id="name" name="overtime" class="form-control" placeholder="Name" value="{{$employee->trax_id}}" disabled>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">
                Name:
            </label>
            <input type="text" id="designation" name="overtime" class="form-control" placeholder="Name" value="{{$employee->designation->name}}" disabled>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">
                Designation:
            </label>
            <input type="text" id="designation" name="overtime" class="form-control" placeholder="Designation" value="{{$employee->designation->name}}" disabled>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">
                Department:
            </label>
            <input type="text" id="department" name="department" class="form-control" placeholder="Department" value="{{$employee->department->name}}" disabled>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">
                City:
            </label>
            <input type="text" id="city" name="overtime" class="form-control" placeholder="City" value="{{$employee->city->name}}" disabled>
        </div>
    </div>
    <div class="col-md-6 joining_date_div">
        <div class="form-group">
            <label for="name">
                Date of Leaving:
            </label>
            <input type="text" id="department" name="department" class="form-control" placeholder="Department" value="{{$fnf->resign_date}}" disabled>
        </div>
    </div>
</div>