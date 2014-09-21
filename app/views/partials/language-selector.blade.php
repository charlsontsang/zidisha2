<?php
$languages = [
    'en' => 'English',
    'fr' => 'Français',
    'in' =>'Bahasa Indonesia'
];
$language = $languages[\App::getLocale()];
unset($languages[\App::getLocale()]);

$query = \Request::getQueryString();
$from = $query ? \Request::path() .'?'. $query : \Request::path();
if (in_array(\Request::segment(1), ['fr', 'in'])) {
    $from = ltrim(substr($from, 2));
}
?>
<div class="dropdown">
    <a data-toggle="dropdown" href="#">
        {{ $language }}
        <b class="caret"></b>
    </a>
    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
        <li role="presentation">
            @foreach($languages as $languageCode => $language)
            <a role="menuitem" tabindex="-1" href="{{route('redirect-language', compact('languageCode', 'from'))}}">{{ $language }}</a>
            @endforeach
        </li>
    </ul>
</div>
