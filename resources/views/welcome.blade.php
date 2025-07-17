<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

</head>
<body>
    <div >
        @foreach ($orders as  $order)
            <p>Order ID: {{ $order->id }}</p>
            <p>Customer Name: {{ $order->receiever }}</p>
            <p>Order Status: {{ $order->order_status }}</p>
            <hr>
            <img src="{{ url('/barcode/' . $order->identifier) }}" alt="Barcode">
            <button onclick="window.print()">Print</button>

        @endforeach
    </div>
    <style media="print">
  button {
    display: none;
  }
</style>

</body>
</html>
