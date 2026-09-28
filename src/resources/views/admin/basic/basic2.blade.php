@extends('admin.layouts.admin')

@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.basic1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.basic2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.basic3View') }}">기초교육관리</a></li>
    </ul>
    <div class="card-body">
        <div class="card-body">
            <form method="get" action="{{ route('admin.applicantExport') }}">
                <input type="hidden" name="state" value="basic2" />
{{--                <input type="hidden" name="targetAction" value="{{ request()->query('action') }}" />--}}
                <button class="btn btn-sm btn-primary text-right" onclick="location.href='{{ route('admin.applicantExport') }}'">엑셀 다운</button>
            </form>
            <table class="table table-hover text-center">
                <tr>
                    <th>번호</th>
                    <th>신청자명</th>
                    <th>대상자여부</th>
                    <th>신청자아이디</th>
                    <th>신청일</th>
{{--                    <th>신청한 컨설팅명</th>--}}
                    <th>교육일시</th>
{{--                    <th>기초교육 강사명</th>--}}
                </tr>
                @foreach($basicApplies as $basicApply)
                    <tr>
                        <td>{{ $basicApply->id }}</td>
                        <td>{{ $basicApply->user->name }}</td>
                        @if($basicApply->user->basicTarget == 0)
                            <td>비대상자</td>
                        @elseif($basicApply->user->basicTarget == 1)
                            <td>대상자</td>
                        @endif
                        <td>{{ $basicApply->user->email }}</td>
                        <td>{{ date('Y.m.d', strtotime($basicApply->created_at)) }}</td>
{{--                        <td>{{ $basicApply->basic->title }}</td>--}}
                        <td>{{$basicApply->basic->startDateTime}} <br />~ {{$basicApply->basic->endDateTime}}</td>
{{--                        <td>{{ $basicApply->basic->consultant()->first()->name }}</td>--}}
                    </tr>
                @endforeach
            </table>
            {{ $basicApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
        </div>
    </div>
@endsection