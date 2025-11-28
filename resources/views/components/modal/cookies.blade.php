<div
    class="cookies-layer"
    data-cookies-layer
    data-hidden="{{ $showCookieNotice ? 'false' : 'true' }}"
    data-expanded="false"
>
    <div class="cookies-notice">
        <div class="container">
            <div class="text">
                {!! $cookieNode->content->notice ?? '' !!}
            </div>
            <div class="actions">
                <a
                    class="button cookies-accept"
                    data-theme="green"
                    data-cookies-accept-all
                    href="{{ $acceptAllCookiesUrl }}"
                >
                    @lang('cookies.accept')
                </a>
                <button class="link cookies-personalize" data-js-cookie-toggle>
                    @lang('cookies.personalize')
                    {{-- @svg('icon-arrow-down') --}} v
                </button>
            </div>
        </div>
    </div>
    <div class="cookies-settings">
        <div class="container">
            <form
                class="options"
                action="{{ $saveCookieSettingsUrl }}"
                method="post"
            >
                {!! csrf_field() !!}
                <div class="checkbox-group">
                    @foreach ($cookieTrackers as $tracker)
                        <x-inputs.checkbox
                            class="{{ !empty($tracker->is_mandatory) ? 'mandatory' : '' }}"
                            name="{{ $tracker->name }}"
                            label="{{ $tracker->title }}"
                            :checked="!empty($tracker->is_mandatory) || !empty($tracker->checked)"
                            :disabled="!empty($tracker->is_mandatory)"
                        />
                    @endforeach
                </div>
                <button
                    class="button cookies-save"
                    data-cookies-accept-selected
                    type="submit"
                >
                    @lang('cookies.accept_selected')
                </button>
            </form>
            <div class="details">
                <div class="content">
                    <div class="overview" data-js-tab-content>
                        <div class="tracker-list" data-js-tracker-list>
                            @foreach ($cookieTrackers as $tracker)
                                <a
                                    class="tracker @if ($loop->iteration == 1) selected @endif"
                                    data-js-tracker="{{ $tracker->name }}"
                                    href="#"
                                >
                                    {{ $tracker->title }}: ({{ $tracker->cookieItems->isNotEmpty() }})
                                </a>
                            @endforeach
                        </div>
                        <div class="tracker-details-container">
                            @foreach ($cookieTrackers as $tracker)
                                <div
                                    class="tracker-details"
                                    id="{{ $tracker->name }}"
                                    data-js-tracker-details
                                    @if ($loop->iteration > 1) style="display: none" @endif
                                >
                                    <div class="description">{!! $tracker->description !!}</div>
                                    @if ($tracker->cookieItems->isNotEmpty())
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">@lang('cookies.name')</th>
                                                    <th scope="col">@lang('cookies.provider')</th>
                                                    <th scope="col">@lang('cookies.purpose')</th>
                                                    <th scope="col">@lang('cookies.expiry')</th>
                                                    <th scope="col">@lang('cookies.type')</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tracker->cookieItems as $item)
                                                    <tr>
                                                        <td>{{ $item->title }}</td>
                                                        <td>{{ $item->provider }}</td>
                                                        <td>{{ $item->description }}</td>
                                                        <td>{{ $item->longevity }}</td>
                                                        <td>{{ $item->type }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
