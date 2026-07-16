@php

    $fonts = \App\Support\ThemeHelper::fonts();

@endphp

<style>

    :root {

        --ghosn-font-en: '{{ $fonts['en'] }}', ui-sans-serif, system-ui, sans-serif;

        --ghosn-font-ar: '{{ $fonts['ar'] }}', ui-sans-serif, system-ui, sans-serif;

    }

    html[dir="ltr"] body,

    html[dir="ltr"] #ghosn-root {

        font-family: var(--ghosn-font-en);

    }

    html[dir="rtl"] body,

    html[dir="rtl"] #ghosn-root,

    html[lang="ar"] body,

    html[lang="ar"] #ghosn-root {

        font-family: var(--ghosn-font-ar);

    }

</style>


