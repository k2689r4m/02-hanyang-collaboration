@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            {{--            <h3 class="dash-tit"><span>교수자 대시보드</span></h3>--}}
            <div class="dash__wrap">
                <div class="dash-top">
                    <div class="dash-top__img">
                        <img src="{{ route('avatar', ['userId' => auth()->id()]) }}" alt="" onerror="this.remove();" />
                        <div></div>
                    </div>
                    <div class="dash-top__txt">
                        <span class="name">{{ Auth::user()->name }}</span>님, 환영합니다!<br>새 활동을 확인해보세요.
                    </div>
                    {{--                    <div class="dash-top__admin">--}}
                    {{--                        <button class="admin-btn btn btn-sm btn-primary">관리</button>--}}
                    {{--                        <div class="img-wrap">--}}
                    {{--                            <img src="" alt="" />--}}
                    {{--                        </div>--}}
                    {{--                        <span class="name">AI의 이해</span>--}}
                    {{--                        <table class="con">--}}
                    {{--                            <tr>--}}
                    {{--                                <td><strong>M</strong></td>--}}
                    {{--                                <td><strong>30</strong><span class="fc-gray">명</span></td>--}}
                    {{--                                <td><strong>5</strong><span class="fc-gray">개</span></td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td>유형</td>--}}
                    {{--                                <td>학생</td>--}}
                    {{--                                <td>팀</td>--}}
                    {{--                            </tr>--}}
                    {{--                        </table>--}}
                    {{--                    </div>--}}
                    <div class="admin-slick__wrap m-none">
                        <div class="admin-slick">
                            @foreach($classes as $class)
                                <div class="dash-top__admin__wrap">
                                    <div class="dash-top__admin">
                                        <div class="img-wrap">
                                            @switch($class->classApply->meca)
                                                @case(1)
                                                <span class="m"></span>
                                                @break
                                                @case(2)
                                                <span class="e"></span>
                                                @break
                                                @case(3)
                                                <span class="c"></span>
                                                @break
                                                @case(4)
                                                <span class="a"></span>
                                                @break
                                            @endswitch
                                        </div>
                                        <span class="name">{{ $class->classObject2->gwamokNm }}</span>
                                        <table class="con">
                                            <colgroup>
                                                <col width="33%" />
                                                <col width="33%" />
                                                <col width="33%" />
                                            </colgroup>
                                            <tr>
                                                <td>
                                                    <strong>
                                                        @switch($class->classApply->meca)
                                                            @case(1)
                                                            M
                                                            @break
                                                            @case(2)
                                                            E
                                                            @break
                                                            @case(3)
                                                            C
                                                            @break
                                                            @case(4)
                                                            A
                                                            @break
                                                        @endswitch
                                                    </strong>
                                                </td>
                                                <td><strong>{{ $class->classMembers->count() }}</strong><span>명</span></td>
                                                <td><strong>{{ $class->teamCount }}</strong><span>개</span></td>
                                            </tr>
                                            <tr>
                                                <td>유형</td>
                                                <td>학생</td>
                                                <td>팀</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="dash-top__admin__wrap m-block scroll-sm">
                        @foreach($classes as $class)
                            <div class="dash-top__admin">
                                <button class="admin-btn" onclick="location.href='{{ route('lectureDetailInfo', ['classApplyId' => $class->classApply->id]) }}'">관리</button>
                                <div class="img-wrap">
                                    @switch($class->classApply->meca)
                                        @case(1)
                                        <span class="m"></span>
                                        @break
                                        @case(2)
                                        <span class="e"></span>
                                        @break
                                        @case(3)
                                        <span class="c"></span>
                                        @break
                                        @case(4)
                                        <span class="a"></span>
                                        @break
                                    @endswitch
                                </div>
                                <span class="name">{{ $class->classObject2->gwamokNm }}</span>
                                <table class="con">
                                    <colgroup>
                                        <col width="33%" />
                                        <col width="33%" />
                                        <col width="33%" />
                                    </colgroup>
                                    <tr>
                                        <td>
                                            <strong>
                                                @switch($class->classApply->meca)
                                                    @case(1)
                                                    M
                                                    @break
                                                    @case(2)
                                                    E
                                                    @break
                                                    @case(3)
                                                    C
                                                    @break
                                                    @case(4)
                                                    A
                                                    @break
                                                @endswitch
                                            </strong>
                                        </td>
                                        <td><strong>{{ $class->classMembers->count() }}</strong><span>명</span></td>
                                        <td><strong>{{ $class->teamCount }}</strong><span>개</span></td>
                                    </tr>
                                    <tr>
                                        <td>유형</td>
                                        <td>학생</td>
                                        <td>팀</td>
                                    </tr>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="dash-board">
                    <div class="col-8 float-left margin-top">
                        <pro-log :user="{{auth()->user()}}"></pro-log>
                        <h4 class="sub-tit">새 알림</h4>
                        {{--                        <ul class="alarm-list">--}}
                        {{--                            <li class="alarm-list__item center"><span class="name">PBL센터</span>가 수업개설 신청을 승인했습니다.<span class="date">3/19금, 11:30</span></li>--}}
                        {{--                            <li class="alarm-list__item center"><span class="name">PBL센터</span>가 수업개설 신청을 승인했습니다.<span class="date">3/19금, 11:30</span></li>--}}
                        {{--                            <li class="alarm-list__item"><span class="name">박상지 학생</span>이 김민주님을 태그했습니다.<span class="date">3/19금, 11:30</span></li>--}}
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
                            @foreach($classes as $class)
                                <?php
                                $problems = 0;
                                $reflections = 0;
                                $teams = 0;
                                ?>
                                @foreach($class->prt as $prt)
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
                                <ul class="current-list con">
                                    <li class="current-list__item"><span class="tit">{{ $class->classObject2->gwamokNm }}</span></li>
                                    <li class="current-list__item"><span class="circle color1">{{ $problems }}</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                    <li class="current-list__item"><span class="circle color2">{{ $teams }}</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                    <li class="current-list__item"><span class="circle color3">0</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                    <li class="current-list__item"><span class="circle color4">{{ $reflections }}</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
                                </ul>
                                @if($class->count() % 2 === 1)
                                    <ul class="current-list con after-none"></ul>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="col-8 float-left">
                        <sch-log :user="{{auth()->user()}}" :today="{{$today}}"></sch-log>
{{--                        <sch-log :user="{{auth()->user()}}" :today="{{01}}"></sch-log>--}}
                        <h4 class="sub-tit">주요 일정</h4>
                        <a href="" class="btn-more">더보기</a>
                    </div>
                    <div class="col-4 float-right s-activity">
                        <h4 class="sub-tit">실시간 활동</h4>
{{--                        <a href="" class="btn-more">더보기</a>--}}
                        {{--                        <ul class="activity-list scroll-sm">--}}
                        {{--                            @for($i=0;$i<10;$i++)--}}
                        {{--                            <li class="activity-list__item team2"><span class="name">(2팀) 박성진</span> [팀활동] ‘3주차 참고사항’ <p class="detail">블록체인 심화 3/19 금, 11:30</p></li>--}}
                        {{--                            <li class="activity-list__item team1"><span class="name">(1팀) 박성진</span> [팀활동] ‘3주차 참고사항’ <p class="detail">블록체인 심화 3/19 금, 11:30</p></li>--}}
                        {{--                            @endfor--}}
                        {{--                        </ul>--}}
                        <act-log :user="{{auth()->user()}}"></act-log>
                    </div>
                </div>

