@if($lead->probability < 20)
    <i class="far fa-meh-blank"></i>
@elseif($lead->probability >= 20 && $lead->probability < 50)
    <i class="far fa-meh"></i>
@elseif($lead->probability >= 50 && $lead->probability < 80)
    <i class="far fa-smile"></i>
@else
    <i class="far fa-grin-stars"></i>
@endif
