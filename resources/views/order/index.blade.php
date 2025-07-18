<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])
    <style media="print">
        button {
            display: none;
        }
        #back {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Order Details</h2>
        <p class="mb-2"><span class="font-semibold">Order ID:</span> {{ $order->order_id }}</p>
        <p class="mb-2"><span class="font-semibold">Branch:</span> {{ $order->user->shop }}</p>
        <p class="mb-2"><span class="font-semibold">Order Date:</span> 
            {{-- {{ $order->Timestamp }} --}}
            @php
                $time= \Carbon\Carbon::parse($order->Timestamp)->format('H:i:s');
                $date = \Carbon\Carbon::parse($order->Timestamp)->format('d-m-Y');
                echo $date ;
            @endphp
        </p>
        <p class="mb-2"><span class="font-semibold">Customer Name:</span> {{ $order->RecipientName }}</p>
        <hr class="mb-4">
        <div class="flex justify-center mb-4">
            <img src="{{ url('/barcode/' . $order->identifier) }}" alt="Barcode" class="h-24">
        </div>
        <div class="flex justify-between">
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow focus:outline-none focus:ring-2 focus:ring-blue-400">Print</button>
            <a id="back" href="{{ route('dashboard') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow focus:outline-none focus:ring-2 focus:ring-blue-400">back</a>
        </div>
    </div>
</body>
</html>
