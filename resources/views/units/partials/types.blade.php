<div class="choice-grid">

    @foreach ($types as $type)
        <div class="choice-card type-choice" data-id="{{ $type->id }}">

            <i class="fas fa-cube"></i>

            <div class="choice-name">
                {{ $type->nama }}
            </div>

        </div>
    @endforeach

</div>
