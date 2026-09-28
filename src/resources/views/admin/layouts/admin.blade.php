<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script src="{{ asset('js/app.js') }}" defer></script>

        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/fontawesome-all.css') }}">
        @yield('script')
    </head>
    <body>

{{--    <ul class="page-nav">--}}
{{--        @yield('naviBar')--}}
{{--        <li class="page-nav__item"><a href="{{ route('admin.dashboardView') }}"><i class="fas fa-home"></i></a></li>--}}
{{--    </ul>--}}

        <header class="navbar navbar-expand-lg bg-white fixed-top">
            <a class="navbar-brand">ADMIN</a>
            <div class="navbar-collapse">
                @php
                    $routeName = Request::route()->getName();
                @endphp
                <ul class="navbar-nav ml-auto navbar-right-top">
                    @if(Auth::user()->authority == '3')
                    <li class="nav-item @if(strpos($routeName, 'apiButton')) active @endif" onclick="location.href='{{ route('admin.apiButton') }}'">API 추가</li>
                    <li class="nav-item @if(strpos($routeName, 'dashboard')) active @endif" onclick="location.href='{{ route('admin.dashboardView') }}'">대시보드</li>
                    <li class="nav-item @if(strpos($routeName, 'class')) active @endif" onclick="location.href='{{ route('admin.class1View') }}'">수업</li>
                    <li class="nav-item @if(strpos($routeName, 'member')) active @endif" onclick="location.href='{{ route('admin.memberView') }}'">참여자</li>
                    <li class="nav-item @if(strpos($routeName, 'basic')) active @endif" onclick="location.href='{{ route('admin.basic1View') }}'">기초교육</li>
                    <li class="nav-item @if(strpos($routeName, 'consulting')) active @endif" onclick="location.href='{{ route('admin.consulting1View') }}'">컨설팅</li>
                    <li class="nav-item @if(strpos($routeName, 'competition')) active @endif" onclick="location.href='{{ route('admin.competition1View') }}'">프로그램</li>
{{--                    <li class="nav-item @if(strpos($routeName, 'notice')) active @endif" onclick="location.href='{{ route('admin.noticeView') }}'">공지</li>--}}
{{--                    <li class="nav-item @if(strpos($routeName, 'faq')) active @endif" onclick="location.href='{{ route('admin.faqView') }}'">FAQ관리</li>--}}
{{--                    <li class="nav-item @if(strpos($routeName, 'req')) active @endif" onclick="location.href='{{ route('admin.reqView') }}'">시스템문의</li>--}}
                    <li class="nav-item @if(strpos($routeName, 'req') || strpos($routeName, 'notice') || strpos($routeName, 'faq')) active @endif" onclick="location.href='{{ route('admin.reqView') }}'">시스템문의</li>
{{--                    @elseif(Auth::user()->authority == '3')--}}
{{--                        <li class="nav-item @if(strpos($routeName, 'class')) active @endif" onclick="location.href='{{ route('admin.class1View') }}'">수업관리</li>--}}
                    @else
                        <li class="nav-item @if(strpos($routeName, 'pwd')) active @endif" onclick="location.href='{{ route('admin.pwdCheckView') }}'">개인정보 변경</li>
                    @endif

                        <li class="nav-item">
                        <button class="btn badge badge-light" onclick="document.getElementById('logout-form').submit()">
                            로그아웃
                        </button>
                    </li>
                </ul>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                    @csrf
                </form>
            </div>
        </header>
        <div class="dashboard-content col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12" id="app">
            @if(!strpos($routeName, 'class'))
                <ul class="page-nav">
                    <li class="page-nav__item"><a href="{{ route('admin.dashboardView') }}"><i class="fas fa-home"></i></a></li>
                    @if(strpos($routeName, 'dashboard'))
                        <li class="page-nav__item"><a href="{{ route('admin.dashboardView') }}">대시보드</a></li>
                    @elseif(strpos($routeName, 'member'))
                        <li class="page-nav__item"><a href="{{ route('admin.memberView') }}">참여자</a></li>
                    @elseif(strpos($routeName, 'basic'))
                        <li class="page-nav__item"><a href="{{ route('admin.basic1View') }}">기초교육</a></li>
                    @elseif(strpos($routeName, 'consulting'))
                        <li class="page-nav__item"><a href="{{ route('admin.consulting1View') }}">컨설팅</a></li>
                    @elseif(strpos($routeName, 'competition'))
                        <li class="page-nav__item"><a href="{{ route('admin.competition1View') }}">공모전</a></li>
                    @elseif(strpos($routeName, 'notice'))
                        <li class="page-nav__item"><a href="{{ route('admin.noticeView') }}">공지</a></li>
                    @elseif(strpos($routeName, 'faq'))
                        <li class="page-nav__item"><a href="{{ route('admin.faqView') }}">FAQ</a></li>
                    @elseif(strpos($routeName, 'req'))
                        <li class="page-nav__item"><a href="{{ route('admin.reqView') }}">1:1문의</a></li>
                    @elseif(strpos($routeName, 'pwd'))
                        <li class="page-nav__item"><a href="{{ route('admin.pwdCheckView') }}">비밀번호변경</a></li>
                        {{--                    @elseif(strpos($routeName, '통계관리'))--}}
                        {{--                        <li class="page-nav__item"><a href="{{ route('admin.dashboardView') }}">통계관리</a></li>--}}
                    @else
                    @endif
                </ul>
            @endif

            <div class="card simple-card">
                {{--내비게이션--}}



        @yield('content')
            </div>
        </div>
    @yield('graphTest')
    </body>
</html>

