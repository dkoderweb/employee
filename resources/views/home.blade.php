@extends('layouts.master')
@section('content')
<style>
    .select2-close-mask{
    z-index: 2099;
}
.select2-dropdown{
    z-index: 3051;
}
</style>
<div class="header_div">
    <h3>Employee List</h3>
    <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">Add</button> 
</div>
<div> 
    <table id="employeeTable" class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Departments</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>    
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal"  aria-labelledby="employeemodel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="post" id="employeeForm">
                    @csrf
                    <input type="hidden" name="id" id="employee_id">
                    <div class="mb-3">
                        <label for="name" class="form-label">Employee Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                        <div class="text-danger" id="nameError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <div class="text-danger" id="emailError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                        <div class="text-danger" id="phoneError"></div>
                    </div>
                    <div class="mb-3">
                        <label for="departments" class="form-label">Departments</label>
                        <select class="select2" id="departments" name="departments[]" multiple="multiple">
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger" id="departmentsError"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="saveButton" onclick="saveEmployee()">Save</button>
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
    $('.select2').select2();

        if($('#employeeTable thead tr').length == '2')
    {
        $('#employeeTable thead tr:eq(0)').remove();
    }

    $('#employeeTable thead tr').clone(true).appendTo( '#employeeTable thead' );

    $('#employeeTable thead tr:eq(0) th').each( function (i) {
        var title = $(this).text();

        var newTitle = title.split(' ').join('_');

        $(this).html( '<input type="text" class="form-control input-sm search_'+newTitle+'" data-col="'+i+'" placeholder="Search" />' );

        $( 'input', this ).on( 'keyup', function () {
            if ( table.column(i).search() !== this.value ) 
            {
                table
                .column(i)
                .search( this.value )
                .draw();
            }
        });
    });

    $('#employeeTable thead tr:eq(0) th input').css({
        'width':'140px',
        'display':'inline-block'
    });

    $('.search_Action').prop('disabled',true);

        table = $('#employeeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('employee.index') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { 
                    data: 'departments', 
                    name: 'departments',
                    render: function(data, type, row) {
                        return Array.isArray(data) ? data.join(', ') : data;
                    }
                },                
            { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });

        @if($errors->any())
            $('#exampleModal').modal('show');
        @endif

        $(document).on('click', '.employeeEdit', function() {
            $('#nameError').text('');
            $('#emailError').text('');
            $('#phoneError').text('');
            $('#departmentsError').text('');
            $('#exampleModal').modal('show');
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('employee.edit', ['employee' => '__id__']) }}".replace('__id__', id),
                method: "GET",
                dataType: "json",
                success: function(response) {
                    var data = response.data;
                    $('#employee_id').val(data.id);
                    $('#name').val(data.name);
                    $('#email').val(data.email);
                    $('#phone').val(data.phone);
                    $('#departments').val([]).trigger('change'); 

                    var departmentArray = data.departments.map(function(department) {
                        return department.id;
                    });

                    if (departmentArray && departmentArray.length > 0) {
                        $('#departments').val(departmentArray).trigger('change');
                    } 
                },
                error: function(error) {
                    console.error("Error fetching employee details:", error);
                }
            });


            $('#modalTitle').text('Edit Employee');
            $('#saveButton').text('Update');
        });


        $('#exampleModal').on('show.bs.modal', function() {
            $('#nameError').text('');
            $('#emailError').text('');
            $('#phoneError').text('');
            $('#departmentsError').text('');
            $('#employee_id').val('');
            $('#name').val('');
            $('#email').val('');
            $('#phone').val('');
            $('#departments').val([]).trigger('change');
            $('#modalTitle').html('Add Employee');
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
                        toastr.error('Error deleting employee.');
                    }
                });
            }
        });
    });

function saveEmployee() {
    var id = $('#employee_id').val();
    var name = $('#name').val();
    var email = $('#email').val();
    var phone = $('#phone').val();
    var departments = $('#departments').val();

    var url = id ? "{{ route('employee.update', ['employee' => '__id__']) }}" : "{{ route('employee.store') }}";
    url = url.replace('__id__', id);
        console.log(id,name,email,phone,departments,url)
    $.ajax({
        url: url,
        type: id ? 'PUT' : 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            name: name,
            email: email,
            phone: phone,
            departments: departments,
        },
        success: function(response) {
            $('#exampleModal').modal('hide');
            table.ajax.reload();
            toastr.success(response.success);
        },
        error: function(error) {
            if (error.responseJSON.errors) {
                $('#nameError').text(error.responseJSON.errors.name ? error.responseJSON.errors.name[0] : '');
                $('#emailError').text(error.responseJSON.errors.email ? error.responseJSON.errors.email[0] : '');
                $('#phoneError').text(error.responseJSON.errors.phone ? error.responseJSON.errors.phone[0] : '');
                $('#departmentsError').text(error.responseJSON.errors.departments ? error.responseJSON.errors.departments[0] : '');
            }
        }
    });
}

</script>
@endsection
