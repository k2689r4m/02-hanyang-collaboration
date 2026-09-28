@extends('layouts.layout')
<script type="module">
        {{--import Card from '{{ asset('js/restful/Card.js') }}'--}}
        {{--import Item from '{{ asset('js/restful/Item.js') }}'--}}

        {{--window.Card = new Card('{{ route('home') }}', '{{ csrf_token() }}')--}}
        {{--window.Item = new Item('{{ route('home') }}', '{{ csrf_token() }}')--}}
    import Rest from '{{ asset('js/restful/Rest.js') }}'
    window.Rest = new Rest('{{ route('home') }}', '{{ csrf_token() }}')
</script>


@section('content')
    <my_page_main
        :user="{{auth()->user()}}"
        :my-page-id="{{ $myPage->id }}"
        :classes="{{ $myClasses }}"
        :daehaks="{{ $daehaks }}"
    ></my_page_main>
@endsection

{{--<div id="modalClass" class="popup confirm d-none">--}}
{{--    <div id="btnModalCancel_" class="popup__dim" onclick=""></div>--}}
{{--    <div class="popup-wrap">--}}
{{--        <div class="confirm-txt">--}}
{{--            {{session()->has('myClassNotFound')}}--}}
{{--        </div>--}}
{{--        <div class="confirm-btn">--}}
{{--            <button id="btnModalCancel" type="button" class="btn w-50 fc-gray">확인</button>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
