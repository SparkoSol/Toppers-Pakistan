<!DOCTYPE html>
<html lang="en">
    <head>
       <title>Apna Store Invoice</title>
    </head>
     <?php
        $date=date_create($saleOrder['invoice_date']);
        $price = 0;
        for ($x = 0; $x < count($items); $x++) {
            $price += ($items[$x]['qty'] * $items[$x]['price']);
        }
     ?>
    <body>
        <div class="ticket">
            <div class="centered">
                <img style="width:100px; height: 80px" src="./images/branch/{{ $saleOrder['branch']['image'] }}" alt="Logo">
            </div>
            <p class="centered">
                Order Receipt # {{ $saleOrder['invoice_id'] }}
                <br>{{ $saleOrder['branch']['name'] }}
                <br>{{ $saleOrder['branch']['address'] }}
            </p>
            <div>
                @if($saleOrder['customer'] !== null)
                    <p>Customer Name: {{ $saleOrder['customer']['name'] }}</p>
                @endif
                @if($saleOrder['address'] !== null)
                    <p>Customer Contact: {{ $saleOrder['address']['mobile'] }}</p>
                    <p>
                      Customer Address:
                       {{ $saleOrder['address']['description'] ? $saleOrder['address']['description'].',' : ''  }}
                       {{ $saleOrder['address']['house'] ? $saleOrder['address']['house'].',' : '' }}
                       {{ $saleOrder['address']['street'] ? $saleOrder['address']['street'].',' : '' }}
                       {{ $saleOrder['address']['area'] ? $saleOrder['address']['area'].',' : '' }}
                       {{ $saleOrder['address']['city'] ? $saleOrder['address']['city'].'' : '' }}
                    </p>
                @endif
            </div>
            <p class="text-center"><strong>Date - Time:</strong>{{Date("Y/m/d h:i a", strtotime($saleOrder['created_at']))}}</p>
            <table style="width:100%" class="table">
                 <tr style="text-align:center;">
                     <td style="font-weight:700;">Sr#</td>
                     <td style="font-weight:700;">Product Name</td>
                     <td style="font-weight:700;">Quantity</td>
                     <td style="font-weight:700;">Price</td>
                 </tr>
                 @for ($i = 0; $i < count($items); $i++)
                 <tr style="text-align:center;">
                     <td>{{$i+1}}</td>
                     <td>
                         @if($items[$i]['variant'] !== null)
                            <p style="margin: 0">{{ $items[$i]['product']['name'] }} - {{ $items[$i]['variant']['name'] }}</p>
                         @else
                            <p style="margin: 0">{{ $items[$i]['product']['name'] }}</p>
                         @endif
                     </td>
                     <td>{{ $items[$i]['qty'] }}</td>
                     <td>{{ $items[$i]['price'] }}</td>
                 </tr>
                @endfor
                 <tr style="border-collapse:collapse">
                    <td style="border-top:none"></td>
                    <td style="border-top:none"></td>
                    <td style="border-top:none"><h2 style="color:#333333">Subtotal:</h2></td>
                    <td style="border-top:none;">Rs. {{ $price }}</td>
                 </tr>
                 <tr style="border-collapse:collapse">
                    <td style="border-top:none"></td>
                    <td style="border-top:none"></td>
                    <td style="border-top:none"><h2 style="color:#333333">Delivery Charges:</h2></td>
                    <td style="border-top:none;">
                        @if($saleOrder['delivery'] > 0)
                            Rs. {{ $saleOrder['delivery_fee'] }}
                        @else
                            Rs. 0.0
                        @endif
                    </td>
                 </tr>
                 <tr style="border-collapse:collapse">
                    <td style="border-top:none"></td>
                    <td style="border-top:none"></td>
                    <td style="border-top:none"><h2 style="color:#333333">Discount:</h2></td>
                    <td style="border-top:none;">
                        @if($saleOrder['discount'] !== null)
                            Rs. {{ $saleOrder['discount'] }}
                        @else
                            Rs. 0.0
                        @endif
                    </td>
                 </tr>
                 <tr style="border-collapse:collapse">
                    <td style="border-top:none"></td>
                    <td style="border-top:none"></td>
                    <td style="border-top:none"><h2 style="color:#333333">Order Total:</h2></td>
                    <td style="border-top:none;">Rs. {{ $saleOrder['amount'] }}</td>
                 </tr>
             </table>
            <p class="text-center"><strong>Print Date - Time:</strong>{{ Date("Y/m/d h:i a") }}</p>
            <p class="centered">Thanks for your purchase!</p>
            <p class="centered">Designed & Maintained By Sparko Sol!</p>
                    </div>
    </body>
</html>

<style>
* {
    font-size: 12px;
    font-family: 'Times New Roman';
    margin:5px;
}

th,
tr,
table {
    padding: 5px 0;
    border-top: 1px solid rgba(0,0,0,0.4);
    border-collapse: collapse;
    width: 100%;
}
td {
    border-top: 1px solid rgba(0,0,0,0.4);
    border-collapse: collapse;
    padding: 5px 0;
}

td.name,
th.name {
    width: 75px;
    max-width: 75px;
    text-align: center;
}

td.quantity,
th.quantity {
    width: 40px;
    max-width: 40px;
    word-break: break-all;
    text-align: center;
}

td.price,
th.price {
    width: 40px;
    max-width: 40px;
    word-break: break-all;
    text-align: center;
}

.centered {
    text-align: center;
    align-content: center;
}

.ticket {
    width: 100%;
    margin: 0 auto;
}
.pageBreak {
    page-break-inside: avoid;
}
</style>
