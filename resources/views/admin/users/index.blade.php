{{-- <html>
 <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Datatables</title>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="{{asset('js/libs.js')}}"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
  <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>  
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
 </head>
 <body> --}}

  @extends('layouts.admin')

  @section('content')
  {{-- <div class="container">     --}}
     <br />
     <h3 align="center">User Datatables</h3>
     <br />
     <div align="right">
      <button type="button" name="create_record" id="create_record" class="btn btn-success btn-sm">Create Record</button>
      <button type="button" name="delete_record" id="delete_record" class="btn btn-danger btn-sm">Delete Records</button>
     </div>
     <br />
   <div class="table-responsive">
    <table id="user_table" class="table table-bordered table-striped">
     <thead>
      <tr>
                <th width="5%"><input type="checkbox" id="all-checked"></th>
                <th width="20%">Name</th>
                <th width="20%">Email</th>
                <th width="15%">Role Name</th>
                <th width="20%">Company Name</th>
                <th width="20%">Action</th>
      </tr>
     </thead>
    </table>
   </div>
   <br />
   <br />
  {{-- </div> --}}

  @endsection
 </body>
</html>

@section('footer')
<div id="formModal" class="modal fade" role="dialog">
 <div class="modal-dialog">
  <div class="modal-content">
   <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add New Record</h4>
        </div>
        <div class="modal-body">
         <span id="form_result"></span>
         <form method="post" id="sample_form" class="form-horizontal">
          @csrf
          <div class="form-group">
            <label class="control-label col-md-4" >Name : </label>
            <div class="col-md-8">
             <input type="text" name="name" id="name" class="form-control" />
            </div>
           </div>
           <div class="form-group">
            <label class="control-label col-md-4">Email : </label>
            <div class="col-md-8">
             <input type="text" name="email" id="email" class="form-control" />
            </div>
           </div>
           <div class="form-group" id="password_inp">
            <label class="control-label col-md-4">Password : </label>
            <div class="col-md-8">
             <input type="text" name="password" id="password" class="form-control" />
            </div>
           </div>
           <div class="form-group">
            <label class="control-label col-md-4">Role Name : </label>
            <div class="col-md-8">
             {{-- <input type="text" name="role_name" id="role_name" class="form-control" /> --}}
              <select name="role_name" id="role_name" class="form-control" >
                <option value="0">none</option>
                <option value="1">admin</option>
                <option value="2">user</option>
              </select>
            </div>
           </div>
           <div class="form-group">
            <label class="control-label col-md-4">Company Name : </label>
            <div class="col-md-8">
             {{-- <input type="text" name="company_name" id="company_name" class="form-control" /> --}}
              <select name="company_name" id="company_name" class="form-control" >
                <option value="0">none</option>
                <option value="1">aerochem</option>
                <option value="2">hubsol</option>
                <option value="3">altInsurance</option>
              </select>
            </div>
           </div>
                <br />
                <div class="form-group" align="center">
                 <input type="hidden" name="action" id="action" value="Add" />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 {{-- <input type="hidden" name="hidden_company_id" id="hidden_company_id" /> --}}
                 <input type="submit" name="action_button" id="action_button" class="btn btn-warning" value="Add" />
                </div>
         </form>
        </div>
     </div>
    </div>
</div>

<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h2 class="modal-title">Confirmation</h2>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>



<script>
$(document).ready(function(){

 $('#user_table').DataTable({
  processing: true,
  serverSide: true,
  ajax: {
   url: "{{ route('admin.index') }}",
  },
  "order":[[0, 'asc']],
  columns: [
   {
    data: 'check', 
    orderable: false
   },
   {
    data: 'name',
    name: 'u.name', 
    orderable: true
    
   },
   {
    data: 'email',
    name: 'u.email', orderable: true
   },
   {
    data: 'role_name',
    name: 'r.name'
   },
   {
    data: 'company_name',
    name: 'c.name'
   },
   {
    data: 'action',
    orderable: false
   }
  ]
 });

 $('#create_record').click(function(){
  $('.modal-title').text('Add New Record');
  $('#action_button').val('Add');
  $('#action').val('Add');
  $('#password_inp').show();
  $('#form_result').html('');

  $('#formModal').modal('show');
 });

 $('#sample_form').on('submit', function(event){
  event.preventDefault();
  var action_url = '';

  // $('#hidden_company_id').val($('#role_name').val());

  if($('#action').val() == 'Add')
  {
   action_url = "{{ route('admin.store') }}";
  }

  if($('#action').val() == 'Edit')
  {
   action_url = "{{ route('admin.update') }}";
  }

  $.ajax({
   url: action_url,
   method:"POST",
   data:$(this).serialize(),
   dataType:"json",
   success:function(data)
   {
    var html = '';
    if(data.errors)
    {
     html = '<div class="alert alert-danger">';
     for(var count = 0; count < data.errors.length; count++)
     {
      html += '<p>' + data.errors[count] + '</p>';
     }
     html += '</div>';
    }
    if(data.success)
    {
     html = '<div class="alert alert-success">' + data.success + '</div>';
     $('#sample_form')[0].reset();
     $('#user_table').DataTable().ajax.reload();
    }
    $('#form_result').html(html);
   }
  });
 });

 $(document).on('click', '.edit', function(){
  var id = $(this).attr('id');
  // var company_id = $(this).data('company_id');
  $('#password_inp').hide();
  $('#form_result').html('');
  $.ajax({
   url :"admin/"+id+"/edit",
   dataType:"json",
   success:function(data)
   {
    $('#name').val(data.result.name);
    $('#email').val(data.result.email);
    $('#role_name').val(data.result.role_id);
    $('#company_name').val(data.result.company_id);
    $('#hidden_id').val(id);

    $('.modal-title').text('Edit Record');
    $('#action_button').val('Edit');
    $('#action').val('Edit');
    $('#formModal').modal('show');
   }
  })
 });

 var user_id;

 $(document).on('click', '.delete', function(){
  user_id = $(this).attr('id');
  $('#confirmModal').modal('show');
  $('#ok_button').text('ok');
 });

 $('#ok_button').click(function(){
  $.ajax({
   url:"admin/destroy/"+user_id,
   beforeSend:function(){
    $('#ok_button').text('Deleting...');
   },
   success:function(data)
   {
    setTimeout(function(){
     $('#confirmModal').modal('hide');
     $('#user_table').DataTable().ajax.reload();
     alert('Data Deleted');
    }, 200);
   }
  })
 });

$('#all-checked').change(function(){
  $('.check-boxes').prop("checked", $(this).prop("checked"));
});

$('#delete_record').click(function(){
  var id = [];
   id = $('.check-boxes:checked').map(function(){
    return $(this).attr('id');
  }).get().join(',');
  // console.log(id);
  $.ajax({
   url:"destroy_records",
   data: {ids : id},
   beforeSend:function(){
   },
   success:function(data)
   {
    $('#user_table').DataTable().ajax.reload();
   }
  })

})


});
</script>

@endsection