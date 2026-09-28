@extends('layouts.layout')

@section('script')
@endsection

@section('content')
    <div class="container">
        <div class="container__wrap">
            <div class="contents__wrap p-40 w-800">
                @foreach($faqs as $faq)
                    <h3 class="board-tit">
                        Q. {{ $faq->title }}
                    </h3>
{{--                    <p>{{ date('Y.m.d', strtotime($faq->created_at)) }}</p>--}}
                    <div class="board-con">
                        <img />
                        <div class="txt">{{ $faq->content }}</div>
                    </div>
                @endforeach
                <div class="t-center">
                {{ $faqs->withQueryString()->links(('vendor.pagination.tailwind2')) }}
                </div>
                   

            </div>
            <div class="contents__wrap p-40 w-800 mt-10">
                <div>
                    <h3 class="board-tit type2">관리자의 답변</h3>
                    {{ $faq->adminAnswer }}
                </div>
            </div>
        </div>
    </div>
@endsection
