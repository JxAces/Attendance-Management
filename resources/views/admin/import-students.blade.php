@extends('layouts.user_type.auth')

@section('content')

<div class="container mb-5"> 
    <h1 class="import">Import</h1>
</div>

<div class="card mx-7">
  <div class="card-header bg-infos text-white">
    <h3><i class="fa fa-cloud-upload" style="font-size: 25px;"></i> Import Students</h3>
  </div>
  <div class="card-body">
    <form action="{{ route('import.students.file') }}" method="POST" enctype="multipart/form-data" id="importForm">
      @csrf

      <input type="file" name="excel_file" required>
      <button onclick="showConfirmation(event)">Import Students</button>
    </form>
  </div>
</div>

<div class="card mt-4 mx-7">
  <div class="card-header bg-successs text-white">
    <h3><i class="fa fa-cloud-upload" style="font-size: 25px;"></i> Import EC Officers</h3>
  </div>
  <div class="card-body">
  <form action="{{ route('import.ecofficers.file') }}" method="POST" enctype="multipart/form-data" id="importECOfficerForm">



      @csrf

      <input type="file" name="excel_file" required>
      <button onclick="showECOfficerConfirmation(event)">Import EC Officers</button>
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
      text: 'Are you sure you want to import the student file?',
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

  function showECOfficerConfirmation(event) {
    event.preventDefault();

    Swal.fire({
      title: 'Confirmation',
      text: 'Are you sure you want to import the EC Officer file?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Import',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('importECOfficerForm').submit();
      }
    });
  }
</script>
@endsection