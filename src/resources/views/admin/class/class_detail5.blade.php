@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')

    <div class="card-body">
{{--        <div class="card-body text-right">--}}
{{--            <form method="GET" action="{{ route('admin.classDetail2View', ['classApplyId' => $classApply->id] ) }}">--}}
{{--                <select class="form-control w-sm" name="type">--}}
{{--                    <option value="name">이름</option>--}}
{{--                    <option value="email">이메일</option>--}}
{{--                </select>--}}
{{--                <input class="form-control w-200" type="text" name="content">--}}
{{--                <button class="btn btn-primary btn-sm">검색</button>--}}
{{--            </form>--}}
{{--        </div>--}}
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="15%" />
                    <col width="25%" />
                    <col width="20%" />
                    <col width="20%" />
                    <col width="20%" />
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
{{--                {{ $teamActivities }}--}}
                @foreach ($teamActivities as $teamActivity)
                    <tr>
                        <td>{{ $teamActivity->id }}</td>
                        <td>{{ $teamActivity->card()->team()->name ?? '' }}</td>
                        <td>{{ $teamActivity->user()->first()->name }}</td>
                        <td>{{ $teamActivity->created_at->format('20y.m.d') }}</td>
{{--                        <td>obj : {{ $classApply->classObjectId }} team : {{ $teamActivity->withTeamActivity->teamId }},--}}
                        <td><button class="btn btn-sm btn-primary btn-line"
                            onclick="location.href='{{ route('admin.classDetail5DetailView',
                            ['classApplyId' => $classApply->id, 'teamActivityId' => $teamActivity->withTeamActivity->id]) }}'"
                            >확인</button>
                            </td>
                    </tr>
                @endforeach
                </tbody>
            </table>{{ $teamActivities->withQueryString()->links(('vendor.pagination.tailwind')) }}
        </div>
    </div>
@endsection
