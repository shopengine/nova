<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="sidebar-icon">
        <path fill="var(--sidebar-icon)" d="M17 16a3 3 0 1 1-2.83 2H9.83a3 3 0 1 1-5.62-.1A3 3 0 0 1 5 12V4H3a1 1 0 1 1 0-2h3a1 1 0 0 1 1 1v1h14a1 1 0 0 1 .9 1.45l-4 8a1 1 0 0 1-.9.55H5a1 1 0 0 0 0 2h12zM7 12h9.38l3-6H7v6zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
    </svg>
    <span class="sidebar-label">ShopEngine</span>
</h3>

@foreach ($structs as $struct)
    @foreach ($struct->getGroups() as $group)
        @include('novashopengine::navigation/group', ['group' => $group])
    @endforeach
@endforeach

