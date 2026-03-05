<?php

namespace App\Http\Controllers;

use App\Models\Household;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\User;
use App\Services\HouseholdUrlService;
use App\Services\PagesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\MeasurmentConversionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;

class HouseholdController extends Controller
{
    use AuthorizesRequests;

    /**
     * Serviss, kas atbild par lapu struktūru un valodām
     */

    /**
     * Dependency Injection – lai nevajadzētu izmantot app()
     */
    public function __construct(PagesService $pagesService, HouseholdUrlService $householdUrlService)
    {
        $this->pagesService = $pagesService;
        $this->householdUrlService = $householdUrlService;
    }

    /**
     * Izveido mājsaimniecības lietotāja mājsaimniecības saiti, izmantojot lietotāja username,
     * balstoties uz pašreizējo valodas lapu
     */
    protected function householdShowUrl(User $user): string
    {
        return $this->householdUrlService->showUrl($user);
    }

    /**
     * Mājsaimniecības sākumlapa:
     * - ja lietotājam jau ir household → pāradresē uz lietotāja mājsaimniecības lapu
     * - ja nav → piedāvā izvēles skatu
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->activeHousehold()) {
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
        $household = $user->activeHousehold();

        $this->authorize('view', $household);

        $products = Product::select('id', 'name', 'measurement_type_id')->get();
        $productCategories = ProductCategory::select('id', 'name')->get();
        $units = Unit::all();

        //Visi mājsaimniecības produkti ar tā saistītajiem modeļiem
        $householdProducts = $household->householdProducts()
            ->with(['product.productCategory', 'product.measurementType'])
            ->get()
            ->map(function ($householdProduct) {
                $formatted = MeasurmentConversionService::fromBaseAmount(
                    $householdProduct->amount,
                    $householdProduct->product
                );

                return [
                    'id' => $householdProduct->id,
                    'productName' => $householdProduct->product->name,
                    'amount' => $formatted['amount'],
                    'unit' => $formatted['unit'],
                    'unitId' => $formatted['unit_id'],
                    'expirationDate' => $householdProduct->expiration_date,
                    'categoryName' => $householdProduct->product->productCategory->name,
                    'measurementTypeId' => $householdProduct->product->measurementType->id,
                ];
            });

        $userRole = $household->users()
            ->where('user_id', $user->id)
            ->first()
            ->pivot->role;

        return Inertia::render('Household/Show', [
            'household' => $household,
            'householdProducts' => $householdProducts,
            'products' => Product::select('id','name','measurement_type_id')->get(),
            'productCategories' => ProductCategory::select('id','name')->get(),
            'units' => Unit::all(),
            'userRole' => $userRole,
            'householdUsersCount' => $household->household_users_count,
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

        // Pārbaudam vai lietotājām jau ir mājsaimniecība
        $user =  $request->user();

        if($user->households()->exists()) {
            abort(403, 'User already has a household.');
        }

        // Piesaistām mājsaimniecību lietotājam
        $user->households()->attach($household->id, [
            'role' => 'owner',
        ]);

        // Pāradresējam uz mājsaimniecības lietotāja lapu
        return redirect(
            $this->householdShowUrl($request->user())
        )->with('success', 'Veiksmīgi izveidota mājsaimniecība');
    }

    public function update(Request $request, Household $household): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:30'],
        ]);

        $household->update($validated);

        return back()->with('success', 'Veiksmīgi atjaunota mājsaimniecības informācija');
    }

    public function leave(Request $request): RedirectResponse
    {
        $user = $request->user();
        $household = $user->activeHousehold();

        $this->authorize('leave', $household);

        // Ja lietotājs ir vienīgais īpašnieks, nododam lomu nākamajam dalībniekam
        $role = $household->users()
            ->where('user_id', $user->id)
            ->first()
            ->pivot->role;

        if ($role === 'owner') {
            $nextUser = $household->users()
                ->where('user_id', '!=', $user->id)
                ->first();

            $household->users()->updateExistingPivot($nextUser->id, ['role' => 'owner']);
        }

        $user->households()->detach($household->id);

        // Ja nav neviena lietotāja mājsaimniecībā to dzēst
        if ($household->users()->count() === 0) {
            $household->delete();
        }

        return redirect($this->householdUrlService->indexUrl())
            ->with('success', 'Veiksmīgi atvienojies no mājsaimniecības');
    }
}
