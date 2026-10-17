<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Jobs;

use App\Models\JobPost;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::site')]
class Show extends Component
{
    public JobPost $job;

    public function mount(JobPost $job): void
    {
        abort_unless($job->is_active, 404);

        $this->job = $job->loadMissing('country');
    }

    public function render(): View
    {
        $closed = $this->job->deadline !== null && $this->job->deadline->lt(today());

        return view('livewire.pages.jobs.show', [
            'closed' => $closed,
            'salary' => $this->salaryText(),
        ])->title($this->job->title.($this->job->country ? ' – '.$this->job->country->name : ''))
            ->layoutData([
                'description' => $this->job->summary,
                'jsonLd' => Seo::jobPosting($this->job),
            ]);
    }

    private function salaryText(): ?string
    {
        $min = $this->job->salary_min;
        $max = $this->job->salary_max;
        $cur = $this->job->salary_currency;

        if ($min === null && $max === null) {
            return null;
        }

        $range = ($min !== null && $max !== null && $min !== $max)
            ? number_format($min).' – '.number_format($max)
            : number_format((int) ($min ?? $max));

        return trim(($cur ? $cur.' ' : '').$range).' / month';
    }
}
