@extends('layouts.layout')

@section('content')

    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>{{ $classApply->korName }}</span></h3>
            <ul class="container-tab">
                <li onclick="location.href='{{ route('lectureDetailInfo', [ $classApply->id ]) }}'">정보</li>
                <li onclick="location.href='{{ route('lectureDetailMember', [ $classObject->id ]) }}'">참여자</li>
                <li onclick="location.href='{{ route('lectureDetailTeamSelect', [ $classObject->id ]) }}'">팀배정</li>
                <li class="active" onclick="location.href='{{ route('lectureDetailProblem', [ $classObject->id ]) }}'">문제분석</li>
                <li onclick="location.href='{{ route('lectureDetailTeam', [ $classObject->id ]) }}'">팀활동보고서</li>
                <li onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ $classObject->id ]) }}'">평가지</li>
                <li onclick="location.href='{{ route('lectureDetailMind', [ $classObject->id ]) }}'">성찰</li>
{{--                <li onclick="location.href='{{ route('lectureDetailStatistics', [ $classApply->classObject->id ]) }}'">통계</li>--}}
            </ul>
            <div class="contents__wrap">
                <table class="list-table sm">
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
                    @foreach ($problems as $problem)
                        <tr>
                            <td>{{ $problem->id }}</th>
                            <td>{{ $problem->card()->team()->name ?? '' }}</th>
                            <td>{{ $problem->user()->first()->name }}</th>
                            <td>{{ $problem->created_at->format('20y.m.d') }}</th>
                            <td><button class="btn btn-sm btn-primary btn-line" onclick="location.href='{{ route('lectureDetailProblemDetail', [ 'classObjectId'=>$classObject->id, 'problemAnalysisId' => $problem->withProblemAnalysis->id ]) }}'">확인</th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $problems->withQueryString()->links(('vendor.pagination.tailwind')) }}
            </div>
        </div>
    </div>

@endsection

{{--@foreach ($teams as $team)--}}
{{--    <div>--}}
{{--        <div>--}}
{{--            팀명 : {{ $team->name }}--}}
{{--            <div>--}}
{{--                &emsp;카드명 : {{ $team->card->title }}--}}
{{--                @foreach ($team->card->items as $item)--}}
{{--                    <div>--}}
{{--                        &emsp;&emsp;아이템명 : {{ $item->title }}--}}
{{--                        @foreach ($item->problemAnalyses as $problemAnalysis)--}}
{{--                            <div>--}}
{{--                                &emsp;&emsp;&emsp;문제분석 작성자 이름 : {{ $problemAnalysis->name }}--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endforeach--}}