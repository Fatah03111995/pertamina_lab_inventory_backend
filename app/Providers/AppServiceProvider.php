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
        // Register model observers to enforce domain invariants at model level.
        GasCompany::observe(GasCompanyObserver::class);
        GasLocation::observe(GasLocationObserver::class);
        GasType::observe(GasTypeObserver::class);

        // Global renderable for domain invariant exceptions.
        // This maps App\Exceptions\InvariantViolationException to a friendly response:
        // - JSON requests -> 422 JSON with message
        // - Web requests -> redirect back with flash `error` (can be displayed as toast)
        $handler = $this->app->make(\Illuminate\Contracts\Debug\ExceptionHandler::class);
        // Some Laravel / custom Handler implementations may not expose `renderable` on the
        // concrete exception handler referenced by the contract. Guard the call to avoid
        // "undefined method" errors in environments where it's not available.
        if (method_exists($handler, 'renderable')) {
            $callback = function (\App\Exceptions\InvariantViolationException $e, $request) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['message' => $e->getMessage()], 422);
                }
                return redirect()->back()->with('error', $e->getMessage());
            };

            // Use reflection to invoke `renderable` to avoid static analysis errors
            // when the concrete handler is typed as the exception handler contract.
            $ref = new \ReflectionMethod($handler, 'renderable');
            $ref->invoke($handler, $callback);
        }
    }
}
