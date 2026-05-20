<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\MeasurmentConversionService;
use App\Services\PdfService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipePdfController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected PdfService $pdf)
    {
    }

    public function __invoke(Recipe $recipe): \Illuminate\Http\Response
    {
        $this->authorize('view', $recipe);

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

        return $this->pdf->render(
            view: 'pdf.recipe',
            data: [
                'recipe' => $recipe,
                'imageBase64' => $this->encodeImage($recipe->image_src),
                'avatarBase64' => $this->encodeImage($recipe->user->avatar_src),
                'servings' => $recipe->servings,
                'translations' => [
                    'prep_time' => trans('recipe.prep_time'),
                    'cook_time' => trans('recipe.cook_time'),
                    'total_time' => trans('recipe.total_time'),
                    'types' => trans('recipe.types'),
                    'categories' => trans('recipe.categories'),
                    'ingredients' => trans('recipe.ingredients'),
                    'instructions' => trans('recipe.instructions'),
                    'reviews' => trans('recipe.reviews.plural'),
                    'servings' => trans('recipe.servings'),
                ],
            ],
            filename: str($recipe->name)->slug() . '.pdf',
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

        $img = imagecreatefromstring(file_get_contents($path));

        if ($img === false) {
            return null;
        }

        imagefilter($img, IMG_FILTER_GRAYSCALE);

        ob_start();
        imagejpeg($img, null, 90);
        $data = base64_encode(ob_get_clean());
        imagedestroy($img);

        return "data:image/jpeg;base64,{$data}";

        $mime = mime_content_type($path);
        $data = base64_encode(file_get_contents($path));

        return "data:{$mime};base64,{$data}";
    }
}
