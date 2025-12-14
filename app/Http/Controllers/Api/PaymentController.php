<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\ProcessPaymentRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    
    public function __construct(private readonly PaymentService $paymentService) {}

    /**
     * GET /api/payments
     */
    public function index(): JsonResponse
    {
        $payments = Payment::query()
            ->with('order')
            ->latest()
            ->paginate((int) request('per_page', 15));

        return response()->json($payments);
    }

    /**
     * GET /api/orders/{order}/payments
     */
    public function listForOrder(Order $order): JsonResponse
    {
        $payments = $order->payments()
            ->latest()
            ->paginate((int) request('per_page', 15));

        return response()->json($payments);
    }

    /**
     * POST /api/orders/{order}/payments
     */
    use InvalidArgumentException;

public function process(ProcessPaymentRequest $request, Order $order): JsonResponse
{
    if ($order->status !== 'confirmed') {
        return response()->json(['message' => 'Order must be confirmed before payment can be processed.'], 422);
    }

    $data = $request->validated();

    try {
        $payment = $this->paymentService->process(
            order: $order,
            method: $data['payment_method'],
            payload: $data['payload'] ?? []
        );
    } catch (InvalidArgumentException $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 422);
    }

    return response()->json($payment, 201);
}

}
