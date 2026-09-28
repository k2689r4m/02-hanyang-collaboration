
<template>
    <div>
        <div class="card-body text-center">
            <div class="calendar2__wrap">
                <div class="calendar-month" v-if="!mode">
                    <h2 class="calendar-top mt-0">
                        <button type="button" class="prev-btn" @click="calendarData(-1)"></button>
                        <button type="button" class="year-btn" @click="setMode">{{ year }}. {{ month }}</button>
                        <button type="button" class="next-btn" @click="calendarData(1)"></button>
                        <button type="button" class="btn btn-primary btn-sm fr" @click="addScheduleModalOn">+ 추가</button>
                    </h2>

                    <table class="calendar-table">
                        <colgroup>
                            <col width="14.2%" />
                            <col width="14.2%" />
                            <col width="14.2%" />
                            <col width="14.2%" />
                            <col width="14.2%" />
                            <col width="14.2%" />
                            <col width="14.2%" />
                        </colgroup>
                        <thead>
                        <th v-for="day in days" :key="day">{{ day }}</th>
                        </thead>
                        <tbody>
                        <tr v-for="(date, idx) in dates" :key="idx">
                            <td
                                    v-for="(day, secondIdx) in date"
                                    @click="returnDate(day, (idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day) ? 'x' + secondIdx : day)"
                                    :key="(idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day) ? 'x' + secondIdx : day"
                                    :ref="(idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day) ? 'x' + secondIdx : day"
                                    :class="{
                              'has-text-info-dark': idx === 0 && day >= lastMonthStart,
                              'has-text-danger': dates.length - 1 === idx && nextMonthStart > day,
                              'active': day === today && month === currentMonth && year === currentYear
                          }"
                            >
                                <span>{{ day }}</span>
                                <span v-if="!((idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day))"
                                      v-for="(count) in [0,1,2]"
                                      :class="(daysData[day] && daysData[day][count]) ? 'schedule' : ''">

                                  {{ daysData[day] ? (daysData[day][count] ? daysData[day][count].title : '&nbsp;') : '&nbsp;' }}
                                </span>
                                <span v-if="(daysData[day]) ? (daysData[day].length > 3) ? true : false : false" class="more">+{{ daysData[day].length - 3 }}</span>
                                <span v-else>&nbsp;</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <!--          <button class="today-btn" @click="setToday">오늘 선택</button>-->
                </div>
                <div class="calendar-year" v-else>
                    <div v-for="(y, idx) in yearList" >
                        <button class="year-ac-btn" @click="year = y">{{y}}년</button>
                        <div class="month-wrap" v-if="y === year">
                            <div class="month-btn" v-for="m in 12">
                                <button @click="setDate(y,m)">{{m}}월</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 id="dataDate">{{ year }}.{{ month }}.{{ today }} {{ weekday }}</h4>
            <table class="table table-bordered text-center">
                <colgroup v-if="server.includes('competition')">
                    <col width="10%" />
                    <col width="70%" />
                    <col width="20%" />
                </colgroup>
                <colgroup v-else-if="server.includes('basic')">
                    <col width="10%" />
<!--                    <col width="50%" />-->
                    <col width="65%" />
                    <col width="15%" />
<!--                    <col width="15%" />-->
                    <col width="10%" />
                </colgroup>
                <colgroup v-else-if="server.includes('consulting')">
                    <col width="10%" />
                    <col width="50%" />
                    <col width="15%" />
                    <col width="15%" />
                    <col width="10%" />
                </colgroup>
                <tr v-if="server.includes('competition')">
                    <th>번호</th>
                    <th>공모전명</th>
                    <th>신청자수</th>
                </tr>
                <tr v-else-if="server.includes('basic')">
                    <th>번호</th>
                    <th>기초교육명</th>
                    <th>시간</th>
