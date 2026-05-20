{{-- resources/views/pdf/recipe.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        /* ── Reset & base ── */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: dejavusans, Arial, sans-serif;
            /* dejavusans is built into mPDF and supports UTF-8 */
            font-size: 14px;
            color: #1a1a1a;
            background: #fff;
        }

        /* ── Section shared ── */
        .section {
            width: 100%;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }

        .section-last {
            border: none;
        }

        h1 {
            font-size: 22px;
            line-height: 1.2;
            margin-bottom: 8px;
            word-break: break-word;
        }

        h3 {
            font-size: 15px;
            margin-bottom: 12px;
        }

        /* ── Stars ── */
        .stars {
            font-size: 16px;
        }

        /* ── Tags ── */
        .tag {
            padding: 3px 8px;
            font-size: 12px;
            color: #4a4a4a;
            display: inline-block;
            margin: 2px 2px 2px 0;
        }

        .label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 2px;
            display: block;
        }

        .value {
            font-size: 15px;
            font-weight: bold;
            color: #1a1a1a;
        }

        /* ── Ingredients ── */
        .servings-badge {
            font-size: 13px;
            font-weight: bold;
            color: #2a2525;
            display: inline-block;
        }

        /* ── Instructions ── */
        .step-number {
            width: 26px;
            font-weight: bold;
            font-size: 13px;
            color: #2a2525;
            display: inline-block;
        }
    </style>
</head>
<body>

    {{-- ── HEADER: title + meta + image ── --}}
    {{-- mPDF supports <table> layout reliably; flexbox/grid are not supported --}}
    <div class="section">
        <table
            width="100%"
            cellpadding="0"
            cellspacing="0"
        >
            <tr>
                {{-- Left column: title, rating, times, tags --}}
                <td
                    width="55%"
                    valign="top"
                    style="padding-right: 16px;"
                >

                    <h1 style="margin-bottom: 16px;">{{ $recipe->name }}</h1>

                    {{-- Rating row --}}
                    @php
                        $rating = round($recipe->average_rating ?? 0);
                        $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                    @endphp
                    <p style="font-size:13px;">
                        <span class="stars">{{ $stars }}</span>
                        {{ number_format($recipe->average_rating ?? 0, 1) }}
                        ({{ $recipe->reviews_count ?? 0 }}) {{ $translations['reviews'] }}
                        &nbsp;•&nbsp;
                        {{-- Avatar + username --}}
                        @if ($recipe->user->avatar_src)
                            @if ($avatarBase64)
                                <img
                                    src="{{ $avatarBase64 }}"
                                    width="20"
                                    height="20"
                                    style="border-radius:10px; vertical-align:middle;"
                                >
                            @endif
                        @endif
                        {{ $recipe->user->username }}
                    </p>

                    {{-- Time grid via table --}}
                    @php
                        function formatTime(int $minutes): string
                        {
                            if ($minutes < 60) {
                                return $minutes . ' min';
                            }
                            $h = intdiv($minutes, 60);
                            $m = $minutes % 60;
                            return $m > 0 ? "{$h} h {$m} min" : "{$h} h";
                        }
                    @endphp
                    <table
                        width="100%"
                        cellpadding="0"
                        cellspacing="5px"
                        style="margin-bottom:16px; margin-top:24px;"
                    >
                        <tr>
                            <td width="33%">
                                <span class="label">{{ $translations['prep_time'] }}</span>
                            </td>
                            <td width="33%">
                                <span class="label">{{ $translations['cook_time'] }}</span>
                            </td>
                            <td width="33%">
                                <span class="label">{{ $translations['total_time'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td width="33%">
                                <span class="value">{{ formatTime($recipe->prep_time) }}</span>
                            </td>
                            <td width="33%">
                                <span class="value">{{ formatTime($recipe->cook_time) }}</span>
                            </td>
                            <td width="33%">
                                <span class="value">{{ formatTime($recipe->total_time) }}</span>
                            </td>
                        </tr>
                    </table>

                    {{-- Tags --}}
                    @if ($recipe->recipeType)
                        <span class="label">{{ $translations['types'] }}</span>
                        <span class="tag">{{ $recipe->recipeType->name }}</span>
                        <br style="margin-bottom:6px;">
                    @endif

                    @if ($recipe->recipeCategories->isNotEmpty())
                        <span class="label" style="margin-top:8px;">{{ $translations['categories'] }}</span>
                        @foreach ($recipe->recipeCategories as $cat)
                            <span class="tag">{{ $cat->name }}</span>
                        @endforeach
                    @endif

                </td>

                {{-- Right column: recipe image --}}
                {{--
                Image sizing strategy for mPDF:
                - width="100%" fills the 45% column
                - max-height caps portrait images; mPDF respects max-height on <img>
                - height="auto" lets mPDF scale proportionally within those bounds
            --}}
                <td
                    width="45%"
                    valign="top"
                    align="right"
                    style="max-height: 200px; overflow: hidden;"
                >
                    @if ($imageBase64)
                        <img
                            src="{{ $imageBase64 }}"
                            width="220"
                            style="max-height:200px; display:block;"
                        >
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ── INGREDIENTS ── --}}
    <div class="section">
        <table
            width="100%"
            cellpadding="0"
            cellspacing="0"
            style="margin-bottom:12px;"
        >
            <tr>
                <td valign="middle">
                    <h3 style="margin:0;">{{ $translations['ingredients'] }}</h3>
                </td>
                <td valign="middle" align="right">
                    <span class="servings-badge">{{ $translations['servings'] }}: {{ $servings }}</span>
                </td>
            </tr>
        </table>

        @foreach ($recipe->recipeProducts as $ingredient)
            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="margin-bottom:6px;"
            >
                <tr>
                    <td width="20" valign="middle">
                        <span style="font-size:20px;">☐</span>
                    </td>
                    <td valign="middle" style="padding-left:4px; font-size:14px;">
                        {{ $ingredient['amount'] }}{{ $ingredient['unit']['name'] }}
                        {{ $ingredient['product']['name'] }}
                    </td>
                </tr>
            </table>
        @endforeach
    </div>

    {{-- ── INSTRUCTIONS ── --}}
    <div class="section section-last">
        <h3>{{ $translations['instructions'] }}</h3>

        @foreach ($recipe->instructions as $i => $step)
            <table
                width="100%"
                cellpadding="0"
                cellspacing="0"
                style="margin-bottom: 10px;"
            >
                <tr>
                    <td width="20" valign="top">
                        <div class="step-number">{{ $i + 1 }}</div>
                    </td>
                    <td valign="top" style="padding-left:5px; font-size:14px; line-height:1.5;">
                        {{ $step }}
                    </td>
                </tr>
            </table>
        @endforeach
    </div>
</body>
</html>
