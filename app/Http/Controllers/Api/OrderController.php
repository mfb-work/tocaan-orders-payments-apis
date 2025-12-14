<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * GET /api/orders?status=confirmed&per_page=15&page=1
     */
    public function index(Request $request): JsonResponse
    {
        $query = Order::query()->with('items');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $orders = $query->latest()->paginate((int) $request->query('per_page', 15));

        return response()->json($orders);
    }

    /**
     * POST /api/orders
     * Body: { items: [...], notes?: "..." }
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();
        $items = $data['items'];

        $order = DB::transaction(function () use ($user, $items, $data) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_amount' => 0,
                'notes' => $data['notes'] ?? null,
            ]);

            $total = 0.0;

            foreach ($items as $item) {
                $subtotal = round(((float) $item['price']) * ((int) $item['quantity']), 2);

                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity' => (int) $item['quantity'],
                    'price' => (float) $item['price'],
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total_amount' => round($total, 2)]);

            return $order->load('items');
        });

        return response()->json($order, 201);
    }

    /**
     * GET /api/orders/{order}
     */
    public function show(Order $order): JsonResponse
    {
        $order->load('items', 'payments');

        return response()->json($order);
    }

    /**
     * PATCH /api/orders/{order}
     */
    public function update(UpdateOrderRequest $request, Order $order): JsonResponse
{
    $data = $request->validated();

    // لو فيه دفع ناجح: امنع تعديل items
    if (isset($data['items']) && $order->payments()->where('status', 'successful')->exists()) {
        return response()->json([
            'message' => 'Cannot modify items for an order that has successful payments.',
        ], 422);
    }

    $order = DB::transaction(function () use ($order, $data) {
        // تحديث حقول بسيطة
        $order->fill([
            'status' => $data['status'] ?? $order->status,
            'notes'  => array_key_exists('notes', $data) ? $data['notes'] : $order->notes,
        ])->save();

        // لو items موجودة: استبدال كامل + إعادة حساب total
        if (isset($data['items'])) {
            $order->items()->delete();

            $total = 0.0;
            foreach ($data['items'] as $item) {
                $subtotal = round(((float)$item['price']) * ((int)$item['quantity']), 2);

                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'quantity'     => (int)$item['quantity'],
                    'price'        => (float)$item['price'],
                    'subtotal'     => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total_amount' => round($total, 2)]);
        }

        return $order->fresh()->load('items', 'payments');
    });

    return response()->json($order);
}


    /**
     * DELETE /api/orders/{order}
     * ممنوع إذا للطلب مدفوعات
     */
    public function destroy(Order $order): JsonResponse
    {
        if ($order->payments()->exists()) {
            return response()->json([
                'message' => 'Order cannot be deleted because it has related payments.',
            ], 422);
        }

        $order->delete();

        return response()->json(null, 204);
    }
}
