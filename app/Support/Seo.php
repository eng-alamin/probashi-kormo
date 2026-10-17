<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\JobPost;

/**
 * Structured data (JSON-LD) for search engines.
 */
final class Seo
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function json(array $data): string
    {
        // HEX flags keep "</script>" and quotes inside values from breaking out of the tag.
        return (string) json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function organization(): array
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'EmploymentAgency',
            'name' => config('site.name'),
            'url' => url('/'),
            'logo' => asset('site/img/logo.jpg'),
            'description' => config('site.tagline'),
        ];

        if (! empty(config('site.facebook'))) {
            $data['sameAs'] = [config('site.facebook')];
        }

        if (Site::phone() !== null) {
            $data['telephone'] = Site::phone();
        }

        if (Site::email() !== null) {
            $data['email'] = Site::email();
        }

        if (Site::address() !== null) {
            $data['address'] = ['@type' => 'PostalAddress', 'streetAddress' => Site::address()];
        }

        return $data;
    }

    /**
     * Returns null for a closed job: a finished opening must not be advertised.
     *
     * @return array<string, mixed>|null
     */
    public static function jobPosting(JobPost $job): ?array
    {
        if (! $job->is_active || ($job->deadline !== null && $job->deadline->lt(today()))) {
            return null;
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => $job->description ?: ($job->summary ?: $job->title),
            'datePosted' => $job->created_at?->toDateString() ?? today()->toDateString(),
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => config('site.name'),
                'sameAs' => url('/'),
                'logo' => asset('site/img/logo.jpg'),
            ],
            'directApply' => true,
        ];

        if ($job->deadline !== null) {
            $data['validThrough'] = $job->deadline->toDateString();
        }

        if ($job->country !== null) {
            $data['jobLocation'] = [
                '@type' => 'Place',
                'address' => ['@type' => 'PostalAddress', 'addressCountry' => $job->country->name],
            ];
        }

        $min = $job->salary_min;
        $max = $job->salary_max;

        if (($min !== null || $max !== null) && ! empty($job->salary_currency)) {
            $value = ['@type' => 'QuantitativeValue', 'unitText' => 'MONTH'];

            if ($min !== null && $max !== null && $min !== $max) {
                $value['minValue'] = $min;
                $value['maxValue'] = $max;
            } else {
                $value['value'] = $min ?? $max;
            }

            $data['baseSalary'] = [
                '@type' => 'MonetaryAmount',
                'currency' => $job->salary_currency,
                'value' => $value,
            ];
        }

        return $data;
    }
}
