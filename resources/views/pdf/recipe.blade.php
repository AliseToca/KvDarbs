{{-- resources/views/pdf/recipe.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        /* ── Reset & base ── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            color: #1a1a1a;
            background: #fff;
            padding: 0;
        }

        /* ── Recipe wrapper ── */
        .recipe {
            width: 100%;
        }

        /* ── Header: image + meta side by side ── */
        .recipe-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .recipe-header-content {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 55%;
        }

        .recipe-header img {
            width: 42%;
            aspect-ratio: 4/3;
            object-fit: cover;
            border-radius: 8px;
        }

        h1 {
            font-size: 1.6rem;
            line-height: 1.2;
            margin-bottom: 8px;
            word-break: break-word;
        }

        /* ── Rating row ── */
        .recipe-header-info {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
        }

        .stars {
            color: #f59e0b;
            font-size: 16px;
            letter-spacing: 1px;
        }

        .divider {
            color: #ccc;
        }

        .author {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .avatar-wrap {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .avatar-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* ── Time grid ── */
        .time-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .time-item {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .label {
            font-size: 11px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .value {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a1a;
        }

        /* ── Tags ── */
        .recipe-tags {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .tag-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .tag {
            background: #f3f0e8;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 12px;
            color: #4a4a4a;
        }

        /* ── Section shared ── */
        section {
            width: 100%;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
            padding-bottom: 20px;
        }

        section:last-child {
            border: none;
        }

        section h3 {
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        /* ── Ingredients ── */
        .ingredients-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .servings-badge {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            background: #2a2525;
            padding: 4px 12px;
            border-radius: 6px;
        }

        .ingredient {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 2px;
            font-size: 14px;
        }

        .ingredient-checkbox {
            width: 16px;
            height: 16px;
            border: 2px solid #ec3f42;
            border-radius: 4px;
            flex-shrink: 0;
            /* Gotenberg can't tick boxes, so show an empty one */
        }

        /* ── Instructions ── */
        .recipe-instructions ol {
            counter-reset: step-counter;
            list-style: none;
            padding-left: 0;
            margin-top: 12px;
        }

        .recipe-instructions ol li {
            counter-increment: step-counter;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
            line-height: 1.5;
        }

        .recipe-instructions ol li::before {
            content: counter(step-counter);
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            border-radius: 50%;
            background-color: #2a2525;
            color: #fff;
            font-weight: bold;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        /* ── Footer ── */
        .pdf-footer {
            text-align: center;
            font-size: 11px;
            color: #aaa;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="recipe">

        {{-- ── HEADER: title + meta + image ── --}}
        <section class="recipe-header">
            <div class="recipe-header-content">
                <div>
                    <h1>{{ $recipe->name }}</h1>

                    <div class="recipe-header-info">
                        {{-- Star rating --}}
                        @php
                            $rating = round($recipe->average_rating ?? 0);
                            $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
                        @endphp
                        <span class="stars">{{ $stars }}</span>
                        <span>{{ number_format($recipe->average_rating ?? 0, 1) }}</span>
                        <span>({{ $recipe->reviews_count ?? 0 }}) {{ $translations['reviews'] }}</span>
                        <span class="divider">•</span>
                        <span class="author">
                            @if ($recipe->user->avatar_src)
                                <div class="avatar-wrap">
                                    <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $recipe->user->avatar_src))) }}" alt="{{ $recipe->user->username }}">
                                </div>
                            @endif
                            {{ $recipe->user->username }}
                        </span>
                    </div>
                </div>

                {{-- ── Time formatting helper ── --}}
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
                {{-- Time grid --}}
                <div class="time-grid">
                    <div class="time-item">
                        <span class="label">{{ $translations['prep_time'] }}</span>
                        <span class="value">{{ formatTime($recipe->prep_time) }}</span>
                    </div>
                    <div class="time-item">
                        <span class="label">{{ $translations['cook_time'] }}</span>
                        <span class="value">{{ formatTime($recipe->cook_time) }}</span>
                    </div>
                    <div class="time-item">
                        <span class="label">{{ $translations['total_time'] }}</span>
                        <span class="value">{{ formatTime($recipe->total_time) }}</span>
                    </div>
                </div>

                {{-- Tags --}}
                <div class="recipe-tags">
                    @if ($recipe->recipeType)
                        <div class="tag-group">
                            <span class="label">{{ $translations['types'] }}</span>
                            <div class="tags">
                                <span class="tag">{{ $recipe->recipeType->name }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($recipe->recipeCategories->isNotEmpty())
                        <div class="tag-group">
                            <span class="label">{{ $translations['categories'] }}</span>
                            <div class="tags">
                                @foreach ($recipe->recipeCategories as $cat)
                                    <span class="tag">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recipe image --}}
            @if ($imageBase64)
                <img src="{{ $imageBase64 }}" alt="{{ $recipe->name }}">
            @endif
        </section>

        {{-- INGREDIENTS --}}
        <section class="recipe-ingredients">
            <div class="ingredients-header">
                <h3>{{ $translations['ingredients'] }}</h3>
                <span class="servings-badge">{{ $translations['servings'] }}: {{ $servings }}</span>
            </div>
            @foreach ($recipe->recipeProducts as $ingredient)
                <div class="ingredient">
                    <div class="ingredient-checkbox"></div>
                    <span>
                        {{ $ingredient['amount'] }}{{ $ingredient['unit']['name'] }}
                        {{ $ingredient['product']['name'] }}
                    </span>
                </div>
            @endforeach
        </section>

        {{-- INSTRUCTIONS --}}
        <section class="recipe-instructions">
            <h3>{{ $translations['instructions'] }}</h3>
            <ol>
                @foreach ($recipe->instructions as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>
        </section>
        <div class="pdf-footer">
            {{ now()->format('d M Y') }}
        </div>
    </div>
</body>
</html>
