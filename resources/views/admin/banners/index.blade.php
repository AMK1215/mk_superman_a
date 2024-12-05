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
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
@endsection
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Banners</h5>

          </div>
          <div class="ms-auto my-auto mt-lg-0 mt-4">
            <div class="ms-auto my-auto">
              <a href="{{ route('admin.banners.create') }}" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; Create</a>
              <button 
              class="btn bg-gradient-secondary btn-sm mb-0"
              data-bs-toggle="modal" data-bs-target="#bulkCreateModal"
              >
                Bulk Create 
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="banners-search">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>Image</th>
              <th>Agent</th>
              {{-- <th>Updated At</th> --}}
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($banners as $key => $banner)
            <tr>
              <td class="text-sm font-weight-normal">{{ ++$key }}</td>
              <td>
                <img width="100px" class="img-thumbnail" src="{{ $banner->img_url }}" alt="">
              </td>
              <td class="text-sm font-weight-normal">{{ $banner->agent->name }}</td>
              {{-- <td class="text-sm font-weight-normal">{{ $banner->updated_at->format('F j, Y') }}</td> --}}
              <td>
                <a href="{{ route('admin.banners.edit', $banner->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit Banner"><i class="material-icons-round text-secondary position-relative text-lg">mode_edit</i></a>
                <a href="{{ route('admin.banners.show', $banner->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Preview Banner Detail">
                  <i class="material-icons text-secondary position-relative text-lg">visibility</i>
                </a>
                <form class="d-inline" action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="transparent-btn" data-bs-toggle="tooltip" data-bs-original-title="Delete Banner">
                    <i class="material-icons text-secondary position-relative text-lg">delete</i>
                  </button>
                </form>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


{{-- bulk create modal --}}
<div class="modal fade" style="z-index: 1000;" id="bulkCreateModal" tabindex="-1" aria-labelledby="bulkCreateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Bulk Create (For My Agents)</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('admin.banners.bulkStore') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-4">
            <label for="img_url" class="form-label">Choose Image</label>
            <input type="file" class="form-control" name="image" id="img_url">
            @error('image')
              <span class="text-danger">*{{ $message }}</span>
            @enderror
          </div>
          <button class="btn btn-secondary" type="submit">
            <i class="fa-solid fa-floppy-disk"></i> Upload
          </button>
        </form>
      </div>
      {{-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div> --}}
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script src="{{ asset('admin_app/assets/js/plugins/datatables.js') }}"></script>
<script>
  if (document.getElementById('banners-search')) {
    const dataTableSearch = new simpleDatatables.DataTable("#banners-search", {
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
<script>
  $(document).ready(function() {
    $('.transparent-btn').on('click', function(e) {
      e.preventDefault();
      let form = $(this).closest('form');
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
</script>
@if(session()->has('success'))
<script>
  Swal.fire({
    icon: 'success',
    title: '{{ session('
    success ') }}',
    showConfirmButton: false,
    timer: 1500
  })
</script>
@endif


@endsection
