    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard - {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    </head>

    <body class="bg-gray-100 min-h-screen">
        <div class="container mx-auto py-8">
            <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Booked Orders Dashboard</h1>
            <div class="flex justify-end mb-4">
                <form action="{{ route('logout') }}" method="POST"
                    onsubmit="return confirm('Are you sure you want to logout?')">
                    @csrf
                    <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded shadow">Logout</button>
                </form>
            </div>
            <div id="order-table-container">
                @if ($orders->isEmpty())
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
                            <tbody class="text-gray-600" id="orders-body">
                                @foreach ($orders as $order)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        <td class="py-3 px-6">{{ $order->id }}</td>
                                        <td class="py-3 px-6">{{ $order->RecipientName ?? ($order->receiever ?? '-') }}</td>
                                        <td class="py-3 px-6">
                                            <span
                                                class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                {{ $order->order_status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6">
                                            <img src="{{ url('/barcode/' . $order->identifier) }}" alt="Barcode"
                                                class="h-10 inline-block">
                                        </td>
                                        <td class="py-3 px-6 flex gap-2">
                                            <a href="{{ route('detail', ['id' => $order->id]) }}"
                                                class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded shadow">Print</a>
                                            <form action="{{ route('handover', ['id' => $order->id]) }}" method="POST">
                                                {{-- onsubmit="return ajaxHandover(event, this, {{ $order->id }})"> --}}
                                                @csrf
                                                <button type="submit"
                                                    class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-1 px-3 rounded shadow">Delivery
                                                    to EPS</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div id="alert-container" class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50"></div>

        <script>
            // async function ajaxHandover(event, form, orderId) {
            //     event.preventDefault();
            //     if (!confirm('Are you sure you want to hand this order over to EPS?')) return false;

            //     const url = form.action;
            //     const token = form.querySelector('input[name="_token"]').value;

            //     try {
            //         const response = await fetch(url, {
            //             method: 'POST',
            //             headers: {
            //                 'X-CSRF-TOKEN': token,
            //                 'Accept': 'application/json',
            //                 'X-Requested-With': 'XMLHttpRequest',
            //             }
            //         });

            //         if (response.ok) {
            //             form.closest('tr').remove();
            //             showAlert('Order handed over to EPS successfully.', 'success');

            //             if (document.querySelectorAll('tbody tr').length === 0) {
            //                 document.querySelector('#order-table-container').innerHTML = `
            //                     <div class="bg-white rounded-lg shadow-md p-8 text-center text-gray-500 text-lg">
            //                         No orders found.
            //                     </div>`;
            //             }
            //         } else {
            //             showAlert('Failed to hand over order.', 'error');
            //         }
            //     } catch {
            //         showAlert('Network error occurred.', 'error');
            //     }
            // }

            function showAlert(message, type) {
                const alertContainer = document.getElementById('alert-container');
                const alertDiv = document.createElement('div');
                alertDiv.className =
                    `mb-4 px-6 py-3 rounded shadow text-white font-semibold ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
                alertDiv.textContent = message;
                alertContainer.appendChild(alertDiv);
                setTimeout(() => alertDiv.remove(), 2500);
            }

            // Auto-refresh every 3 seconds
            setInterval(fetchNewOrders, 3000);

            async function fetchNewOrders() {
                try {
                    const response = await fetch('{{ route('orders.fetch') }}');
                    const orders = await response.json();
                    const tbody = document.getElementById('orders-body');

                    if (!orders.length) {
                        document.querySelector('#order-table-container').innerHTML = `
                            <div class="bg-white rounded-lg shadow-md p-8 text-center text-gray-500 text-lg">
                                No orders found.
                            </div>`;
                        return;
                    }

                    tbody.innerHTML = '';

                    orders.forEach(order => {
                        const row = document.createElement('tr');
                        row.className = 'border-b border-gray-200 hover:bg-gray-50 transition';
                        row.innerHTML = `
                            <td class="py-3 px-6">${order.id}</td>
                            <td class="py-3 px-6">${order.RecipientName ?? order.receiever ?? '-'}</td>
                            <td class="py-3 px-6">
                                <span class="inline-block px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">${order.order_status}</span>
                            </td>
                            <td class="py-3 px-6">
                                <img src="/barcode/${order.identifier}" class="h-10 inline-block" alt="Barcode">
                            </td>
                            <td class="py-3 px-6 flex gap-2">
                                <a href="/dashboard/${order.id}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-3 rounded shadow">Print</a>
                                <form action="/handover/${order.id}" method="POST" onsubmit="return ajaxHandover(event, this, ${order.id})">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-1 px-3 rounded shadow">Delivery to EPS</button>
                                </form>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } catch (e) {
                    console.error('Failed to fetch new orders:', e);
                }
            }
        </script>

        <style media="print">
            button,
            form button {
                display: none;
            }
        </style>
    </body>

    </html>
