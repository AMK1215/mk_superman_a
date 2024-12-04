@extends('admin_layouts.app')
@section('styles')
<style>
  .transparent-btn {
    background: none;
    border: none;
    padding: 0;
    outline: none;
    cursor: pointer;
    box-shadow: none;
    appearance: none;
    /* For some browsers */
  }


  .custom-form-group {
    margin-bottom: 20px;
  }

  .custom-form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #555;
  }

  .custom-form-group input,
  .custom-form-group select {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #e1e1e1;
    border-radius: 5px;
    font-size: 16px;
    color: #333;
  }

  .custom-form-group input:focus,
  .custom-form-group select:focus {
    border-color: #d33a9e;
    box-shadow: 0 0 5px rgba(211, 58, 158, 0.5);
  }

  .submit-btn {
    background-color: #d33a9e;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 18px;
    font-weight: bold;
  }

  .submit-btn:hover {
    background-color: #b8328b;
  }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/material-icons@1.13.12/iconfont/material-icons.min.css">
@endsection
@section('content')
<div class="row">
  <div class="col-12">
    <div class="container mb-3">
      <a class="btn btn-icon btn-2 btn-primary float-end me-5" href="{{ route('admin.banks.index') }}">
        <span class="btn-inner--icon mt-1"><i class="material-icons">arrow_back</i>Back</span>
      </a>
    </div>
    <div class="container my-auto mt-5">
      <div class="row">
        <div class="col-lg-10 col-md-2 col-12 mx-auto">
          <div class="card z-index-0 fadeIn3 fadeInBottom">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-2 pe-1">
                <h4 class="text-white font-weight-bolder text-center mb-2">Bank Create</h4>
              </div>
            </div>
            <div class="card-body">
              <form role="form" class="text-start" action="{{ route('admin.banks.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="custom-form-group">
                  <label for="title">Bank Type <span class="text-danger">*</span></label>
                  <div class="custom-select-wrapper">
                    <select name="payment_type_id" class="form-control custom-select">
                      <option value="">Choose Bank Type</option>
                      @foreach ($payment_types as $type)
                      <option value="{{ $type->id}}">{{$type->name}}</option>
                      @endforeach
                    </select>
                    @error('payment_type_id')
                      <span class="text-danger d-block">*{{ $message }}</span>
                    @enderror
                  </div>
                </div>
                <div class="custom-form-group">
                  <label for="title">Account Name</label>
                  <input type="text" class="form-control" name="account_name">
                  @error('account_name')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                <div class="custom-form-group">
                  <label for="title">Account No</label>
                  <input type="text" class="form-control" name="account_number">
                  @error('account_number')
                  <span class="text-danger d-block">*{{ $message }}</span>
                  @enderror
                </div>
                @if(Auth::user()->hasRole('Master'))
                  <div class="custom-form-group">
                    <label for="title">Select Agent</label>
                    <select name="agent_id" class="form-control form-select" id="">
                      <option value="">Select Agent</option>
                      @foreach (Auth::user()->agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                      @endforeach
                    </select>
                    @error('agent_id')
                      <span class="text-danger">*{{ $message }}</span>
                    @enderror
                    {{-- <input type="file" class="form-control" id="inputEmail3" name="image"> --}}
                  </div>
                @endif
                <div class="custom-form-group" type="submit">
                  <button class="btn btn-primary" type="submit">Create</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>

<script src="{{ asset('admin_app/assets/js/plugins/choices.min.js') }}"></script>
<script src="{{ asset('admin_app/assets/js/plugins/quill.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

@endsection