<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @includeWhen(isset($metaData), 'layouts.partials.head.meta')
    @include('layouts.partials.head.scripts')
    @include('layouts.partials.head.styles')
    @include('layouts.partials.head.favicon')

    @includeWhen(!empty($settings->google_tag_manager_id), 'layouts.partials.gtm-head')
    @includeWhen(!empty($settings->google_analytics_id), 'layouts.partials.analytics')

</head>
<body>
    <a class="skip-main" href="#main">@lang('main.skip_to_content')</a>
    @includeWhen(!empty($settings->google_tag_manager_id), 'layouts.partials.gtm-body')

    @include('layouts.partials.header')

    @includeWhen($cookiesPage && !empty($cookiesPage->content['cookie_notice']), 'layouts.partials.cookies')

    <main id="main" tabindex="-1">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <x-modal.container />

</body>
</html>
