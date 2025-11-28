<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Services\ImageService;
use Faker\Factory as Faker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;


class ComponentPageController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (app()->environment('production') && !Str::endsWith($request->getHost(), '.cube.lv')) {
                abort(Response::HTTP_NOT_FOUND);
            }

            if (! session()->has('errors')) {
                $messageBag = new MessageBag();
                $errors = new ViewErrorBag();

                $messageBag->add('text_with_error', 'Error Message');
                $messageBag->add('select_with_error', 'Error Message');
                $messageBag->add('checkbox_with_error', 'Error Message');
                $messageBag->add('radio_with_error', 'Error Message');

                $errors->put('default', $messageBag);
                $request->session()->flash('errors', $errors);

                return redirect($request->getRequestUri());
            }

            return $next($request);
        });
    }

    public function index(): View
    {
        $faker = Faker::create();
        $testimonials = [];
        for ($i = 0; $i < 5; $i++) {
            $testimonials[] = [
                'name' => $faker->name,
                'role' => $faker->jobTitle,
                'company' => $faker->company,
                'description' => $faker->paragraph,
            ];
        }

        return view('ui-library.components.index', [
            'testimonials' => $testimonials,
            'buttonStyles' => $this->getButtonVariations(resource_path('assets/scss/global/buttons.scss')),
            'demoImage' => resolve(ImageService::class)->getImageAndSourceSetUrls('demo-image.jpg'),
        ]);
    }

    public function getDemoModal(): JsonResponse
    {
        $faker = Faker::create();
        $testimonials = [];
        for ($i = 0; $i < 10; $i++) {
            $testimonials[] = [
                'name' => $faker->name,
                'role' => $faker->jobTitle,
                'company' => $faker->company,
                'description' => $faker->paragraph,
            ];
        }

        return response()->json([
            'html' => view('ui-library.components.part.demo-modal', [
                'testimonials' => $testimonials,
            ])->render(),
        ]);
    }

    /**
     * Parse the SCSS button configuration file and build a list of button size/style combinations
     * for the styleguide. Falls back gracefully if parsing fails.
     *
     * Returned structure:
     * [
     *   'types' => ['primary', 'outline', ...],
     *   'sizes' => ['md', 'lg'],
     *   'combinations' => [
     *       ['type' => 'primary', 'size' => 'md', 'label' => 'Primary md', 'text' => 'Primary button'],
     *       ...
     *   ]
     * ]
     */
    private function getButtonVariations(string $scssPath): array
    {
        if (! is_file($scssPath)) {
            return [
                'types' => [],
                'sizes' => [],
                'combinations' => [],
            ];
        }

        $contents = file_get_contents($scssPath) ?: '';

        // Extract button types from %button-types placeholder block
        $types = [];
        if (preg_match('/%button-types\s*{([\s\S]*?)}\s*%/m', $contents, $match)) {
            $block = $match[1];
        } else {
            // Fallback: use entire file
            $block = $contents;
        }
        if (preg_match_all('/&\.(?<name>[a-zA-Z0-9_-]+)/', $block, $m)) {
            $types = array_values(array_unique($m['name']));
        }

        // Extract size keys from $button-sizes map
        $sizes = [];
        if (preg_match('/\$button-sizes\s*:\s*\((?<map>[\s\S]*?)\n\);/m', $contents, $mSizes)) {
            $mapBody = $mSizes['map'];
            if (preg_match_all('/\n\s*([a-zA-Z0-9_-]+)\s*:\s*\(/', $mapBody, $mSizeKeys)) {
                $sizes = array_values(array_unique($mSizeKeys[1]));
            }
        }

        // Sensible fallbacks if nothing parsed
        if (empty($types)) {
            $types = ['primary', 'secondary', 'outline', 'cta', 'link', 'blank', 'icon-only', 'go-back'];
        }
        if (empty($sizes)) {
            $sizes = ['md', 'lg'];
        }

        // Build all combinations
        $combinations = [];
        foreach ($types as $type) {
            foreach ($sizes as $size) {
                // Some button styles might not meaningfully support multiple sizes (e.g. go-back);
                // still list them unless we want to filter. Keep everything for completeness.
                $label = ucfirst(str_replace(['-', '_'], ' ', $type));
                $text = ucfirst(str_replace(['-', '_'], ' ', $type)) . ' button';
                $combinations[] = [
                    'type' => $type,
                    'size' => $size,
                    'label' => $label,
                    'text' => $text,
                ];
            }
        }

        return [
            'types' => $types,
            'sizes' => $sizes,
            'combinations' => $combinations,
        ];
    }

}
