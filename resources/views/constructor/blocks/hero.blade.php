<section class="hero-block">
    <div class="hero-block-inner">
        <header class="hero-block-content">
            @if ($title)
                <h1 class="hero-block-title">{!! nl2br(e($title)) !!}</h1>
            @endif

            @if ($subtitle)
                <p class="hero-block-subtitle">{!! nl2br(e($subtitle)) !!}</p>
            @endif

            @if ($ctaLabel)
                <a class="button primary hero-block-cta" href="{{ $ctaUrl }}">
                    {{ $ctaLabel }} <i class="hero-block-cta-arrow pi pi-chevron-right"></i>
                </a>
            @endif
        </header>

        <div class="hero-block-media" aria-hidden="true">
            <div class="hero-block-food-main">
                <img
                    class="hero-block-food-img hero-block-food-img--main"
                    src="{{ Storage::url($imageMain) }}"
                    alt=""
                >
            </div>

            @if ($imageTop)
                <div class="hero-block-food-card hero-block-food-card--top">
                    <img
                        class="hero-block-food-img"
                        src="{{ Storage::url($imageTop) }}"
                        alt=""
                    >
                </div>
            @endif

            @if ($imageBottom)
                <div class="hero-block-food-card hero-block-food-card--bottom">
                    <img
                        class="hero-block-food-img"
                        src="{{ Storage::url($imageBottom) }}"
                        alt=""
                    >
                </div>
            @endif

            {{-- Tomato --}}
            <span class="hero-block-deco hero-block-deco--tomato" aria-hidden="true">
                <svg
                    viewBox="0 0 80 90"
                    xmlns="http://www.w3.org/2000/svg"
                    width="80"
                    height="90"
                >
                    <!-- stem -->
                    <path
                        d="M40 22 C40 14 34 8 28 10 C33 10 36 16 38 20"
                        fill="#3a7d44"
                        stroke="none"
                    />
                    <path
                        d="M40 22 C40 12 48 6 54 9 C48 9 44 16 41 21"
                        fill="#2d6b38"
                        stroke="none"
                    />
                    <path
                        d="M40 22 C38 10 42 4 46 6 C42 7 40 14 40 20"
                        fill="#4a8f55"
                        stroke="none"
                    />
                    <!-- body -->
                    <ellipse
                        cx="40"
                        cy="52"
                        rx="28"
                        ry="27"
                        fill="#e03030"
                    />
                    <ellipse
                        cx="40"
                        cy="52"
                        rx="28"
                        ry="27"
                        fill="url(#tomatoShine)"
                        opacity="0.4"
                    />
                    <!-- highlight -->
                    <ellipse
                        cx="30"
                        cy="38"
                        rx="8"
                        ry="6"
                        fill="#f06060"
                        opacity="0.5"
                        transform="rotate(-20 30 38)"
                    />
                    <!-- shadow -->
                    <ellipse
                        cx="44"
                        cy="68"
                        rx="14"
                        ry="6"
                        fill="#a01010"
                        opacity="0.25"
                    />
                    <defs>
                        <radialGradient
                            id="tomatoShine"
                            cx="35%"
                            cy="30%"
                            r="60%"
                        >
                            <stop offset="0%" stop-color="#ff8080" />
                            <stop offset="100%" stop-color="#a01010" />
                        </radialGradient>
                    </defs>
                </svg>
            </span>

            {{-- Garlic --}}
            <span class="hero-block-deco hero-block-deco--garlic" aria-hidden="true">
                <svg
                    viewBox="0 0 70 80"
                    xmlns="http://www.w3.org/2000/svg"
                    width="60"
                    height="70"
                >
                    <!-- stem -->
                    <path
                        d="M35 18 C35 10 33 5 35 2 C37 5 35 10 35 18"
                        fill="#8a9a60"
                        stroke="none"
                    />
                    <!-- left clove -->
                    <ellipse
                        cx="26"
                        cy="42"
                        rx="13"
                        ry="18"
                        fill="#f0ece0"
                        transform="rotate(-12 26 42)"
                    />
                    <ellipse
                        cx="26"
                        cy="42"
                        rx="11"
                        ry="16"
                        fill="#f7f4ec"
                        transform="rotate(-12 26 42)"
                    />
                    <path
                        d="M22 28 C18 34 16 44 20 52 C22 46 22 34 26 30 Z"
                        fill="#e8e4d8"
                        opacity="0.6"
                        transform="rotate(-12 26 42)"
                    />
                    <!-- right clove -->
                    <ellipse
                        cx="44"
                        cy="42"
                        rx="13"
                        ry="18"
                        fill="#ede9dc"
                        transform="rotate(12 44 42)"
                    />
                    <ellipse
                        cx="44"
                        cy="42"
                        rx="11"
                        ry="16"
                        fill="#f5f2ea"
                        transform="rotate(12 44 42)"
                    />
                    <path
                        d="M48 28 C52 34 54 44 50 52 C48 46 48 34 44 30 Z"
                        fill="#e0dcd0"
                        opacity="0.6"
                        transform="rotate(12 44 42)"
                    />
                    <!-- center clove -->
                    <ellipse
                        cx="35"
                        cy="40"
                        rx="11"
                        ry="20"
                        fill="#f8f5ee"
                    />
                    <ellipse
                        cx="35"
                        cy="40"
                        rx="9"
                        ry="18"
                        fill="#faf8f2"
                    />
                    <path
                        d="M32 22 C30 30 30 44 33 56 C34 48 34 32 36 24 Z"
                        fill="#f0ece4"
                        opacity="0.5"
                    />
                    <!-- base wrap -->
                    <path d="M18 56 C20 64 28 70 35 70 C42 70 50 64 52 56 C46 60 40 62 35 62 C30 62 24 60 18 56Z" fill="#d4c9a8" />
                    <path
                        d="M22 56 C24 62 30 66 35 66 C40 66 46 62 48 56"
                        fill="none"
                        stroke="#c4b898"
                        stroke-width="1"
                    />
                </svg>
            </span>

            {{-- Basil leaf top-left --}}
            <span class="hero-block-deco hero-block-deco--leaf-tl" aria-hidden="true">
                <svg
                    viewBox="0 0 90 100"
                    xmlns="http://www.w3.org/2000/svg"
                    width="90"
                    height="100"
                >
                    <path d="M45 90 C45 90 10 65 12 38 C14 18 30 8 45 10 C60 8 76 18 78 38 C80 65 45 90 45 90Z" fill="#3d7a45" />
                    <path d="M45 90 C45 90 15 62 18 38 C20 22 32 12 45 14 C58 12 70 22 72 38 C75 62 45 90 45 90Z" fill="#4a9055" />
                    <!-- veins -->
                    <path
                        d="M45 85 L45 15"
                        fill="none"
                        stroke="#2d6035"
                        stroke-width="1.5"
                        opacity="0.6"
                    />
                    <path
                        d="M45 35 C38 32 28 28 22 26"
                        fill="none"
                        stroke="#2d6035"
                        stroke-width="0.8"
                        opacity="0.5"
                    />
                    <path
                        d="M45 50 C36 46 26 42 18 40"
                        fill="none"
                        stroke="#2d6035"
                        stroke-width="0.8"
                        opacity="0.5"
                    />
                    <path
                        d="M45 65 C38 62 28 60 22 60"
                        fill="none"
                        stroke="#2d6035"
                        stroke-width="0.8"
                        opacity="0.5"
                    />
                    <path
                        d="M45 35 C52 32 62 28 68 26"
                        fill="none"
                        stroke="#2d6035"
                        stroke-width="0.8"
                        opacity="0.5"
                    />
                    <path
                        d="M45 50 C54 46 64 42 72 40"
                        fill="none"
                        stroke="#2d6035"
                        stroke-width="0.8"
                        opacity="0.5"
                    />
                    <path
                        d="M45 65 C52 62 62 60 68 60"
                        fill="none"
                        stroke="#2d6035"
                        stroke-width="0.8"
                        opacity="0.5"
                    />
                    <!-- shine -->
                    <path
                        d="M38 20 C34 28 32 38 34 48"
                        fill="none"
                        stroke="#6ab875"
                        stroke-width="2"
                        opacity="0.35"
                        stroke-linecap="round"
                    />
                </svg>
            </span>

            {{-- Basil leaf bottom-right (smaller, rotated) --}}
            <span class="hero-block-deco hero-block-deco--leaf-br" aria-hidden="true">
                <svg
                    viewBox="0 0 70 80"
                    xmlns="http://www.w3.org/2000/svg"
                    width="65"
                    height="75"
                >
                    <path d="M35 72 C35 72 8 52 10 30 C12 14 22 6 35 8 C48 6 58 14 60 30 C62 52 35 72 35 72Z" fill="#2e6b38" />
                    <path d="M35 72 C35 72 12 50 14 30 C16 17 25 10 35 12 C45 10 54 17 56 30 C58 50 35 72 35 72Z" fill="#3a8046" />
                    <path
                        d="M35 68 L35 12"
                        fill="none"
                        stroke="#225530"
                        stroke-width="1.2"
                        opacity="0.6"
                    />
                    <path
                        d="M35 28 C29 26 22 22 17 20"
                        fill="none"
                        stroke="#225530"
                        stroke-width="0.7"
                        opacity="0.5"
                    />
                    <path
                        d="M35 42 C28 39 20 36 14 34"
                        fill="none"
                        stroke="#225530"
                        stroke-width="0.7"
                        opacity="0.5"
                    />
                    <path
                        d="M35 28 C41 26 48 22 53 20"
                        fill="none"
                        stroke="#225530"
                        stroke-width="0.7"
                        opacity="0.5"
                    />
                    <path
                        d="M35 42 C42 39 50 36 56 34"
                        fill="none"
                        stroke="#225530"
                        stroke-width="0.7"
                        opacity="0.5"
                    />
                    <path
                        d="M30 16 C27 22 26 30 28 38"
                        fill="none"
                        stroke="#5aaa65"
                        stroke-width="1.5"
                        opacity="0.3"
                        stroke-linecap="round"
                    />
                </svg>
            </span>

            {{-- Peppercorns --}}
            <span class="hero-block-deco hero-block-deco--pepper" aria-hidden="true">
                <svg
                    viewBox="0 0 50 30"
                    xmlns="http://www.w3.org/2000/svg"
                    width="50"
                    height="30"
                >
                    <circle
                        cx="8"
                        cy="15"
                        r="7"
                        fill="#2a1a1a"
                    />
                    <circle
                        cx="6"
                        cy="13"
                        r="2.5"
                        fill="#3e2a2a"
                        opacity="0.6"
                    />
                    <circle
                        cx="26"
                        cy="10"
                        r="6"
                        fill="#1e1212"
                    />
                    <circle
                        cx="24"
                        cy="8"
                        r="2"
                        fill="#382222"
                        opacity="0.6"
                    />
                    <circle
                        cx="42"
                        cy="18"
                        r="5"
                        fill="#251818"
                    />
                    <circle
                        cx="40"
                        cy="16"
                        r="1.8"
                        fill="#352020"
                        opacity="0.6"
                    />
                </svg>
            </span>
        </div>
    </div>
</section>
