<div class="stage-guide">
<p class="eyebrow">TAHAP PERTUMBUHAN</p>@foreach(\App\Services\HabitProgress::STAGES as $i=>$item)<div @class(['stage-row','current'=>$stage['index']===$i])>
<span>{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }} / {{ $item['name'] }}</span>
<span>{{ $item['range'] }}</span>
</div>@endforeach</div>
