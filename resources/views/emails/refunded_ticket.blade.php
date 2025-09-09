<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Проведен возврат билета</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif;">
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f4f4;">
    <tr>
        <td align="center" style="padding: 20px;">
            <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <tr>
                    <td style="padding: 30px; background-color: #4d0099 ; color: white; text-align: center; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                        <h1 style="margin: 0; font-size: 24px; color: white;">Проведен возврат билета</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #eddefa; border-radius: 6px; margin-bottom: 20px;">
                            <tr>
                                <td style="padding: 15px; border-left: 4px solid #310062;">
                                    <h2 style="margin: 0 0 10px 0; color: #2c3e50; font-size: 18px; border-bottom: 2px solid #28a745; padding-bottom: 5px;">Информация по проведенному возврату</h2>
                                    <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                        <strong style="color: #495057;">Провел(а) администратор:</strong> {{ $username ?? "Администратор"}}
                                    </p>
                                    <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                        <strong style="color: #495057;">Дата проведения возврата:</strong> {{ $ticket->updated_at->format('d.m.Y в H:i') ?? "ошибка получения даты возврата" }}
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f9f9f9; border-radius: 6px; margin-bottom: 20px;">
                            <tr>
                                <td style="padding: 15px;">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td width="100%" style="vertical-align: top;">
                                                <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                                    <strong style="color: #495057;">Заказ:</strong> {{ $ticket->order->ba_operation_id }}
                                                </p>
                                                <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                                    <strong style="color: #495057;">Номер билета:</strong> {{ $ticket->ba_ticket_id }}
                                                </p>
                                                <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                                    <strong style="color: #495057;">Маршрут:</strong> {{ $ticket->route_name }}
                                                </p>
                                                <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                                    <strong style="color: #495057;">Рейс:</strong> {{ $ticket->departure_station }} → {{ $ticket->arrival_station }}
                                                </p>
                                                <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                                    <strong style="color: #495057;">Дата отправления:</strong> {{ \Carbon\Carbon::parse($ticket->departure_date)->format('d.m.Y') }} в {{ \Carbon\Carbon::parse($ticket->departure_time)->format('H:i') }}
                                                </p>
                                                <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                                    <strong style="color: #495057;">Место пассажира:</strong> {{ $ticket->place }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>

                                    <div style="margin-top: 10px;">
                                        <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                            <strong style="color: #495057;">Дата покупки:</strong> {{ $ticket->created_at->format('d.m.Y в H:i') }}
                                        </p>
                                        <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                            <strong style="color: #495057;">Дата возврата:</strong> {{ $ticket->updated_at->format('d.m.Y в H:i') }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #e9f7ef; border-radius: 6px; margin-bottom: 20px;">
                            <tr>
                                <td style="padding: 15px; border-left: 4px solid #28a745;">
                                    <h2 style="margin: 0 0 10px 0; color: #2c3e50; font-size: 18px; border-bottom: 2px solid #d9534f; padding-bottom: 5px;">Информация о пассажире</h2>
                                    <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                        <strong style="color: #495057;">ФИО:</strong> {{ $name ?? null}}
                                    </p>
                                    <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                        <strong style="color: #495057;">Телефон пассажира:</strong> {{ $phone ?? "ошибка получения телефона пассажира" }}
                                    </p>
                                    <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                        <strong style="color: #495057;">Почта пассажира:</strong> {{ $email ?? "ошибка получения почты пассажира" }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f3f5; border-radius: 6px; margin-bottom: 20px;">
                            <tr>
                                <td style="padding: 15px; border-left: 4px solid #28a745;">
                                    <p style="margin: 0; color: #333333; font-size: 14px; font-style: italic;">
{{--                                        @if($avrokassa)--}}
{{--                                            <strong style="color: #495057;">Автокасса, обслуживающая рейс: {{ $avrokassa }}</strong>--}}
{{--                                        @endif--}}
                                        <strong style="color: #495057;">Причина возврата:</strong> {{ $reason ?? "другое" }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #eddefa; border-radius: 6px; margin-bottom: 20px;">
                            <tr>
                                <td style="padding: 15px; border-left: 4px solid #310062;">
                                    <p style="margin: 5px 0; color: #333333; font-size: 14px;">
                                        <strong style="color: #495057;">Сумма возврата:</strong> {{ $refundAmount ?? "ошибка получения суммы возврата" }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        @if ($comment)
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f3f5; border-radius: 6px; margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 15px; border-left: 4px solid #310062;">
                                        <h2 style="margin: 0 0 10px 0; color: #2c3e50; font-size: 18px; border-bottom: 2px solid #d9534f; padding-bottom: 5px;">Комментарий</h2>
                                        <p style="margin: 0; color: #333333; font-size: 14px; font-style: italic;">
                                            {{ $comment }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td style="text-align: center; color: #6c757d; font-size: 12px; padding-top: 15px; border-top: 1px solid #e9ecef;">
                                    <p style="margin: 5px 0;">Это письмо отправлено автоматически. Пожалуйста, не отвечайте на него.</p>
                                    <p style="margin: 5px 0;">При возникновении вопросов свяжитесь с нашей службой поддержки.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
