@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Возврат билета</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        h2 {
            color: #d9534f;
        }

        .ticket-info {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .highlight {
            font-weight: bold;
            color: #d9534f;
            font-size: 18px;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #777;
        }

        a {
            color: #0275d8;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Уважаемый клиент!</h2>
    <p>Зарегистрирована заявка на возврат билета.</p>

    <div class="ticket-info">
        <div><strong>Возврат билета №:</strong> {{ $ticket->ba_ticket_id }}</div>
        <div><strong>Заказ:</strong> {{ $ticket->order->ba_operation_id }}</div>
    </div>

    <div class="ticket-info">
        <div><strong>Тип билета:</strong> Интернет</div>
        <div><strong>Маршрут:</strong> {{ $ticket->route_name }}</div>
    </div>

    <div class="ticket-info">
        <div><strong>Рейс:</strong> {{ $ticket->departure_station }} → {{ $ticket->arrival_station }}</div>
        <div><strong>Дата отправления:</strong> {{ Carbon::parse($ticket->departure_date)->format('d.m.Y') }}
            в {{ Carbon::parse($ticket->departure_time)->format('H:i') }}</div>
    </div>

    <div class="ticket-info">
        <div><strong>Место в автобусе:</strong> {{ $ticket->place }}</div>
        <div><strong>ФИО пассажира:</strong> {{ $ticket->passenger_name }}</div>
    </div>

    <div class="ticket-info">
        <div><strong>Дата покупки:</strong> {{ $ticket->created_at->format('d.m.Y в H:i') }}</div>
        <div><strong>Дата возврата:</strong> {{ $ticket->updated_at->format('d.m.Y в H:i') }}</div>
    </div>

    <p class="highlight">Ваш билет более недействителен.</p>

    <p>Если вы не создавали заявку на возврат билета или у вас остались вопросы, вы можете связаться с нами по телефону
        <strong>+7(3012) 26-80-03</strong> (с 04:00 до 13:00 ч. по МСК в будни) или написать на <a
            href="mailto:autovokzal@biletavto.ru">info@biletavto.ru</a>.</p>

    <p class="footer">Обратите внимание, что возврат денежных средств осуществляется в среднем за 2-5 рабочих дней и
        зависит от скорости обработки заявки банком.</p>
</div>
</body>
</html>
