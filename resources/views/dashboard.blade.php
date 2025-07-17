<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name') }}</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Booked Orders Dashboard</h1>
        @if($orders->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-8 text-center text-gray-500 text-lg">
                No orders found.
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Recipient</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-left">Barcode</th>
                        <th class="py-3 px-6 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @foreach ($orders as $order)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                            <td class="py-3 px-6">{{ $order->id }}</td>
                            <td class="py-3 px-6">{{ $order->RecipientName ?? $order->receiever ?? '-' }}</td>
                            <td class="py-3 px-6">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ $order->order_status }}
                                </span>
                            </td>
                            <td class="py-3 px-6">
                                <img src="{{ url('/barcode/' . $order->identifier) }}" alt="Barcode" class="h-10 inline-block">
                            </td>
                            <td class="py-3 px-6 flex gap-2">
                                <a href="{{ route('detail', ['id' => $order->id]) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded shadow focus:outline-none focus:ring-2 focus:ring-green-400">Print</a>
                                <form action="{{ route('handover',['id' => $order->id]) }}" method="POST" style="display:inline;" onsubmit="return confirmHandover(event)">
                                    @csrf
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-1 px-3 rounded shadow focus:outline-none focus:ring-2 focus:ring-indigo-400">Delivery to EPS</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    <script>
        function printOrder(orderId) {
            // Print only the row for the selected order
            // For now, just print the whole page
            window.print();
        }
        function confirmHandover(event) {
            if (!confirm('Are you sure you want to hand this order over to EPS?')) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
    <style media="print">
        button, form button {
            display: none;
        }
    </style>
</body>
</html>
