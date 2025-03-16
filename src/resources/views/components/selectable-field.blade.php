@if($isApprovalPending === '承認待ち' || $isApproved === true)
<p class="form-content">{{ $value }}</p>
@else
<select class="form-control" id="{{ $id }}" name="{{ $name }}">
    @if ($type === 'year')
    @for ($year = $startYear; $year <= $endYear; $year++)
    <option value="{{ $year }}" {{ $year == $selected ? 'selected' : '' }}>
    {{ $year }}年
    </option>
    @endfor
    @elseif ($type === 'date')
    @foreach (range(1, 12) as $month)
    @foreach (range(1, 31) as $day)
    @if (!($month == 2 && $day > ($isLeapYear ? 29 : 28)) &&
    !in_array($month, [4, 6, 9, 11]) || $day <= 30)
    <option value="{{ $month }}月{{ $day }}日" {{ $month.'月'.$day.'日' == $selected ? 'selected' : '' }}>
    {{ $month }}月{{ $day }}日
    </option>
    @endif
    @endforeach
    @endforeach
    @endif
</select>
@endif