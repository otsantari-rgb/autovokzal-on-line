<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\PdfGenerator;
use App\Services\RefundService;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private RefundService $refundService;
    protected PdfGenerator $pdfGenerator;

    public function __construct(PdfGenerator $pdfGenerator, RefundService $refundService)
    {
        $this->pdfGenerator = $pdfGenerator;
        $this->refundService = $refundService;
    }

    public function generatePdf($uuid): Application|Response|JsonResponse|ResponseFactory
    {
        $ticket = Ticket::where('uuid', $uuid)->firstOrFail();
        $order = $ticket->order()->first();
        $tickets = $order->tickets()->where('status', 'confirmed')->get();
        $ticketUrls = $tickets->pluck('ticket_url')->toArray();

        if (empty(array_filter($ticketUrls))) {
            echo 'Не удалось загрузить билеты';

            exit;
        }

        try {
            [$pdf, $tempFiles] = $this->pdfGenerator->generateFromUrls($ticketUrls);

            return response($pdf->Output('S', 'autovokzal_tickets.pdf'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="autovokzal_tickets.pdf"');
        } catch (Exception $e) {
            // Удаляем временные файлы в случае ошибки
            foreach ($tempFiles ?? [] as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showRefundTicket($uuid): \Inertia\Response
    {
        try {
            $ticket = Ticket::where('uuid', $uuid)->firstOrFail();

            return Inertia::render('tickets/Refund', [
                'ticket' => $ticket,
                'status' => $ticket->getTranslatedStatus(),
                'refund_limit' => config("refund_route_time_rules.$ticket->route_name"),
            ]);
        } catch (ModelNotFoundException $e) {
            return Inertia::render('tickets/Refund', [
                'error' => 'Билет не найден',
            ]);
        }
    }

    public function refund(Request $request)
    {
        $user = Auth::user();
        $ticket = Ticket::where('uuid', $request->get('uuid'))->firstOrFail();
        $refundAmount = null;
        $comment = '';

        try {
            $response = $this->refundService->refundTicket($ticket, $user->role, $request->get('type'), $refundAmount, $comment);
            if ($response['success'] === true) {
                return redirect()->back()->with('response', $response);
            } else {
                return redirect()->back()->with('response', [
                    'success' => false,
                    'message' => "Возникла ошибка. Не получилось оформить возврат билета. " .
                        "Пожалуйста, напишите нам на почту info@biletavto.ru для оформления возврата билета, предоставив данные о данном билете",
                ]);
            }
        } catch (Exception $e) {
            Log::info($e);

            return redirect()->back()->with('response', [
                'success' => false,
                'message' => 'Произошла ошибка при оформлении возврата!',
            ]);
        }
    }
}
