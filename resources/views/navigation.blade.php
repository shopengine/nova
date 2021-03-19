<h3 class="flex items-center font-normal text-white mb-6 text-base no-underline">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="sidebar-icon">
        <path fill="var(--sidebar-icon)" d="M17 16a3 3 0 1 1-2.83 2H9.83a3 3 0 1 1-5.62-.1A3 3 0 0 1 5 12V4H3a1 1 0 1 1 0-2h3a1 1 0 0 1 1 1v1h14a1 1 0 0 1 .9 1.45l-4 8a1 1 0 0 1-.9.55H5a1 1 0 0 0 0 2h12zM7 12h9.38l3-6H7v6zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm10 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
    </svg>
    <span class="sidebar-label">ShopEngine</span>
</h3>

<ul class="list-reset mb-8">
    <li class="leading-tight ml-4 text-sm">
        <router-link tag="a" :to="{path: '/novashopengine/purchases'}" class="block px-4 py-1 text-white text-justify no-underline dim">Bestellungen</router-link>
    </li>
    <li class="leading-tight ml-4 text-sm">
        <router-link tag="a" :to="{path: '/novashopengine/codepool-groups'}" class="block px-4 py-1 text-white text-justify no-underline dim">Marketing Channel</router-link>
    </li>
    <li class="leading-tight ml-4 text-sm">
        <router-link tag="a" :to="{path: '/novashopengine/codepools'}" class="block px-4 py-1 text-white text-justify no-underline dim">Marketing Kampagnen</router-link>
    </li>
    <li class="leading-tight ml-4 text-sm">
        <router-link tag="a" :to="{path: '/novashopengine/codes'}" class="block px-4 py-1 text-white text-justify no-underline dim">Codes</router-link>
    </li>

    <li class="leading-tight ml-4 text-sm">
        <router-link tag="a" :to="{path: '/novashopengine/lastCodes'}" class="block px-4 py-1 text-white text-justify no-underline dim">Letzte Code Anwendungen</router-link>
    </li>
</ul>

<h4 class="ml-8 mb-1 text-xs text-white-50% uppercase tracking-wide">Admin</h4>

<ul class="list-reset mb-8">
    <li class="leading-tight ml-4 text-sm">
        <router-link tag="a" :to="{path: '/novashopengine/shipping-costs'}" class="block px-4 py-1 text-white text-justify no-underline dim">Versandarten</router-link>
    </li>
    <li class="leading-tight ml-4 text-sm">
        <router-link tag="a" :to="{path: '/novashopengine/payment-methods'}" class="block px-4 py-1 text-white text-justify no-underline dim">Bezahlarten</router-link>
    </li>
</ul>
