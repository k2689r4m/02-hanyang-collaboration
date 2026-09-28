@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
{{--            <h3 class="dash-tit"><span>학습자 대시보드</span></h3>--}}
            <div class="dash__wrap">
                <dash-top
                        :user="{{auth()->user()}}"
                        :my-classes="{{$myClasses}}"
                ></dash-top>
{{--                <div class="dash-top">--}}
{{--                    <div class="dash-top__img">--}}
{{--                        <img src="{{ route('avatar', ['userId' => auth()->id()]) }}" alt="" onerror="this.remove();" />--}}
{{--                        <div></div>--}}
{{--                    </div>--}}
{{--                    <div class="dash-top__txt">--}}
{{--                        <span class="name">{{ Auth::user()->name }}</span>학생, 환영합니다!<br>새 활동을 확인해보세요.--}}
{{--                    </div>--}}
{{--                    @foreach($items as $item)--}}
{{--                        @if($item->withClassApply)--}}
{{--                            <div class="dash-top__admin">--}}
{{--                                <button class="admin-btn">관리</button>--}}
{{--                                <div class="img-wrap">--}}
{{--                                    <img src="" alt="" onerror="this.style.display='none';" />--}}
{{--                                </div>--}}
{{--                                <span class="name">{{ $item->withClassApply->korName }}</span>--}}
{{--                                <table class="con">--}}
{{--                                    <colgroup>--}}
{{--                                        <col width="33%" />--}}
{{--                                        <col width="33%" />--}}
{{--                                        <col width="33%" />--}}
{{--                                    </colgroup>--}}
{{--                                    <tr>--}}
{{--                                        <td><strong>--}}
{{--                                                @if( $item->withClassApply->meca == 1 ) M--}}
{{--                                                @elseif( $item->withClassApply->meca == 2 ) E--}}
{{--                                                @elseif( $item->withClassApply->meca == 3 ) C--}}
{{--                                                @elseif( $item->withClassApply->meca == 4 ) A--}}
{{--                                                @endif--}}
{{--                                            </strong></td>--}}
{{--                                        <td><strong>{{ $item->classApply->classListCount() }}</strong><span>명</span></td>--}}
{{--                                        <td><strong>{{ $item->withClassApply->teamCount() }}</strong><span>개</span></td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td>유형</td>--}}
{{--                                        <td>학생</td>--}}
{{--                                        <td>팀</td>--}}
{{--                                    </tr>--}}
{{--                                </table>--}}
{{--                            </div>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                    <div class="admin-slick__wrap m-none">--}}
{{--                        <div class="admin-slick">--}}
{{--                            @foreach($myClasses as $myClass)--}}
{{--                                <div class="dash-top__admin__wrap">--}}
{{--                                    <div class="dash-top__admin">--}}
{{--                                        <button class="admin-btn stu" onclick="moveClass({{$myClass->id}})">&gt;</button>--}}
{{--                                        <div class="img-wrap">--}}
{{--                                            @switch($myClass->classApply->meca)--}}
{{--                                                @case(1)--}}
{{--                                                <span class="m"></span>--}}
{{--                                                @break--}}
{{--                                                @case(2)--}}
{{--                                                <span class="e"></span>--}}
{{--                                                @break--}}
{{--                                                @case(3)--}}
{{--                                                <span class="c"></span>--}}
{{--                                                @break--}}
{{--                                                @case(4)--}}
{{--                                                <span class="a"></span>--}}
{{--                                                @break--}}
{{--                                            @endswitch--}}
{{--                                            <span class="badge">--}}
{{--                                            @switch($myClass->classApply->meca)--}}
{{--                                                    @case(1)--}}
{{--                                                    현장통합형--}}
{{--                                                    @break--}}
{{--                                                    @case(2)--}}
{{--                                                    현장평가형--}}
{{--                                                    @break--}}
{{--                                                    @case(3)--}}
{{--                                                    문제해결형--}}
{{--                                                    @break--}}
{{--                                                    @case(4)--}}
{{--                                                    현장문제형--}}
{{--                                                    @break--}}
{{--                                                @endswitch--}}
{{--                                        </span>--}}
{{--                                        </div>--}}
{{--                                        <span class="name">{{ $myClass->classApply->korName }}</span>--}}
{{--                                        <table class="con">--}}
{{--                                            <colgroup>--}}
{{--                                                <col width="50%" />--}}
{{--                                                <col width="50%" />--}}
{{--                                            </colgroup>--}}
{{--                                            <tr>--}}
{{--                                                <td><strong>{{ $myClass->classMembers->count() }}</strong><span>명</span></td>--}}
{{--                                                <td><strong>{{ $myClass->teamCount }}</strong><span>개</span></td>--}}
{{--                                            </tr>--}}
{{--                                            <tr>--}}
{{--                                                <td>학생</td>--}}
{{--                                                <td>팀</td>--}}
{{--                                            </tr>--}}
{{--                                        </table>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            @endforeach--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="dash-top__admin__wrap m-block scroll-sm">--}}
{{--                        @foreach($myClasses as $myClass)--}}
{{--                            <div class="dash-top__admin">--}}
{{--                                <button class="admin-btn stu" onclick="moveClass({{$myClass->id}})">&gt;</button>--}}
{{--                                <div class="img-wrap">--}}
{{--                                    @switch($myClass->classApply->meca)--}}
{{--                                        @case(1)--}}
{{--                                        <span class="m"></span>--}}
{{--                                        @break--}}
{{--                                        @case(2)--}}
{{--                                        <span class="e"></span>--}}
{{--                                        @break--}}
{{--                                        @case(3)--}}
{{--                                        <span class="c"></span>--}}
{{--                                        @break--}}
{{--                                        @case(4)--}}
{{--                                        <span class="a"></span>--}}
{{--                                        @break--}}
{{--                                    @endswitch--}}
{{--                                    <span class="badge">--}}
{{--                                            @switch($myClass->classApply->meca)--}}
{{--                                            @case(1)--}}
{{--                                            현장통합형--}}
{{--                                            @break--}}
{{--                                            @case(2)--}}
{{--                                            현장평가형--}}
{{--                                            @break--}}
{{--                                            @case(3)--}}
{{--                                            문제해결형--}}
{{--                                            @break--}}
{{--                                            @case(4)--}}
{{--                                            현장문제형--}}
{{--                                            @break--}}
{{--                                        @endswitch--}}
{{--                                        </span>--}}
{{--                                </div>--}}
{{--                                <span class="name">{{ $myClass->classApply->korName }}</span>--}}
{{--                                <table class="con">--}}
{{--                                    <colgroup>--}}
{{--                                        <col width="50%" />--}}
{{--                                        <col width="50%" />--}}
{{--                                    </colgroup>--}}
{{--                                    <tr>--}}
{{--                                        <td><strong>{{ $myClass->classMembers->count() }}</strong><span>명</span></td>--}}
{{--                                        <td><strong>{{ $myClass->teamCount }}</strong><span>개</span></td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td>학생</td>--}}
{{--                                        <td>팀</td>--}}
{{--                                    </tr>--}}
{{--                                </table>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}

                <div class="dash-board">
                    <div class="col-8 float-left margin-top">
                        <pro-log :user="{{auth()->user()}}"></pro-log>
{{--                        <h4 class="sub-tit">피드백</h4>--}}
{{--                        <ul class="alarm-list">--}}
{{--                            <li class="alarm-list__item center"><span class="name">김민주 교수</span>가 내 팀활동에 "<span class="tit">1주차 문제 분석의 문제 분석에 대하여</span>" <strong>댓글</strong>을 추가했습니다.<span class="date">3/19금, 11:30</span></li>--}}
{{--                            <li class="alarm-list__item"><span class="name">황주현 학생</span>이 내 문제분석에 "<span class="tit">수고하셨습니다</span>" <strong>댓글</strong>을 추가했습니다.<span class="date">3/19금, 11:30</span></li>--}}
{{--                            <li class="alarm-list__item"><span class="name">박상지 학생</span>이 김민주님을 <strong>태그</strong>했습니다.<span class="date">3/19금, 11:30</span></li>--}}
{{--                        </ul>--}}
                    </div>
                    <div class="col-8 float-left">
                        <h4 class="sub-tit">수업 현황</h4>
                        <ul class="current-list tit">
                            <li class="current-list__item">수업</li>
                            <li class="current-list__item">문제분석</li>
                            <li class="current-list__item">팀활동</li>
                            <li class="current-list__item">평가</li>
                            <li class="current-list__item">성찰</li>
                        </ul>
                        <div class="current-list__wrap scroll-sm">
                            @foreach($myClasses as $myClass)
                                <?php
                                $problems = 0;
                                $reflections = 0;
                                $teams = 0;
                                ?>
                                @foreach($myClass->prt as $prt)
                                    @foreach($prt->items2 as $item)
                                        @switch($item->type)
                                            @case(1)
                                                @if($item->userId == auth()->user()->id)
                                                <?php
                                                $problems++;
                                                ?>
                                                @endif
                                            @break
                                            @case(2)
                                                @if($item->userId == auth()->user()->id)
                                                <?php
                                                $reflections++;
                                                ?>
                                                @endif
                                            @break
                                            @case(5)
                                                @if($item->userId == auth()->user()->id)
                                                <?php
                                                $teams++;
                                                ?>
                                                @endif
                                            @break
                                        @endswitch
                                    @endforeach
                                @endforeach
                                <ul class="current-list con">
                                    <li class="current-list__item"><span class="tit">{{ $myClass->classApply->korName }}</span></li>
                                    <li class="current-list__item"><span class="circle color1">{{ $problems }}</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                    <li class="current-list__item"><span class="circle color2">{{ $teams }}</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                    <li class="current-list__item"><span class="circle color3">0</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                    <li class="current-list__item"><span class="circle color4">{{ $reflections }}</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                </ul>
                                @if(count($myClasses) % 2 === 1)
                                        <ul class="current-list con after-none"></ul>
                                @endif
                            @endforeach
{{--                            @for($i=0;$i<5;$i++)--}}
{{--                                <ul class="current-list con">--}}
{{--                                    <li class="current-list__item"><span class="tit">AI의 이해</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color1">12</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color2">10</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color3">9</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color4">3</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                </ul>--}}
{{--                                <ul class="current-list con">--}}
{{--                                    <li class="current-list__item"><span class="tit">AI의 이해</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color1">12</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color2">10</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color3">9</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                    <li class="current-list__item"><span class="circle color4">3</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
{{--                                </ul>--}}
{{--                            @endfor--}}
                        </div>
                    </div>
                    <div class="col-8 float-left schedule">
                        <sch-log :user="{{auth()->user()}}" :today="{{$today}}" :next="{{$nextDay}}"></sch-log>
{{--                        <h4 class="sub-tit">주요 일정</h4>--}}
{{--                        <a href="" class="btn-more">더보기</a>--}}
{{--                        <ul class="schedule-list">--}}
{{--                            <li class="schedule-list__item tit">오늘({{ date('m/d', strtotime('now')) }})</li>--}}
{{--                            @foreach($today as $t)--}}
{{--                                <li class="schedule-list__item"><span class="tit">{{ $t->content }}</span><span class="detail"><span class="subject">{{ $t->classObject->gwamokNm }}</span> ~ {{ date('H:m', strtotime($t->dateTime)) }}</span></li>--}}
{{--                            @endforeach--}}
{{--                            --}}{{--                            <li class="schedule-list__item"><span class="tit">1주차 학습자료 업로드</span><span class="detail">AI의 이해 ~ 18:00</span></li>--}}
{{--                        </ul>--}}
{{--                        <ul class="schedule-list color2">--}}
{{--                            <li class="schedule-list__item tit color2">내일({{ date('m/d', strtotime('now + 1 DAY')) }})</li>--}}
{{--                            @foreach($nextDay as $n)--}}
{{--                                <li class="schedule-list__item"><span class="tit">{{ $n->content }}</span><span class="detail"><span class="subject">{{ $n->classObject->gwamokNm }}</span> ~ {{ date('H:m', strtotime($n->dateTime)) }}</span></li>--}}
{{--                            @endforeach--}}
{{--                            --}}{{--                            <li class="schedule-list__item"><span class="tit">1주차 학습자료 업로드</span><span class="detail">AI의 이해 ~ 18:00</span></li>--}}
{{--                            --}}{{--                            <li class="schedule-list__item"><span class="tit">1주차 학습자료 업로드</span><span class="detail">AI의 이해 ~ 18:00</span></li>--}}
{{--                        </ul>--}}
{{--                        </ul>--}}
                    </div>
                    <div class="col-4 float-right s-activity">
                        <h4 class="sub-tit">실시간 활동</h4>
{{--                        <a href="" class="btn-more">더보기</a>--}}
{{--                        <ul class="activity-list scroll-sm h-stu">--}}

{{--                            --}}
{{--                            @for($i=0;$i<10;$i++)--}}
{{--                                <li class="activity-list__item team2"><span class="name">(2팀) 박성진</span> [팀활동] ‘3주차 참고사항’ <p class="detail">블록체인 심화 3/19 금, 11:30</p></li>--}}
{{--                                <li class="activity-list__item team1"><span class="name">(1팀) 박성진</span> [팀활동] ‘3주차 참고사항’ <p class="detail">블록체인 심화 3/19 금, 11:30</p></li>--}}
{{--                            @endfor--}}
{{--                        </ul>--}}
                        <act-log :user="{{auth()->user()}}"></act-log>
                    </div>
                </div>

{{--                <ul class="dash-tab-menu">--}}
{{--                    @foreach($myClasses as $key=>$myClass)--}}
{{--                        <li class="dash-tab-menu__item @if(!$key) active @endif" name='tab{{ $key }}' onclick="tabActive(this, {{ $myClass->classObjectId }})">{{ $myClass->classApply->korName }}</li>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--                <div class="dash-tab__wrap contents__wrap">--}}
{{--                    @foreach($myClasses as $key=>$myClass)--}}
{{--                        <div class="dash-tab__con @if(!$key) active @endif" id='tab{{ $key }}'>--}}
{{--                            <div class="left">--}}
{{--                                <h4 class="con-tit">학습활동 현황</h4>--}}
{{--                                <div id="graph{{ $myClass->classObjectId }}_1">--}}
{{--                                    <div class="graph-top">--}}
{{--                                        <span class="text-right">(개)</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="right">--}}
{{--                                <h4 class="con-tit">학습활동 점수</h4>--}}
{{--                                <div id="graph{{ $myClass->classObjectId }}_2">--}}
{{--                                    <h5 class="sub-tit dot-none">--}}
{{--                                        <span class="tail" id="stateMessage{{ $myClass->classObjectId }}"></span>--}}
{{--                                        <span class="text-right">(점)</span>--}}
{{--                                    </h5>--}}
{{--                                </div>--}}
{{--                                <div id="graph{{ $myClass->classObjectId }}_3">--}}
{{--                                    <h5 class="sub-tit dot-none">개인랭킹<span class="fc-gray fs-md ml-5"><span id="totalLank{{ $myClass->classObjectId }}"></span>명중 <span id="userLank{{ $myClass->classObjectId }}"></span>등</span></h5>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('js/d3.js') }}"></script>
<script type="text/javascript">

    const handleDrawInlineGraph = (data, id, teamName = null) => {
        //test code
        // data[0].value1 = 2;
        // data[0].value2 = 15;
        // data[0].value3 = 7;

        let min = d3.min(data, (d) => +d3.min([+d.value1, +d.value2, +d.value3]));
        let max = d3.max(data, (d) => +d3.max([+d.value1, +d.value2, +d.value3]));

        min = Math.floor(min / 10) * 10;
        max = Math.ceil(max / 10) * 10;

        const height = 400;
        const width = 500;
        const margin = ({top: 30, right: 30, bottom: 35, left: 30});
        const labelPadding = 5;
        const series = data.columns.slice(1).map(key => data.map(({[key]: value, name1}, i) => ({i, name1, key, value})));
        // const z = d3.scaleOrdinal(data.columns.slice(2), d3.schemeCategory10);
        const z = (key) => {
            switch(key) {
                case 'value1':
                    return '#89dbe0';
                    break;
                case 'value2':
                    return '#ffd265';
                    break;
                case 'value3':
                    return '#ff95a1';
                    break;
                default:
                    return 'black';
            }
        };

        const y = d3.scaleLinear()
            .domain([min, max === 0 ? 100 : max])
            .range([height - margin.bottom, margin.top]);
        const x = d3.scaleUtc()
            .domain([0, 3])
            .range([margin.left, width - margin.right]);

        const format = x.tickFormat(".2");
        // const xAxis = g => g
        //     .attr("transform", `translate(0,${height - margin.bottom})`)
        //     .call(d3.axisBottom(x).ticks(width / 80).tickSizeOuter(0));

        // console.log(series);

        const svg = d3.select(id/*'svg'*//*'#graph'*/).append('svg')
            .attr("viewBox", [0, 0, width, height]);

        // svg.append("g")
        //     .call(xAxis);

        const line = d3.line()([[0, y(min)], [width, y(min)]]);
        // svg.append(line);

        svg.append("path")
            .attr("d", line)
            .attr("stroke", "#cfdbe5");

        // console.log(data);

        // svg.append('foreignObject').data(data).join('xhtml:div')
        // .append('div').html('test');

        [
            { name: '전체', value: data[0].value1, color: '#89dbe0' },
            { name: teamName, value: data[0].value2, color: '#ffd265' },
            { name: '{{ Auth::user()->name }}', value: data[0].value3, color: '#ff95a1' }
        ].forEach((d) => {
            const height = 24;
            const width = 55;

            // <span class="badge badge-green line">

            svg.append('foreignObject')
                .attr('x', x(0))
                .attr('y', y(d.value) - height / 2)
                .attr('width', width)
                .attr('height', height)
                // .append('xhtml:div')
                .append('xhtml:span')
                // .classed('badge badge-green line', true)
                .style('font-size', '14px')
                .style('text-align', 'center')
                .style('border', `2px solid ${d.color}`)
                .style('background', '#fff')
                .style('padding', '3px 5px')
                .style('height', '100%')
                // .style('height', 'calc(100% - 4px)')
                .style('width', '100%')
                // .style('width', 'calc(100% - 4px)')
                // .style('box-shadow', '4px 4px black')
                .style('border-radius', '20px')
                .style('display', 'table')
                // .append('p')
                .html(d.name)
                // .style('text-align', 'center')
                // .style('display', 'table-cell')
                // .style('vertical-align', 'middle')
        })

        svg.append("g")
            .attr("fill", "#0c161e")
            .attr("stroke", "#0c161e")
            .attr("stroke-width", "0.1")
            .attr("text-anchor", "middle")
            .attr("font-family", "NanumSquare")
            .attr("font-size", 16)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", (d, i) => x(i))
            .attr("y", y(min))
            .attr("dy", 30)
            .attr("dx", 80)
            .text(d => d.name1)
            // .call(text => text.filter(d => x(d.value) - x(0) < 20) // short bars
            //     .attr("dx", -80)
            //     .attr("fill", "black")
            //     .attr("text-anchor", "middle"));

        // svg.append("g")
        //     .attr("class", "grid")
        //     .attr('color', '#e8f1f8')
        //     .attr("transform", "translate(0," + (height - 36) + ")")
        //     .attr("dx", 80)
        //     .call(d3.axisBottom(x)
        //         .ticks(3)
        //         .tickSize(-height)
        //         .tickFormat(format)
        //     )

        svg.append("line")
            .attr("class", "grid")
            .attr("stroke-dasharray", "2,2")
            .attr("x1", 110)
            .attr("x2", 110)
            .attr("y1", 20)
            .attr("y2", height - 35)
            .style("stroke", "#d7e1e9");

        svg.append("line")
            .attr("class", "grid")
            .attr("stroke-dasharray", "2,2")
            .attr("x1", 257)
            .attr("x2", 257)
            .attr("y1", 20)
            .attr("y2", height - 35)
            .style("stroke", "#d7e1e9");

        svg.append("line")
            .attr("class", "grid")
            .attr("stroke-dasharray", "2,2")
            .attr("x1", 403)
            .attr("x2", 403)
            .attr("y1", 20)
            .attr("y2", height - 35)
            .style("stroke", "#d7e1e9");

        const serie = svg.append("g")
            .selectAll("g")
            .data(series)
            .join("g");

        serie.append("path")
            .attr("fill", "none")
            .attr("stroke", d => z(d[0].key))
            .attr("stroke-width", 1.5)
            .attr("d", d3.line()
                .x(d => x(d.i) + 80)
                .y(d => y(d.value)));

        // serie.append('circle')
        //     .attr('cx',-106.661513 )
        //     .attr('cy', 35.05917399 )
        //     .attr('r','10px')
        //     .style('fill', 'red');

        serie.append("g")
            .attr("stroke-linecap", "round")
            .attr("stroke-linejoin", "round")
            .attr("fill", "white")
            .selectAll("circle")
            .data(d => d)
            .join("circle")
            .attr('cx', d => x(d.i) + 80 )
            .attr('cy', d => y(d.value))
            .attr('r','3px')
            .clone(true).lower()
            .attr("fill", "none")
            .attr("stroke", d => z(d.key))
            .attr("stroke-width", labelPadding);

        serie.append("g")
            .attr("font-family", "NanumSquare")
            .attr("font-size", 16)
            .attr("text-anchor", "middle")
            .selectAll("text")
            .data(d => d)
            .join("text")
            .text(d => d.value)
            .attr("dy", "-.7em")
            .attr("x", d => x(d.i) + 80)
            .attr("y", d => y(d.value))
            .clone(true).lower()
            .attr("fill", "none")
            .attr("stroke", "white")
            .attr("stroke-width", labelPadding);

    }

    const handleHorizontalBarChart = (data, id, title = null) => {
        const max = 100;
        const min = d3.min(data, d => d.value);

        const margin = ({top: 10, right: 50, bottom: 10, left: 20})
        const width = 550;
        const height = title ? 300 : 80;
        const barHeight = (height - 30) / 8;

        const color = [
            '#aab4bc',
            '#ff96a2',
        ];
        const circleLineColor = [
            '#aab4bc',
            '#ff96a2',
        ];

        if (!title) {
            color.splice(0, 1);
            circleLineColor.splice(0, 1);
        }

        const x = d3.scaleLinear()
            .domain([0, max])
            .range([margin.left, width - margin.right]);

        const y = d3.scaleBand()
            .domain(d3.range(data.length))
            .rangeRound([margin.top, height - margin.bottom])
            .padding(0.1);

        const svg = d3.select(id/*'svg'*//*'#graph'*/).append('svg')
            .attr("viewBox", [0, 0, width, height]);

        svg.append("g")
            // .attr("fill", '#cfdbe5')
            .selectAll("rect")
            .data(data)
            .join("rect")
            .attr("x", x(0))
            .attr("y", (d, i) => title ? y(i) + 6 : y(i))
            .attr("width", d => x(d.value) - x(0))
            .attr("height", 10)
            .attr('fill', (d, i) => color[i])

        svg.append("g")
            // .attr("fill", "#616cec")
            // .attr("stroke", "#616cec")
            // .attr("stroke-width", "0.3")
            .attr("text-anchor", "end")
            // .attr("font-family", "NanumSquare")
            .attr("font-size", 16)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", d => x(d.value) + 35)
            .attr("y", (d, i) => title ? y(i) : y(i) + 12)
            .attr("dy", barHeight - 8)
            .attr("dx", -5)
            .attr('font-weight', '600')
            // .attr("stroke", (d, i) => { if (i < 1) return '#00b9a1'; else return '#7d909f'; })
            // .attr("stroke-width", "0.3")
            // .attr("fill", (d, i) => { if (i < 1) return '#00b9a1'; else return '#7d909f'; })
            .text(d => (x(d.value) - x(0) > 30) ? d.value : '')

        svg.append("g")
            .attr("stroke-linecap", "round")
            .attr("stroke-linejoin", "round")
            // .attr("fill", "none")
            .selectAll("circle")
            .data(data)
            .join("circle")
            .attr('cx', x(0))
            .attr('cy', (d, i) => y(i) + 5)
            .attr('r', (d, i) => 11)
            .attr('fill', (d, i) => "#fff")
            .clone(true).lower()
            .attr("fill", "none")
            .attr("stroke", (d, i) => circleLineColor[i])
            .attr("stroke-width", 6);

        svg.append("g")
            .attr("text-anchor", "middle")
            .attr("font-family", "NanumSquare")
            .attr("font-size", 15)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", x(0))
            .attr("y", (d, i) => y(i) + 5)
            .attr("dy", 5)
            .attr("dx", 0)
            .text(d => d.rank)
            .attr('fill', (d, i) => color[i])
            .attr("stroke", (d, i) => color[i])

        svg.append("g")
            .attr("text-anchor", "start")
            .attr("font-family", "NanumSquare")
            .attr("font-size", 14)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", x(0) - 10)
            .attr("y", (d, i) => title ? y(i) + 25 : y(i) + 22)
            .attr("dy", 5)
            .attr("dx", 30)
            .text(d => d.name)

        // if (title) {
        //     svg.append('text')
        //         .attr('text-anchor', 'middle')
        //         .attr("font-family", "NanumSquare")
        //         .attr("font-size", 15)
        //         .attr("x", x(0))
        //         .attr("y", (y(2) + y(3)) / 2)
        //         .attr("dy", barHeight - 5)
        //         .attr("dx", -50)
        //         .text(title)
        // }
    }

    const handleVerticalBarChart = (data, id) => {
        const margin = ({top: 20, right: 50, bottom: 100, left: 50});

        const height = 400;
        const width = 500;

        const barWidth = 45;

        const color = [
            '#ff96a2',
            '#ffd46a',
            '#8bdde2',
        ];

        const y = d3.scaleLinear()
            .domain([0, 100]).nice()
            .range([height - margin.bottom, margin.top]);

        const x = d3.scaleBand()
            .domain(d3.range(data.length))
            .range([margin.left, width - margin.right])
            .padding(0.1);

        const svg = d3.select(id/*'svg'*//*'#graph'*/).append('svg')
            .attr("viewBox", [0, 0, width, height]);

        // svg.append('line')
        //     .attr('x1', x(0))
        //     .attr('x2', x(data.length - 1) + x.bandwidth())
        //     .attr('y1', y(0))
        //     .attr('y2', y(0) + 0.5)
        //     .style('stroke', '#cfdbe5')
        //     .style('stroke-width', 1);

        svg.append("g")
            .attr("fill", '#cfdbe5')
            .selectAll("rect")
            .data(data)
            .join("rect")
            .attr("x", (d, i) => x(i) + (x.bandwidth() / 2) - (barWidth / 2))
            .attr("y", d => y(d.value))
            .attr("height", d => y(0) - y(d.value))
            .attr('fill', (d, i) => color[i])
            .attr("width", barWidth)

        svg.append("g")
            .attr("fill", "#0c161e")
            .attr("stroke", "#0c161e")
            .attr("stroke-width", "0.1")
            .attr("text-anchor", "middle")
            // .attr("font-family", "NanumSquare")
            .attr("font-size", 13)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", (d, i) => x(i) + (x.bandwidth() / 2))
            .attr("y", y(0) + 10)
            .attr("dy", 8)
            .attr("dx", 0)
            .text(d => d.name)
        // .call(text => text.filter(d => x(d.value) - x(0) < 20) // short bars
        //     .attr("dx", -80)
        //     .attr("fill", "black")
        //     .attr("text-anchor", "middle"));

        svg.append("g")
            .attr("fill", "#0c161e")
            .attr("stroke", "#0c161e")
            .attr("stroke-width", "0.1")
            .attr("text-anchor", "middle")
            // .attr("font-family", "NanumSquare")
            .attr("font-size", 13)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", (d, i) => x(i) + (x.bandwidth() / 2))
            .attr("y", y(0))
            .attr("dy", 33)
            .attr("dx", 0)
            .text(d => { if (d.hasOwnProperty('name2')) { return d.name2; } else return null; })

        svg.append("g")
            .attr("fill", "black")
            .attr("stroke", "black")
            .attr("stroke-width", "0.1")
            .attr("text-anchor", "middle")
            .attr("font-family", "NanumSquare")
            .attr("font-size", 14)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", (d, i) => x(i) + (x.bandwidth() / 2) + 13)
            .attr("y", (d, i) => y(d.value))
            .attr("dy", -10)
            .attr("dx", -12.5)
            .text(d => d.value)
    }

    let dataCheck = [];
    const tabActive = (target, id) => {
        const activeName = target.getAttribute('name');
        let flag = false;
        dataCheck.forEach(function(item, index){
            item === id ? flag = true : '';
        });
        if(!flag){
            dataCheck.push(id);
            axios({
                url: "{{ route('stuGraphData', ['classObjectId' => '0']) }}" + id,
                method: 'get',
            }).then(function (response) {
                console.log(response.data);

                const teamName = response.data.info.teamName;
                const userName = '{{ Auth::user()->name }}';

                const r_data1 = response.data[0];
                let data1 = [
                    { name1: '협력활동', value1: r_data1[0][0] ? r_data1[0][0] : '', value2: r_data1[1][0] ? r_data1[1][0] : '', value3: r_data1[2][0] ? r_data1[2][0] : '' },
                    { name1: '성취활동', value1: r_data1[0][1] ? r_data1[0][1] : '', value2: r_data1[1][1] ? r_data1[1][1] : '', value3: r_data1[2][1] ? r_data1[2][1] : '' },
                    { name1: '피드백', value1: r_data1[0][2] ? r_data1[0][2] : '', value2: r_data1[1][2] ? r_data1[1][2] : '', value3: r_data1[2][2] ? r_data1[2][2] : '' },
                ];

                data1['columns'] = [
                    'name1', 'value1', 'value2', 'value3'
                ];
                handleDrawInlineGraph(data1, '#graph' + id + '_1', teamName);

                const r_data2 = response.data[1];
                const data2 = [
                    { name: '내 점수', name2: '', value: r_data2[0] ? r_data2[0] : '' },
                    { name: teamName ? teamName + ' 평균' : '', name2: '', value: r_data2[1] ? r_data2[1] : '' },
                    { name: '수업 평균', name2: '', value: r_data2[2] ? r_data2[2] : '' },
                ];
                handleVerticalBarChart(data2, '#graph' + id + '_2');

                const r_data3 = response.data[2];
                const _data = [
                    { name: teamName ? userName + ' (' + teamName + ')' : userName, value: r_data3[1] ? r_data3[1] : '', rank: r_data3[0] ? r_data3[0] : '' },
                ];
                handleHorizontalBarChart(_data, '#graph' + id + '_3');

                //document.getElementById('teamName').innerText = teamName;
                document.getElementById('totalLank' + id).innerText = response.data.info.classMemberCount;
                document.getElementById('userLank' + id).innerText = r_data3[0] ? r_data3[0] : '';

                if(r_data2){
                    if(90 <= Number(r_data2[0])){
                        document.getElementById('stateMessage' + id).innerHTML = '<img src="/images/icon/emoji_1.png" /> 아주 좋아요!';
                    }
                    else if(80 <= Number(r_data2[0]) && Number(r_data2[0]) <= 89){
                        document.getElementById('stateMessage' + id).innerHTML = '<img src="/images/icon/emoji_2.png" /> 잘 하고 있어요, 좀 더 힘내요!';
                    }
                    else{
                        document.getElementById('stateMessage' + id).innerHTML = '<img src="/images/icon/emoji_3.png" /> 더 분발해주세요!';
                    }
                }
            });
        }

        document.querySelector('.dash-tab-menu__item.active').classList.remove('active');
        target.classList.add('active');
        document.querySelector('.dash-tab__con.active').classList.remove('active');
        document.getElementById(activeName).classList.add('active');
    }

    const moveClass = (myClassId) => {
        document.location.href = "{!! route('myClassView', ['myClassId' => '']); !!}" + myClassId;
    }

    window.onload = () => {
        $(".lecture-list.m-none").slick({
            slidesToShow: 2,
            infinite: false,
            centerMode: false,
        });
        $(".lecture-list.m-block").slick({
            slidesToShow: 1,
            infinite: false,
        });
        $(".admin-slick").slick({
            slidesToShow: 2,
            infinite: false,
        });
        $(".dash-tab-menu").slick({
            slidesToShow: 6,
            slidesToScroll: 6,
            infinite: false,
            centerMode: false,
        });

        $('[name="tab0"]').click();

        // let data1 = [
        //     { name1: '협력활동', value1: 30, value2: 20, value3: 10 },
        //     { name1: '성취활동', value1: 30, value2: 15, value3: 5 },
        //     { name1: '피드백', value1: 35, value2: 30, value3: 20 },
        // ];
        //
        //
        // data1['columns'] = [
        //     'name1', 'value1', 'value2', 'value3'
        // ];
        // handleDrawInlineGraph(data1, '#graph1');
        //
        // const data2 = [
        //     { name: '내 점수', name2: '', value: 65 },
        //     { name: '1팀 평균', name2: '', value: 75 },
        //     { name: '수업 평균', name2: '', value: 82 },
        // ];
        // handleVerticalBarChart(data2, '#graph2');
        //
        // const _data = [
        //     { name: '김소윤 (2팀)', value: 65, rank: '17' },
        // ];
        // handleHorizontalBarChart(_data, '#graph3');
    }
</script>