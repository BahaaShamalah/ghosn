@props(['en' => '', 'ar' => '', 'arClass' => ''])

<span data-en="">{{ \App\Support\HtmlText::clean($en) }}</span><span data-ar="" @class([$arClass])>{{ \App\Support\HtmlText::clean($ar) }}</span>
