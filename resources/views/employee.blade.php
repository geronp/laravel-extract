@extends('main')
@section('css')
<link rel="stylesheet" href="{{ asset('css/employee.css') }}">
@endsection
@section('content')
{{$user=null}}
<div class="col-md-12">
  <!-- USERS LIST -->
  <div class="del_msg">
  </div>
  @if (session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  <div class="card">
    <div class="card-header">
      @can('add_employee')<a href="{{url('/employees/add')}}" class="btn mybtn-red btn-sm"><i class="fas fa-plus"></i>&nbsp;<span>Add New Employee</span></a>@endcan
    </div>
    <!-- /.card-header -->
    <div class="card-body p-4">
      <table id="employee" width="100%" class="data-table table-striped table">
        <thead>
          <tr>
            <th class="text-center">No</th>
            <th>Employee ID</th>
            <th>Employee Name</th>
            <th>Email</th>
            <th>Join Date</th>
            <th class="db" data-orderable="false">Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <!-- Employee Link up -->
      <div class="modal fade" id="link_emp" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Link Employee</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group col-md-12">
                <label>User to Link:</label>
                <select name="user_id" id="user_name" class="form-control">
                  <option value="">Choose...</option>
                  @foreach($unlink_users as $u)
                  <option value="{{$u->id}}">{{$u->first_name}} {{$u->last_name}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 30px !important;padding: 8px 20px !important;">Cancel</button>
              <input type="button" name="sbt" onclick="link_employee()" data-dismiss="modal" class="btn mybtn-red" value="Link Employee">
            </div>
          </div>
        </div>
      </div>
      <!----End of employee link -------->
      <!-- Delete employee --->
      <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Alert</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              Do you want to delete employee?
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 30px !important;padding: 8px 20px !important;">Cancel</button>
              <a class="btn btn-danger" data-dismiss="modal" onclick="employee_delete()" style="border-radius: 30px !important;padding: 8px 20px !important;">Delete</a>
            </div>
          </div>
        </div>
      </div>
      <!--- End of employee delete--->
      <!-- /.users-list -->
    </div>
    <!-- /.card-body -->
  </div>
  <!--/.card -->
</div>
@endsection
@section('js')
{!! $js['datatable'] !!}
<script src="{{ asset('js/validation.js') }}"></script>
<script>
  var emp_id;
  var link_employee;
  var employee_delete;
  $(document).ready(function() {
    $('#link_emp').on('shown.bs.modal', function(e) {
      emp_id = $(e.relatedTarget).data("id");
    });
    $('#exampleModalCenter').on('shown.bs.modal', function(e) {
      emp_id = $(e.relatedTarget).data("id");
    });
    link_employee = function() {
      var user_id = $('#user_name').val();
      $.ajax({
        url: "{{route('linkEmp')}}",
        type: 'GET',
        data: {
          "_token": "{{ csrf_token() }}",
          "employee_id": emp_id,
          "user_id": user_id
        },
        success: function(data) {
          table.draw();
        },
        error: function(data) {
          console.log("error" + data);
        }
      });
    }
    var table = $('.data-table').DataTable({
      processing: true,
      serverSide: true,
      responsive: true,
      order: [1, "asc"],
      columnDefs: [{
          responsivePriority: 1,
          targets: [1, 2, 5]
        },
        {
          targets: 0,
          "orderable" : false,
          className: "text-center"
        }
      ],
      buttons: [{
          extend: 'copyHtml5',
          text: '<img src="{{asset("/img/copy.png") }}"  width=25px />',
          titleAttr: 'copy',
          exportOptions: {
            columns: 'th:not(:last-child)'
          }
        },
        {
          extend: 'csvHtml5',
          text: '<img src="{{asset("/img/csv.png") }}"  width=25px />',
          titleAttr: 'CSV',
          exportOptions: {
            columns: 'th:not(:last-child)'
          }
        },
        {
          extend: 'excelHtml5',
          text: '<img src="{{asset("/img/excel.png") }}"  width=25px />',
          titleAttr: 'Excel',
          title: 'Employee Information',
          exportOptions: {
            columns: 'th:not(:last-child)'
          }
        },
        {
          extend: 'pdfHtml5',
          text: '<img src="{{asset("/img/pdf.png") }}"  width=25px />',
          titleAttr: 'PDF',
          title: 'Employee Information',
          exportOptions: {
            columns: 'th:not(:last-child)'
          }
        },
        {
          extend: 'print',
          text: '<img src="{{asset("/img/print.png") }}"  width=25px />',
          titleAttr: 'Print',
          title: 'Employee Information',
          exportOptions: {
            columns: 'th:not(:last-child)'
          }
        }
      ],
      dom: 'lfrBtip',
      ajax: "{{ route('employee') }}",
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex'
        },
        {
          data: 'employee_eid',
          name: 'employee_eid'
        },
        {
          data: 'employee_name',
          name: 'employee_name'
        },
        {
          data: 'email',
          name: 'email'
        },
        {
          data: 'join_date_formatted',
          name: 'join_date'
        },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false
        },
      ]
    });
    employee_delete = function() {
      $.ajax({
        url: "{{route('employee.delete')}}",
        data: {
          "employee_id": emp_id,
        },
        success: function(data) {
          $('#exampleModalCenter').modal('hide');
          table.draw();
          $('.del_msg').append('<div class="alert alert-success alert-dismissible fade show" role="alert">Employee deleted successfully.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>');
          setTimeout(function() {
            $('.alert').fadeOut('fast');
          }, 4000);
        },
        error: function(data) {}
      });
    }
  });
</script>
@endsection