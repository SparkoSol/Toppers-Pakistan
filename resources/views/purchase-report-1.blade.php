<!DOCTYPE html>
<html lang="en">
    <head>
       <title>Toppers Purchase Report</title>
    </head>
    <?php
        $total = 0;
        for ($x = 0; $x < count($purchases); $x++) {
            $total += $purchases[$x]['amount'];
        }
     ?>
    <body>
        <div style="width:100%">
            <p style="text-align: center;"><img src="https://toppers-pakistan.web.app/_nuxt/img/ToppersPakistanLogo.31cd84b.png" alt="" width="178" height="130" /></p>
            <h2 style="text-align: center;"><strong>Purchase Report</strong></h2>
            @if($from !== null && $to !== null)
                <p><strong>Duration: From {{ $from }} to {{ $to }}</strong></p>
            @endif
            <table style="height: 90px; margin-left: auto; margin-right: auto;width:100%;" cellspacing=0>
            <tbody>
                <tr>
                    <td style="background-color: #efefef; text-align: center;">DATE</td>
                    <td style="background-color: #efefef; text-align: center;">INVOICE N0.</td>
                    <td style="background-color: #efefef; text-align: center;">PARTY NAME</td>
                    <td style="background-color: #efefef; text-align: center;">Branch</td>
                    <td style="background-color: #efefef; text-align: center;">Origin</td>
                    <td style="background-color: #efefef; text-align: center;">Payment Type</td>
                    <td style="background-color: #efefef; text-align: center;">Total</td>
                    <td style="background-color: #efefef; text-align: center;">Discount</td>
                    <td style="background-color: #efefef; text-align: center;">Balance</td>
                    <td style="background-color: #efefef; text-align: center;">Status</td>
                </tr>
                 @for ($i = 0; $i < count($purchases); $i++)
                 <tr style="text-align:center;border-bottom:1px solid rgba(0,0,0,0.4);">
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ date_format(date_create($purchases[$i]['invoice_date']),"Y/m/d") }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $purchases[$i]['invoice_id'] }}</td>
                    @if($purchases[$i]['supplier'] !== null)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $purchases[$i]['supplier']['name'] }}</td>
                    @else
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)"></td>
                    @endif
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $purchases[$i]['branch']['name'] }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $purchases[$i]['origin'] }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $purchases[$i]['payment_type'] }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs {{ $purchases[$i]['amount'] }}</td>
                    @if($purchases[$i]['discount'] !== null)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs {{ $purchases[$i]['discount'] }}</td>
                    @else
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs 0</td>
                    @endif
                    @if($purchases[$i]['balance_due'] !== null)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs {{ $purchases[$i]['balance_due'] }}</td>
                    @else
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs 0</td>
                    @endif
                    @if($purchases[$i]['balance_due'] == null || $purchases[$i]['balance_due'] == 0)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Paid</td>
                    @elseif($purchases[$i]['amount'] === $purchases[$i]['balance_due'])
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Unpaid</td>
                    @elseif($purchases[$i]['amount'] != $purchases[$i]['balance_due'] && $purchases[$i]['balance_due'] > 0)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Partial</td>
                    @endif
                 </tr>
                @endfor
            </tbody>
            </table>
            <p style="text-align: right;"><strong>Total Sale: Rs. {{ $total }}</strong></p>
        </div>
    </body>
</html>
