@extends('layouts.master')
@section('content')
<div class="header_div">
    <h3>
        Department List
    </h3>
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Add
    </button>
</div>
<div> 
    <table id="departmentTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>    
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="departmentmodel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="post" id="departmentForm">
                    @csrf
                    <input type="hidden" name="id" id="department_id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                        <div class="text-danger" id="nameError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveButton" onclick="saveDepartment()">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection
@section('script')
<script>
     var table;
    $(document).ready(function() {
          table = $('#departmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('department.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        @if($errors->any())
            $('#exampleModal').modal('show');
        @endif

        $(document).on('click', '.departmentEdit', function() {
            console.log('sdss')
            $('#exampleModal').modal('show');

            var data = table.row($(this).parents('tr')).data();
            var id = data.id;
            var name = data.name;

            $('#department_id').val(id);
            $('#name').val(name);

            $('#modalTitle').text('Edit Department');
            $('#saveButton').text('Update');
        });

        $('#exampleModal').on('show.bs.modal', function() {
            $('#department_id').val('');
            $('#name').val('');  
            $('#modalTitle').html('Add Department');
            $('#saveButton').html('Save');
        });


        $(document).on('click', '.delete-confirm', function(event) {
            event.preventDefault();
            var url = $(this).attr('href');
            if (confirm('Are you sure you want to delete this item?')) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        table.ajax.reload();
                        toastr.success(response.success);
                    },
                    error: function(error) {
                        toastr.error('Error deleting department.');
                    }
                });
            }
        });

    });

    function saveDepartment() {
        var id = $('#department_id').val();
        var name = $('#name').val();

        var url = id ? "{{ route('department.update', ['department' => '__id__']) }}" : "{{ route('department.store') }}";
        url = url.replace('__id__', id);

        $.ajax({
            url: url,
            type: id ? 'PUT' : 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                name: name,
            },
            success: function(response) {
                $('#exampleModal').modal('hide');
                table.ajax.reload();
                toastr.success(response.success);
            },
            error: function(error) {
                if (error.responseJSON.errors) {
                    $('#nameError').text(error.responseJSON.errors.name[0]);
                }
            }
        });
    }

</script>
@endsection
