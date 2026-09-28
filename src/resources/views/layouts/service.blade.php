@extends('layouts.layout')

@section('script')
    @yield('_script')
@endsection

@section('content')
{{--    access denied service modal--}}
    @error('service error')
    <div id="mainConfirmModal" class="popup confirm">
        <div class="popup__dim" onclick="mainConfirmModalOff(this)"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                {{ $message }}
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-100" onclick="mainConfirmModalOff(this)">확인</button>
            </div>
        </div>
    </div>
    @enderror

    <div class="container">
        <div class="container__wrap">


            <div class="container__wrap">
                <h3 class="container-tit">
                    <span>
                    @php
                        $routeName = Request::route()->getName();
                    @endphp
                    @if(str_contains($routeName, 'notice'))
                        공지사항
                    @elseif(str_contains($routeName, 'firstNoticeView'))
                        공지사항
                    @elseif(str_contains($routeName, 'faq'))
                        FAQ
                    @elseif(str_contains($routeName, 'req'))
                        1:1문의
                    @endif
                    </span>
                </h3>
            </div>

            <ul class="container-tab">
                <li class="@if(str_contains($routeName, 'notice')) active @endif" onclick="location.href='{{ route('noticeView') }}'">
                    공지사항
                </li>
                <li class="@if(str_contains($routeName, 'faq')) active @endif" onclick="location.href='{{ route('faqView') }}'">
                    FAQ
                </li>
                <li class="@if(str_contains($routeName, 'req')) active @endif" onclick="location.href='{{ route('reqView') }}'">
                    1:1문의
                </li>
            </ul>
            @yield('_content')
        </div>
    </div>
@endsection

<script type="text/javascript">
    const mainConfirmModalOff = (target) => {
        document.querySelector('#mainConfirmModal').remove();
    }
</script>
