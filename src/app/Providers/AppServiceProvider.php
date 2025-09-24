<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Observers\LogModelChanges;
use Laravel\Passport\Passport;
use Carbon\CarbonInterval;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Passport::ignoreRoutes();
    }
 

    public function boot()
    {
        Passport::tokensExpireIn(CarbonInterval::days(15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));

        Product::observe(LogModelChanges::class);
        Category::observe(LogModelChanges::class);
        Supplier::observe(LogModelChanges::class);
    }
}
