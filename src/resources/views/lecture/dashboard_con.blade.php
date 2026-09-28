@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            {{--            <h3 class="dash-tit"><span>교수자 대시보드</span></h3>--}}
            <div class="dash__wrap">
                <dash-top
                        :user="{{auth()->user()}}"
                ></dash-top>

                <div class="dash-board con">
                    <div class="col-4 mr-15">
                        <h4 class="sub-tit">개설신청 현황</h4>
                        <ul>
                            <li class="mb-10">AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                            <li class="mb-10">AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                            <li>AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                        </ul>
                    </div>
                    <div class="col-4 mr-15">
                        <h4 class="sub-tit">기초교육 현황</h4>
                        <ul>
                            <li class="mb-10">AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                            <li class="mb-10">AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                            <li>AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                        </ul>
                    </div>
                    <div class="col-4">
                        <h4 class="sub-tit">컨설팅 현황</h4>
                        <ul>
                            <li class="mb-10">AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                            <li class="mb-10">AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                            <li>AI의 이해<span>김민주</span><span class="badge badge-primary">대기</span></li>
                        </ul>
                    </div>

                    <div class="col-12">
                        <select class="float-right nice-select custom-select">
                            <option>2021-1학기</option>
                        </select>
                        <h4 class="sub-tit">수업 현황</h4>
                        <div class="col-5">
                            <h5>유형(Before)<span>(개)</span></h5>
                            <table>
                                <tr>
                                    <td>M</td>
                                    <td>E</td>
                                    <td>C</td>
                                    <td>A</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>5</td>
                                    <td>8</td>
                                    <td>7</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-5">
                            <h5>유형(After)<span>(개)</span></h5>
                            <table>
                                <tr>
                                    <td>M</td>
                                    <td>E</td>
                                    <td>C</td>
                                    <td>A</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>5</td>
                                    <td>8</td>
                                    <td>7</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-2">
                            <h5>인증서 발급현황</h5>
                            85/100
                        </div>
                        <div class="col-9">
                            <ul class="current-list tit">
                                <li class="current-list__item">수업</li>
                                <li class="current-list__item">문제분석</li>
                                <li class="current-list__item">팀활동</li>
                                <li class="current-list__item">평가</li>
                                <li class="current-list__item">성찰</li>
                            </ul>
                            <div class="current-list__wrap scroll-sm">
                                <ul class="current-list con">
                                    <li class="current-list__item">
                                        <span class="tit">테스트</span>
                                    </li>
                                    <li class="current-list__item">
                                        <span class="circle color1">2</span>
                                        <span class="hover">읽지 않은 새 활동 2개가 있습니다.</span>
                                    </li>
                                    <li class="current-list__item">
                                        <span class="circle color2">3</span>
                                        <span class="hover">읽지 않은 새 활동 2개가 있습니다.</span>
                                    </li>
                                    <li class="current-list__item">
                                        <span class="circle color3">0</span>
                                        <span class="hover">읽지 않은 새 활동 2개가 있습니다.</span>
                                    </li>
                                    <li class="current-list__item">
                                        <span class="circle color4">0</span>
                                        <span class="hover">읽지 않은 새 활동 2개가 있습니다.</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-3">
                            <h5>교수학습</h5>
                            <div>
                                <span>8</span>/10<br />
                                요약보고서
                            </div>
                            <div>
                                <span>5</span>/10<br />
                                포트폴리오
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <h4 class="sub-tit">학기별 현황</h4>
                        <div id="dashGraph"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

<script src="{{ asset('js/d3.js') }}"></script>
<script type="text/javascript">

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

    const tabActive = (target) => {
        const activeName = target.getAttribute('name');
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
        // $(".current-list__wrap").slick({
        //     slidesToShow: 2,
        //     slidesToScroll: 2,
        //     infinite: false,
        // });
        $(".admin-slick").slick({
            slidesToShow: 2,
            infinite: false,
        });

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
    }

</script>