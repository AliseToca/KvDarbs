<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Services\GotenbergPdfService;
use App\Services\MeasurmentConversionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RecipePdfController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected GotenbergPdfService $pdf)
    {
    }

    /**
     * Download a single recipe as a PDF.
     *
     * Route: GET /recipes/{recipe}/pdf
     * Name: recipe.pdf
     */
    public function __invoke(Recipe $recipe): \Illuminate\Http\Response
    {
        $this->authorize('view', $recipe);

        // Reuse the same eager-load + conversion logic as RecipeController@show
        $recipe->load([
            'user:id,username,avatar_src',
            'recipeProducts.product.measurementType.units',
            'recipeType',
            'recipeCategories',
        ]);

        $recipe->recipeProducts->transform(function ($recipeProduct) {
            $converted = MeasurmentConversionService::fromBaseAmount(
                $recipeProduct->amount,
                $recipeProduct->product
            );

            return [
                'id' => $recipeProduct->id,
                'amount' => $converted['amount'],
                'product' => [
                    'id' => $recipeProduct->product_id,
                    'name' => $recipeProduct->product->name,
                ],
                'unit' => [
                    'id' => $converted['unit_id'],
                    'name' => $converted['unit'],
                ],
            ];
        });

        $filename = str($recipe->name)->slug() . '.pdf';

        return $this->pdf->fromView(
            view: 'pdf.recipe',
            data: [
                'recipe'      => $recipe,
                'imageBase64' => $this->encodeImage($recipe->image_src),
                'servings'    => $recipe->servings,
                'translations' => [
                    'prep_time'   => trans('recipe.prep_time'),
                    'cook_time'   => trans('recipe.cook_time'),
                    'total_time'  => trans('recipe.total_time'),
                    'types'       => trans('recipe.types'),
                    'categories'  => trans('recipe.categories'),
                    'ingredients' => trans('recipe.ingredients'),
                    'instructions'=> trans('recipe.instructions'),
                    'reviews'     => trans('recipe.reviews.plural'),
                    'servings'    => trans('recipe.servings'),
                ],
            ],
            filename: $filename,
            inline: true,
        );
    }

    private function encodeImage(?string $imageSrc): ?string
    {
        if (!$imageSrc) {
            return null;
        }

        $path = storage_path("app/public/{$imageSrc}");

        if (!file_exists($path)) {
            return null;
        }

        $mime = mime_content_type($path);
        $data = base64_encode(file_get_contents($path));

        return "data:{$mime};base64,{$data}";
    }
}
