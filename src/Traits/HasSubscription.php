<?php

namespace Err0r\Larasub\Traits;

use Carbon\Carbon;
use Err0r\Larasub\Facades\SubscriptionHelperService;
use Err0r\Larasub\Models\PlanFeature;
use Err0r\Larasub\Models\Subscription;
use Err0r\Larasub\Models\SubscriptionFeatureUsage;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

trait HasSubscription
{
    use Subscribable;

    /**
     * Get the latest active subscription.
     *
     * @return Subscription|null
     */
    public function activeSubscription()
    {
        return $this->subscriptions()->active()->latest('start_at')->first();
    }

    /**
     * Check if the subscriber has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * Get features usage for the active subscription.
     *
     * @return Collection<SubscriptionFeatureUsage>
     */
    public function featuresUsage()
    {
        $subscription = SubscriptionHelperService::validateActiveSubscription($this);

        return $subscription->featuresUsage()->get();
    }

    /**
     * Get the usage of a specific feature for the active subscription.
     *
     * @return Collection<SubscriptionFeatureUsage>
     */
    public function featureUsage(string $slug)
    {
        $subscription = SubscriptionHelperService::validateActiveSubscription($this);

        return $subscription->featureUsage($slug)->get();
    }

    /**
     * Get a specific feature for the active subscriptions.
     *
     * @return PlanFeature|null
     */
    public function planFeature(string $slug)
    {
        return SubscriptionHelperService::validateActiveSubscription($this)->planFeature($slug);
    }

    /**
     * Check if the feature exists in the active subscription.
     */
    public function hasFeature(string $slug): bool
    {
        return SubscriptionHelperService::validateActiveSubscription($this)->hasFeature($slug);
    }

    /**
     * Check if features exist in the active subscription.
     */
    public function hasFeatures(iterable $slugs): bool
    {
        $subscription = SubscriptionHelperService::validateActiveSubscription($this);

        return collect($slugs)->every(fn ($slug) => $subscription->hasFeature($slug));
    }

    /**
     * Get the remaining usage of a specific feature for the active subscription.
     */
    public function remainingFeatureUsage(string $slug): ?float
    {
        return SubscriptionHelperService::validateActiveSubscription($this)
            ->remainingFeatureUsage($slug);
    }

    /**
     * Get the next time a feature will be available for use
     *
     * @param string $slug The feature slug to check
     * @return Carbon|bool|null
     *
     * @throws InvalidArgumentException
     *
     * @see Subscription::nextAvailableFeatureUsage
     */
    public function nextAvailableFeatureUsage(string $slug)
    {
        return SubscriptionHelperService::validateActiveSubscription($this)
            ->nextAvailableFeatureUsage($slug);
    }

    /**
     * Check if the feature is available for use in the active subscription.
     */
    public function canUseFeature(string $slug, float $value): bool
    {
        return SubscriptionHelperService::validateActiveSubscription($this)
            ->canUseFeature($slug, $value);
    }

    /**
     * Use a specific feature for the first applicable active subscription.
     *
     * @return SubscriptionFeatureUsage
     *
     * @throws InvalidArgumentException
     */
    public function useFeature(string $slug, float $value)
    {
        return SubscriptionHelperService::validateActiveSubscription($this)
            ->useFeature($slug, $value);
    }
}
