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
        
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="users-search">
          <thead class="thead-light">
            <th>#</th>
            <th>PlayerId</th>
            <th>PlayerName</th>
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
              <td>{{ number_format($deposit->amount) }}</td>
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
        </table>
      </div>
    </div>
    @endsection
    @section('scripts')
    <script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
    <script>
      if (document.getElementById('users-search')) {
        const dataTableSearch = new simpleDatatables.DataTable("#users-search", {
          searchable: true,
          fixedHeight: false,
          perPage: 7
        });

        document.querySelectorAll(".export").forEach(function(el) {
          el.addEventListener("click", function(e) {
            var type = el.dataset.type;

            var data = {
              type: type,
              filename: "material-" + type,
            };

            if (type === "csv") {
              data.columnDelimiter = "|";
            }

            dataTableSearch.export(data);
          });
        });
      };
    </script>
    <script>
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      })
    </script>

    @endsection