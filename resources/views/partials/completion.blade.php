<form method="POST" action="{{ $done ? route('completions.destroy',$habit) : route('completions.store',$habit) }}" class="completion-form">@csrf @method($done ? 'DELETE' : 'PUT')<input type="hidden" name="date" value="{{ $today->toDateString() }}">
<button @class(['complete-toggle','is-done'=>$done]) type="submit" aria-label="{{ $done ? 'Batalkan selesai: ' : 'Tandai selesai: ' }}{{ $habit->name }}" title="{{ $done ? 'Batalkan selesai hari ini' : 'Tandai selesai hari ini' }}">
<span aria-hidden="true">{{ $done ? '✓' : '+' }}</span>
</button>
</form>
