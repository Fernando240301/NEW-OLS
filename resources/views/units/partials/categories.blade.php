<div class="choice-grid">

    @foreach ($categories as $cat)
        <div class="choice-card category-choice" data-id="{{ $cat->id }}">

            <i class="fas fa-layer-group"></i>

            <div class="choice-name">
                {{ $cat->alias }}
            </div>

        </div>
    @endforeach

</div>
