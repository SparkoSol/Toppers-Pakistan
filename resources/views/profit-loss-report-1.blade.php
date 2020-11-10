<!DOCTYPE html>
<html lang="en">
    <head>
       <title>Toppers Profit & Loss Report</title>
    </head>
    <?php
        $gross = $sale - $creditNote - $purchase + $debitNote - $openingStock + $closingStock;
        $net = $gross - $expense;
    ?>
    <body>
        <div style="width:100%">
            <p style="text-align: center;"><img src="https://toppers-pakistan.web.app/_nuxt/img/ToppersPakistanLogo.31cd84b.png" alt="" width="178" height="130" /></p>
            <h2 style="text-align: center;"><strong>Profit Loss Report</strong></h2>
            <table style="width: 100%" cellspacing="0">
                <thead style="background-color: #efefef">
                  <tr>
                    <td style="background-color: #efefef">Particulars</td>
                    <td style="background-color: #efefef;width: 120px">Amount</td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Sale(+)</td>
                    <td style="color: #42b983">Rs. {{ $sale }}</td>
                  </tr>
                  <tr>
                    <td>Credit Note(-)</td>
                    <td style="color: #bc282b">Rs. {{ $creditNote }}</td>
                  </tr>
                  <tr>
                    <td>Purchase(-)</td>
                    <td style="color: #bc282b">Rs. {{ $purchase }}</td>
                  </tr>
                  <tr>
                    <td>Debit Note(+)</td>
                    <td style="color: #42b983">Rs. {{ $debitNote }}</td>
                  </tr>
                  <tr>
                    <td>Opening Stock(-)</td>
                    <td style="color: #bc282b">Rs. {{ $openingStock }}</td>
                  </tr>
                  <tr>
                    <td>Closing Stock(+)</td>
                    <td style="color: #42b983">Rs. {{ $closingStock }}</td>
                  </tr>
                  <tr>
                    <td
                      style="border-bottom: 1px solid black;border-top: 1px solid black;"
                    >
                      Gross Profit
                    </td>
                    <td
                      style="border-bottom: 1px solid black;border-top: 1px solid black;color: #42b983"
                    >
                      Rs. {{ $gross }}
                    </td>
                  </tr>
                  <tr>
                    <td>Expenses(-)</td>
                    <td style="color: #bc282b">Rs. {{ $expense }}</td>
                  </tr>
                  <tr>
                    <td
                      style="border-bottom: 1px solid black;border-top: 1px solid black;"
                    >
                      Net Profit
                    </td>
                    <td
                      style="border-bottom: 1px solid black;border-top: 1px solid black;color: #42b983"
                    >
                      Rs. {{ $net }}
                    </td>
                  </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>

<style>
td {
  padding: 10px;
}
</style>
