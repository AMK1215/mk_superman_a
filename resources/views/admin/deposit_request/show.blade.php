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

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <!-- Card header -->
            <div class="card-header">
                <div class="d-lg-flex">
                    <div>
                        <h5 class="mb-0">DepositRequest Detail</h5>
                    </div>
                    <div class="ms-auto my-auto mt-lg-0 mt-4">
                        <div class="ms-auto my-auto">
                            <a class="btn btn-icon btn-2 btn-primary" href="{{ route('admin.agent.deposit') }}">
                                <span class="btn-inner--icon mt-1"><i
                                        class="material-icons">arrow_back</i>Back</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <img src="{{asset('assets/img/deposit/'. $deposit->image) }}" class="img-fluid rounded"
                            alt="">
                    </div>
                    <div class="col-md-6">
                        <div class="custom-form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $deposit->user->name }}"
                                readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">Amount</label>
                            <input type="text" class="form-control" name="amount" value="{{ number_format($deposit->amount, 2) }}"
                                readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">DateTime</label>
                            <input type="text" class="form-control" name="amount"
                                value="{{ $deposit->created_at->format('d-m-Y H:i:s') }}"
                                readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">Bank Account Name</label>
                            <input type="text" class="form-control" name="account_name"
                                value="{{ $deposit->bank->account_name }}" readonly>
                        </div>
                        <div class="custom-form-group"><label class="form-label">Bank Account No</label>
                            <input type="text" class="form-control" name="account_number"
                                value="{{ $deposit->bank->account_number }}" readonly>
                        </div>
                        <div class="custom-form-group">
                            <label class="form-label">Payment Method</label>
                            <input type="text" class="form-control" name=""
                                value="{{ $deposit->bank->paymentType->name }}" readonly>
                        </div>
                        <div class="d-lg-flex">
                            <form action="{{ route('admin.agent.depositStatusreject', $deposit->id) }}"
                                method="post" id="form">
                                @csrf
                                <input type="hidden" name="status" value="2">
                                @if($deposit->status == 0)
                                <button class="btn btn-danger" type="submit">
                                    Reject
                                </button>
                                @endif
                            </form>
                            <form action="{{ route('admin.agent.depositStatusUpdate', $deposit->id) }}"
                                method="post">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $deposit->amount }}">
                                <input type="hidden" name="status" value="1">
                                <input type="hidden" name="player" value="{{ $deposit->user_id }}">
                                @if($deposit->status == 0)
                                <button class="btn btn-success" type="submit" style="margin-left: 5px" id="submit">
                                    Approve
                                </button>
                                @endif
                            </form>
                        </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
    $(document).ready(function() {
        $('form').submit(function(e) {
            if ($(this).hasClass('submitted')) {
                e.preventDefault();
            } else {
                $(this).addClass('submitted');
            }
        });
    });
</script>
@endsection