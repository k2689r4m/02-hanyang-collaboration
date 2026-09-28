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
        @if($classApply->state === 'wait') <li class="page-nav__item"><a href="{{ route('admin.class1View') }}">수업(대기)</a></li>
        @elseif($classApply->state === 'complete') <li class="page-nav__item"><a href="{{ route('admin.class3View') }}">수업(진행중)</a></li>
        @elseif($classApply->state === 'end')<li class="page-nav__item"><a href="{{ route('admin.class2View') }}">수업(수업완료)</a></li>
        @else<li class="page-nav__item"><a href="{{ route('admin.class1View') }}">수업</a></li>
        @endif
        @if(strpos($routeName, 'classDetail1View'))<li class="page-nav__item">정보</li>@endif
        @if(strpos($routeName, 'classDetail2View'))<li class="page-nav__item">참여자</li>@endif
        @if(strpos($routeName, 'classDetail3View'))<li class="page-nav__item">팀배정</li>@endif
        @if(strpos($routeName, 'classDetail4View'))<li class="page-nav__item">문제분석</li>@endif
        @if(strpos($routeName, 'classDetail5View'))<li class="page-nav__item">팀활동보고서</li>@endif
        @if(strpos($routeName, 'classDetail6View'))<li class="page-nav__item">평가지</li>@endif
        @if(strpos($routeName, 'classDetail7View'))<li class="page-nav__item">성찰</li>@endif
        @if(strpos($routeName, 'classDetail8View'))<li class="page-nav__item">통계</li>@endif
    </ul>
    <ul class="nav nav-tabs">
        <li class="nav-item"><a href="{{ route('admin.classDetail1View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail1View')) active @endif border-left-0">정보</a></li>
        @if($classApply->classObjectId && $classApply->state = 'wait')
            <li class="nav-item"><a href="{{ route('admin.classDetail2View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail2View')) active @endif border-left-0">참여자</a></li>
            <li class="nav-item"><a href="{{ route('admin.classDetail3View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail3View')) active @endif border-left-0">팀배정</a></li>
            <li class="nav-item"><a href="{{ route('admin.classDetail4View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail4View')) active @endif border-left-0">문제분석</a></li>
            <li class="nav-item"><a href="{{ route('admin.classDetail5View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail5View')) active @endif border-left-0">팀활동보고서</a></li>
            <li class="nav-item"><a href="{{ route('admin.classDetail6View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail6View')) active @endif border-left-0">평가지</a></li>
            <li class="nav-item"><a href="{{ route('admin.classDetail7View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail7View')) active @endif border-left-0">성찰</a></li>
            <li class="nav-item"><a href="{{ route('admin.classDetail8View', ['classApplyId' => $classApply->id]) }}" class="nav-link @if(strpos($routeName, 'classDetail8View')) active @endif border-left-0">통계</a></li>
        @endif
    </ul>
{{--        <ul class="page-nav">--}}
{{--            @if(strpos($routeName, 'classDetail7DetailView'))--}}
{{--                <li class="page-nav__item">--}}
{{--                    <a href="{{ route('admin.classDetail7View', ['classApplyId' => $classApply->id]) }}">--}}
{{--                        <h3>목록으로</h3>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @else--}}
{{--            <li class="page-nav__item"><a href="{{ route('admin.class1View') }}"><h3>수업관리</h3></a></li>--}}
{{--            @endif--}}

{{--        </ul>--}}

    @yield('_content')
@endsection