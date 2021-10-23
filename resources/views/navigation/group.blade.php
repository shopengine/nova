@if ($group->showTitle())
    <h4 class="ml-8 mb-1 text-xs text-white-50% uppercase tracking-wide">
        {{ __('shopengine.'.$group->getTitle())  }}
    </h4>
@endif

<ul class="list-reset mb-8">
    @foreach ($group->getItems() as $item)
        @include('novashopengine::navigation/item', ['item' => $item])
    @endforeach
</ul>
