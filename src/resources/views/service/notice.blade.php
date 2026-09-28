@extends('layouts.service')

@section('_content')
    <div class="contents__wrap">
        <table class="list-table">
            <colgroup>
                <col width="10%" />
                <col width="75%" />
                <col width="15%" />
            </colgroup>
            <thead>
                <th>번호</th>
                <th class="left">제목</th>
                <th>작성일</th>
            </thead>
            <tbody>
            @foreach($notices as $notice)
                <tr>
                    <td>{{ $notice->id }}</td>
                    <td class="left"><a href="{{ route('noticeDetailView', ['noticeId' => $notice->id, 'listPage' => $notices->currentPage()]) }}">{{ $notice->title }}</a></td>
                    <td>{{ date('Y.m.d', strtotime($notice->created_at)) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $notices->links(('vendor.pagination.tailwind')) }}
    </div>
@endsection

