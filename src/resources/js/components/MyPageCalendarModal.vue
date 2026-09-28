<template>
    <div>
        <div class="popup">
            <div class="card-modal__dim" @mousedown="$emit('modalOff')"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm">
                    <template v-if="calendarType === 1">
                        <h3 class="popup-tit" v-if="isBasicTarget">기초교육신청<span class="fr">본인은 기초교육 수강 <span class="fc-blue">대상자</span> 입니다.</span></h3>
                        <h3 class="popup-tit" v-else>기초교육신청<span class="fr">본인은 기초교육 수강 <span >대상자</span>가 아닙니다.</span></h3>
                    </template>
                    <template v-else>
                        <h3 class="popup-tit" v-if="isBasicTarget">컨설팅신청<span class="fr">본인은 컨설팅 <span class="fc-blue">대상자</span> 입니다.</span></h3>
                        <h3 class="popup-tit" v-else>컨설팅신청<span class="fr">본인은 컨설팅 <span >대상자</span>가 아닙니다.</span></h3>
                    </template>

                    <div class="calendar__wrap">
                        <div class="calendar-month" v-if="!mode">
                            <h2 class="calendar-top">
                                <button type="button" class="prev-btn" @click="calendarData(-1)"></button>
                                <button type="button" class="year-btn" @click="setMode">{{ year }}. {{ month }}</button>
                                <button type="button" class="next-btn" @click="calendarData(1)"></button>
                            </h2>
                            <table class="calendar-table">
                                <thead>
                                <th v-for="day in days" :key="day">{{ day }}</th>
                                </thead>
                                <tbody>
                                <tr v-for="(date, idx) in dates" :key="idx">
                                    <template v-for="(day, secondIdx) in date">
                                        <td v-if="(idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day)">
                                            <span>&nbsp;</span>
                                        </td>
                                        <td v-else :class="{'has-text-primary': day === today}">
                                            <span @click="returnDate(day)" class="center" :class="{ active : sDatas[day], day }">{{day}}</span>
                                        </td>
                                    </template>
                                </tr>
                                </tbody>
                            </table>
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

                    <h4 class="popup-sub2">
                        {{year}}.{{String(month).padStart(2,'0')}}.{{String(today).padStart(2,'0')}} {{weekday}}</h4>
                    <ul class="request-list">
                        <li v-for="(sData, idx) in sDatas[today]" class="request-list__item">
                            <div class="con-wrap">
                                <span class="name" v-if="calendarType !== 1">{{ sData.consultant.name }}</span>
                                <span class="name" v-else>{{ sData.title }}</span>
                                <span class="con">
                                  <template v-if="calendarType !== 1">
                                    {{ sData.title }}
                                  </template>
                                  <span class="fc-gray">(
                                  {{9 <= new Date(sData.startDateTime).getMonth() ? Number(new Date(sData.startDateTime).getMonth())+1 : '0' + (Number(new Date(sData.startDateTime).getMonth())+1)}}.{{10 <= new Date(sData.startDateTime).getDate() ? new Date(sData.startDateTime).getDate() : '0' + new Date(sData.startDateTime).getDate()}}
                                  {{ 10 <= new Date(sData.startDateTime).getHours() ? new Date(sData.startDateTime).getHours() : '0' + new Date(sData.startDateTime).getHours() }}:{{ 10 <= new Date(sData.startDateTime).getMinutes() ? new Date(sData.startDateTime).getMinutes() : '0' + new Date(sData.startDateTime).getMinutes() }} ~
                                    {{9 <= new Date(sData.endDateTime).getMonth() ? Number(new Date(sData.endDateTime).getMonth())+1 : '0' + (Number(new Date(sData.endDateTime).getMonth())+1)}}.{{10 <= new Date(sData.endDateTime).getDate() ? new Date(sData.endDateTime).getDate() : '0' + new Date(sData.endDateTime).getDate()}}
                                  {{ 10 <= new Date(sData.endDateTime).getHours() ? new Date(sData.endDateTime).getHours() : '0' + new Date(sData.endDateTime).getHours() }}:{{ 10 <= new Date(sData.endDateTime).getMinutes() ? new Date(sData.endDateTime).getMinutes() : '0' + new Date(sData.endDateTime).getMinutes() }}
                                  )</span>
                                </span>
                            </div>
                            <button v-if="!sData.state" class="btn btn-primary btn-line" disabled>종료</button>
                            <button v-else-if="sData.is_accepted_count" class="btn btn-primary btn-line" disabled>신청완료</button>
