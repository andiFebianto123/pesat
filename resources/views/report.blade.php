@extends(backpack_view('blank'))
@section('content')
<h1>Report</h1>
</br>
<form id="form_report" method="post" action="javascript:void(0)">
<div class="row">
    <div class="col-1"> 
        <label><strong>Start date</strong></label>
        <div id= "startdate" class="input-group date">
            <input id ="start_date" type="text" name ="start" class="form-control" required>
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="col-1">
    <label><strong>End date</strong></label>
    <div id= "enddate" class="input-group date">
            <input id="end_date" type="text" name="end" class="form-control" required>
            <div class="input-group-addon">
                <span class="glyphicon glyphicon-th"></span>
            </div>
        </div>
    </div>
    <div class="col-1" style="margin-top: 30px;">
    <button id = "btsubmit" type="button" class="btn btn-success">Apply Filter</button>
    </div>
</div>
</br>
<div class="card" style="width:35%">
		<div class="card-header">
        Rangkuman Data Bulanan        
        </div>
		<div class="card-body">
        <table class="table table-bordered">
        <tr>
            <td colspan="2">
             <p id="totalamount">   Rp.5.700.000 </p>
            </br>
                Total Uang Bulan Ini
            </td>
        </tr>
        <tr>
            <td colspan="2">
                Elvriyanti T. Oinan
            </br>
                Anak tersponsori bulan ini (terdanai 1)
            </td>
        </tr>
        <tr>
            <td>
            37 Orders
            <br>
            menunggu proses
            </td>
            <td>
            0 orders
            </br>
            on-hold
            </td>
        </tr>
        <tr>
            <td>
            0 Data
            <br>
            low in stock
            </td>
            <td>
            1 product
            </br>
            tersponsori
            </td>
        </tr>
        <tr>
            <td>
            <p id="sponsor">4</p> sponsor baru
            <br>
            sponsor mendaftar bulan ini
            </td>
            <td>
            Rp.5500.000,00
            </br>
            Pendapatan dari sponsor bulan ini
            </td>
        </tr>
        <tr>
            <td>
           1 renewal
            <br>
            subscription pembaruan this month
            </td>
            <td>
            Rp.150.000,00
            </br>
            pendapatan dari pembaharuan sponsor bulan ini
            </td>
        </tr>
        
        </table>
        </div>
	</div>
</form>
@endsection
@push('after_styles')
<link rel="stylesheet" href="{{ asset('packages/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css') }}">
@endpush
@push('after_scripts')
<script src="{{ asset('packages/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
 if (jQuery.ui) {
            var datepicker = $.fn.datepicker.noConflict();
            $.fn.bootstrapDP = datepicker;
        } else {
            $.fn.bootstrapDP = $.fn.datepicker;
        }

        var dateFormat=function(){var a=/d{1,4}|m{1,4}|yy(?:yy)?|([HhMsTt])\1?|[LloSZ]|"[^"]*"|'[^']*'/g,b=/\b(?:[PMCEA][SDP]T|(?:Pacific|Mountain|Central|Eastern|Atlantic) (?:Standard|Daylight|Prevailing) Time|(?:GMT|UTC)(?:[-+]\d{4})?)\b/g,c=/[^-+\dA-Z]/g,d=function(a,b){for(a=String(a),b=b||2;a.length<b;)a="0"+a;return a};return function(e,f,g){var h=dateFormat;if(1!=arguments.length||"[object String]"!=Object.prototype.toString.call(e)||/\d/.test(e)||(f=e,e=void 0),e=e?new Date(e):new Date,isNaN(e))throw SyntaxError("invalid date");f=String(h.masks[f]||f||h.masks.default),"UTC:"==f.slice(0,4)&&(f=f.slice(4),g=!0);var i=g?"getUTC":"get",j=e[i+"Date"](),k=e[i+"Day"](),l=e[i+"Month"](),m=e[i+"FullYear"](),n=e[i+"Hours"](),o=e[i+"Minutes"](),p=e[i+"Seconds"](),q=e[i+"Milliseconds"](),r=g?0:e.getTimezoneOffset(),s={d:j,dd:d(j),ddd:h.i18n.dayNames[k],dddd:h.i18n.dayNames[k+7],m:l+1,mm:d(l+1),mmm:h.i18n.monthNames[l],mmmm:h.i18n.monthNames[l+12],yy:String(m).slice(2),yyyy:m,h:n%12||12,hh:d(n%12||12),H:n,HH:d(n),M:o,MM:d(o),s:p,ss:d(p),l:d(q,3),L:d(q>99?Math.round(q/10):q),t:n<12?"a":"p",tt:n<12?"am":"pm",T:n<12?"A":"P",TT:n<12?"AM":"PM",Z:g?"UTC":(String(e).match(b)||[""]).pop().replace(c,""),o:(r>0?"-":"+")+d(100*Math.floor(Math.abs(r)/60)+Math.abs(r)%60,4),S:["th","st","nd","rd"][j%10>3?0:(j%100-j%10!=10)*j%10]};return f.replace(a,function(a){return a in s?s[a]:a.slice(1,a.length-1)})}}();dateFormat.masks={default:"ddd mmm dd yyyy HH:MM:ss",shortDate:"m/d/yy",mediumDate:"mmm d, yyyy",longDate:"mmmm d, yyyy",fullDate:"dddd, mmmm d, yyyy",shortTime:"h:MM TT",mediumTime:"h:MM:ss TT",longTime:"h:MM:ss TT Z",isoDate:"yyyy-mm-dd",isoTime:"HH:MM:ss",isoDateTime:"yyyy-mm-dd'T'HH:MM:ss",isoUtcDateTime:"UTC:yyyy-mm-dd'T'HH:MM:ss'Z'"},dateFormat.i18n={dayNames:["Sun","Mon","Tue","Wed","Thu","Fri","Sat","Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],monthNames:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec","January","February","March","April","May","June","July","August","September","October","November","December"]},Date.prototype.format=function(a,b){return dateFormat(this,a,b)};

        var datestart=$('#startdate').bootstrapDP({
            format: 'M yyyy',
            minViewMode:1,
            maxViewMode:2
        })
        var datestart=$('#enddate').bootstrapDP({
            format: 'M yyyy',
            minViewMode:1,
            maxViewMode:2
        })
        $('#btsubmit').click(function(){
            console.log($('#datestart').bootstrapDP('getDate'))
        }
        )


$(document).ready(function(){
$('#btsubmit').click(function(e){
   e.preventDefault();
   /*Ajax Request Header setup*/
   if($('#start_date').val()=='' || $('#end_date').val()==''){
    
    alert('form start date / end date tidak boleh kosong');
   
    }else{
    
   
   $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
 
   
   /* Submit form data using ajax*/
   $.ajax({
      url: "{{ backpack_url('filter-report')}}",
      method: 'post',
      data: $('#form_report').serialize(),
      success: function(response){
         //------------------------
            $('#res_message').show();
            $('#res_message').html(response.msg);
            $('#msg_div').removeClass('d-none');
            console.log(response.name);
            $('#totalamount').replaceWith("<p id='totalamount'>"+response.totalamount+"</p>");
            $('#sponsor').replaceWith("<p id='sponsor'>"+response.newsponsor+"</p>");
            document.getElementById("form_report").reset(); 
            setTimeout(function(){
            $('#res_message').hide();
            $('#msg_div').hide();
            },10000);
         //--------------------------
      }});
    }
    });
   
});
</script>
@endpush