@extends('pdf')
@section('content-area')
<style>
@page {
  size: A4;
}
@page {
  size: A4;
  margin: 30px;
}
.pdf_container table{
    border-spacing:0;
    border:none;
}
.pdf_container table td{
    line-height: 25px;
    padding: 0 0 0 10px;
}
.pdf_container table td a{
    text-decoration: none;
    color:blue;
}
.pdf_container table th{
    line-height: 25px;
    padding: 0 0 0 10px;
}
.t_right{
    text-align: right;
}
.t_left{
    text-align: left;
}
.t_center{
    text-align: center;
}
.tb_none{
    border-top: none;
}
.bb_none{
    border-bottom: none;
}
.rb_none{
    border-right: none;
}
.lb_none{
    border-left: none;
}
.border_all{
    border: 0.01em solid darkgray;
    width:50%;
}
.tb_border{
    border-top: 1px solid darkgray;
    border-bottom: 1px solid darkgray;
}
</style>
<page size="A4">
@php

@endphp
<div class="pdf_container">
    <table border="2" style="width:100%; table-layout: fixed;">
        <tr>
            <td colspan="2" rowspan="3" class="tb_none bb_none rb_none lb_none"><img src="pdf hotel.png" alt="logo" style=" height:65px;"></td>
            <td colspan="2" class="t_right tb_none bb_none rb_none lb_none">
                <h2>Booking Number #{{$booking->booking_number  ?? ''}}</h2>
            </td>
        </tr>
        <tr>
            <td colspan="3" class="t_right tb_none bb_none rb_none lb_none">Booking Date: {{ date("d F y H:s:A", strtotime($booking->booking_date))  ?? ''}}</td>
        </tr>
        <tr>
            <td colspan="3" class="t_right tb_none bb_none rb_none lb_none">Payment Status: Unpaid</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left tb_none bb_none rb_none lb_none">{{$booking->Hotel->hotel_name  ?? '' }} </th>
            <th colspan="2" class="t_right tb_none bb_none rb_none lb_none">Guest Information</th>
        </tr>
        <tr>
            <td colspan="2" class="t_left tb_none bb_none rb_none lb_none">United States, America</td>
            <td colspan="2" class="t_right tb_none bb_none rb_none lb_none">{{ $booking->Customer->name ?? ''}}</td>
        </tr>
        <tr>
            <td colspan="2" class="t_left tb_none bb_none rb_none lb_none" style="vertical-align:top;">{{ !empty($booking->Customer->hotel_phone) ? 'Mobile: (Phone)' . $booking->Customer->hotel_phone  : '' }}</td>
            <td colspan="2" class="t_right tb_none bb_none rb_none lb_none" > {{ !empty($booking->Customer->addres) ? 'Address:' .  $booking->Customer->address  : '' }} </td>
        </tr>
        <tr>
            <td colspan="4" class="t_right tb_none bb_none rb_none lb_none"> {{ !empty($booking->Customer->phone) ? 'Mobile: (Phone)' . $booking->Customer->phone  : '' }}</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left tb_none bb_none rb_none lb_none">Email</th>
            <th colspan="2" class="t_right tb_none bb_none rb_none lb_none">Email</th>
        </tr>
        <tr>
            <td colspan="2" class="t_left tb_none bb_none rb_none lb_none"><a href="#">{{ !empty($booking->Hotel->hotel_email) ?  $booking->Hotel->hotel_email  : '' }}</a></td>
            <td colspan="2" class="t_right tb_none bb_none rb_none lb_none"><a href="#">{{ !empty($booking->Customer->email) ?  $booking->Customer->email  : '' }}</a></td>    
        </tr>
<!-- room details section 1st -->

        <tr>
            <th colspan="4" class="tb_none bb_none rb_none lb_none">Room Details</th>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all">Room Type</th>
            <td colspan="2" class="border_all">Twin Room</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Room No.</th>
            <td colspan="2" class="border_all">105</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Check In</th>
            <td colspan="2" class="border_all">2023-07-15 00:00:00</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Check Out</th>
            <td colspan="2" class="border_all">2023-07-15 00:00:00</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all">Booking Status</th>
            <td colspan="2" class="border_all">Pending</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Adults</th>
            <td colspan="2" class="border_all">2</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Number Of Rooms</th>
            <td colspan="2" class="border_all">1</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all">Nights</th>
            <td colspan="2" class="border_all">1</td>
        </tr>
        <tr>
            <td colspan="4" class="tb_none bb_none rb_none lb_none">&nbsp;</td>
        </tr>
        <tr>
            <th class="lb_none rb_none tb_border t_left">#</th>
            <th class="lb_none rb_none tb_border t_left">Date</th>
            <th class="lb_none rb_none tb_border t_left" colspan="2">Price</th>
        </tr>
        <tr>
            <td class="tb_border bb_none rb_none lb_none">1</td>
            <td class="tb_border bb_none rb_none lb_none">2023-07-15 </td>
            <td class="tb_border bb_none rb_none lb_none" colspan="2">2000.00</td>
        </tr>
        <tr>
            <td colspan="4" class="tb_none bb_none rb_none lb_none">&nbsp;</td>
        </tr>
<!-- room details section 2nd -->
<tr>
            <th colspan="4" class="tb_none bb_none rb_none lb_none">Room Details</th>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all">Room Type</th>
            <td colspan="2" class="border_all rb_border">Twin Room</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Room No.</th>
            <td colspan="2" class="border_all">105</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Check In</th>
            <td colspan="2" class="border_all">2023-07-15 00:00:00</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Check Out</th>
            <td colspan="2" class="border_all">2023-07-15 00:00:00</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all">Booking Status</th>
            <td colspan="2" class="border_all">Pending</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Adults</th>
            <td colspan="2" class="border_all">2</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all ">Number Of Rooms</th>
            <td colspan="2" class="border_all">1</td>
        </tr>
        <tr>
            <th colspan="2" class="t_left border_all">Nights</th>
            <td colspan="2" class="border_all">1</td>
        </tr>
        <tr>
            <td colspan="4" class="tb_none bb_none rb_none lb_none">&nbsp;</td>
        </tr>
        <tr>
            <th class="lb_none rb_none tb_border t_left">#</th>
            <th class="lb_none rb_none tb_border t_left">Date</th>
            <th class="lb_none rb_none tb_border t_left" colspan="2">Price</th>
        </tr>
        <tr>
            <td class="tb_border bb_none rb_none lb_none">1</td>
            <td class="tb_border bb_none rb_none lb_none">2023-07-15 </td>
            <td class="tb_border bb_none rb_none lb_none" colspan="2">2000.00</td>
        </tr>
        <tr>
            <td colspan="4" class="tb_none bb_none rb_none lb_none">&nbsp;</td>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">Sub - Total Amount : 2000</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">RST 3.00% : 60</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">GST 4.00% : 80</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">DST 4.00% : 80</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">MSL 3.50% : 70</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">Tax : 290</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">Grand Total : $2,290.00</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">Paid Amount : $0</th>
        </tr>
        <tr>
            <th colspan="4" class="t_right tb_none bb_none rb_none lb_none">Due Amount : $2290</th>
        </tr>
    </table>
</div>
</page>
@endsection