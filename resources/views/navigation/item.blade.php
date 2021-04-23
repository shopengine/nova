<li class="leading-tight ml-4 text-sm">
    <router-link tag="a" :to="{path: '{{$item->getPath()}}' }" class="block px-4 py-1 text-white text-justify no-underline dim">{{ __('se.'.$item->getTitle()) }}</router-link>
</li>
