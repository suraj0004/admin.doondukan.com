<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td {
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .final-total {
            text-align: center;
            font-size: 25px;
            background-color: #2196F3;
            color: white;
            margin-top: 40px !important;
        }

        .final-total td {
            padding: 20px !important;
            border: 5px solid black;
        }

        .title img {
            border-radius: 100%
        }

        .header {
            border-bottom: 1px solid black;
        }

        .badge-quantity {
            background-color: #2196F3 !important,
                color: white;
            border-radius: 10px;
        }

    </style>
</head>

<body>
    @php
        $data = json_decode($data);
    @endphp
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="3">
                    <table class="header">
                        <tr>
                            <td class="title">
                                <img src="{{ $data->store->logo }}" height="100" width="100" />
                            </td>

                            <td class="text-left">
                                <strong>Shop:</strong> {{ $data->store->shop_name }}<br />
                                <strong>Shop Owner:</strong> {{ $data->store->seller_name }} (
                                {{ $data->store->mobile }} )<br />
                                <strong>Shop Address:</strong> {{ $data->store->address }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="3">
                    <table>
                        <tr>
                            <td>
                                <strong>Order No:</strong> #{{ $data->order_no }} <br />
                                <strong>Name:</strong> {{ $data->buyer->name }} <br />
                                <strong>Phone:</strong> {{ $data->buyer->phone }} <br />
                            </td>
                            <td class="text-left">
                                <strong>Date:</strong> {{ date('M d, Y', strtotime($data->created_at)) }} <br />
                                {{-- <strong>Delivery Type:</strong> Home-Delivery | User Self Collected<br />
                                <strong>Address:</strong> Rajpur road <br />
                                <strong>Timing:</strong> 10:00 AM - 11:00 AM <br /> --}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>


            <tr class="heading">
                <td>Item</td>
                <td class="text-left">Quantity</td>
                <td class="text-right">Price</td>
            </tr>

            @php
                $total = 0;
            @endphp
            @foreach ($data->items as $item)
                @php
                    $total += $item->price * $item->quantity;
                @endphp
                <tr class="item">
                    <td>
                        <img src="{{ $item->product->thumbnail }}" height="100" width="100" />
                    </td>
                    <td class="text-left">
                        {{ $item->product->name }}<br>
                        {{ $item->product->weight }}<br>
                        <span
                            style="background:#2196F3; color: white; border-radius:10px; text-align: center; padding-left:3px;padding-right:3px;">
                            {{ $item->quantity }} </span> X Rs.
                        {{ formatIndianCurrency($item->price) }}<br>
                    </td>
                    <td class="text-right"> Rs.
                        {{ formatIndianCurrency($item->price * $item->quantity) }}
                    </td>
                </tr>
            @endforeach




            <tr class="total">
                <td colspan="2" class="text-right">Item Total:</td>
                <td> Rs. {{ formatIndianCurrency($total) }}</td>
            </tr>
            {{-- <tr class="total">
                <td colspan="2" class="text-right">Delivery:</td>
                <td> Rs. 0</td>
            </tr> --}}

            <tr class="final-total">
                <td colspan="3">Total Amount: <strong>Rs.
                        {{ formatIndianCurrency($total) }}</strong> /-
                </td>
            </tr>

        </table>
    </div>
</body>

</html>
