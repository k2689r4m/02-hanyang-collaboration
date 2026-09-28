@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')
    <div class="contents__wrap card-body">
        <h4 class="con-tit mb-30 font-bold">교수학습 현황</h4>
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
    <div class="contents__wrap card-body">
        <h4 class="con-tit mb-20 font-bold">수업 현황</h4>
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
        <h4 class="sub-tit m-t-30"><span>팀</span></h4>
        <table class="table table-hover text-center">
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
                <th>협력활동</th>
                <th>성취활동</th>
                <th>피드백&nbsp;</th>
                <th>점수&nbsp;</th>
                <th>순위&nbsp;</th>
            </tr>
        </table>
        <div class="list-table__scroll scroll-sm">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="10%" />
                    <col width="30%" />
                    <col width="12%" />
                    <col width="12%" />
                    <col width="12%" />
                    <col width="12%" />
                    <col width="12%" />
                </colgroup>
{{--                @php--}}
{{--                    $index = 0--}}
{{--                @endphp--}}
{{--                @foreach( $classObject->teams() as $team )--}}
{{--                    <tr>--}}
{{--                        <td>{{ $loop->iteration }}</td>--}}
{{--                        <td>{{ $team->name }}</td>--}}
{{--                        <td>no</td>--}}
{{--                        <td>no</td>--}}
{{--                        <td>no</td>--}}
{{--                        <td>no</td>--}}
{{--                        <td>no</td>--}}
{{--                    </tr>--}}
{{--                @endforeach--}}
                @foreach($classApply->teams() as $team)
                    <?php
                    $problems = 0;
                    $reflections = 0;
                    $teams = 0;
                    ?>
                    @foreach($classApply->prt as $prt)
                        @foreach($prt->items2 as $item)
                            @switch($item->type)
                                @case(1)
                                <?php
                                $problems++;
                                ?>
                                @break
                                @case(2)
                                <?php
                                $reflections++;
                                ?>
                                @break
                                @case(5)
                                <?php
                                $teams++;
                                ?>
                                @break
                            @endswitch
                        @endforeach
                    @endforeach
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $team->name }}</td>
                    <td>none</td>
                    <td>none</td>
                    <td>none</td>
                    <td>none</td>
                    <td>none</td>
                </tr>
                @endforeach
            </table>
        </div>
        <h4 class="sub-tit m-t-30"><span>학습자</span></h4>
        <table class="table table-hover text-center">
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
                <th>협력활동</th>
                <th>성취활동</th>
                <th>피드백&nbsp;</th>
                <th>점수&nbsp;</th>
                <th>순위&nbsp;</th>
            </tr>
        </table>
        <div class="list-table__scroll scroll-sm m-b-10">
            <table class="table table-hover text-center">
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
                @foreach($classLists as $classList)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $classList->name ??  $classList->user()->name }}</td>
                    @if($classList->team())<td>{{ $classList->team()->name }}</td>
                    @else<td>none</td>@endif
                    <td>none</td>
                    <td>none</td>
                    <td>none</td>
                    <td>none</td>
                    <td>none</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection