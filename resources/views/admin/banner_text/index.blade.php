@extends('admin_layouts.app')
@section('content')
<div class="row mt-4">
  <div class="col-12">
    <div class="card">
      <!-- Card header -->
      <div class="card-header pb-0">
        <div class="d-lg-flex">
          <div>
            <h5 class="mb-0">Banner Texts</h5>

          </div>
          <div class="ms-auto my-auto mt-lg-0 mt-4">
            <div class="ms-auto my-auto">
              <a href="{{ route('admin.text.create') }}" class="btn bg-gradient-primary btn-sm mb-0">+&nbsp; New Banner Text</a>
            </div>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-flush" id="banners-search">
          <thead class="thead-light">
            <tr>
              <th>#</th>
              <th>Text</th>
              <th>Agent</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($texts as $key => $text)
            <tr>
              <td class="text-sm font-weight-normal">{{ ++$key }}</td>
              <td>{{ $text->text }}</td>
              <td>{{ $text->agent->name }}</td>
              <td class="text-sm font-weight-normal">{{ $text->created_at->format('M j, Y') }}</td>
              <td>
                <a href="{{ route('admin.text.edit', $text->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Edit Banner"><i class="material-icons-round text-secondary position-relative text-lg">mode_edit</i></a>
                <a href="{{ route('admin.text.show', $text->id) }}" data-bs-toggle="tooltip" data-bs-original-title="Preview Banner Detail">
                  <i class="material-icons text-secondary position-relative text-lg">visibility</i>
                </a>
                <form class="d-inline" action="{{ route('admin.text.destroy', $text->id) }}" method="POST">
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
@endsection
@section('scripts')
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
@endsection