{{--                <ul class="dash-tab-menu">--}}
{{--                    @foreach($classes as $key=>$class)--}}
{{--                        <li class="dash-tab-menu__item @if(!$key) active @endif" name='tab{{ $key }}' onclick="tabActive(this, {{ $class->classObjectId }})">{{ $class->classObject2->gwamokNm }}</li>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--                <div class="dash-tab__wrap contents__wrap">--}}
{{--                    @foreach($classes as $key=>$class)--}}
{{--                        <div class="dash-tab__con @if(!$key) active @endif" id='tab{{ $key }}'>--}}
{{--                            <div class="left">--}}
{{--                                <h4 class="con-tit">학습활동 현황</h4>--}}
{{--                                <div id="graph{{ $class->classObjectId }}_1" class="mark">--}}
{{--                                    <h5 class="sub-tit">협력활동<span class="text-right">(개)</span></h5>--}}
{{--                                </div>--}}
{{--                                <div id="graph{{ $class->classObjectId }}_2" class="mark">--}}
{{--                                    <h5 class="sub-tit">성취활동</h5>--}}
{{--                                </div>--}}
{{--                                <div id="graph{{ $class->classObjectId }}_3" class="mark">--}}
{{--                                    <h5 class="sub-tit">피드백</h5>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="right">--}}
{{--                                <h4 class="con-tit">학습활동 점수</h4>--}}
{{--                                <div id="graph{{ $class->classObjectId }}_4">--}}
{{--                                    <h5 class="sub-tit">팀<span class="text-right">(점)</span></h5>--}}
{{--                                </div>--}}
{{--                                <div id="graph{{ $class->classObjectId }}_5">--}}
{{--                                    <h5 class="sub-tit">개인</h5>--}}
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

    const handleHorizontalBarChart = (data, id, title = null) => {
        let max = d3.max(data, d => d.value);
        let min = d3.min(data, d => d.value);

        const margin = ({top: 10, right: 40, bottom: 10, left: 20})
        const width = 550;
        const height = title ? 300 : 160;
        const barHeight = (height - 30) / 8;

        const color = [
            '#aab4bc',
            '#ff3c0f',
            '#ffa019',
            '#ffd618',
            '#ff3c0f',
            '#1d496e',
        ];
        const circleLineColor = [
            '#aab4bc',
            '#ff3c0f',
            '#ffa019',
            '#ffd618',
            '#ff3c0f',
            '#1d496e',
        ];

        if (!title) {
            color.splice(0, 1);
            circleLineColor.splice(0, 1);
        }

        const x = d3.scaleLinear()
            .domain([0, (max === '0' || max === '') ? 100 : max])
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
            .attr("y", (d, i) => title ? y(i) + 6 : y(i) + 1)
            .attr("width", d => x(d.value) - x(0))
            .attr("height", 8)
            .attr('fill', (d, i) => color[i])

        svg.append("g")
            // .attr("fill", "#616cec")
            // .attr("stroke", "#616cec")
            // .attr("stroke-width", "0.3")
            .attr("text-anchor", "start")
            // .attr("font-family", "NanumSquare")
            .attr("font-size", 16)
            .selectAll("text")
            .data(data)
            .join("text")
            .attr("x", d => x(d.value))
            .attr("y", (d, i) => title ? y(i) : y(i) + 12)
            .attr("dy", barHeight - 18)
            .attr("dx", d => (x(d.value) - x(0) > 30) ? 10 : 20)
            .attr('font-weight', '600')
            // .attr("stroke", (d, i) => { if (i < 1) return '#00b9a1'; else return '#7d909f'; })
            // .attr("stroke-width", "0.3")
            // .attr("fill", (d, i) => { if (i < 1) return '#00b9a1'; else return '#7d909f'; })
            // .text(d => (x(d.value) - x(0) > 30) ? d.value : '')
            .text(d => d.value)

        svg.append("g")
            .attr("stroke-linecap", "round")
            .attr("stroke-linejoin", "round")
            // .attr("fill", "none")
            .selectAll("circle")
            .data(data)
            .join("circle")
            .attr('cx', x(0))
            .attr('cy', (d, i) => y(i) + barHeight / 3)
            .attr('r', (d, i) => title ? barHeight / 3 : barHeight / 1.5)
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
            .attr("y", (d, i) => y(i) + barHeight / 3)
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
            .attr("y", (d, i) => title ? y(i) + 25 : y(i) + 20)
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
            '#ff95a1',
            '#dea5e0',
            '#8e8fcb',
            '#75a9d2',
            '#89dbe0',
            '#aab4bc',
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
                url: "{{ route('outGraphData', ['classObjectId' => '0']) }}" + id,
                method: 'get',
            }).then(function (response) {
                const coop = response.data.coop.individual;
                const _coop = response.data.coop.team;
                const coopKey = Object.keys(_coop);
                const data = [
                    { name: '수업 평균', value: response.data.coop.class, rank: null },
                    { name: coop[0] ? coop[0]['userName'] + (coop[0]['teamName'] !== null ? '(' + coop[0]['teamName'] + ')' : '') : ''
                        , value: coop[0] ? coop[0]['score'] : '', rank: '1' },
                    { name: coop[1] ? coop[1]['userName'] + (coop[1]['teamName'] !== null ? '(' + coop[1]['teamName'] + ')' : '') : ''
                        , value: coop[1] ? coop[1]['score'] : '', rank: '2' },
                    { name: coop[2] ? coop[2]['userName'] + (coop[2]['teamName'] !== null ? '(' + coop[2]['teamName'] + ')' : '') : ''
                        , value: coop[2] ? coop[2]['score'] : '', rank: '3' },
                    { name: _coop[coopKey[0]] ? _coop[coopKey[0]].name : '', value: _coop[coopKey[0]] ? _coop[coopKey[0]].score : '', rank: '1'  },
                    { name: _coop[coopKey[1]] ? _coop[coopKey[1]].name : '', value: _coop[coopKey[1]] ? _coop[coopKey[1]].score : '', rank: coopKey[1]  },
                ];

                const achievement = response.data.achievement.individual;
                const _achievement = response.data.achievement.team;
                const achievementKey = Object.keys(_achievement);
                const data_2 = [
                    { name: '수업 평균', value: response.data.achievement.class, rank: null },
                    { name: achievement[0] ? achievement[0]['userName'] + (achievement[0]['teamName'] !== null ? '(' + achievement[0]['teamName'] + ')' : '') : ''
                        , value: achievement[0] ? achievement[0]['score'] : '', rank: '1' },
                    { name: achievement[1] ? achievement[1]['userName'] + (achievement[1]['teamName'] !== null ? '(' + achievement[1]['teamName'] + ')' : '') : ''
                        , value: achievement[1] ? achievement[1]['score'] : '', rank: '2' },
                    { name: achievement[2] ? achievement[2]['userName'] + (achievement[2]['teamName'] !== null ? '(' + achievement[2]['teamName'] + ')' : '') : ''
                        , value: achievement[2] ? achievement[2]['score'] : '', rank: '3' },
                    { name: _achievement[achievementKey[0]] ? _achievement[achievementKey[0]].name : '', value: _achievement[achievementKey[0]] ? _achievement[achievementKey[0]].score : '', rank: '1'  },
                    { name: _achievement[achievementKey[1]] ? _achievement[achievementKey[1]].name : '', value: _achievement[achievementKey[1]] ? _achievement[achievementKey[1]].score : '', rank: achievementKey[1]  },
                ];

                const feedback = response.data.feedback.individual;
                const _feedback = response.data.feedback.team;
                const feedbackKey = Object.keys(_feedback);
                const data_3 = [
                    { name: '수업 평균', value: response.data.feedback.class, rank: null },
                    { name: feedback[0] ? feedback[0]['userName'] + (feedback[0]['teamName'] !== null ? '(' + feedback[0]['teamName'] + ')' : '') : ''
                        , value: feedback[0] ? feedback[0]['score'] : '', rank: '1' },
                    { name: feedback[1] ? feedback[1]['userName'] + (feedback[1]['teamName'] !== null ? '(' + feedback[1]['teamName'] + ')' : '') : ''
                        , value: feedback[1] ? feedback[1]['score'] : '', rank: '2' },
                    { name: feedback[2] ? feedback[2]['userName'] + (feedback[2]['teamName'] !== null ? '(' + feedback[2]['teamName'] + ')' : '') : ''
                        , value: feedback[2] ? feedback[2]['score'] : '', rank: '3' },
                    { name: _feedback[feedbackKey[0]] ? _feedback[feedbackKey[0]].name : '', value: _feedback[feedbackKey[0]] ? _feedback[feedbackKey[0]].score : '', rank: '1'  },
                    { name: _feedback[feedbackKey[1]] ? _feedback[feedbackKey[1]].name : '', value: _feedback[feedbackKey[1]] ? _feedback[feedbackKey[1]].score : '', rank: feedbackKey[1]  },
                ];

                handleHorizontalBarChart(data, '#graph'+ id +'_1', '협력활동');
                handleHorizontalBarChart(data_2, '#graph'+ id +'_2', '성취활동');
                handleHorizontalBarChart(data_3, '#graph'+ id +'_3', '피드백');

                const teamRank = response.data.teamRank;
                const data2 = [
                    { name: (teamRank[0] && teamRank[0].id) ? teamRank[0].name : '', name2: (teamRank[0] && teamRank[0].id) ? '평균' : '' , value: (teamRank[0] && teamRank[0].id) ? teamRank[0].rankScore : '' },
                    { name: (teamRank[1] && teamRank[1].id) ? teamRank[1].name : '', name2: (teamRank[1] && teamRank[1].id) ? '평균' : '' , value: (teamRank[1] && teamRank[1].id) ? teamRank[1].rankScore : '' },
                    { name: (teamRank[2] && teamRank[2].id) ? teamRank[2].name : '', name2: (teamRank[2] && teamRank[2].id) ? '평균' : '' , value: (teamRank[2] && teamRank[2].id) ? teamRank[2].rankScore : '' },
                    { name: (teamRank[3] && teamRank[3].id) ? teamRank[3].name : '', name2: (teamRank[3] && teamRank[3].id) ? '평균' : '' , value: (teamRank[3] && teamRank[3].id) ? teamRank[3].rankScore : '' },
                    { name: (teamRank[4] && teamRank[4].id) ? teamRank[4].name : '', name2: (teamRank[4] && teamRank[4].id) ? '평균' : '' , value: (teamRank[4] && teamRank[4].id) ? teamRank[4].rankScore : '' },
                    { name: '팀', name2: '평균', value: teamRank[teamRank.length - 1].rankScore },
                ];

                handleVerticalBarChart(data2, '#graph'+ id +'_4');

                const userRank = response.data.userRank;
                const data3 = [
                    { name: userRank[0] ? userRank[0].userName + (userRank[0].teamName !== null ? '(' + userRank[0].teamName + ')' : '') : '' , value: userRank[0] ? userRank[0].rankScore : '', rank: '1' },
                    { name: userRank[1] ? userRank[1].userName + (userRank[1].teamName !== null ? '(' + userRank[1].teamName + ')' : '') : '' , value: userRank[1] ? userRank[1].rankScore : '', rank: '2'  },
                    { name: userRank[2] ? userRank[2].userName + (userRank[2].teamName !== null ? '(' + userRank[2].teamName + ')' : '') : '', value: userRank[2] ? userRank[2].rankScore : '', rank: '3'  },
                ];

                handleHorizontalBarChart(data3, '#graph'+ id +'_5');
            });
        }

        document.querySelector('.dash-tab-menu__item.active').classList.remove('active');
        target.classList.add('active');
        document.querySelector('.dash-tab__con.active').classList.remove('active');
        document.getElementById(activeName).classList.add('active');
    }

    window.onload = () => {
        $(".lecture-list.m-none").slick({
            slidesToShow: 2,
            infinite: false,
        });
        $(".lecture-list.m-block").slick({
            slidesToShow: 1,
            infinite: false,
        });
        $(".admin-slick").slick({
            slidesToShow: 2,
            infinite: false,
        });

        $('[name="tab0"]').click();
    }

</script>