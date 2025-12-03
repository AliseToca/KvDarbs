<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class StyleGuidePageController extends Controller
{
    const MAX_COLOR_ARRAY_COUNT = 2;

    protected $variableMaps = [
        'icons' => [
            'path' => 'resources/assets/svg/sprite.svg',
            'getter' => 'getSvgIcons'
        ],
        'colors' => [
            'path' => 'assets/scss/environment/variables/color.scss',
            'getter' => 'getColors'
        ],
        'Breakpoints' => [
            'path' => 'assets/scss/environment/variables/breakpoint.scss',
        ],
        'svgs' => [
            'path' => 'assets/scss/environment/variables/svg.scss',
            'getter' => 'getSvgs'
        ],
        'fonts' => [
            'path' => 'assets/scss/global/fonts.scss',
            'getter' => 'getFontFaces'
        ]
    ];

    public function index()
    {
        $content = [];
        foreach ($this->variableMaps as $name => $variable) {
            if ($name === 'icons') {
                $path = resource_path('assets/svg/sprite.svg');
                if (!$path || !is_file($path)) {
                    continue;
                }
            } else {
                $path = resource_path($variable['path']);
                if (!is_file($path)) {
                    continue;
                }
            }
            if (isset($variable['getter']) && method_exists($this, $variable['getter'])) {
                $view = call_user_func_array(array($this, $variable['getter']), array($path));
            } else {
                $fileContent = file($path, FILE_IGNORE_NEW_LINES);
                $view = View::make('ui-library.style-guide.' . $name, [
                    'content' => $fileContent,
                ]);
            }
            $content[$name] = $view;
        }

        return view('ui-library.index', [
            'content' => $content,
        ]);
    }
    /**
     * @SuppressWarnings("unused")
     */
    private function getSvgs($filePath)
    {
        $result = [];
        $svgs = file_get_contents($filePath);
        // Extract the content within the $svg-images variable.
        preg_match('/\$svg-images:\s*\((.*?)\);/s', $svgs, $matches);
        if (isset($matches[1])) {
            $svgContent = $matches[1];

            // Split the content into individual SVG image definitions.
            preg_match_all('/\s*(\w+):\s*\'(.*?)\'/s', $svgContent, $matches, PREG_SET_ORDER);

            foreach ($matches as $match) {
                $imageKey = trim($match[1]);
                $svgMarkup = trim($match[2]);

                // Add the image key and SVG markup to the result array.
                $result[$imageKey] = $svgMarkup;
            }
        }
        return View::make('ui-library.style-guide.svgs', [
            'svgs' => $result,
            'title' => 'SVGs'
        ]);
    }

    /**
     * @SuppressWarnings("unused")
     */
    private function getColors($filePath)
    {
        $colors = file($filePath, FILE_IGNORE_NEW_LINES);
        $return = array_map(function ($color) {
            $colorArray = explode(":", $color);
            if (count($colorArray) < self::MAX_COLOR_ARRAY_COUNT || empty($colorArray[0])) {
                return null;
            }
            return [
                'name' => trim($colorArray[0]),
                'hex' => $colorArray[1]
            ];
        }, $colors);
        return View::make('ui-library.style-guide.colors', [
            'colors' => array_filter($return),
            'title' => 'Colors'
        ]);
    }

    /**
     * @SuppressWarnings("unused")
     */
    private function getFontFaces($filePath)
    {
        $fontFamilyNames = array();
        $fontWeights = array();
        $fontStyles = array();
        $content = file_get_contents($filePath);

        preg_match_all('/@font-face\s*{[^}]*font-family:\s*(?:["\']([^"\']+)["\']|([^;]+));[^}]*}/s', $content, $fontFamilyMatches);

        if (!empty($fontFamilyMatches[1])) {
            $fontFamilyNames = array_map('trim', array_filter(array_merge($fontFamilyMatches[1], $fontFamilyMatches[2])));
        }

        preg_match_all('/@font-face\s*{[^}]*font-weight:\s*([^;]+);[^}]*}/s', $content, $fontWeightMatches);

        if (!empty($fontWeightMatches[1])) {
            $fontWeights = $fontWeightMatches[1];
        }

        preg_match_all('/@font-face\s*{[^}]*font-style:\s*([^;]+);[^}]*}/s', $content, $fontStyleMatches);

        if (!empty($fontStyleMatches[1])) {
            $fontStyles = $fontStyleMatches[1];
        }

        $fonts = array_map(function ($familyName, $weight, $style) {
            return ['familyName' => $familyName, 'weight' => $weight, 'style' => $style];
        }, $fontFamilyNames, $fontWeights, $fontStyles);

        // Sort fonts by weight (lowest first)
        usort($fonts, function ($a, $b) {
            $weightA = $this->normalizeWeight($a['weight']);
            $weightB = $this->normalizeWeight($b['weight']);
            return $weightA <=> $weightB;
        });

        return View::make('ui-library.style-guide.fonts', [
            'fonts' => $fonts,
            'title' => 'Font faces'
        ]);
    }

    private function normalizeWeight($weight)
    {
        $weight = trim(strtolower($weight));

        // Handle numeric weights
        if (is_numeric($weight)) {
            return (int)$weight;
        }

        // Handle text weight values
        $textWeights = [
            'thin' => 100,
            'extralight' => 200,
            'ultralight' => 200,
            'light' => 300,
            'normal' => 400,
            'regular' => 400,
            'medium' => 500,
            'semibold' => 600,
            'demibold' => 600,
            'bold' => 700,
            'extrabold' => 800,
            'ultrabold' => 800,
            'black' => 900,
            'heavy' => 900,
        ];

        return $textWeights[$weight] ?? 400;
    }

    /**
     * @SuppressWarnings("unused")
     */
    private function getSvgIcons($filePath)
    {
        $content = file_get_contents($filePath);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadXML($content);
        libxml_use_internal_errors(false);

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query("//*[@id]");

        $ids = [];
        foreach ($nodes as $node) {
            $ids[] = $node->getAttribute('id');
        }

        return View::make('ui-library.style-guide.icons', [
            'icons' => $ids,
            'title' => 'SVG sprite icons'
        ]);
    }
}
