<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\GasCompany;
use App\Models\GasLocation;
use App\Models\GasType;
use App\Observers\GasCompanyObserver;
use App\Observers\GasLocationObserver;
use App\Observers\GasTypeObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        GasCompany::observe(GasCompanyObserver::class);
        GasLocation::observe(GasLocationObserver::class);
        GasType::observe(GasTypeObserver::class);

        $handler = $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);

        if (method_exists($handler, 'renderable')) {
            $callback = function (\App\Exceptions\InvariantViolationException $e, $request) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $e->getMessage()], 422);
                }
                return redirect()->back()->with('error', $e->getMessage());
            };
            $ref = new \ReflectionMethod($handler, 'renderable');
            $ref->invoke($handler, $callback);
        }
    }
}
