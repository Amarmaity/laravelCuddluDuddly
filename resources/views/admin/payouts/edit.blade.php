@extends('admin.layouts.admin')

@section('content')
<div class="container-fluid py-3">
    <h4>Edit Payout</h4>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.payouts.update', $payout->id) }}">
                @csrf @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Seller</label>
                    <input type="text" class="form-control" value="{{ $payout->seller->name }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="text" class="form-control" value="{{ $payout->amount }}" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="pending" {{ $payout->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $payout->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $payout->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ $payout->status == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Transaction ID</label>
                    <input type="text" name="transaction_id" class="form-control" value="{{ $payout->transaction_id }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control">{{ $payout->remarks }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('admin.payouts.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
