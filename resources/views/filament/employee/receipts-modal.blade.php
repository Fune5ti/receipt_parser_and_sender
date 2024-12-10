<div class="px-4 py-3">
    <h3 class="text-lg font-medium mb-4">Payroll Receipts</h3>
    <div class="space-y-2">
        @if($receipts->isEmpty())
        <div>No receipts found for this employee.</div>
        @else
        @foreach($receipts as $receipt)
        <div class="flex items-center justify-between border-b py-2">
            <span>{{ $receipt['month'] }}/{{ $receipt['year'] }}</span>
            <a href="{{ $receipt['url'] }}" target="_blank" class="text-primary-600 hover:text-primary-500">
                Download PDF
            </a>
        </div>
        @endforeach
        @endif
    </div>
</div>