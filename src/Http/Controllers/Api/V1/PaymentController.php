<?php

namespace Celios\Core\Http\Controllers\Api\V1;

use Celios\Core\Http\Controllers\Controller;
use Celios\Core\Http\Resources\V1\PaymentGatewayResource;
use Celios\Core\Http\Resources\V1\PaymentResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Payment\DTO\CustomerData;
use Modules\Payment\DTO\PaymentChargeRequest;
use Modules\Payment\Models\Payment;
use Modules\Payment\Models\PaymentGatewayConfig;
use Modules\Payment\Services\PaymentService;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * List active payment gateways.
     */
    public function gateways(): AnonymousResourceCollection
    {
        $gateways = PaymentGatewayConfig::query()
            ->active()
            ->get();

        return PaymentGatewayResource::collection($gateways);
    }

    /**
     * Initiate a new payment / checkout session.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'size:3'],
            'gateway' => ['nullable', 'string'],
            'description' => ['nullable', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'return_url' => ['nullable', 'url'],
            'cancel_url' => ['nullable', 'url'],
            'metadata' => ['nullable', 'array'],
        ]);

        $user = $request->user('sanctum');

        $customer = CustomerData::fromArray([
            'name' => $validated['customer_name'] ?? $user?->name,
            'email' => $validated['customer_email'] ?? $user?->email,
            'phone' => $validated['customer_phone'] ?? $user?->phone,
            'ip' => $request->ip(),
        ]);

        $chargeRequest = PaymentChargeRequest::make(
            (float) $validated['amount'],
            strtoupper($validated['currency'])
        )
            ->withCustomer($customer)
            ->withUrls($validated['return_url'] ?? null, $validated['cancel_url'] ?? null)
            ->withMetadata($validated['metadata'] ?? []);

        if (! empty($validated['description'])) {
            $chargeRequest = $chargeRequest->withDescription($validated['description']);
        }

        if ($user) {
            $chargeRequest = $chargeRequest->withPayable($user);
        }

        $response = $this->paymentService->charge($chargeRequest, $validated['gateway'] ?? null);

        return response()->json([
            'success' => $response->isSuccessful() || $response->isRedirect(),
            'status' => $response->status->value,
            'reference' => $response->reference,
            'transaction_id' => $response->transactionId,
            'redirect_url' => $response->redirectUrl,
            'error_message' => $response->errorMessage,
        ], 201);
    }

    /**
     * List payments for the authenticated user (or all payments for admins).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();
        $perPage = min(max($request->integer('per_page', 15), 1), 50);

        $query = Payment::query()->latest('id');

        if (! $user->hasAnyRole(['super_admin', 'super-admin', 'admin', 'panel_user'])) {
            $query->where(function ($q) use ($user) {
                $q->where('customer_email', $user->email)
                    ->orWhere(function ($sub) use ($user) {
                        $sub->where('payable_type', get_class($user))
                            ->where('payable_id', $user->id);
                    });
            });
        }

        return PaymentResource::collection($query->paginate($perPage)->withQueryString());
    }

    /**
     * Get payment status and details by UUID or reference.
     */
    public function show(string $uuidOrReference): PaymentResource
    {
        $payment = Payment::query()
            ->where('uuid', $uuidOrReference)
            ->orWhere('reference', $uuidOrReference)
            ->firstOrFail();

        return new PaymentResource($payment);
    }

    /**
     * Refund a paid payment (Admin).
     */
    public function refund(Request $request, string $uuidOrReference): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $response = $this->paymentService->refund(
            $uuidOrReference,
            (float) $validated['amount'],
            $validated['reason'] ?? null
        );

        return response()->json([
            'success' => $response->isSuccessful,
            'refund_id' => $response->refundId,
            'amount' => $response->amount,
            'error_message' => $response->errorMessage,
        ]);
    }
}
