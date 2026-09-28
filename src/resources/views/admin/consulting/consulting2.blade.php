@extends('admin.layouts.admin')

@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.consulting1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.consulting2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.consulting3View') }}">컨설팅관리</a></li>
    </ul>
    <div class="card-body">
        <div class="card-body">
            <form method="get" action="{{ route('admin.applicantExport') }}">
                <input type="hidden" name="state" value="consulting2" />
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
                    <th>일시</th>
{{--                    <th>컨설팅 강사명</th>--}}
                </tr>
                @foreach($consultingApplies as $consultingApply)
                <tr>
                    <td>{{ $consultingApply->id }}</td>
                    <td>{{ $consultingApply->user->name }}</td>
                    @if($consultingApply->user->consultingTarget == 0)
                        <td>비대상자</td>
                    @elseif($consultingApply->user->consultingTarget == 1)
                        <td>대상자</td>
                    @endif
                    <td>{{ $consultingApply->user->email }}</td>
                    <td>{{ date('Y.m.d', strtotime($consultingApply->created_at)) }}</td>
{{--                    <td>{{ $consultingApply->consulting->title }}</td>--}}
                    <td>{{ date('h:m', strtotime($consultingApply->consulting->startDateTime)) }} ~ {{ date('h:m', strtotime($consultingApply->consulting->endDateTime)) }}</td>
{{--                    <td>{{ $consultingApply->consulting->consultant()->first()->name }}</td>--}}
                </tr>
                @endforeach
            </table>
            {{ $consultingApplies->withQueryString()->links('vendor.pagination.tailWind3') }}
        </div>
    </div>
@endsection
