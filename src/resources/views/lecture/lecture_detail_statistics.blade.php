@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>{{ $classApply->korName }}</span></h3>
            {{--탭--}}
            <ul class="container-tab">
                <li onclick="location.href='{{ route('lectureDetailInfo', [ $classApply->id ]) }}'">정보</li>
                <li onclick="location.href='{{ route('lectureDetailMember', [$classApply->classObject->id]) }}'">참여자</li>
                <li onclick="location.href='{{ route('lectureDetailTeamSelect', [ $classApply->classObject->id ]) }}'">팀배정</li>
                <li onclick="location.href='{{ route('lectureDetailProblem', [ $classApply->classObject->id ]) }}'">문제분석</li>
                <li onclick="location.href='{{ route('lectureDetailTeam', [ $classApply->classObject->id ]) }}'">팀활동보고서</li>
                <li onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ $classApply->classObject->id ]) }}'">평가지</li>
                <li onclick="location.href='{{ route('lectureDetailMind', [ $classApply->classObject->id ]) }}'">성찰</li>
{{--                <li class="active" onclick="location.href='{{ route('lectureDetailStatistics', [ $classApply->classObject->id ]) }}'">통계</li>--}}
            </ul>
            <div class="contents__wrap mb-10">
                <h4 class="con-tit mb-30">교수학습 현황</h4>
                <div class="lecture-list__wrap statistics">
                    <div class="lecture-list__item">
                        <table>
                            <colgroup>
                                <col width="20%" />
                                <col width="20%" />
                                <col width="20%" />
                                <col width="20%" />
                                <col width="20%" />
                            </colgroup>
                            <tr>
                                <td><span class="circle line">1</span></td>
                                <td><span class="circle gray">미신청</span></td>
                                <td><span class="circle">대기</span></td>
                                <td><span class="circle gray">미제출</span></td>
                                <td><span class="circle blue">완료</span></td>
                            </tr>
                            <tr>
                                <td>기초교육</td>
                                <td>기초교육</td>
                                <td>컨설팅</td>
                                <td>요약보고서</td>
                                <td>포트폴리오</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="contents__wrap">
                <h4 class="con-tit mb-20">수업 현황</h4>
                <h4 class="sub-tit"><span>수업</span></h4>
                <div class="statistics-table__wrap">
                    <table class="statistics-table">
                        <colgroup>
                            <col width="16%" />
                            <col width="28%" />
                            <col width="28%" />
                            <col width="28%" />
                        </colgroup>
                        <tr>
                            <th class="bg" rowspan="2"><p class="bg">전체</p></th>
                            <th>협력활동(개)</th>
                            <th>성취활동(개)</th>
                            <th>피드백(개)</th>
                        </tr>
                        <tr>
                            <td>35</td>
                            <td>35</td>
                            <td>35</td>
                        </tr>
                    </table>
                    <table class="statistics-table">
                        <colgroup>
                            <col width="12%" />
                            <col width="22%" />
                            <col width="22%" />
                            <col width="22%" />
                            <col width="22%" />
                        </colgroup>
                        <tr>
                            <th class="bg" rowspan="2"><p class="bg">평균</p></th>
                            <th>협력활동(개)</th>
                            <th>성취활동(개)</th>
                            <th>피드백(개)</th>
                            <th>점수</th>
                        </tr>
                        <tr>
                            <td>35</td>
                            <td>35</td>
                            <td>35</td>
                            <td>35</td>
                        </tr>
                    </table>
                </div>
                <h4 class="sub-tit mt-30"><span>팀</span></h4>
                <table class="list-table">
                    <colgroup>
                        <col width="10%" />
                        <col width="30%" />
                        <col width="12%" />
                        <col width="12%" />
                        <col width="12%" />
                        <col width="12%" />
                        <col width="12%" />
                    </colgroup>
                    <tr>
                        <th>번호</th>
                        <th>팀</th>
                        <th>협력<br class="m-block" />활동</th>
                        <th>성취<br class="m-block" />활동<span class="m-none">&nbsp;</span></th>
                        <th>피드백&nbsp;&nbsp;</th>
                        <th>점수&nbsp;&nbsp;</th>
                        <th>순위&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                </table>
                <div class="list-table__scroll scroll-sm">
                    <table class="list-table">
                        <colgroup>
                            <col width="10%" />
                            <col width="30%" />
                            <col width="12%" />
                            <col width="12%" />
                            <col width="12%" />
                            <col width="12%" />
                            <col width="12%" />
                        </colgroup>
                        @php
                            $index = 0
                        @endphp
                        @foreach( $classObject->teams() as $team )
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $team->name }}</td>
                            <td>no</td>
                            <td>no</td>
                            <td>no</td>
                            <td>no</td>
                            <td>no</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
                <h4 class="sub-tit mt-30"><span>학습자</span></h4>
                <table class="list-table">
                    <colgroup>
                        <col width="12%" />
                        <col width="16%" />
                        <col width="12%" />
                        <col width="12%" />
                        <col width="12%" />
                        <col width="12%" />
                        <col width="12%" />
                        <col width="12%" />
                    </colgroup>
                    <tr>
                        <th>번호</th>
                        <th>이름</th>
                        <th>팀</th>
                        <th>협력<br class="m-block" />활동</th>
                        <th>성취<br class="m-block" />활동</th>
                        <th>피드백&nbsp;</th>
                        <th>점수&nbsp;</th>
                        <th>순위&nbsp;&nbsp;</th>
                    </tr>
                </table>
                <div class="list-table__scroll scroll-sm">
                    <table class="list-table">
                        <colgroup>
                            <col width="12%" />
                            <col width="16%" />
                            <col width="12%" />
                            <col width="12%" />
                            <col width="12%" />
                            <col width="12%" />
                            <col width="12%" />
                            <col width="12%" />
                        </colgroup>
                        @php
                            $index = 0
                        @endphp
                        @foreach( $classLists as $classList )
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $classList->name }}</td>
                                {{--                            <td>{{ $classList->team()->name }}</td>--}}
                                @if($classList->team())<td>{{ $classList->team()->name }}</td>
                                @else<td>no</td>@endif
                                <td>no</td>
                                <td>no</td>
                                <td>no</td>
                                <td>no</td>
                                <td>no</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection