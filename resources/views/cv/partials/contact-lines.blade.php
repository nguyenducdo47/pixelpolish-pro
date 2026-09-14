@if(!empty($profile['date_of_birth']))
    <p class="contact-line">
        <img src="{{ $icons['calendar'] }}" width="10" height="10" class="contact-icon" alt="">
        <span class="contact-value">{{ $profile['date_of_birth'] }}</span>
    </p>
@endif

@if(!empty($profile['email']))
    <p class="contact-line">
        <img src="{{ $icons['envelope'] }}" width="10" height="10" class="contact-icon" alt="">
        <span class="contact-value">{{ $profile['email'] }}</span>
    </p>
@endif

@if(!empty($profile['phone']))
    <p class="contact-line">
        <img src="{{ $icons['phone'] }}" width="10" height="10" class="contact-icon" alt="">
        <span class="contact-value">{{ $profile['phone'] }}</span>
    </p>
@endif

@if(!empty($profile['location']))
    <p class="contact-line">
        <img src="{{ $icons['location'] }}" width="10" height="10" class="contact-icon" alt="">
        <span class="contact-value">{{ $profile['location'] }}</span>
    </p>
@endif

@if(!empty($profile['website']))
    <p class="contact-line">
        <img src="{{ $icons['website'] }}" width="10" height="10" class="contact-icon" alt="">
        <span class="contact-value">{{ $stripProtocol($profile['website']) }}</span>
    </p>
@endif

@if(!empty($github['url']))
    <p class="contact-line">
        <img src="{{ $icons['github'] }}" width="10" height="10" class="contact-icon" alt="">
        <span class="contact-value">{{ $stripProtocol($github['url']) }}</span>
    </p>
@endif
