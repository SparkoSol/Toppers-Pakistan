
<?php  
if($data['delivery']) {
    $delivery = 50.0;
}  
else{
    $delivery = 0.0;
}
?>

<p>Hi, {{$data['name']}}</p>
<p>Your Order is placed at {{$data['branch']}}.</p>
<p>Your food will be delivered soon.</p>
<p>We appreciate you ordering food from Toppers Pakistan.</p>
<p>Have a nice day.</p>
<table style="width:100%" class="table">
    <tr>
        <th>Sr#</th>
        <th>Product Name</th>
        <th>Quantity</th>
    </tr>
    @for ($i = 0; $i < count($data['items']); $i++)
    <tr>
        <th>{{$i+1}}</th>
        <th>{{$data['items'][$i]->product->name}}</th>
        <th>{{$data['items'][$i]->quantity}}</th>
    </tr>
    @endfor
</table>

<table style="width:50%" class="table">
    <tr>
        <th>Sub Total</th>
        <td>Rs. {{$data['price'] - $delivery}}</td>
    </tr>
    <tr>
        <th>Delivery Charges</th>
        <td>Rs. {{$delivery}}</td>
    </tr>
    <tr>
        <th>Tax</th>
        <td>Rs. 0.0</td>
    </tr>
    <tr>
        <th>Total</th>
        <td>Rs. {{$data['price']}}</td>
    </tr>
</table>