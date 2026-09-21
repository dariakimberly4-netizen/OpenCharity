<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex flex-col gap-2">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-primary-600">
                    Government Payout Workflow
                </p>
                <h2 class="text-2xl font-bold text-gray-950 dark:text-white">
                    {{ $this->getTitle() }}
                </h2>
                <p class="max-w-3xl text-sm leading-6 text-gray-600 dark:text-gray-300">
                    {{ static::getStageDescription() }}
                </p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                ['Verification', 'Validate applications'],
                ['Assessment', 'Evaluate eligibility'],
                ['Approval', 'Authorize assistance'],
                ['Payout', 'Prepare payment'],
                ['Released', 'Confirm release'],
                ['Reports', 'Review summaries'],
                ['Audit Trail', 'Track every action'],
            ] as [$name, $description])
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <div class="text-sm font-semibold text-gray-950 dark:text-white">{{ $name }}</div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $description }}</div>
                </div>
            @endforeach
        </div>

        <div class="rounded-2xl border border-primary-200 bg-primary-50 p-5 text-sm text-primary-900 dark:border-primary-500/20 dark:bg-primary-500/10 dark:text-primary-100">
            Workflow order:
            <strong>Verification → Assessment → Approval → Payout → Released → Reports → Audit Trail</strong>
        </div>
    </div>
</x-filament-panels::page>
