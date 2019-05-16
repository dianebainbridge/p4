@if($errors->get($fieldName))
    <div class="outcome"'>{{ $errors->first($fieldName) }}</div>
@endif