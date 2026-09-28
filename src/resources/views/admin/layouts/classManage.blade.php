@extends('admin.layouts.admin')

@section('script')
    @yield('_script')
@endsection

@section('content')
    @php
        $routeName = Request::route()->getName();
    @endphp
    <ul class="page-nav class">
        <li class="page-nav__item"><a href="{{ route('admin.dashboardView') }}"><i class="fas fa-home"></i></a></li>
        @if(str_contains($routeName, 'classStatisticsView')) <li class="page-nav__item"><a href="{{ route('admin.classStatisticsView') }}">수업(통계)</a></li>
        @elseif(str_contains($routeName, 'class1')) <li class="page-nav__item"><a href="{{ route('admin.class1View') }}">수업(대기)</a></li>
        @elseif(str_contains($routeName, 'class2')) <li class="page-nav__item"><a href="{{ route('admin.class2View') }}">수업(승인완료)</a></li>
        @elseif(str_contains($routeName, 'class3')) <li class="page-nav__item"><a href="{{ route('admin.class3View') }}">수업(진행중)</a></li>
        @elseif(str_contains($routeName, 'class3ProfessorView')) <li class="page-nav__item"><a href="{{ route('admin.class3ProfessorView') }}">수업(진행중/교수)</a></li>
        @elseif(str_contains($routeName, 'class4')) <li class="page-nav__item"><a href="{{ route('admin.class4View') }}">수업(수업완료)</a></li>
        @elseif(str_contains($routeName, 'classCertificateView')) <li class="page-nav__item"><a href="{{ route('admin.classCertificateView') }}">수업(인증서)</a></li>
        @else
            <li class="page-nav__item"><a href="{{ route('admin.class1View') }}">수업관리</a></li>
        @endif
    </ul>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a href="{{ route('admin.classStatisticsView') }}" class="nav-link @if(str_contains($routeName, 'admin.classStatisticsView')) active @endif border-left-0">통계</a></li>
        <li class="nav-item"><a href="{{ route('admin.class1View') }}" class="nav-link @if(str_contains($routeName, 'class1')) active @endif border-left-0">대기</a></li>
        <li class="nav-item"><a href="{{ route('admin.class2View') }}" class="nav-link @if(str_contains($routeName, 'class2')) active @endif">승인완료</a></li>
        <li class="nav-item"><a href="{{ route('admin.class3View') }}" class="nav-link @if(str_contains($routeName, 'class3')) active @endif">진행중</a></li>
        <li class="nav-item"><a href="{{ route('admin.class4View') }}" class="nav-link @if(str_contains($routeName, 'class4')) active @endif">수업완료</a></li>
        <li class="nav-item"><a href="{{ route('admin.classCertificateView') }}" class="nav-link @if(str_contains($routeName, 'classCertificateView')) active @endif">인증서</a></li>
    </ul>
{{--    <ul class="page-nav">--}}
{{--        <li class="page-nav__item"><a href="{{ route('admin.class1View') }}">수업관리이동</a></li>--}}
{{--    </ul>--}}
    @yield('_content')
@endsection