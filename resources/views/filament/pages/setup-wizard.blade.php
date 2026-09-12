<x-filament-panels::page>
    <style>
        .setup-wizard .fi-sc-wizard-header {
            display: flex !important;
            flex-wrap: wrap;
            gap: 0.5rem;
            overflow: visible !important;
            padding: 0.75rem;
        }

        .setup-wizard .fi-sc-wizard-header-step {
            flex: 1 1 9rem;
            min-width: 8.5rem;
            max-width: 100%;
        }

        .setup-wizard .fi-sc-wizard-header-step-separator {
            display: none !important;
        }

        .setup-wizard .fi-sc-wizard-header-step-btn {
            width: 100%;
            column-gap: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.75rem;
        }

        .setup-wizard .fi-sc-wizard-header-step-icon-ctn {
            width: 1.75rem !important;
            height: 1.75rem !important;
        }

        .setup-wizard .fi-sc-wizard-header-step-icon-ctn .fi-icon {
            width: 1rem;
            height: 1rem;
        }

        .setup-wizard .fi-sc-wizard-header-step-text {
            width: auto !important;
            max-width: 100% !important;
        }

        .setup-wizard .fi-sc-wizard-header-step-description {
            display: none;
        }

        .setup-wizard .fi-sc-wizard-header-step.fi-active .fi-sc-wizard-header-step-btn {
            background: color-mix(in oklab, var(--primary-500) 16%, transparent);
            box-shadow: inset 0 0 0 1px color-mix(in oklab, var(--primary-600) 55%, transparent);
        }

        .setup-wizard .fi-sc-wizard-header-step.fi-active .fi-sc-wizard-header-step-label {
            color: var(--primary-700) !important;
            font-weight: 650;
        }

        .dark .setup-wizard .fi-sc-wizard-header-step.fi-active .fi-sc-wizard-header-step-btn {
            background: color-mix(in oklab, var(--primary-400) 22%, transparent);
            box-shadow: inset 0 0 0 1px color-mix(in oklab, var(--primary-400) 50%, transparent);
        }

        .dark .setup-wizard .fi-sc-wizard-header-step.fi-active .fi-sc-wizard-header-step-label {
            color: var(--primary-300) !important;
        }
    </style>

    {{ $this->content }}
</x-filament-panels::page>
