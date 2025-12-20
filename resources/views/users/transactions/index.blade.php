@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-wallet2 text-primary me-2"></i>My Credit History</h1>
            <p class="text-muted small mb-0">Track your credit usage, top-ups, and refunds.</p>
        </div>
        <div class="card bg-primary text-white shadow-sm border-0">
            <div class="card-body py-2 px-3 d-flex align-items-center">
                <div class="me-3">
                    <span class="d-block small opacity-75">Current Balance</span>
                    <span class="h4 fw-bold mb-0">{{ Auth::user()->credits }} Credits</span>
                </div>
                <i class="bi bi-coin fs-1 opacity-25"></i>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Date & Time</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th class="text-end pe-4">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $trx->created_at->format('M d, Y') }}</div>
                                <small class="text-muted">{{ $trx->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                {{ $trx->description }}
                                @if($trx->related_id)
                                <br><small class="text-muted font-monospace text-xs">{{ substr($trx->related_id, 0, 8) }}...</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badge = match($trx->type) {
                                        'booking' => 'bg-danger bg-opacity-10 text-danger',
                                        'refund' => 'bg-success bg-opacity-10 text-success',
                                        'topup' => 'bg-primary bg-opacity-10 text-primary',
                                        default => 'bg-secondary bg-opacity-10 text-secondary',
                                    };
                                    $icon = match($trx->type) {
                                        'booking' => 'bi-arrow-down-right',
                                        'refund' => 'bi-arrow-up-right',
                                        'topup' => 'bi-plus-circle',
                                        default => 'bi-circle',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} px-2 py-1 rounded-pill fw-normal border">
                                    <i class="bi {{ $icon }} me-1"></i> {{ ucfirst($trx->type) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if($trx->amount > 0)
                                    <span class="text-success fw-bold">+{{ number_format($trx->amount) }}</span>
                                @else
                                    <span class="text-danger fw-bold">{{ number_format($trx->amount) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="bi bi-wallet2 display-6 text-muted mb-3 d-block opacity-50"></i>
                                <h5 class="text-muted fw-normal">No transaction history yet</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
