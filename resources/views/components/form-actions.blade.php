@props(['submit', 'cancel'])
<div class="flex gap-2 pt-2">
    <button type="submit" class="btn btn-primary">{{ $submit }}</button>
    <a href="{{ $cancel }}" class="btn btn-secondary">Cancel</a>
</div>
