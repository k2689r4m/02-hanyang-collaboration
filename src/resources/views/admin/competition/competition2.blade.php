@extends('admin.layouts.admin')

@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.competition1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.competition2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.competition3View') }}">공모전관리</a></li>
    </ul>
    <div class="card-body">
        <div class="card-body">
            <div class="text-right card-body">
                <form method="get" action="{{ route('admin.applicantExport') }}">
                    <input type="hidden" name="state" value="program2" />
                    <button class="btn btn-sm btn-primary text-right" onclick="location.href='{{ route('admin.applicantExport') }}'">엑셀 다운</button>
                </form>
            </div>
            <table class="table table-hover text-center">
                <tr>
                    <th>번호</th>
                    <th>신청자명</th>
                    <th>신청자 아이디</th>
                    <th>한양대 아이디</th>
                    <th>신청한 공모전명</th>
                    <th>공모전날짜</th>
                </tr>
                @foreach($competitions as $competition)
                    <tr>
                        <td>{{ $competition->id }}</td>
                        <td>{{ $competition->user->name }}</td>
                        <td>{{ $competition->user->email }}</td>
                        <td></td>
                        <td>{{ $competition->competition->title }}</td>
                        <td>{{ $competition->competition->year }}.{{ $competition->competition->month }}.{{ $competition->competition->day }}</td>
                    </tr>
                @endforeach
            </table>
            {{ $competitions->links('vendor.pagination.tailWind3') }}
        </div>
    </div>
@endsection