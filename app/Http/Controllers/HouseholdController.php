<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\User;
use App\Services\PagesService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use CubeAgency\FilamentPageManager\Models\Page;

class HouseholdController extends Controller
{
    /**
     * Serviss, kas atbild par lapu struktūru un valodām
     */
    protected PagesService $pagesService;

    /**
     * Dependency Injection – lai nevajadzētu izmantot app()
     */
    public function __construct(PagesService $pagesService)
    {
        $this->pagesService = $pagesService;
    }

    /**
     * Izveido mājsaimniecības lietotāja mājsaimniecības saiti, izmantojot lietotāja username,
     * balstoties uz pašreizējo valodas lapu
     */
    protected function householdShowUrl(User $user): string
    {
        // Aktīvās valodas saknes lapa
        $currentLanguage = $this->pagesService->getLanguagePage();

        // Atrodam mājsaimniecības lapu konkrētajā valodā
        $page = Page::query()
            ->where('template', 'App\Filament\Templates\HouseholdTemplate')
            ->where('parent_id', $currentLanguage->id)
            ->firstOrFail();

        // Izveidojam saiti ar {user:username} parametru
        return $page->getUrl('show', [
            'user' => $user->username,
        ]);
    }

    /**
     * Mājsaimniecības sākumlapa:
     * - ja lietotājam jau ir household → pāradresē uz lietotāja mājsaimniecības lapu
     * - ja nav → piedāvā izvēles skatu
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->household) {
            return redirect(
                $this->householdShowUrl($user)
            );
        }

        return Inertia::render('Household/Index');
    }

    /**
     * Konkrētas mājsaimniecības skats
     */
    public function show(User $user)
    {
        // $this->authorize('view', $user->household);

        $household = $user->household;

        $products = Product::select('id', 'name')->get();

        $productCategories = ProductCategory::select('id', 'name')->get();

        $units = Unit::all();

        //Visi mājsaimniecības produkti ar tā saistītajiem modeļiem
        $householdProducts = $user->household->householdProducts()
            ->with(['product.productCategory', 'unit'])
            ->get()
            //Pārveidojam modeļu datus uz vienkārsu masīvu front-end
            ->map(function ($householdProduct) {
                return [
                    'id' => $householdProduct->id,
                    'productName' => $householdProduct->product->name,
                    'amount' => $householdProduct->amount,
                    'unitName' => $householdProduct->unit->name,
                    'expirationDate' => $householdProduct->expiration_date,
                    'categoryName' => $householdProduct->product->productCategory->name,
                ];
            });

        return Inertia::render('Household/Show', [
            'household' => $household,
            'householdProducts' => $householdProducts,
            'products' => $products,
            'productCategories' => $productCategories,
            'units' => $units,
        ]);
    }

    /**
     * Izveido jaunu mājsaimniecību
     * un piesaista to autentificētajam lietotājam
     */
    public function store(Request $request)
    {
        // Validējam ievadi
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        // Izveidojam mājsaimniecību
        $household = Household::create($validated);

        // Piesaistām mājsaimniecību lietotājam
        $request->user()
            ->household()
            ->associate($household)
            ->save();

        // Pāradresējam uz mājsaimniecības lietotāja lapu
        return redirect(
            $this->householdShowUrl($request->user())
        );
    }
}
