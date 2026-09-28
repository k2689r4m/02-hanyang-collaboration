<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/card.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery.atwho.css') }}" rel="stylesheet">
    <link href="{{ asset('css/selectordie.css') }}" rel="stylesheet">
    <link href="{{ asset('css/slick.css') }}" rel="stylesheet"/>

    <script src="{{ asset('js/app.js') }}" defer></script>

    <script type="text/javascript" src="{{ asset('js/jquery.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/jquery.caret.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/jquery.atwho.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/selectordie.js') }}" defer></script>
    <script type="text/javascript" src="{{ asset('js/slick.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">


    @yield('script')
    
    <!-- Styles -->
{{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
</head>

<body onclick="myInfoOff()">
    @php
        $routeName = Request::route()->getName();
        $directoryURI = $_SERVER['REQUEST_URI'];
        $path = parse_url($directoryURI, PHP_URL_PATH);
        $components = explode('/', $path);
        $first_part = $components[1];
    @endphp
    @if($routeName === 'home')
    <div id="app" class="main-app">
        <div class="header main-header" id="mobileFixed">
    @else
    <div id="app">
        <div class="header" id="mobileFixed">
    @endif
            <div class="header-left">
                <h1 class="header-logo"><a href="{{ route('home') }}"></a></h1>
{{--                <input type="text" class="input-search" placeholder="검색어를 두자 이상 입력하세요.">--}}
{{--                <button class="input-search__btn"></button>--}}
            </div>

            <div class="header-right">
                <ul class="header-menu">
                    @auth
                        <li class="header-menu__item">
                            <a href="javascript:;" @if(($first_part === 'myPage' && auth()->user()->authority === 1) || $first_part === 'dashboardStu' || $first_part === 'dashboardPro' || $first_part === 'dashboardCon' || $first_part === 'dashboardOut' || $first_part === 'lectureList') class="active" @endif>마이페이지</a>
                            <div class="depth-2">
                                @if(auth()->user()->authority === 1)
                                    <a href="{{ route('dashboardStuView') }}">대시보드</a>
                                    <a href="{{ route('myStuCom') }}">공모전</a>
                                    <a href="javascript:;">인증서</a>
                                @elseif(auth()->user()->authority === 2)
                                    <a href="{{ route('dashboardProView') }}">대시보드</a>
                                    <a href="{{route('lectureList') }}">수업</a>
                                @elseif(auth()->user()->authority === 4)
                                    <a href="{{ route('dashboardConView') }}">대시보드</a>
                                    <a href="{{route('lectureList') }}">수업</a>
                                @else
                                    <a href="{{ route('dashboardOutView') }}">대시보드</a>
                                    <a href="{{route('lectureList') }}">수업</a>
                                @endif
                            </div>
                        </li>
                        @if (in_array(auth()->user()->authority, [1, 2, 5, 6]))
                            <li class="header-menu__item">
                                @if(auth()->user()->authority === 2 || auth()->user()->authority === 6 || auth()->user()->authority === 4)
                                    <a href="{{ route('myPageView') }}" @if($first_part === 'myPage') class="active" @endif>수업준비</a>
                            </li>
                            <li class="header-menu__item">
                                    <a href="{{ route('myClassView') }}" @if($first_part === 'myClass') class="active" @endif>수업운영</a>
                                @else
                                    <a href="{{ route('myClassView') }}" @if($first_part === 'myClass') class="active" @endif>수업참여</a>
                                @endif
                                {{--                        {{ ['userId' => auth()->id()] }}--}}
{{--                                <div class="depth-2">--}}
{{--                                    @if(auth()->user()->authority === 2)--}}
{{--                                        <a href="{{ route('myPageView') }}">수업준비</a>--}}
{{--                                        <a href="{{ route('myClassView') }}">수업운영</a>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
                            </li>
                        @endif
{{--                        <li class="header-menu__item">--}}
{{--                            <a href="{{ route('searchView') }}">검색</a>--}}
{{--                        </li>--}}
                    @endauth
                    <li class="header-menu__item">
                        <a href="{{ route('firstNoticeView') }}" @if($first_part === 'notice') class="active" @endif>공지사항 및 문의</a>
                    </li>
                    @guest
                        <li class="header-menu__item">
                            <a href="{{ route('login') }}" class="btn-login">로그인</a>
                        </li>
                        @php
                            $routeName = Request::route()->getName();

                            $in_service = str_contains($routeName, 'notice') || str_contains($routeName, 'faq') || str_contains($routeName, 'req');
                        @endphp
                        @if($in_service)
{{--                            <li class="header-menu__item">--}}
{{--                                <a href="{{ route('home') }}">로그인</a>--}}
{{--                            </li>--}}
                        @endif
                    @endguest
                    @auth
                        <li class="header-menu__item my-info-wrap">
                            <button class="header-menu__user" id="myInfo" onclick="myInfoOn()">
                                <div class="img-wrap">
                                    <img src="{{ route('avatar', ['userId' => auth()->id()]) }}" />
                                </div>
                            </button>
                            <div class="depth-2 my-info">
                                <div class="my-profile">
                                    <div class="img-wrap">
                                        <img src="{{ route('avatar', ['userId' => auth()->id()]) }}" />
                                    </div>
                                    <p class="name">{{ auth()->user()->name }}</p>
                                    @if(auth()->user()->daehakNm != null)<p class="info">{{ auth()->user()->daehakNm }}</p>@endif
                                    @if(auth()->user()->hakgwaNm != null)<p class="info">{{ auth()->user()->hakgwaNm }}</p>@endif
                                    @if(auth()->user()->gaeinNo != null)<p class="info">{{ auth()->user()->gaeinNo }}</p>@endif
                                </div>
                                <a href="{{ route('profileView') }}">개인정보 수정</a>
                                <a onclick="btnOnClickedLogout()">로그아웃</a>
                            </div>
{{--                            <a href="{{ route('logout') }}" onclick="btnOnClickedLogout()">--}}
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    @endauth
                </ul>
{{--                <button class="header-menu__user"></button>--}}
            </div>
{{--            <div class="m-menu__left">--}}
{{--                <a href="{{ route('searchView') }}" class="search"></a>--}}
{{--            </div>--}}
            <div class="m-menu__right">
                <button type="button" class="menu" onclick="btnMobileMenuOn()"></button>
            </div>
            <div class="m-menu scroll-sm" id="mobileMenu">
                <button type="button" class="m-menu__close" onclick="btnMobileMenuOff()"></button>
                <div class="m-menu__top">
                    @auth
                    <div class="img-wrap" onclick="location.href='{{ route('profileView') }}'">
                        <img src="{{ route('avatar', ['userId' => auth()->id()]) }}" onerror="this.remove();" />
                        <div></div>
                    </div>
                    @if(auth()->user()->name)
                            <p class="name" onclick="location.href='{{ route('profileView') }}'">
                                <strong>{{auth()->user()->name}}</strong>
                                <a href="{{ route('profileView') }}" class="edit"></a>
                            </p>
                            @if(auth()->user()->daehakNm != null)<span class="info">{{ auth()->user()->daehakNm }}</span>@endif
                            @if(auth()->user()->hakgwaNm != null)<span class="info">{{ auth()->user()->hakgwaNm }}</span>@endif
                            @if(auth()->user()->gaeinNo != null)<p class="info">{{ auth()->user()->gaeinNo }}</p>@endif
{{--                        <button onclick="btnOnClickedLogout()" class="btn-login">로그아웃</button>--}}
                    @endif
                    @endauth
                    @guest
                        <div class="img-wrap">
                            <img src="" onerror="this.style.display='none';" />
                        </div>
                        <a href="{{ route('login') }}" class="btn-login">로그인</a>
                    @endguest
                </div>
                <ul class="m-menu__list">
                    @auth
                        @if(auth()->user()->authority === 4)
                            <li class="m-menu__item is-tit">
                                <h3 class="tit">마이페이지</h3>
                                <a href="{{ route('dashboardOutView') }}">대시보드</a>
                                <a href="{{ route('myPageView') }}">수업</a>
                            </li>
                        @else
                        <li class="m-menu__item is-tit">
                            <h3 class="tit">마이페이지</h3>
                            @if(auth()->user()->authority === 2)
                                <a href="{{ route('dashboardProView') }}">대시보드</a>
                                <a href="{{ route('lectureList') }}">수업</a>
                            </li>
                            <li class="m-menu__item is-tit">
                                <h3 class="tit">수업</h3>
                                <a href="{{route('myPageView') }}">수업준비</a>
                                <a href="{{route('myClassView') }}">수업운영</a>
                            @else
                                <a href="{{ route('dashboardStuView') }}">대시보드</a>
                                <a href="{{ route('myStuCom') }}">공모전</a>
                                <a href="javascript:;">인증서</a>
                            </li>
                            <li class="m-menu__item">
                                <a href="{{route('myClassView') }}">수업참여</a>
                            </li>
{{--                            <li class="m-menu__item">--}}
{{--                                <a href="javascript:;">인증센터</a>--}}
                            @endif
                        </li>
                        @endif
                    @endauth
                    <li class="m-menu__item">
                        <a href="{{ route('noticeView') }}">공지사항 및 문의</a>
                    </li>
                    @auth
                    <li class="m-menu__item">
                        <button type="button" class="btn" onclick="btnOnClickedLogout()">로그아웃</button>
                    </li>
                    @endauth
                </ul>
                <div class="m-menu__footer">
                    <h2 class="logo"></h2>
                    <div class="footer-menu">
                        <a href="https://site.hanyang.ac.kr/web/priv">개인정보처리방침</a>
                        <a href="javascript:;">이용약관</a>
                    </div>
                    <div class="footer-con">
                        ERICA캠퍼스 : 15588 경기도 안산시 상록구 한양대학로 55<br>
                        IC-PBL센터번호 : 031-400-4891~7<br>
                        COPYRIGHT ⓒ 2016 HANYANG UNIVERSITY. ALL RIGHTS RESERVED.
                    </div>
                </div>
            </div>
            <div class="m-menu__dim" onclick="btnMobileMenuOff()"></div>
        </div>
        <div class="content" id="popupScrollLock">
            @yield('content')
        </div>
        <footer class="footer">
            <div class="footer-wrap">
                <h2 class="footer-logo"></h2>
                <div class="footer-menu">
                    <a href="https://site.hanyang.ac.kr/web/priv" target="_blank">개인정보처리방침</a>
                    <a href="javascript:;">이용약관</a>
                </div>
                <div class="footer-con">
                    ERICA캠퍼스 : 15588 경기도 안산시 상록구 한양대학로 55<br />
                    IC-PBL센터번호 : 031-400-4891~7<br />
                    COPYRIGHT ⓒ 2016 HANYANG UNIVERSITY. ALL RIGHTS RESERVED.
                </div>
            </div>
        </footer>
    </div>

    <div id="logoutConfirm" class="popup confirm d-none">
        <div class="popup__dim" onclick="btnOnClickedCancel()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                로그아웃 하시겠습니까?
            </div>
            <div class="confirm-btn">
                <button id="btnConfirmCancel" type="button" class="btn w-50 fc-gray" onclick="btnOnClickedCancel()">취소</button>
                <button id="btnCommitPeed" class="btn w-50" href="{{ route('logout') }}" onclick="
                        event.preventDefault();
                        document.getElementById('logout-form').submit();"
                >확인</button>
            </div>
        </div>
    </div>

    <div id="modalClass" class="popup confirm d-none">
        <div id="btnModalCancel_" class="popup__dim"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                @if($errors->any())
                    {!! nl2br($errors->first()) !!}
                @endif
            </div>
            <div class="confirm-btn">
                <button id="btnModalCancel" type="button" class="btn w-100 fc-gray">확인</button>
            </div>
        </div>
    </div>
</body>



<script type="text/javascript">
    window.onscroll = () =>{
        if(document.getElementsByClassName('main-header').length){
            if(document.documentElement.scrollTop > 1){
                document.getElementsByClassName('main-header')[0].classList.add('scroll');
            }else{
                document.getElementsByClassName('main-header')[0].classList.remove('scroll');
            }
        }
    }
    function btnOnClickedLogout(){
        document.getElementById('logoutConfirm').classList.remove('d-none');
    }

    function btnOnClickedCancel(){
        document.getElementById('logoutConfirm').classList.add('d-none');
    }

    function btnMobileMenuOn(){
        document.getElementById('mobileMenu').classList.add('open');
    }
    function btnMobileMenuOff(){
        document.getElementById('mobileMenu').classList.remove('open');
    }
    function myInfoOn(){
        event.stopImmediatePropagation();
        document.getElementById('myInfo').classList.toggle('open');
    }
    function myInfoOff(){
        document.getElementById('myInfo') ? document.getElementById('myInfo').classList.remove('open') : '';
    }

    @if($errors->any())
    const confirmModal = document.getElementById("modalClass");
    confirmModal.classList.remove("d-none");

    const confirmModalOk = document.getElementById("btnModalCancel");
    const confirmModalOk_ = document.getElementById("btnModalCancel_");

    confirmModalOk.addEventListener("click", () => {
        confirmModal.classList.add("d-none");
    })

    confirmModalOk_.addEventListener("click", () => {
        confirmModal.classList.add("d-none");
    })
    @endif
</script>

</html>
