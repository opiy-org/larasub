<?php

declare(strict_types=1);

namespace Err0r\Larasub;

use Err0r\Larasub\Commands\CheckEndingSubscriptions;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LarasubServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('larasub')
            ->hasConfigFile()
            ->hasMigrations([
                'create_plans_table',
                'create_features_table',
                'create_plan_features_table',
                'create_subscriptions_table',
                'create_subscription_feature_usage_table',
                'create_events_table',
            ])
            ->hasCommand(CheckEndingSubscriptions::class);
    }

    public function packageBooted(): void
    {
        $this->app->booted(function () {
            /** @var Schedule */
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('larasub:check-ending-subscriptions')
                ->everyThreeHours()
                ->withoutOverlapping()
                ->when(config('larasub.scheduling.enabled'));
        });
    }
}
