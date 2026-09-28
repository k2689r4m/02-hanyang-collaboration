@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')
    <div class="card-body">
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="15%" />
                    <col width="30%" />
                    <col width="20%" />
                    <col width="20%" />
                    <col width="15%" />
                </colgroup>
                <thead>
                <tr>
                    <th>번호</th>
                    <th>팀명</th>
                    <th>작성자</th>
                    <th>제출일</th>
                    <th>상세확인</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($problems as $problem)
                    <tr>
                        <td>{{ $problem->id }}</td>
                        <td>{{ $problem->card()->team()->name ?? '' }}</td>
                        <td>{{ $problem->user()->first()->name }}</td>
                        <td>{{ $problem->created_at->format('20y.m.d') }}</td>
                        <td><a class="btn btn-primary btn-xs" href="{{ route('admin.classDetail4DetailView', ['classApplyId'=> $classApply->id ,'problemAnalysisId' => $problem->withProblemAnalysis->id]) }}">확인</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{ $problems->withQueryString()->links(('vendor.pagination.tailwind')) }}
{{--            {{ $classLists->withQueryString()->links('vendor.pagination.tailWind3') }}--}}
        </div>
    </div>
@endsection
