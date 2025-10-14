<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; color: #555; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        .logo { width: 100%; max-width: 300px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .hotel-info { text-align: right; }
        .status-badge { padding: 5px 10px; border-radius: 5px; font-weight: bold; }
        .status-paid { background-color: #4CAF50; color: white; }
        .status-unpaid { background-color: #f44336; color: white; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <p>Number: <strong>{{ $invoice->invoice_number }}</strong></p>
            </div>
            <div class="hotel-info">
                <h2>Grand Hotel</h2>
                <p>123 Luxury Avenue</p>
                <p>City, Country 12345</p>
                <p>Phone: +1 (234) 567-8900</p>
                <p>Email: info@grandhotel.com</p>
            </div>
        </div>
        
        <table>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Bill To:</strong><br>
                                {{ $invoice->payment->booking->guest->name }}<br>
                                {{ $invoice->payment->booking->guest->email }}<br>
                                {{ $invoice->payment->booking->guest->phone ?? 'N/A' }}
                            </td>
                            <td>
                                <strong>Invoice Details:</strong><br>
                                Issue Date: {{ $invoice->issue_date }}<br>
                                Due Date: {{ $invoice->due_date }}<br>
                                Status: <span class="status-badge status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        
        <table>
            <tr class="heading">
                <td>Description</td>
                <td>Amount</td>
            </tr>
            
            <tr class="item">
                <td>
                    Room {{ $invoice->payment->booking->room->room_number }} - 
                    {{ $invoice->payment->booking->room->type }} Room<br>
                    Check-in: {{ $invoice->payment->booking->check_in_date }}<br>
                    Check-out: {{ $invoice->payment->booking->check_out_date }}<br>
                    Nights: {{ \Carbon\Carbon::parse($invoice->payment->booking->check_in_date)
                        ->diffInDays(\Carbon\Carbon::parse($invoice->payment->booking->check_out_date)) }}
                </td>
                <td>${{ number_format($invoice->payment->booking->total_amount, 2) }}</td>
            </tr>
            
            @if($invoice->payment->booking->additional_services)
            <tr class="item">
                <td>
                    Additional Services:<br>
                    @foreach(json_decode($invoice->payment->booking->additional_services, true) as $service)
                    • {{ $service['name'] }}: ${{ number_format($service['price'], 2) }}<br>
                    @endforeach
                </td>
                <td>${{ number_format(collect(json_decode($invoice->payment->booking->additional_services, true))->sum('price'), 2) }}</td>
            </tr>
            @endif
            
            <tr class="total">
                <td></td>
                <td>Total: ${{ number_format($invoice->amount, 2) }}</td>
            </tr>
        </table>
        
        <table>
            <tr>
                <td>
                    <strong>Payment Method:</strong><br>
                    {{ ucfirst(str_replace('_', ' ', $invoice->payment->payment_method)) }}<br>
                    @if($invoice->payment->transaction_id)
                    Transaction ID: {{ $invoice->payment->transaction_id }}<br>
                    @endif
                    Payment Date: {{ $invoice->payment->payment_date }}
                </td>
            </tr>
        </table>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <p>Thank you for choosing Grand Hotel. We look forward to serving you again!</p>
            <p>For questions regarding this invoice, please contact our billing department at billing@grandhotel.com</p>
        </div>
    </div>
</body>
</html>