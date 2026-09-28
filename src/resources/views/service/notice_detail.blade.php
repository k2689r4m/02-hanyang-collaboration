@extends('layouts.layout')

@section('script')
@endsection

@section('content')
    <div class="container">
        <div class="container__wrap">
            <div class="contents__wrap p-40 w-800">
            @foreach($notices as $notice)
                <h3 class="board-tit">
                    {{ $notice->title }}
                </h3>
                <p class="board-date">{{ date('Y.m.d', strtotime($notice->created_at)) }}</p>
                <div class="board-con">
                    <img
                    @if($notice->imagePathName)
                        src="{{ route('notice.image', ['imagePathName' => $notice->imagePathName]) }}"
                    @endif
                    />
                    <div class="txt">{{ $notice->content }}</div>
                </div>
            @endforeach
                <div class="t-center">
                {{ $notices->withQueryString()->links(('vendor.pagination.tailwind2')) }}
                </div>
            </div>
        </div>
    </div>
@endsection