<!--                            <button v-else-if="!((new Date()).getTime()<(new Date(sData.startDateTime)).getTime()) || !(sData.applier_count < (sData.maxMemberCount == null ? 1 : sData.maxMemberCount))" class="btn btn-gray" disabled>신청불가</button>-->
                            <button v-else-if="calendarType === 1" class="btn btn-primary btn-line" @click="saveModalOn(idx)">신청</button>
                            <button v-else-if="calendarType === 2" class="btn btn-primary btn-line" @click="saveModal2On(idx)">신청</button>
                        </li>
                    </ul>
                </div>
                <div class="confirm-btn">
                    <button class="btn w-50 fc-gray" @click="$emit('modalOff')">취소</button>
                    <button class="btn w-50" @click="$emit('modalOff')">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="saveModal2Up">
            <div class="card-modal__dim" @mousedown="saveModal2Off"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm t-center overflow-visible2">
                    컨설팅을 신청할 수업을 선택하세요.<br />
                    <vue-picker class="select b-gray mt-10 w-100" v-model="isClass">
                        <vue-picker-option value="-1">선택</vue-picker-option>
                        <template v-for="(cls, idx) in myClasses">
                            <vue-picker-option :value="cls.classObjectId+''">
                                {{ cls.korName }}
                            </vue-picker-option>
                        </template>
                    </vue-picker>
                </div>
                <div class="confirm-btn">
                    <button class="btn w-50 fc-gray" @mousedown="saveModal2Off">취소</button>
                    <button class="btn w-50" @click="saveModalOn(isIdx)">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="saveModalUp">
            <div class="card-modal__dim" @mousedown="saveModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm t-center">
                    신청하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn w-50 fc-gray" @mousedown="saveModalOff">취소</button>
                    <button class="btn w-50" @click="confirmModalOn">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="confirmModalUp">
            <div class="card-modal__dim" @mousedown="confirmModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm t-center">
                    신청되었습니다.
                </div>
                <div class="confirm-btn">
                    <button class="btn w-100" @mousedown="confirmModalOff">확인</button>
                </div>
            </div>
        </div>
    </div>

</template>
<script>
export default {
    props: ['calendarType', 'myClasses','myPage'],

    data() {
        return {
            saveModalUp: false,
            saveModal2Up: false,
            confirmModalUp: false,

            mode: false,
            days: [
                '일',
                '월',
                '화',
                '수',
                '목',
                '금',
                '토',
            ],
            dates: [],
            currentYear: 0,
            currentMonth: 0,
            year: 0,
            month: 0,
            lastMonthStart: 0,
            nextMonthStart: 0,
            today: 0,
            yearList: [],
            selectedYear: 0,
            selectedMonth: 0,
            selectedDay: 0,
            daysData: [],
            isClass: '-1',
            weekday: '',
            sDatas : {},
            isBasicTarget: false,
            isIdx : null,
        }
    },
    created() {     //렌더링이 되기전
        this.init();
    },
    mounted() {     //렌더링이 되고 나서
        this.$nextTick(() => {
            // 모든 화면이 렌더링된 후 실행
        });
    },
    beforeUpdate() {    //data 값이 바뀌기는 전 순간에 호출
    },
    updated() {          //data 값이 바뀌고나서 호출
    },
    methods: {
        init(){
            for(let y = 2000;y<2050;y++){
                this.yearList.push(y);
            }
            this.setToday();
        },
        saveModalOn(idx){
            this.saveModal2Off();
            this.isIdx = idx;
            this.saveModalUp = true;
        },
        saveModalOff(){
            this.saveModalUp = false;
        },
        saveModal2On(idx){
            this.isIdx = idx;
            this.saveModal2Up = true;
        },
        saveModal2Off(){
            this.saveModal2Up = false;
        },
        confirmModalOn(){
            const targetId = this.sDatas[this.today][this.isIdx].id;
            console.log(this.myPage)

            if(this.calendarType === 1) {
                axios.post(window.location.origin + '/myPage/basic/apply/' + targetId, {year:this.myPage.year, semester:this.myPage.semester}).then(re => {
                    if (re.data) {
                        console.log(re.data);

                        this.$emit('remoteItem', _.cloneDeep(re.data));
                        this.saveModalUp = false;
                        this.confirmModalUp = true;
                        this.sDatas[this.today][this.isIdx].is_accepted_count = 1;
                    }
                })
            }
            else{
                console.log(this.isClass);
                axios.post(window.location.origin + '/myPage/consulting/apply/' + targetId +'/'+this.isClass, {year:this.myPage.year, semester:this.myPage.semester}).then(re => {
                    console.log(re);
                    if (re.data) {
                        console.log(re.data);

                        this.$emit('remoteItem', _.cloneDeep(re.data));
                        this.saveModalUp = false;
                        this.confirmModalUp = true;
                        this.sDatas[this.today][this.isIdx].is_accepted_count = 1;
                    }
                })
            }

        },
        confirmModalOff(){
            this.confirmModalUp = false;
        },
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


            if(this.calendarType === 1){
                axios.get(window.location.origin + '/myPage/basic/list/'+this.year+'/'+this.month).then(re => {
                    this.sDatas = re.data.basics;
                    this.isBasicTarget = re.data.isBasicTarget;
                })
            }
            else{
                axios.get(window.location.origin + '/myPage/consulting/list/'+this.year+'/'+this.month).then(re => {
                    this.sDatas = re.data.consultings;
                    this.isBasicTarget = re.data.isConsultingTarget;
                    // this.myClasses = re.data.myClasses;

                    console.log(re.data);
                })
            }

            this.returnDate(this.today);

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
        setDate(y, m){
            this.year = y;
            this.month = m;
            this.calendarData();
            this.mode = false;
        },
        setMode(){
            this.mode = true;
        },
        returnDate(day){
            const week = ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'];
            const date = new Date(this.year, this.month - 1, day).getDay();
            this.weekday = week[date] ;
            this.today = day;
        },
    },
}
</script>
