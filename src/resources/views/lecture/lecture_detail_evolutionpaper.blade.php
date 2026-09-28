@extends('layouts.layout')

@section('title')
    타이틀
@endsection


@section('content')

    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>{{$classApply->korName}} </span></h3>
            {{--탭--}}
            <ul class="container-tab">
                <li onclick="location.href='{{ route('lectureDetailInfo', [ $classApply->id ]) }}'">정보</li>
                <li onclick="location.href='{{ route('lectureDetailMember', [ $classObjectId ]) }}'">참여자</li>
                <li onclick="location.href='{{ route('lectureDetailTeamSelect', [ $classObjectId ]) }}'">팀배정</li>
                <li onclick="location.href='{{ route('lectureDetailProblem', [ $classObjectId ]) }}'">문제분석</li>
                <li onclick="location.href='{{ route('lectureDetailTeam', [ $classObjectId ]) }}'">팀활동보고서</li>
                <li class="active" onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ $classObjectId ]) }}'">평가지</li>
                <li onclick="location.href='{{ route('lectureDetailMind', [ $classObjectId ]) }}'">성찰</li>
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
                    @php
                        $index = 1;
                    @endphp
                    @foreach($teams as $team)
                        <tr>
                            <td>{{ $index++ }}</th>
                            <td>{{ $team->name }}</th>
                            <td>홍길동</th>
                            <td>2021.02.05</th>
                            <td><button class="btn btn-sm btn-primary btn-line" onclick="location.href='{{ route('lectureDetailEvolutionPaperDetail', [ 'classObjectId'=>$classObjectId, 'teamId' => $team->id ]) }}'">확인</th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <ul class="pagination">
                    <li class="prev">&nbsp;</li>
                    <li class="active">1</li>
                    <li>2</li>
                    <li>3</li>
                    <li class="next">&nbsp;</li>
                </ul>
            </div>
        </div>
    </div>

@endsection
