@extends('admin.layouts.admin')

@section('script')
    <script src="{{ asset('js/d3.js') }}"></script>

    <script>
        const handleDrawDashGraph = (data, id) => {
            const min = d3.min(data, (d) => d3.min([d.value1, d.value2, d.value3]));
            const max = d3.max(data, (d) => d3.max([d.value1, d.value2, d.value3]));

            const min1 = d3.min(data, (d) => d.value1);
            const max1 = d3.max(data, (d) => d.value1);
            const middle1 = (min1 + max1) / 2;

            const min2 = d3.min(data, (d) => d.value2);
            const max2 = d3.max(data, (d) => d.value2);
            const middle2 = (min2 + max2) / 2;

            //design settings
            const height = 300;
            const width = 1200;
            const margin = ({top: 30, right: 30, bottom: 35, left: 30});
            const barWidth = 30;
            const valueFontSize = 12;
            const nameFontSize = 13;
            const radius = 3;
            const labelPadding = 4;
            const circleToCircleLineWidth = 1.5;

            const lineGraphColor = '#f19d9d';
            const lineGraphTextColor = '#c61818';

            const verticalGraphColor = '#9b9dd9';
            const verticalGraphTextColor = '#1d3972';

            // const spanColor = [
            //     '#fedede', '#c5c6e9'
            // ]
            //
            // const left = [
            //     {
            //         y: middle1,
            //         text: '학생 수'
            //
            //     },
            //     {
            //         y: middle2,
            //         text: '수업 수'
            //     }
            // ];
            //end design settings

            const series = data.columns.slice(1).map(key => data.map(({[key]: value, name1}, i) => ({i, name1, key, value})));

            const z = (key) => {
                switch(key) {
                    case 'value1':
                        return lineGraphColor;
                    // case 'value2':
                    //     return '#616cec';
                    // case 'value3':
                    //     return '#ff2a66';
                    default:
                        return 'black';
                }
            };
            const y = d3.scaleLinear()
                .domain([min - 10, max + 10])
                .range([height - margin.bottom, margin.top]);
            const x = d3.scaleUtc()
                .domain([0, 6])
                .range([margin.left, width - margin.right]);

            const svg = d3.select(id).append('svg')
                .attr("viewBox", [0, 0, width, height]);

            // svg.append("g")
            //     .selectAll("rect")
            //     .data(left)
            //     .join("rect")
            //     .attr("x", x(0) - 90)
            //     .attr("y", d => y(d.y))
            //     .attr("height", 30)
            //     .attr("width", 60)
            //     .attr("fill", (d, i) => spanColor[i])
            //
            // svg.append("g")
            //     .attr("fill", "#0c161e")
            //     .attr("stroke", "#0c161e")
            //     .attr("stroke-width", "0.1")
            //     .attr("text-anchor", "middle")
            //     // .attr("font-family", "NanumSquare")
            //     .attr("font-size", nameFontSize)
            //     .selectAll("text")
            //     .data(left)
            //     .join("text")
            //     .attr("x", x(0) - 90 + (60 / 2))
            //     .attr("y", d => y(d.y))
            //     .attr("dy", 20)
            //     .attr("dx", 0)
            //     .text(d => d.text)

            svg.append("g")
                .attr("fill", verticalGraphColor)
                .selectAll("rect")
                .data(data)
                .join("rect")
                .attr("x", (d, i) => x(i) - (barWidth/ 2))
                .attr("y", d => y(d.value2))
                .attr("height", d => y(min - 10) - y(d.value2))
                .attr("width", barWidth)

            svg.append("g")
                .attr("fill", verticalGraphTextColor)
                .attr("stroke", verticalGraphTextColor)
                .attr("stroke-width", "0.1")
                .attr("text-anchor", "middle")
                // .attr("font-family", "NanumSquare")
                .attr("font-size", valueFontSize)
                .selectAll("text")
                .data(data)
                .join("text")
                .attr("x", (d, i) => x(i))
                .attr("y", d => y(d.value2))
                .attr("dy", -10)
                .attr("dx", 0)
                .text(d => d.value2)

            const line = d3.line()([[0, y(min-10)], [width, y(min-10)]]);

            svg.append("path")
                .attr("d", line)
                .attr("stroke", "#cfdbe5")

            svg.append("g")
                .attr("fill", lineGraphTextColor)
                .attr("stroke", lineGraphTextColor)
                .attr("stroke-width", "0.1")
                .attr("text-anchor", "middle")
                // .attr("font-family", "NanumSquare")
                .attr("font-size", valueFontSize)
                .selectAll("text")
                .data(data)
                .join("text")
                .attr("x", (d, i) => x(i))
                .attr("y", d => y(d.value1))
                .attr("dy", -10)
                .attr("dx", 0)
                .text(d => d.value1)

            const serie = svg.append("g")
                .selectAll("g")
                .data([series[0]])
                .join("g");

            serie.append("path")
                .attr("fill", "none")
                .attr("stroke", d => z(d[0].key))
                .attr("stroke-width", circleToCircleLineWidth)
                .attr("d", d3.line()
                    .x(d => x(d.i))
                    .y(d => y(d.value)));

            serie.append("g")
                .attr("stroke-linecap", "round")
                .attr("stroke-linejoin", "round")
                .attr("fill", "white")
                .selectAll("circle")
                .data(d => d)
                .join("circle")
                .attr('cx', d => x(d.i) )
                .attr('cy', d => y(d.value))
                .attr('r', radius)
                .clone(true).lower()
                .attr("fill", "none")
                .attr("stroke", d => z(d.key))
                .attr("stroke-width", labelPadding);

            svg.append("g")
                .attr("fill", "#0c161e")
                .attr("stroke", "#0c161e")
                .attr("stroke-width", "0.1")
                .attr("text-anchor", "middle")
                // .attr("font-family", "NanumSquare")
                .attr("font-size", nameFontSize)
                .selectAll("text")
                .data(data)
                .join("text")
                .attr("x", (d, i) => x(i))
                .attr("y", y(min - 10))
                .attr("dy", 14)
                .attr("dx", 0)
                .text(d => d.name1)
        }

        const handleHorizontalBarChart = (data, id, title = null) => {
            const max = d3.max(data, d => d.value);
            const min = d3.min(data, d => d.value);

            const margin = ({top: 10, right: title ? 10 : 30, bottom: 10, left: title ? 100 : 40})
            const width = 550;
            const height = 200;
            const barHeight = (height - 30) / (title ? 8 : 4);

            const color = [
                '#a4d3e5',
                '#ffe699',
                '#eaeaf5',
                '#c7e4ef',
                '#ffe0a0',
                '#f09898',
            ];
            const circleLineColor = [
                'none',
                '#ffbf05',
                '#6060b4',
                '#64b4d3',
                '#ffc30b',
                '#ee8a8a',
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

            // svg.append("g")
            //     .attr("class", "grid")
            //     .attr('color', '#e8f1f8')
            //     .attr("transform", "translate(0," + (height - 20) + ")")
            //     .call(d3.axisBottom(x)
            //         .ticks(4)
            //         .tickSize(-height + 40)
            //         .tickFormat(format)
            //     )

            // svg.selectAll('text').style('font-size', 12).style('color', '#7d909f').style('transform', 'translate(0,5px)')

            ///////////////////////////////////////////////////////////////////////////////////////

            // svg.append("g")
            //     .attr("fill", "#0c161e")
            //     .attr("stroke", "#0c161e")
            //     .attr("stroke-width", "0.3")
            //     .attr("text-anchor", "left")
            //     .attr("font-family", "NanumSquare")
            //     .attr("font-size", 15)
            //     .selectAll("text")
            //     .data(data)
            //     .join("text")
            //     .attr("x", d => x(0))
            //     .attr("y", (d, i) => y(i))
            //     .attr("dy", d => { if (d.hasOwnProperty('name2')) { return ".75em"; } else return "1.1em"; })
            //     .attr("dx", -10)
            //     .text(d => d.name)

            // svg.append("g")
            //     .attr("fill", "#0c161e")
            //     .attr("stroke", "#0c161e")
            //     .attr("stroke-width", "0.3")
            //     .attr("text-anchor", "end")
            //     .attr("font-family", "NanumSquare")
            //     .attr("font-size", 11)
            //     .selectAll("text")
            //     .data(data)
            //     .join("text")
            //     .attr("x", d => x(min))
            //     .attr("y", (d, i) => y(i) + i * 10 - 4)
            //     .attr("dy", "2.3em")
            //     .attr("dx", -10)
            //     .text(d => { if (d.hasOwnProperty('name2')) { return d.name2; } else return null; })

            svg.append("g")
                // .attr("fill", '#cfdbe5')
                .selectAll("rect")
                .data(data)
                .join("rect")
                .attr("x", x(0))
                .attr("y", (d, i) => y(i))
                .attr("width", d => x(d.value) - x(0))
                .attr("height", barHeight)
                .attr('fill', (d, i) => color[i])

            svg.append("g")
                // .attr("fill", "#616cec")
                // .attr("stroke", "#616cec")
                // .attr("stroke-width", "0.3")
                .attr("text-anchor", "end")
                // .attr("font-family", "NanumSquare")
                .attr("font-size", 15)
                .selectAll("text")
                .data(data)
                .join("text")
                .attr("x", d => x(d.value))
                .attr("y", (d, i) => y(i))
                .attr("dy", barHeight - (title ? 5 : 17))
                .attr("dx", -10)
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
                .attr('cy', (d, i) => y(i) + barHeight / 2)
                .attr('r', (d, i) => d.rank ? barHeight / 2 : 0)
                .attr('fill', (d, i) => color[i])
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
                .attr("y", (d, i) => y(i) + barHeight / 2)
                .attr("dy", 5)
                .attr("dx", 0)
                .text(d => d.rank)

            svg.append("g")
                .attr("text-anchor", "start")
                .attr("font-family", "NanumSquare")
                .attr("font-size", 13)
                .selectAll("text")
                .data(data)
                .join("text")
                .attr("x", x(0))
                .attr("y", (d, i) => y(i) + barHeight / 2)
                .attr("dy", 5)
                .attr("dx", title ? 20 : 30)
                .text(d => d.name)

            if (title) {
                svg.append('text')
                    .attr('text-anchor', 'middle')
                    .attr("font-family", "NanumSquare")
                    .attr("font-size", 15)
                    .attr("x", x(0))
                    .attr("y", (y(2) + y(3)) / 2)
                    .attr("dy", barHeight - 5)
                    .attr("dx", -50)
                    .text(title)
            }
        }

        const handleVerticalBarChart = (data, id) => {
            const margin = ({top: 35, right: 0, bottom: 35, left: 0});

            const height = 300;
            const width = 525;

            const barWidth = 65;

            const color = [
                '#fbb5b5',
                '#ffdfa0',
                '#fbb5b5',
                '#ffdfa0',
                '#a4d3e5',
                '#9b9dd9',
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

        window.onload = () => {
            let _data = [
                { name1: '18.1학기', value1: 150, value2: 50 },
                { name1: '18.2학기', value1: 160, value2: 60 },
                { name1: '19.1학기', value1: 170, value2: 70 },
                { name1: '19.2학기', value1: 170, value2: 70 },
                { name1: '20.1학기', value1: 160, value2: 60 },
                { name1: '20.2학기', value1: 165, value2: 65 },
                { name1: '21.1학기', value1: 200, value2: 80 },
            ];
            _data['columns'] = [
                'name1', 'value1', 'value2'
            ];

            handleDrawDashGraph(_data, '#dashGraph');

            _data = [
                { name: '수업 평균', value: 21, rank: null },
                { name: '김소윤 (2팀)', value: 30, rank: '1' },
                { name: '하지민 (3팀)', value: 29, rank: '2'  },
                { name: '박수정 (5팀)', value: 28, rank: '3'  },
                { name: '3팀', value: 50, rank: '1'  },
                { name: '5팀', value: 20, rank: '5'  },
            ];

            handleHorizontalBarChart(_data, '#graph1', '협력활동');
            handleHorizontalBarChart(_data, '#graph2', '성취활동');
            handleHorizontalBarChart(_data, '#graph3', '피드백');

            const data2 = [
                { name: '1팀', name2: '평균', value: 65 },
                { name: '2팀', name2: '평균', value: 90 },
                { name: '3팀', name2: '평균', value: 65 },
                { name: '4팀', name2: '평균', value: 90 },
                { name: '5팀', name2: '평균', value: 85 },
                { name: '팀', name2: '평균', value: 85 },
            ];
            handleVerticalBarChart(data2, '#graph4');

            _data = [
                { name: '김소윤 (2팀)', value: 98, rank: '1' },
                { name: '하지민 (3팀)', value: 93, rank: '2'  },
                { name: '박수정 (5팀)', value: 89, rank: '3'  },
            ];
            handleHorizontalBarChart(_data, '#graph5');
        }
    </script>
@endsection

@section('content')
{{--    <button onclick="location.href='{{ route('admin.class1View') }}'">임시 버튼(class1)</button>--}}
{{--    <button>임시 버튼(basic1)</button>--}}
{{--    <button>임시 버튼(consulting1)</button>--}}
{{--    <button onclick="location.href='{{ route('admin.memberView') }}'">임시 버튼(member)</button>--}}
{{--    <button onclick="location.href='{{ route('admin.noticeView') }}'">임시 버튼(notice)</button>--}}
@php
    $loop = 0;
@endphp
    <div class="card-body dashboard">
        <div class="card-body row">
            <div class="dash-top card-body">
                <div class="dash-top__img">
                    <img src="" alt="" onerror="this.style.display='none';" />
                </div>
                <div class="dash-top__txt">
                    <span class="name">{{ Auth::user()->name }}</span>님, 환영합니다!<br>새 알림을 확인해보세요.
                </div>
                <admin-log></admin-log>
{{--                <ul class="dash-top__list">--}}
{{--                    <li class="dash-top__list__item">--}}
{{--                        <span class="text-primary">김민주 교수</span>가 "<span class="text-primary">AI의 이해</span>" 수업을 <span class="text-primary">개설 신청</span>했습니다.--}}
{{--                        <span class="date">3/19 금, 11:30</span>--}}
{{--                    </li>--}}
{{--                    <li class="dash-top__list__item">--}}
{{--                        <span class="text-primary">김민주 교수</span>가 "<span class="text-primary">AI의 이해</span>" 수업을 <span class="text-primary">개설 신청</span>했습니다.--}}
{{--                        <span class="date">3/19 금, 11:30</span>--}}
{{--                    </li>--}}
{{--                    <li class="dash-top__list__item">--}}
{{--                        <span class="text-primary">김민주 교수</span>가 "<span class="text-primary">AI의 이해</span>" 수업을 <span class="text-primary">개설 신청</span>했습니다.--}}
{{--                        <span class="date">3/19 금, 11:30</span>--}}
{{--                    </li>--}}
{{--                </ul>--}}
            </div>

            <div class="col-md-4 card-body">
                <h4><a href="{{ route('admin.class1View') }}">개설 신청현황({{ $classApplies->count() }}건)
                        <i class="fas fa-arrow-right"></i></a></h4>
                <div class="list-group__wrap scroll-sm">
                    <ul class="list-group">
                    @php
                        $i=0;
                    @endphp
                    @foreach($classApplies as $classApply)
                        <li class="list-group-item sm">
                            <span>{{ $classApply->korName }}</span>{{ $classApply->aplName }}
                            @if($classApply->state == 'wait')<button class="btn btn-primary btn-xs fr" onclick="location.href='{{ route('admin.classDetail1View', ['classApplyId' => $classApply->id]) }}'">
                                대기
                            </button>
                            @elseif($classApply->state == 'complete')
                                <button class="btn btn-light btn-xs fr" onclick="location.href='{{ route('admin.classDetail1View', ['classApplyId' => $classApply->id]) }}'">
                                    승인
                                </button>
                            @endif
                        </li>
{{--                        @break($loop->iteration==3)--}}
                    @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-4 card-body">
                <h4><a href="{{ route('admin.basic1View') }}">기초교육 신청현황({{ $basicApplies->count() }}건) <i class="fas fa-arrow-right"></i></a></h4>
                <div class="list-group__wrap scroll-sm">
                    <ul class="list-group">
                        @foreach($basicApplies as $basicApply)
                            <li class="list-group-item sm">
                                <span>{{ $basicApply->basic->title }}</span>{{ $basicApply->user()->first()->name }}
                                @if($basicApply->state == '0')
                                <button class="btn btn-primary btn-xs fr" onclick="location.href='{{ route('admin.basic1View') }}'">대기</button>
                                @elseif($basicApply->state == '1')
                                <button class="btn btn-light btn-xs fr" onclick="location.href='{{ route('admin.basic2View') }}'">승인</button>
                                @endif

                            </li>
    {{--                        @if($loop->iteration==3) @break @endif--}}
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-4 card-body">
                <h4><a href="{{ route('admin.consulting1View') }}">컨설팅 신청현황({{ $consultingApplies->count() }}건) <i class="fas fa-arrow-right"></i></a></h4>
                <div class="list-group__wrap scroll-sm">
                    <ul class="list-group">
                        @foreach($consultingApplies as $consultingApply)
                            <li class="list-group-item sm">
                                <span>{{ $consultingApply->consulting->title }}</span>{{ $consultingApply->user()->first()->name }}
                                @if($consultingApply->state == '0')
                                    <button class="btn btn-primary btn-xs fr" onclick="location.href='{{ route('admin.consulting1View') }}'">대기</button>
                                @elseif($consultingApply->state == '1')
                                    <button class="btn btn-light btn-xs fr" onclick="location.href='{{ route('admin.consulting2View') }}'">승인</button>
                                @endif
                            </li>
    {{--                        @if($loop->iteration==3) @break @endif--}}
                        @endforeach
    {{--                    @if(sizeof($consultingApplies) < 3)--}}
    {{--                        @for($i=0;$i<3-sizeof($consultingApplies);$i++)--}}
    {{--                            <li class="list-group-item sm"></li>--}}
    {{--                        @endfor--}}
    {{--                    @endif--}}
                    </ul>
                </div>
            </div>
        </div>

{{--        <div class="card-body">--}}
{{--            <h4>--}}
{{--            <select class="form-control w-sm">--}}
{{--                <option>--}}
{{--                    2021--}}
{{--                </option>--}}
{{--                <option>--}}
{{--                    2022--}}
{{--                </option>--}}
{{--                <option>--}}
{{--                    2023--}}
{{--                </option>--}}
{{--                <option>--}}
{{--                    2024--}}
{{--                </option>--}}
{{--                <option>--}}
{{--                    2025--}}
{{--                </option>--}}
{{--            </select> 년--}}
{{--            <select class="form-control w-sm">--}}
{{--                <option>1</option>--}}
{{--                <option>2</option>--}}
{{--            </select>--}}
{{--            학기 학과별 개설현황</h4>--}}

{{--            <table class="table table-bordered text-center m-b-20">--}}
{{--                <colgroup>--}}
{{--                    <col width="22%" />--}}
{{--                    <col width="11%" />--}}
{{--                    <col width="22%" />--}}
{{--                    <col width="11%" />--}}
{{--                    <col width="22%" />--}}
{{--                    <col width="11%" />--}}
{{--                </colgroup>--}}
{{--                <thead>--}}
{{--                    <th>학과</th>--}}
{{--                    <th>인원</th>--}}
{{--                    <th>학과</th>--}}
{{--                    <th>인원</th>--}}
{{--                    <th>학과</th>--}}
{{--                    <th>인원</th>--}}
{{--                </thead>--}}
{{--                <tbody>--}}
{{--                    @foreach($departments as $key=>$department)--}}
{{--                        @if ($loop->iteration % 3 == 1)--}}
{{--                            <tr>--}}
{{--                        @endif--}}
{{--                            <td name="{{ $loop->iteration }}">{{ $department->first()->department }}</td>--}}
{{--                            <td name="{{ $loop->iteration }}">{{ $department->count() }}</td>--}}
{{--                        @if ($loop->iteration % 3 == 3)--}}
{{--                            </tr>--}}
{{--                        @endif--}}
{{--                        @break($loop->iteration == 15)--}}
{{--                    @endforeach--}}
{{--                </tbody>--}}
{{--            </table>--}}
{{--        </div>--}}
{{--        <div class="card-body">--}}
{{--            <h4>--}}
{{--                <select class="form-control w-sm">--}}
{{--                    <option>--}}
{{--                        2021--}}
{{--                    </option>--}}
{{--                    <option>--}}
{{--                        2022--}}
{{--                    </option>--}}
{{--                    <option>--}}
{{--                        2023--}}
{{--                    </option>--}}
{{--                        2024--}}
{{--                    </option>--}}
{{--                        2025--}}
{{--                    </option>--}}
{{--                </select> 년--}}
{{--                <select class="form-control w-sm">--}}
{{--                    <option>1</option>--}}
{{--                    <option>2</option>--}}
{{--                </select>--}}
{{--                <a href="{{ route('admin.class3View') }}">학기 진행수업({{ $completeClasses->count() }}건) <i class="fas fa-arrow-right"></i></a></h4>--}}
{{--            </h4>--}}
{{--            <table class="table table-bordered text-center m-b-20">--}}
{{--                <colgroup>--}}
{{--                    <col width="22%" />--}}
{{--                    <col width="11%" />--}}
{{--                    <col width="22%" />--}}
{{--                    <col width="11%" />--}}
{{--                    <col width="22%" />--}}
{{--                    <col width="11%" />--}}
{{--                </colgroup>--}}
{{--                <tr>--}}
{{--                    <th>학과</th>--}}
{{--                    <th>인원</th>--}}
{{--                    <th>학과</th>--}}
{{--                    <th>인원</th>--}}
{{--                    <th>학과</th>--}}
{{--                    <th>인원</th>--}}
{{--                </tr>--}}
{{--                @foreach($completeClasses as $completeClass)--}}
{{--                    @if ($loop->iteration % 3 == 1)<tr>@endif--}}
{{--                    <td>{{ $completeClass->korName }}</td>--}}
{{--                    <td>{{ $completeClass->classList()->classObject()->classLists()->count() }}</td>--}}
{{--                    <td>{{ $completeClass->classListUserCount() }}</td>--}}
{{--                    @if ($loop->iteration % 3 == 3)</tr>@endif--}}
{{--                    @break($loop->iteration == 15)--}}
{{--                @endforeach--}}

{{--            </table>--}}
{{--        </div>--}}

        <div class="dash-card__wrap m-t-5">
            <h4 class="m-l-10 m-r-10 p-t-10">
                수업 현황
                <div class="float-right__top">
                    <form method="get" action="{{ route('admin.dashboardView') }}">
                        <select class="form-control w-sm" name="term" onchange="this.form.submit()">
                            <option value="">{{ $year }}년
                                @if($term == '10')
                                    1
                                @elseif($term == '20')
                                    2
                                @endif
                                학기
                            </option>
                            @foreach(range(2010, 2030) as $y)
                                <option value="{{ $y }}-10">{{ $y }}년 1 학기</option>
                                <option value="{{ $y }}-20">{{ $y }}년 2 학기</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </h4>

            <div class="dash-card col-md-5">
                <div class="card-body">
                    <h4 class="fc-gray m-b-20">신청유형<span class="float-right">(개)</span></h4>
                    <table class="meca-table">
                        <colgroup>
                            <col width="25%" />
                            <col width="25%" />
                            <col width="25%" />
                            <col width="25%" />
                        </colgroup>
                        <tr>
                            <th><span class="circle color1">M</span></th>
                            <th><span class="circle color2">E</span></th>
                            <th><span class="circle color3">C</span></th>
                            <th><span class="circle color4">A</span></th>
                        </tr>
                        <tr>
                            <td><span class="text color1">{{ $beforeMeca['count1'] }}</span></td>
                            <td><span class="text color2">{{ $beforeMeca['count2'] }}</span></td>
                            <td><span class="text color3">{{ $beforeMeca['count3'] }}</span></td>
                            <td><span class="text color4">{{ $beforeMeca['count4'] }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="dash-card col-md-5">
                <div class="card-body">
                    <h4 class="fc-gray m-b-20">확정유형<span class="float-right">(개)</span></h4>
                    <table class="meca-table">
                        <colgroup>
                            <col width="25%" />
                            <col width="25%" />
                            <col width="25%" />
                            <col width="25%" />
                        </colgroup>
                        <tr>
                            <th><span class="circle color1">M</span></th>
                            <th><span class="circle color2">E</span></th>
                            <th><span class="circle color3">C</span></th>
                            <th><span class="circle color4">A</span></th>
                        </tr>
                        <tr>
                            <td><span class="text color1">{{ $beforeMeca['count1'] }}</span></td>
                            <td><span class="text color2">{{ $beforeMeca['count2'] }}</span></td>
                            <td><span class="text color3">{{ $beforeMeca['count3'] }}</span></td>
                            <td><span class="text color4">{{ $beforeMeca['count4'] }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="dash-card col-md-2">
                <div class="card-body">
                    <h4 class="fc-gray m-b-20">인증서 발급현황<span class="float-right">(건)</span></h4>
                    <div class="count-wrap">
                        <span class="number point">85</span>
                        <span class="number">/100</span>
                    </div>
                </div>
            </div>
            <div class="dash-card col-md-9">
                <div class="card-body">
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
                                <li class="current-list__item"><span class="tit">{{ $myClass->classObject2->gwamokNm }}</span></li>
                                <li class="current-list__item"><span class="circle color1">{{ $problems }}</span><span class="hover"></span></li>
                                <li class="current-list__item"><span class="circle color2">{{ $teams }}</span><span class="hover"></span></li>
                                <li class="current-list__item"><span class="circle color3">0</span><span class="hover"></span></li>
                                <li class="current-list__item"><span class="circle color4">{{ $reflections }}</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>
{{--                                <li class="current-list__item"><span class="circle color4">3</span><span class="hover">읽지 않은 새 활동 2개가 있습니다.</span></li>--}}
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="dash-card col-md-3">
                <div class="card-body">
                    <h4 class="fc-gray m-b-20">수업 포트폴리오<span class="float-right">(개)</span></h4>
                    <div class="count-wrap col-md-6">
                        <span class="number point">8</span>
                        <span class="number">/10</span>
                        <span class="name">요약보고서</span>
                    </div>
                    <div class="count-wrap col-md-6">
                        <span class="number point text-primary">5</span>
                        <span class="number">/10</span>
                        <span class="name">포트폴리오</span>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
        <div class="dash-card__wrap card-body">
            <h4 class="">운영 현황</h4>
            <div class="col-md-12 graph-wrap">
                <div id="dashGraph">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('graphTest')
{{--    <div>--}}
{{--        <div style="display: inline-block; height: 600px; width: 100%">--}}
{{--            <div style="width: 48%; height: 600px;">--}}
{{--                <h4>학습활동 현황</h4>--}}
{{--                <div>--}}
{{--                    <div style="height: 33.3%" id="graph1">--}}

{{--                    </div>--}}
{{--                    <div style="height: 33.3%" id="graph2">--}}

{{--                    </div>--}}
{{--                    <div style="height: 33.3%" id="graph3">--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div style="width: 48%; height: 600px;">--}}
{{--                <h4>학습활동 점수</h4>--}}
{{--                <div style="height: 570px;">--}}
{{--                    <h4>팀</h4>--}}
{{--                    <div style="height: 307px" id="graph4">--}}

{{--                    </div>--}}
{{--                    <h4>개인</h4>--}}
{{--                    <div style="height: 193px" id="graph5">--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div style="display: flex; justify-content: space-between">--}}
{{--            <div style="width: 48%; height: 600px;">--}}
{{--                <h4>학습활동 현황</h4>--}}
{{--                <div style="height: 570px;">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div style="width: 48%; height: 600px;">--}}
{{--                <h4>학습활동 점수</h4>--}}
{{--                <div class="card graph" style="height: 570px;display: flex; flex-direction: column">--}}
{{--                    <h4>더 분발해주세요!</h4>--}}
{{--                    <div style="flex: 6" id="graph4">--}}

{{--                    </div>--}}
{{--                    <h4>개인 랭킹 32명중 17등</h4>--}}
{{--                    <div style="flex: 4" id="graph5">--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection