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
@endsection
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">DepositRequest</h5>
          </div>
        </div>
        <form action="{{route('admin.agent.deposit')}}" method="GET">
          <div class="row mt-3">
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">PlayerId</label>
                <input type="text" class="form-control" name="player_id" value="{{request()->player_id}}">
              </div>
            </div>
            @can('master_access')
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">AgentId</label>
                <select name="agent_id" class="form-control">
                  <option value="">Select AgentName</option>
                  @foreach($agents as $agent)
                  <option value="{{$agent->id}}" {{request()->agent_id == $agent->id ? 'selected' : ''}}>{{$agent->user_name}}-{{$agent->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            @endcan
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">Start Date</label>
                <input type="datetime-local" class="form-control" name="start_date" value="{{request()->get('start_date')}}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">EndDate</label>
                <input type="datetime-local" class="form-control" name="end_date" value="{{request()->get('end_date')}}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">Status</label>
                <select name="status" id="" class="form-control">
                  <option value="">Select Status</option>
                  <option value="1" {{request()->status == 1 ? 'selected' : ''}}>approved</option>
                  <option value="2" {{request()->status == 2 ? 'selected' : ''}}>Reject</option>
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <div class="input-group input-group-static mb-4">
                <label for="">PaymentType</label>
                <select name="payment_type_id" id="" class="form-control">
                  <option value="">Select Status</option>
                  @foreach($paymentTypes as $type)
                  <option value="{{$type->id}}" {{request()->payment_type_id == $type->id ? 'selected' : ''}}>{{$type->name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-md-3">
              <button class="btn btn-sm btn-primary mt-3" id="search" type="submit">Search</button>
              <button class="btn btn-outline-primary btn-sm  mb-0 mt-sm-0" data-type="csv" type="button" name="button" id="export-csv">Export</button>
              <a href="{{route('admin.agent.deposit')}}" class="btn btn-link text-primary ms-auto border-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Refresh">
                <i class="material-icons text-lg mt-0">refresh</i>
              </a>
            </div>
          </div>
        </form>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="users-search">
          <thead class="thead-light">
            <th>#</th>
            <th>PlayerId</th>
            <th>PlayerName</th>
            <th>AgentName</th>
            <th>Requested Amount</th>
            <th>Payment Method</th>
            <th>Status</th>
            <th>DateTime</th>
            <th>Action</th>
          </thead>
          <tbody>
            @foreach ($deposits as $deposit)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $deposit->user->user_name}}</td>
              <td>{{ $deposit->user->name }}</td>
              <td><span class="badge text-bg-warning text-white ">{{$deposit->user->parent->name}}</span></td>
              <td class="amount">{{ number_format($deposit->amount) }}</td>
              <td>{{ $deposit->bank->paymentType->name }}</td>
              <td>
                @if ($deposit->status == 0)
                <span class="badge text-bg-warning text-white mb-2">Pending</span>
                @elseif ($deposit->status == 1)
                <span class="badge text-bg-success text-white mb-2">Approved</span>
                @elseif ($deposit->status == 2)
                <span class="badge text-bg-danger text-white mb-2">Rejected</span>
                @endif
              </td>
              <td>{{ $deposit->created_at->setTimezone('Asia/Yangon')->format('d-m-Y H:i:s') }}</td>
              <td>
                <div class="d-flex align-items-center">
                  <a href="{{route('admin.agent.depositShow', $deposit->id)}}" class="text-white btn btn-info">Detail</a>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
          <tr id="tfoot">
          </tr>
        </table>
      </div>
    </div>
    @endsection
    @section('scripts')
    <script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('users-search')) {
          const dataTableSearch = new simpleDatatables.DataTable("#users-search", {
            searchable: false,
            fixedHeight: false,
            perPage: 7
          });

          function updateTotalAmount() {
            let totalAmount = 0;

            // Get the visible rows in the current page
            const visibleRows = document.querySelectorAll('#users-search tbody tr');
            visibleRows.forEach(function(row) {
              const amountCell = row.querySelector('.amount');
              if (amountCell) {
                totalAmount += parseFloat(amountCell.textContent.replace(/,/g, '')) || 0;
              }
            });

            const footerRow = `
        <th colspan="4" class="text-center text-dark">Total Amount:</th>
        <th class="text-dark">${totalAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</th>
        <th colspan="6"></th>
      `;
            document.querySelector('#users-search #tfoot').innerHTML = footerRow;
          }

          updateTotalAmount();

          dataTableSearch.on('datatable.page', updateTotalAmount);
          dataTableSearch.on('datatable.perpage', updateTotalAmount);

          document.getElementById('export-csv').addEventListener('click', function() {
            dataTableSearch.export({
              type: "csv",
              filename: "deposit",
            });
          });
        }

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });
      });
    </script>
    @endsection