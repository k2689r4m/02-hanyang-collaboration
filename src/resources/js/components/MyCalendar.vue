
<template>
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
                      <td
                              v-for="(day, secondIdx) in date"
                              @click="returnDate(day, (idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day) ? 'x' + secondIdx : day)"
                              :key="secondIdx"
                              :ref="(idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day) ? 'x' + secondIdx : day"
                              :class="{
                                  'has-text-info-dark': idx === 0 && day >= lastMonthStart,
                                  'has-text-danger': dates.length - 1 === idx && nextMonthStart > day,
                                  'has-text-primary': day === today
                              }"
                      >
                        <span
                            v-if="!((idx === 0 && day >= lastMonthStart) || (dates.length - 1 === idx && nextMonthStart > day))"
                            :class="{ active : daysData[day], day }">{{ day }}</span>
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
</template>
<script>
export default {
    props: {
      server: String,
    },


    data() {
        return {
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
            weekday: ''
        }
    },
    created() {     //렌더링이 되기전
        for(let y = 2000;y<2050;y++){
            this.yearList.push(y);
        }
        this.setToday();
    },
    mounted() {     //렌더링이 되고 나서
        this.$nextTick(() => {
            // 모든 화면이 렌더링된 후 실행
        });
    },
    beforeUpdate() {    //data 값이 바뀌기는 전 순간에 호출
    },
    updated() {          //data 값이 바뀌고나서 호출
        //year 선택시 열려있는 위치로 이동
        if(document.querySelector('.month-wrap')){
            let offsetTop = document.querySelector('.month-wrap').offsetTop;
            document.querySelector('.calendar-year').scrollTo(0, offsetTop - 208);
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

          axios.post(this.server + '/reload', { year: this.year, month: this.month}).then((result) => {
            this.daysData = result.data.days;
            window.days = this.daysData;

            // console.log(this.daysData);

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

            if (typeof key != 'string') {
              this.today = day;
              const week = new Array('일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일');
              const date = new Date(this.year, this.month - 1, this.today).getDay();
              this.weekday = week[date];
              const dataDate = document.getElementById('dataDate');
              const dataList = document.getElementById('dataList');
              if(dataDate){
                dataDate.innerText = this.year + '.' + this.month + '.' + this.today + ' ' + this.weekday;
              }
              dataList.innerHTML = '';
              if(dataList && this.daysData[day]){
                this.daysData[day].forEach((element, idx) => {
                  dataList.innerHTML += `
                  <li class="request-list__item">
                      <div class="con-wrap">
                          <span class="con">${element.title}</span>
                      </div>
                      <button id="${element.id}" type="button" onclick="requestModalOn(this)" class="btn btn-primary btn-line"
                      ${element.competition_applies.length ? 'disabled' : ''}
                      >${element.competition_applies.length ? '신청완료' : '신청'}</button>
                  </li>
                  `
                })
              }
            }
        }

    },
}
</script>
