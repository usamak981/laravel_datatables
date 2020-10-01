@extends('layouts.admin')

@section('content')



<!-- <div class="form-group">
        <label class="control-label col-md-1">Date : </label>
    <div class="col-md-3">
        <input type="text" name="datefilter" value="" class="form-control"/>
  </div>
</div> -->

<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 35%">
    <i class="fa fa-calendar"></i>&nbsp;
    <span></span> <i class="fa fa-caret-down"></i>
</div>

<!-- <input type="text" name="datefilter" value="" /> -->


<canvas id="myChart" width="380" height="220"></canvas>


@endsection

@section('footer')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js"></script>

<script>
window.onload = function() {

    

$(function() {

var start = moment().subtract(29, 'days');
var end = moment();


function cb(start, end) {

    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    dateStartInp = start.format('YYYY-MM-DD');
    dateEndInp = end.format('YYYY-MM-DD');
    // console.log(dateStartInp);
    // console.log(dateEndInp);
    if(myChart){
    myChart.destroy();
    }
    ajaxWrapper(dateStartInp, dateEndInp);
 
}

$('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
    ranges: {
       'Today': [moment(), moment()],
       'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
       'Last 7 Days': [moment().subtract(6, 'days'), moment()],
       'Last 30 Days': [moment().subtract(29, 'days'), moment()],
       'This Month': [moment().startOf('month'), moment().endOf('month')],
       'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    }
}, cb);

cb(start, end);


});

// $(function() {

// $('input[name="datefilter"]').daterangepicker({
//     autoUpdateInput: false,
//     locale: {
//         cancelLabel: 'Clear'
//     }
// });

// $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
//     $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
//     dateStartInp = picker.startDate.format('YYYY-MM-DD');
//     dateEndInp = picker.endDate.format('YYYY-MM-DD');
//     myChart.destroy();
//     ajaxWrapper(dateStartInp, dateEndInp);
//     // console.log(dateInp);
// });

// $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
//     $(this).val('');
// });

// });


var dateStartInp;
var dateEndInp;
var myChart;

var ctx = document.getElementById('myChart');

function ajaxWrapper(startDate, endDate){
    $.ajax({
        url: "{{route('admin.dashboard')}}",
        method: "GET",
        data: {start_date: startDate, end_date: endDate},
        success: function(data) {
            console.log(data);
            var date = [];
            var users = [];

            for(var i in data) {
                date.push( data[i].date);
                users.push(data[i].user_count);
            }

             myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: date,
                datasets: [{
                    label: 'Users',
                    data: users,
                    backgroundColor: 
                        '#1b91ab',
                    borderColor: 
                        '#1b91ab'
                    ,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

            
        },
        error: function(data) {
            console.log(data);
        }
    });
}



}
</script>

@endsection