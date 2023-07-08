@extends('layouts.user_type.auth')

@section('content')

<div class="card">
  <div class="card-header bg-info text-white">
    <h3><i class="fa fa-cloud-upload" style="font-size: 25px;"></i> Import Students</h3>
  </div>
  <div class="card-body">
    <form action="{{ route('import.students.file') }}" method="POST" enctype="multipart/form-data" id="importForm">
      @csrf

      <input type="file" name="excel_file" required>
      <button onclick="showConfirmation(event)">Import</button>
    </form>
  </div>
</div>

@endsection

@section('scripts')
<script>
  function showConfirmation(event) {
    event.preventDefault();

    Swal.fire({
      title: 'Confirmation',
      text: 'Are you sure you want to import the file?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Import',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('importForm').submit();
      }
    });
  }
</script>
@endsection
