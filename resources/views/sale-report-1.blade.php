<!DOCTYPE html>
<html lang="en">
    <head>
       <title>Apna Store Sale Report</title>
    </head>
    <?php
//        $date=date_create($saleOrder['invoice_date']);
        $total = 0;
        for ($x = 0; $x < count($sales); $x++) {
            $total += $sales[$x]['amount'];
        }
     ?>
    <body>
        <div style="width:100%">
            @if ($branch > 0)
                <p style="text-align: center;"><img src="./images/branch/{{ $sales[0]['branch']['image'] }}" alt="" width="178" height="130" /></p>
            @else
                <p style="text-align: center;"><img src="./images/ApnaPos.jpeg" alt="" width="178" height="130" /></p>
            @endif
            <h2 style="text-align: center;"><strong>Sale Report</strong></h2>
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
                <tr>
                    <td style="color:white;text-align: center;">D</td>
                    <td style="color:white;text-align: center;">I</td>
                    <td style="color:white;text-align: center;">P</td>
                    <td style="color:white;text-align: center;">B</td>
                    <td style="color:white;text-align: center;">O</td>
                    <td style="color:white;text-align: center;">P</td>
                    <td style="color:white;text-align: center;">T</td>
                    <td style="color:white;text-align: center;">D</td>
                    <td style="color:white;text-align: center;">B</td>
                    <td style="color:white;text-align: center;">S</td>
                </tr>
                 @for ($i = 0; $i < count($sales); $i++)
                 <tr style="text-align:center;border-bottom:1px solid rgba(0,0,0,0.4);">
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ date_format(date_create($sales[$i]['invoice_date']),"Y/m/d") }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $sales[$i]['invoice_id'] }}</td>
                    @if($sales[$i]['customer'] !== null)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $sales[$i]['customer']['name'] }}</td>
                    @else
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)"></td>
                    @endif
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $sales[$i]['branch']['name'] }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $sales[$i]['origin'] }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">{{ $sales[$i]['payment_type'] }}</td>
                    <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs {{ $sales[$i]['amount'] }}</td>
                    @if($sales[$i]['discount'] !== null)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs {{ $sales[$i]['discount'] }}</td>
                    @else
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs 0</td>
                    @endif
                    @if($sales[$i]['balance_due'] !== null)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs {{ $sales[$i]['balance_due'] }}</td>
                    @else
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Rs 0</td>
                    @endif
                    @if($sales[$i]['balance_due'] == null || $sales[$i]['balance_due'] == 0)
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Paid</td>
                    @elseif($sales[$i]['amount'] === $sales[$i]['balance_due'])
                        <td style="text-align: center;border-bottom:1px solid rgba(0,0,0,0.4)">Unpaid</td>
                    @elseif($sales[$i]['amount'] != $sales[$i]['balance_due'] && $sales[$i]['balance_due'] > 0)
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
