@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Возврат билета</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #d9534f;
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            justify-content: space-between;
        }

        .column {
            width: 48%;
        }

        p {
            margin: 8px 0;
            font-size: 14px;
        }

        .label {
            font-weight: bold;
            color: #333;
        }

        .passenger-info {
            background: #e9f7ef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .refund-amount {
            background: #ffd700;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }

        .comment-section {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
            font-style: italic;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Возврат билета</h2>

        <div class="info">
            <div class="row">
                <div class="column">
                    <p><span class="label">Заказ:</span> {{ $ticket->order->id }}</p>
                    <p><span class="label">Номер билета:</span> {{ $ticket->id }}</p>
                </div>
                <div class="column">
                    <p><span class="label">Заказ (в БИЛЕТАВТО):</span> {{ $ticket->order->ba_operation_id }}</p>
                    <p><span class="label">Номер билета (в БИЛЕТАВТО):</span> {{ $ticket->ba_ticket_id }}</p>
                </div>
            </div>

            <p><span class="label">Маршрут:</span> {{ $ticket->route_name }}</p>
            <p><span class="label">Рейс:</span> {{ $ticket->departure_station }} → {{ $ticket->arrival_station }}</p>
            <p><span class="label">Дата отправления:</span> {{ Carbon::parse($ticket->departure_date)->format('d.m.Y') }}
                в {{ Carbon::parse($ticket->departure_time)->format('H:i') }}</p>
            <p><span class="label">Место пассажира:</span> {{ $ticket->place }}</p>
            <p><span class="label">Дата покупки:</span> {{ $ticket->created_at->format('d.m.Y в H:i') }}</p>
            <p><span class="label">Дата возврата:</span> {{ $ticket->updated_at->format('d.m.Y в H:i') }}</p>
        </div>

        <div class="passenger-info">
            <h3>Информация о пассажире</h3>
            <p><span class="label">ФИО:</span> {{ $ticket->passenger_name }}</p>
            <p><span class="label">Телефон:</span> {{ $ticket->passenger_phone }}</p>
            <p><span class="label">Почта:</span> {{ $ticket->user->email }}</p>
        </div>

        <div class="refund-amount">
            Сумма к возврату: {{ $refundAmount }} руб.
        </div>

        @if ($comment)
            <div class="comment-section">
                <p><span class="label">Комментарий:</span> {{ $comment }}</p>
            </div>
        @endif

        <div class="footer">
            Это письмо отправлено автоматически. Пожалуйста, не отвечайте на него.
        </div>
    </div>
</body>
</html>
