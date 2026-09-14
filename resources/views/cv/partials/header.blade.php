@php

    $isSidebar = ($layout ?? 'modern') === 'sidebar';

    $isClassic = ($layout ?? 'modern') === 'classic';

    $isModern = ! $isSidebar && ! $isClassic;

    $headerClass = match ($layout ?? 'modern') {

        'classic' => 'classic-header',

        'sidebar' => 'sidebar-header',

        default => 'modern-header',

    };

@endphp



@if($isModern)

    <div class="cv-modern-accent"></div>

@endif



@if($isSidebar)

    <div class="cv-sidebar-profile">

        @if(!empty($settings['show_avatar']) && !empty($profile['avatar']))

            <img src="{{ $profile['avatar'] }}" width="80" height="80" loading="lazy" alt="">

        @endif

        <h1>{{ $profile['full_name'] ?? '' }}</h1>

        @if(!empty($profile['headline']))

            <p class="sidebar-headline">{{ $profile['headline'] }}</p>

        @endif

    </div>

    <div class="cv-sidebar-divider"></div>

    <div class="cv-sidebar-block">

        <div class="contact-list sidebar-contact">

            @include('cv.partials.contact-lines', compact('profile', 'icons', 'github', 'stripProtocol'))

        </div>

    </div>

@else

<div class="header {{ $headerClass }}">

    @if($isClassic)

        <hr class="cv-classic-rule classic-rule" />

        @if(!empty($settings['show_avatar']) && !empty($profile['avatar']))

            <div class="classic-avatar">

                <img src="{{ $profile['avatar'] }}" width="72" height="72" loading="lazy">

            </div>

        @endif

        <h1 class="cv-classic-name classic-name">{{ $profile['full_name'] ?? '' }}</h1>

        @if(!empty($profile['headline']))

            <p class="cv-classic-headline classic-headline muted">{{ $profile['headline'] }}</p>

        @endif

        <hr class="cv-classic-rule classic-rule" />

    @endif



    <div class="header-row">

        @if($isModern)

            <div class="cv-modern-intro modern-intro">

                @if(!empty($settings['show_avatar']) && !empty($profile['avatar']))

                    <div class="cv-modern-avatar modern-avatar">

                        <img src="{{ $profile['avatar'] }}" width="72" height="72" loading="lazy">

                    </div>

                @endif

                <div class="modern-identity">

                    <h1>{{ $profile['full_name'] ?? '' }}</h1>

                    <p class="cv-modern-headline modern-headline">{{ $profile['headline'] ?? '' }}</p>

                </div>

            </div>

        @endif



        <div class="header-contact {{ $isClassic ? 'classic-contact classic-contact-list' : 'modern-contact' }}">

            <div class="contact-list {{ $isClassic ? 'classic-contact-list' : '' }}">

                @include('cv.partials.contact-lines', compact('profile', 'icons', 'github', 'stripProtocol'))

            </div>

        </div>

    </div>



    @if($isClassic)

        <hr class="cv-classic-rule cv-classic-rule-strong classic-rule classic-rule-strong" />

    @endif

</div>

@endif

