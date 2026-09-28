@extends('layouts.service')
@section('script')


@endsection

@section('_content')

    <div class="contents__wrap">
        <div class="t-right mb-20">
            @if(Auth::check())
            <button class="btn btn-primary btn-md" onclick="location.href='{{ route('reqWriteView') }}'">문의하기</button>
            @endif
        </div>
        <table class="list-table">
            <colgroup>
                <col width="10%" />
                <col width="65%" />
                <col width="15%" />
                <col width="10%" />
            </colgroup>
            <thead>
            <th>번호</th>
            <th class="left">제목</th>
            <th>작성일</th>
            <th>답변 상태</th>
            </thead>
            <tbody>
            @if($reqs)
                @foreach($reqs as $req)
                    <tr>
                        <td>{{ $req->id }}</td>
                        <td class="left"><a href="{{ route('reqDetailView', ['reqId' => $req->id, 'listPage' => $reqs->currentPage()]) }}">{{ $req->title }}{{ is_null($req->adminAnswer) ? '' : '(1)' }}</td></td>
                        <td>{{ date('Y.m.d', strtotime($req->created_at)) }}</td>
                        <td>{{ $req->adminAnswer ? '답변완료' : '미답변' }}</td>
                    </tr>
                @endforeach
            @else
            <tr>
                <td></td>
                <td class="left"></td></td>
                <td></td>
                <td></td>
            </tr>
            @endif
            </tbody>
        </table>
        {{ $reqs->links(('vendor.pagination.tailwind')) }}
    </div>
@endsection

