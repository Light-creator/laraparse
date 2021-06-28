<header class="c-header c-header-light c-header-fixed c-header-with-subheader">
    <div class="container flex-header-container d-flex justify-content-center align-items-center">
        <button class="c-header-toggler c-class-toggler d-lg-none mr-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show"><span class="c-header-toggler-icon"></span></button><a class="c-header-brand d-sm-none" href="{{route("backend.dashboard")}}"><img class="c-header-brand" src="{{asset("img/backend-logo.jpg")}}" style="max-height:50px;min-height:40px;" alt="{{ app_name() }}"></a>
    
        <div class="first_header_block">
            <h3>Выбор источника</h3>
            <ol class="breadcrumb">
                @yield('breadcrumbs')
            </ol>
        </div>
    
        <div class="c-subheader-nav d-md-down-none mfe-2">
            <span class="c-subheader-nav-link">
                <div class="btn-group" role="group" aria-label="Button group">
                    <p class="date">{{ date_today() }}</p>&nbsp;<div id="liveClock" class="clock" onload="showTime()"></div>
                </div>
            </span>
        </div>
    
        {{-- <button class="c-header-toggler c-class-toggler ml-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true"><span class="c-header-toggler-icon"></span></button> --}}
    
        {{-- <ul class="c-header-nav d-md-down-none">
            <li class="c-header-nav-item px-3">
                <a class="c-header-nav-link" href="{{ route('frontend.index') }}" target="_blank">
                    <i class="c-icon cil-external-link"></i>&nbsp;
                    {{ app_name() }}
                </a>
            </li>
        </ul> --}}
    
        <ul class="c-header-nav ml-auto mr-4">
            <li class="c-header-nav-item dropdown d-md-down-none mx-2">
                <div class="lang-block">
                    <a class="choose-item {{ app()->getLocale() == 'ru' ? 'c-active-choose-item' : 'choose-item' }}" href="{{route("language.switch", "ru")}}">
                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQn-iuoDX56rDQ-SjxP7pZLSLhIboEZTrx6Sg&usqp=CAU" alt="">
                        ru
                    </a>
                    <a class="{{ app()->getLocale() == 'en' ? 'c-active-choose-item' : 'choose-item ' }}" href="{{route("language.switch", "en")}}">
                        <img src="https://image.flaticon.com/icons/png/512/330/330425.png" alt="">
                        en
                    </a>
                </div>
            </li>


            <li class="c-header-nav-item dropdown">
                <a class="c-header-nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <div class="c-avatar">
                        <i class="c-icon far fa-user"></i>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right pt-0">
                    <div class="dropdown-header bg-light py-2"><strong>@lang('Account')</strong></div>
    
                    <a class="dropdown-item" href="{{route('backend.users.profile', Auth::user()->id)}}">
                        <i class="c-icon cil-user"></i>&nbsp;
                        {{ Auth::user()->name }}
                    </a>
                    <a class="dropdown-item" href="{{route('backend.users.profile', Auth::user()->id)}}">
                        <i class="c-icon cil-at"></i>&nbsp;
                        {{ Auth::user()->email }}
                    </a>
    
                    <div class="dropdown-header bg-light py-2"><strong>@lang('Settings')</strong></div>
    
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;"> @csrf </form>
                </div>
            </li>
            <li class="c-header-nav-item">
                <a class="c-header-nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="c-avatar">
                        <i class="c-icon fas fa-cog"></i>
                    </div>
                </a>
            </li>
            <li class="c-header-nav-item">
                <a class="c-header-nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="c-avatar">
                        <i class="c-icon cil-account-logout"></i>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</header>

@push('after-scripts')
<script type="text/javascript">

$(function () {
    // Show the time
    showTime();
})

function showTime(){
    var date = new Date();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();

    minutes = minutes < 10 ? '0'+minutes : minutes;
    seconds = seconds < 10 ? '0'+seconds : seconds;

    var time = hours + ":" + minutes;
    document.getElementById("liveClock").innerText = time;
    document.getElementById("liveClock").textContent = time;

    setTimeout(showTime, 1000);
}
</script>
@endpush