<!--                    <th>강사명</th>-->
                    <th>참여자수</th>
                </tr>
                <tr v-else-if="server.includes('consulting')">
                    <th>번호</th>
                    <th>컨설팅명</th>
                    <th>시간</th>
                    <th>강사명</th>
                    <th>참여자수</th>
                </tr>
                <tbody id="dataTable">
                </tbody>
            </table>
        </div>

        <div class="popup" v-if="addScheduleModalUp">
            <div class="popup__dim" @mousedown="addScheduleModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con w-500 scroll-sm text-left">
                    <h4 class="popup-tit" v-if="server.includes('competition')">공모전 추가</h4>
                    <h4 class="popup-tit" v-else-if="server.includes('basic')">기초교육 추가</h4>
                    <h4 class="popup-tit" v-else-if="server.includes('consulting')">컨설팅 추가</h4>
                    <ul class="card-modal__con p-0">
                        <li class="card-modal__con__item"  v-if="server.includes('competition')">
                            <label class="no-icon">날짜</label>
                            <input id="selectDate" type="date" class="line" v-model="scheduleDate">
                        </li>
                        <li class="card-modal__con__item" v-if="server.includes('basic') || server.includes('consulting')">
                            <label class="no-icon">시작 날짜</label>
                            <input id="selectDate_" type="date" class="line" v-model="startDate">
                            <span class="m-r-10"></span>

                            <vue-picker class="select" v-model="startHour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>시</span>&nbsp;&nbsp;
                            <vue-picker class="select" v-model="startMin">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>분</span>&nbsp;

                            <br>
                            <label class="no-icon">종료 날짜</label>
                            <input id="selectDate__" type="date" class="line" v-model="endDate">
                            <span class="m-r-10"></span>
                            <vue-picker class="select" v-model="endHour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>시</span>&nbsp;&nbsp;
                            <vue-picker class="select" v-model="endMin">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>분</span>


                        </li>
                        <li class="card-modal__con__item" v-if="server.includes('consulting')">
                            <label class="no-icon">컨설턴트</label>
                            <vue-picker class="select w-100" v-model="consultantSelect">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="(consultant, idx) in consultants">
                                    <vue-picker-option :value="consultant.id+''">
                                        {{consultant.name}} / {{consultant.email}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                        </li>
                        <li class="card-modal__con__item">
                            <label class="no-icon" v-if="server.includes('competition')">공모전명</label>
                            <label class="no-icon" v-else-if="server.includes('basic')">기초교육명</label>
                            <label class="no-icon" v-else-if="server.includes('consulting')">컨설팅명</label>
                            <input type="text" placeholder="내용을 입력해주세요." class="form-control" v-model="scheduleContent"></input>
                        </li>
<!--                        <li class="card-modal__con__item" v-if="server.includes('basic')">-->
<!--                            <label class="no-icon">인원제한</label>-->
<!--                            <vue-picker class="select" v-model="memberLimit">-->
<!--                                <vue-picker-option value="-1">선택</vue-picker-option>-->
<!--                                <template v-for="m in 100">-->
<!--                                    <vue-picker-option :value="Number(m)+''">-->
<!--                                        {{10 <= Number((m)) ? Number(m) + '' : '0'+(Number(m))}}-->
<!--                                    </vue-picker-option>-->
<!--                                </template>-->
<!--                            </vue-picker>-->
<!--                        </li>-->
                    </ul>
                </div>
                <div class="confirm-btn text-center">
                    <button type="button" class="btn btn-light" @mousedown="addScheduleModalOff">취소</button>
                    <button type="button" class="btn btn-primary" @click="saveBtnClick">저장</button>
                </div>
            </div>
        </div>

        <!-- view popup -->
        <div class="popup" v-if="viewScheduleModalUp">
          <div class="popup__dim" @mousedown="viewScheduleModalOff"></div>
          <div class="popup-wrap">
            <div class="popup-con w-500 scroll-sm text-left">
              <h4 class="popup-tit" v-if="server.includes('competition')">공모전 상세보기</h4>
              <h4 class="popup-tit" v-else-if="server.includes('basic')">기초교육 상세보기</h4>
              <h4 class="popup-tit" v-else-if="server.includes('consulting')">컨설팅 상세보기</h4>
              <ul class="card-modal__con p-0">
                <li class="card-modal__con__item"  v-if="server.includes('conpetition')">
                  <label class="no-icon">날짜</label>
                  <input id="selectDateView" type="date" class="line" v-model="scheduleDate">
                </li>
                <li class="card-modal__con__item" v-if="server.includes('basic') || server.includes('consulting')">
                  <label class="no-icon">시작 날짜</label>
                  <input id="selectDateView_" type="date" class="line" v-model="startDate">
                  <span class="m-r-10"></span>

                  <vue-picker class="select" v-model="startHour">
                    <vue-picker-option value="-1">선택</vue-picker-option>
                    <template v-for="h in 24">
                      <vue-picker-option :value="Number(h-1)+''">
                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                      </vue-picker-option>
                    </template>
                  </vue-picker>
                  &nbsp;<span>시</span>&nbsp;&nbsp;
                  <vue-picker class="select" v-model="startMin">
                    <vue-picker-option value="-1">선택</vue-picker-option>
                    <template v-for="m in 60">
                      <vue-picker-option :value="Number(m-1)+''">
                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                      </vue-picker-option>
                    </template>
                  </vue-picker>
                  &nbsp;<span>분</span>&nbsp;

                  <br>
                  <label class="no-icon">종료 날짜</label>
                  <input id="selectDateView__" type="date" class="line" v-model="endDate">
                  <span class="m-r-10"></span>
                  <vue-picker class="select" v-model="endHour">
                    <vue-picker-option value="-1">선택</vue-picker-option>
                    <template v-for="h in 24">
                      <vue-picker-option :value="Number(h-1)+''">
                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                      </vue-picker-option>
                    </template>
                  </vue-picker>
                  &nbsp;<span>시</span>&nbsp;&nbsp;
                  <vue-picker class="select" v-model="endMin">
                    <vue-picker-option value="-1">선택</vue-picker-option>
                    <template v-for="m in 60">
                      <vue-picker-option :value="Number(m-1)+''">
                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                      </vue-picker-option>
                    </template>
                  </vue-picker>
                  &nbsp;<span>분</span>


                </li>
                <li class="card-modal__con__item" v-if="server.includes('consulting')">
                  <label class="no-icon">컨설턴트</label>
                  <vue-picker class="select w-100" v-model="consultantSelect">
                    <vue-picker-option value="-1">선택</vue-picker-option>
                    <template v-for="(consultant, idx) in consultants">
                      <vue-picker-option :value="consultant.id+''">
                        {{consultant.name}} / {{consultant.email}}
                      </vue-picker-option>
                    </template>
                  </vue-picker>
                </li>
                <li class="card-modal__con__item">
                  <label class="no-icon" v-if="server.includes('competition')">공모전명</label>
                  <label class="no-icon" v-else-if="server.includes('basic')">기초교육명</label>
                  <label class="no-icon" v-else-if="server.includes('consulting')">컨설팅명</label>
                  <input type="text" placeholder="내용을 입력해주세요." class="form-control" v-model="scheduleContent"></input>
                </li>
<!--                <li class="card-modal__con__item" v-if="server.includes('basic')">-->
<!--                  <label class="no-icon">인원제한</label>-->
<!--                  <vue-picker class="select" v-model="memberLimit">-->
<!--                    <vue-picker-option value="-1">선택</vue-picker-option>-->
<!--                    <template v-for="m in 100">-->
<!--                      <vue-picker-option :value="Number(m)+''">-->
<!--                        {{10 <= Number((m)) ? Number(m) + '' : '0'+(Number(m))}}-->
<!--                      </vue-picker-option>-->
<!--                    </template>-->
<!--                  </vue-picker>-->
<!--                </li>-->
              </ul>
            </div>
            <div class="confirm-btn text-center">
              <button type="button" class="btn btn-light" @mousedown="viewScheduleModalOff">취소</button>
              <button type="button" class="btn btn-primary" @click="">수정</button>
            </div>
          </div>
        </div>







        <div class="popup" v-if="viewScheduleModalUp2">
            <div class="popup__dim" @mousedown="viewScheduleModalUp2 = false"></div>
            <div class="popup-wrap">
                <div class="popup-con w-500 scroll-sm text-left">
                    <h4 class="popup-tit" v-if="server.includes('competition')">공모전 상세보기</h4>
                    <h4 class="popup-tit" v-else-if="server.includes('basic')">기초교육 상세보기</h4>
                    <h4 class="popup-tit" v-else-if="server.includes('consulting')">컨설팅 상세보기</h4>
                    <ul class="card-modal__con p-0">
                        <li class="card-modal__con__item"  v-if="server.includes('competition')">
                            <label class="no-icon">날짜</label>
                            <input id="selectDateView2" type="date" class="line" v-model="scheduleDate">
                        </li>
                        <li class="card-modal__con__item" v-if="server.includes('basic') || server.includes('consulting')">
                            <label class="no-icon">시작 날짜</label>
                            <input id="selectDateView_2" type="date" class="line" v-model="startDate">
                            <span class="m-r-10"></span>

                            <vue-picker class="select" v-model="startHour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>시</span>&nbsp;&nbsp;
                            <vue-picker class="select" v-model="startMin">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>분</span>&nbsp;

                            <br>
                            <label class="no-icon">종료 날짜</label>
                            <input id="selectDateView__2" type="date" class="line" v-model="endDate">
                            <span class="m-r-10"></span>
                            <vue-picker class="select" v-model="endHour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>시</span>&nbsp;&nbsp;
                            <vue-picker class="select" v-model="endMin">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;<span>분</span>


                        </li>
                        <li class="card-modal__con__item" v-if="server.includes('consulting')">
                            <label class="no-icon">컨설턴트</label>
                            <vue-picker class="select w-100 readonly" v-model="consultantSelect">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="(consultant, idx) in consultants">
                                    <vue-picker-option :value="consultant.id+''">
                                        {{consultant.name}} / {{consultant.email}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                        </li>
                        <li class="card-modal__con__item">
                            <label class="no-icon" v-if="server.includes('competition')">공모전명</label>
                            <label class="no-icon" v-else-if="server.includes('basic')">기초교육명</label>
                            <label class="no-icon" v-else-if="server.includes('consulting')">컨설팅명</label>
<!--                            <input type="text" placeholder="내용을 입력해주세요." class="form-control" v-model="scheduleContent" readonly></input>-->
                            <input type="text" placeholder="내용을 입력해주세요." class="form-control" v-model="scheduleContent"></input>
                        </li>
<!--                        <li class="card-modal__con__item" v-if="server.includes('basic')">-->
<!--                            <label class="no-icon">인원제한</label>-->
<!--                            <vue-picker class="select readonly" v-model="memberLimit">-->
<!--                                <vue-picker-option value="-1">선택</vue-picker-option>-->
<!--                                <template v-for="m in 100">-->
<!--                                    <vue-picker-option :value="Number(m)+''">-->
<!--                                        {{10 <= Number((m)) ? Number(m) + '' : '0'+(Number(m))}}-->
<!--                                    </vue-picker-option>-->
<!--                                </template>-->
<!--                            </vue-picker>-->
<!--                        </li>-->
                    </ul>
                </div>
                <div class="confirm-btn text-center">
                    <button type="button" class="btn btn-light" @mousedown="viewScheduleModalUp2 = false">취소</button>
                    <button type="button" class="btn btn-primary" @click="dataSave2">수정</button>
                </div>
            </div>
        </div>








        <div class="popup" v-if="saveModalUp">
            <div class="popup__dim" @mousedown="saveModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm text-center">
                    저장하시겠습니까?
                </div>
                <div class="confirm-btn text-center">
                    <button type="button" class="btn btn-light" @mousedown="saveModalOff">취소</button>
                    <button type="button" class="btn btn-primary" @click="dataSave">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="confirmModalUp">
            <div class="popup__dim" @mousedown="confirmModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm text-center">
                    모든 내용을 입력해 주세요.
                </div>
                <div class="confirm-btn text-center">
                    <button type="button" class="btn btn-primary" @mousedown="confirmModalOff">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="confirmModalUp2">
            <div class="popup__dim" @mousedown="confirmModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm text-center">
                    입력 값을 다시 확인해 주세요.
                </div>
                <div class="confirm-btn text-center">
                    <button type="button" class="btn btn-primary" @mousedown="confirmModalOff">확인</button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    props: {
        _year : Number,
        _month : Number,
        _day : Number,
        _week : String,
        server : String,
        consultants : Array,
    },
    data() {
        return {
            mode: false,
            days: [
                'Sun',
                'Mon',
                'Tue',
                'Wed',
                'Thu',
                'Fri',
                'Sat',
            ],
            dates: [],
            currentYear: 0,
            currentMonth: 0,
            year: 0,
            startHour: '-1',
            startMin: '-1',
            endHour: '-1',
            endMin: '-1',
            month: 0,
            lastMonthStart: 0,
            nextMonthStart: 0,
            today: 0,
            weekday: '',
            yearList: [],
            selectedYear: 0,
            selectedMonth: 0,
            selectedDay: 0,
            addScheduleModalUp: false,
            saveModalUp: false,
            confirmModalUp: false,
            confirmModalUp2: false,
            scheduleDate: '',
            viewScheduleModalUp: false,
            viewScheduleModalUp2: false,

            startDate: '',
            endDate: '',

            scheduleContent: '',
            daysData: [],
            consultantSelect: '-1',
            // memberLimit: '-1',

            targetId: null,
        }
    },
    created() {     //렌더링이 되기전
        for(let y = 2000;y<2050;y++){
            this.yearList.push(y);
        }
        this._setToday();
    },
    mounted() {     //렌더링이 되고 나서
        this.$nextTick(
                () => {

                    // 모든 화면이 렌더링된 후 실행
                });

    },
    beforeUpdate() {    //data 값이 바뀌기는 전 순간에 호출
    },
    updated() {          //data 값이 바뀌고나서 호출
        //year 선택시 열려있는 위치로 이동
        if(document.querySelector('.month-wrap')){
            let offsetTop = document.querySelector('.month-wrap').offsetTop;
            document.querySelector('.calendar-year').scrollTo(0, offsetTop - 140);
        }
    },
    methods: {
        calendarData(arg) { // 인자를 추가
            if (arg < 0) { // -1이 들어오면 지난 달 달력으로 이동
                this.month -= 1;
            } else if (arg === 1) { // 1이 들어오면 다음 달 달력으로 이동
                this.month += 1;
            }
            if (this.month === 0) { // 작년 12월
                this.year -= 1;
                this.month = 12;
            } else if (this.month > 12) { // 내년 1월
                this.year += 1;
                this.month = 1;
            }

            const [
                monthFirstDay,
                monthLastDate,
                lastMonthLastDate,
            ] = this.getFirstDayLastDate(this.year, this.month);

            this.dates = this.getMonthOfDays(
                    monthFirstDay,
                    monthLastDate,
                    lastMonthLastDate,
            );

            document.querySelectorAll('td').forEach((element) => {
                element.classList.remove('active');
            })

            this.daysData = [];

            axios.post(this.server + '/reload', { year: this.year, month: this.month}).then((result) => {
                this.daysData = result.data.days;

                this.returnDate(this.today, this.today);
            });
        },
        getFirstDayLastDate(year, month) {
            const firstDay = new Date(year, month - 1, 1).getDay(); // 이번 달 시작 요일
            const lastDate = new Date(year, month, 0).getDate(); // 이번 달 마지막 날짜
            let lastYear = year;
            let lastMonth = month - 1;
            if (month === 1) {
                lastMonth = 12;
                lastYear -= 1;
            }
            const prevLastDate = new Date(lastYear, lastMonth, 0).getDate(); // 지난 달 마지막 날짜
            return [firstDay, lastDate, prevLastDate];
        },
        getMonthOfDays(monthFirstDay, monthLastDate, prevMonthLastDate,) {
            let day = 1;
            let prevDay = (prevMonthLastDate - monthFirstDay) + 1;
            const dates = [];
            let weekOfDays = [];
            while (day <= monthLastDate) {
                if (day === 1) {
                    // 1일이 어느 요일인지에 따라 테이블에 그리기 위한 지난 셀의 날짜들을 구할 필요가 있다.
                    for (let j = 0; j < monthFirstDay; j += 1) {
                        if (j === 0) this.lastMonthStart = prevDay; // 지난 달에서 제일 작은 날짜
                        weekOfDays.push(prevDay);
                        prevDay += 1;
                    }
                }
                weekOfDays.push(day);
                if (weekOfDays.length === 7) {
                    // 일주일 채우면
                    dates.push(weekOfDays);
                    weekOfDays = []; // 초기화
                }
                day += 1;
            }
            const len = weekOfDays.length;
            if (len > 0 && len < 7) {
                for (let k = 1; k <= 7 - len; k += 1) {
                    weekOfDays.push(k);
                }
            }
            if (weekOfDays.length > 0) dates.push(weekOfDays); // 남은 날짜 추가
            this.nextMonthStart = weekOfDays[0]; // 이번 달 마지막 주에서 제일 작은 날짜
            return dates;
        },
        setToday(){
            const date = new Date();
            this.currentYear = date.getFullYear(); // 이하 현재 년, 월 가지고 있기
            this.currentMonth = date.getMonth() + 1;
            this.year = this.currentYear;
            this.month = this.currentMonth;
            this.today = date.getDate(); // 오늘 날짜

            this.calendarData();
        },
        _setToday(){
            this.year = this._year;
            this.month = this._month;
            this.today = this._day;
            this.weekday = this._week;

            this.calendarData();
        },
        setDate(y, m){
            this.year = y;
            this.month = m;
            this.calendarData();
            this.mode = false;
        },
        setMode(){
            this.mode = true;
        },
        returnDate(day, key){
            const _date = this.year + '.' + this.month + '.' + day;
            this.$emit('calendarOff', _date);

            window.zxc = (day,idx) => {
                this.editedSave(day,idx);
            }

            // console.log(this.daysData);
            // console.log(key);
            // console.log(this.$refs['day'][key]);
            // console.log(this.$refs[key]);

            if (typeof key != 'string') {
                document.querySelectorAll('td').forEach((element) => {
                    element.classList.remove('active');
                });

                // this.$refs['day'].forEach((elem) => {
                //   elem.classList.remove('active');
                // })
                // this.$refs['day'][key].classList.add('active');
                // event.target.closest('td').setAttribute('class', 'active');
                this.$refs[key][0].classList.add('active');
                this.today = day;
                const week = new Array('일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일');
                const date = new Date(this.year, this.month - 1, this.today).getDay();
                this.weekday = week[date];
                // const dataDate = document.getElementById('dataDate');
                const dataTable = document.getElementById('dataTable');
                // dataDate.innerText = this.year + '.' + this.month + '.' + this.today + ' ' + this.weekday;
                dataTable.innerHTML = '';
                if(this.daysData[day]) {
                    this.daysData[day].forEach((element, idx) => {
                        const startDateTime = new Date(element.startDateTime);
                        const endDateTime = new Date(element.endDateTime);
                        dataTable.innerHTML += `
            <tr>
                <td>${element.id}</td>
                <td class="pointer" onclick="window.zxc(${day}, ${idx})">${element.title}</td>
                ${(this.server.includes('basic') || this.server.includes('consulting')) ? `<td>
                ${startDateTime.getFullYear()}-${(startDateTime.getMonth()+1) < 10 ? '0' + (startDateTime.getMonth()+1) : (startDateTime.getMonth()+1)}-${startDateTime.getDate() < 10 ? '0' + startDateTime.getDate() : startDateTime.getDate()}
                ${startDateTime.getHours() < 10 ? '0' + startDateTime.getHours() : startDateTime.getHours()}:${startDateTime.getMinutes() < 10 ? '0' + startDateTime.getMinutes() : startDateTime.getMinutes()}
                <br />~ ${endDateTime.getFullYear()}-${(endDateTime.getMonth()+1) < 10 ? '0' + (endDateTime.getMonth()+1) : (endDateTime.getMonth()+1)}-${endDateTime.getDate() < 10 ? '0' + endDateTime.getDate() : endDateTime.getDate()}
                ${endDateTime.getHours() < 10 ? '0' + endDateTime.getHours() : endDateTime.getHours()}:${endDateTime.getMinutes() < 10 ? '0' + endDateTime.getMinutes() : endDateTime.getMinutes()}
                </td>`
                                :
                                ``}
                ${(this.server.includes('consulting')) ? `<td>${element.consultant.name}</td>` : ``}
                <td ${element.applies.length ? `onclick="popupOn(${idx})" class="pointer"` : ``}>${element.applies.length}</td>
            </tr>
            `
                    })
                    window.members = this.daysData[day];
                }
            }
        },
        addScheduleModalOn(){
            this.addScheduleModalUp = true;
        },
        addScheduleModalOff(){
            this.saveModalUp = false;
            this.addScheduleModalUp = false;
        },
        viewScheduleModalOn(){
          this.viewScheduleModalUp = true;
        },
        viewScheduleModalOff(){
          this.viewScheduleModalUp = false;
        },
        saveModalOn(){
            this.saveModalUp = true;
        },
        saveModalOff(){
            this.saveModalUp = false;
        },
        confirmModalOn(){
            this.confirmModalUp = true;
        },
        confirmModalOn2(){
            this.confirmModalUp2 = true;
        },
        confirmModalOff(){
            this.confirmModalUp = false;
            this.confirmModalUp2 = false;
        },
        saveBtnClick(){
            if(this.server.includes('competition')){
                (this.scheduleDate === '' || this.scheduleContent === '') ? this.confirmModalOn() : this.saveModalOn();
            }else if(this.server.includes('basic')){
                // (this.scheduleDate === '' || this.startHour === '-1' ||
                //         this.startMin === '-1' || this.endHour === '-1' ||
                //         this.endMin === '-1' || this.consultantSelect === '-1' ||
                //         this.memberLimit === '-1' || this.scheduleContent === '') ? this.confirmModalOn() : this.saveModalOn();

                this.saveModalOn();

            }else if(this.server.includes('consulting')){
                // (this.scheduleDate === '' || this.startHour === '-1' ||
                //         this.startMin === '-1' || this.endHour === '-1' ||
                //         this.endMin === '-1' || this.consultantSelect === '-1' || this.scheduleContent === '') ? this.confirmModalOn() : this.saveModalOn();
                this.saveModalOn();
            }
        },
        dataSave(){
            if(this.server.includes('competition')) {
                axios.post(location.href, {date: this.scheduleDate, content: this.scheduleContent}).then((result) => {
                    if (result.data) {
                        const date = new Date(this.scheduleDate);
                        location.href = `${this.server}?year=${date.getFullYear()}&month=${date.getMonth() + 1}&day=${date.getDate()}`;
                    }
                });
            }else if(this.server.includes('basic')){
                //seon
                let startDateTime = this.startDate + ' ' + this.startHour + ':' + this.startMin + ':00';
                let endDateTime = this.endDate + ' ' + this.endHour + ':' + this.endMin + ':00';
                axios.post(location.href, { startDateTime, endDateTime, consultantId: this.consultantSelect, title: this.scheduleContent}).then((result) => {
                // axios.post(location.href, { startDateTime, endDateTime, consultantId: this.consultantSelect, title: this.scheduleContent, maxMemberCount: this.memberLimit}).then((result) => {
                    if (result.data) {
                        const date = new Date(this.startDate);
                        location.href = `${this.server}?year=${date.getFullYear()}&month=${date.getMonth() + 1}&day=${date.getDate()}`;
                    }else{
                        this.saveModalOff();
                        this.confirmModalOn2();
                    }
                });
            }else if(this.server.includes('consulting')){
                let startDateTime = this.startDate + ' ' + this.startHour + ':' + this.startMin + ':00';
                let endDateTime = this.endDate + ' ' + this.endHour + ':' + this.endMin + ':00';
                axios.post(location.href, { startDateTime, endDateTime, consultantId: this.consultantSelect, title: this.scheduleContent}).then((result) => {
                    if (result.data) {
                        const date = new Date(this.startDate);
                        location.href = `${this.server}?year=${date.getFullYear()}&month=${date.getMonth() + 1}&day=${date.getDate()}`;
                    }else{
                        this.saveModalOff();
                        this.confirmModalOn2();
                    }
                });
            }
        },
        editedSave(day, idx){
            const sYear = (new Date(this.daysData[day][idx].startDateTime)).getFullYear();
            const sMonth = (new Date(this.daysData[day][idx].startDateTime)).getMonth() + 1;
            const sDay = (new Date(this.daysData[day][idx].startDateTime)).getDate();

            this.startDate = sYear+'-'+String(sMonth).padStart(2,'0')+'-'+String(sDay).padStart(2,'0');
            this.startHour = (new Date(this.daysData[day][idx].startDateTime)).getHours() + '';
            this.startMin = (new Date(this.daysData[day][idx].startDateTime)).getMinutes() + '';


            const eYear = (new Date(this.daysData[day][idx].endDateTime)).getFullYear();
            const eMonth = (new Date(this.daysData[day][idx].endDateTime)).getMonth() + 1;
            const eDay = (new Date(this.daysData[day][idx].endDateTime)).getDate();

            this.endDate = eYear+'-'+String(eMonth).padStart(2,'0')+'-'+String(eDay).padStart(2,'0');
            this.endHour = (new Date(this.daysData[day][idx].endDateTime)).getHours() + '';
            this.endMin = (new Date(this.daysData[day][idx].endDateTime)).getMinutes() + '';

            this.scheduleContent =  this.daysData[day][idx].title;
            if (this.server.includes('consulting')) {
              this.consultantSelect = this.daysData[day][idx].consultant.id+'';
            }
            this.targetId = this.daysData[day][idx].id;

            this.viewScheduleModalUp2 = true;
            // this.memberLimit = this.daysData[day][idx].maxMemberCount+'';

            console.log(this.daysData[day][idx]);
        },
        dataSave2(){
            if(this.server.includes('competition')) {
                console.log('11');
            }else if(this.server.includes('basic')){
                axios.post(window.location.origin + '/admin/basic3/modi/' + this.targetId, {
                    startDateTime:this.startDate + ' ' + this.startHour+':'+this.startMin+':00',
                    endDateTime:this.endDate + ' ' + this.endHour+':'+this.endMin+':00',
                    title:this.scheduleContent
                }).then(re => {
                    if (re.data) {
                        const date = new Date(this.startDate);
                        location.href = `${this.server}?year=${date.getFullYear()}&month=${date.getMonth() + 1}&day=${date.getDate()}`;
                    }
                })
            }else if(this.server.includes('consulting')){
                axios.post(window.location.origin + '/admin/consulting3/modi/' + this.targetId, {
                    startDateTime:this.startDate + ' ' + this.startHour+':'+this.startMin+':00',
                    endDateTime:this.endDate + ' ' + this.endHour+':'+this.endMin+':00',
                    title:this.scheduleContent
                }).then(re => {
                    if (re.data) {
                        const date = new Date(this.startDate);
                        location.href = `${this.server}?year=${date.getFullYear()}&month=${date.getMonth() + 1}&day=${date.getDate()}`;
                    }
                })
            }
        },
    },
}
</script>
