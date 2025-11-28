<section
    class="cookies-layer"
    data-cookies-layer
    data-hidden="{{ $isCookieNoticeActive ? 'false' : 'true' }}"
>
    <div class="cookies-notice">
        <div class="container">
            {!! $cookiesPage->content['cookie_notice'] !!}
            <div class="actions">
                <a
                    class="button"
                    data-cookies-accept-all
                    href="{{ route('acceptAllCookies') }}"
                >
                    {{ __('cookies.accept') }}
                </a>
                @if ($cookieGroups->isNotEmpty())
                    <button
                        class="button"
                        data-js-cookie-toggle
                        type="button"
                    >
                        {{ __('cookies.personalize') }}
                    </button>
                @endif
                <a class="button" href="{{ route('rejectAllCookies') }}">
                    {{ __('cookies.reject') }}
                </a>
            </div>
        </div>
    </div>
    @if ($cookieGroups->isNotEmpty())
        <div class="cookies-settings">
            <div class="container">
                <form action="{{ route('saveSelectedCookies') }}" method="post">
                    @csrf
                    <div class="cookie-group-options">
                        @foreach ($cookieGroups as $group)
                            @include('components.inputs.checkbox', [
                                'label' => $group->title,
                                'name' => $group->name,
                                'value' => '1',
                                'checked' => !empty($group->is_mandatory) || !empty($group->checked),
                                'disabled' => !empty($group->is_mandatory),
                            ])
                        @endforeach

                    </div>
                    <button class="button" type="submit">
                        {{ __('cookies.accept_selected') }}
                    </button>
                </form>
                <div class="details">
                    <div class="tabs" data-js-cookie-tabs>
                        @include('components.inputs.radio-button', [
                            'name' => 'rating',
                            'value' => 'overview',
                            'label' => __('cookies.cookie_declaration'),
                            'checked' => true,
                        ])
                        @if (!empty($cookiesPage->content['cookie_notice_description']))
                            @include('components.inputs.radio-button', [
                                'name' => 'rating',
                                'value' => 'about-cookies',
                                'label' => __('cookies.about_cookies'),
                            ])
                        @endif
                    </div>
                    <div class="content">
                        <div class="overview" data-js-tab-content>
                            <div class="cookie-group-list" data-js-cookie-group-list>
                                @foreach ($cookieGroups as $group)
                                    <button
                                        class="cookie-group @if ($loop->iteration == 1) selected @endif"
                                        data-js-cookie-group="{{ $group->name }}"
                                        type="button"
                                    >
                                        {{ $group->title }}: ({{ $group->cookies->count() }})
                                    </button>
                                @endforeach
                            </div>
                            <div class="cookie-group-details-container">
                                @foreach ($cookieGroups as $group)
                                    <div
                                        class="cookie-group-details"
                                        id="{{ $group->name }}"
                                        data-js-cookie-group-details
                                        @if ($loop->iteration > 1) style="display: none" @endif
                                    >
                                        <div class="description">{!! $group->description !!}</div>
                                        @if ($group->cookies->count() > 0)
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">{{ __('cookies.name') }}</th>
                                                        <th scope="col">{{ __('cookies.provider') }}</th>
                                                        <th scope="col">{{ __('cookies.purpose') }}</th>
                                                        <th scope="col">{{ __('cookies.expiry') }}</th>
                                                        <th scope="col">{{ __('cookies.type') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($group->cookies as $cookie)
                                                        <tr>
                                                            <td>{{ $cookie->title }}</td>
                                                            <td>{{ $cookie->provider }}</td>
                                                            <td>{{ $cookie->purpose }}</td>
                                                            <td>{{ $cookie->expiration }}</td>
                                                            <td>{{ $cookie->type }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if (!empty($cookiesPage->content['cookie_notice_description']))
                            <div class="about-cookies" data-js-tab-content>
                                {!! $cookiesPage->content['cookie_notice_description'] !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
