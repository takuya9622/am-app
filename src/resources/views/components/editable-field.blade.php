@if($isApprovalPending === '承認待ち' || $isApproved === true)
<p class="form-content {{ $class ?? null }}">{{ $value }}</p>
@else
<input type="{{ $type }}" name="{{ $name }}" value="{{ $getValue() ?? $value }}" class="form-control">
@endif