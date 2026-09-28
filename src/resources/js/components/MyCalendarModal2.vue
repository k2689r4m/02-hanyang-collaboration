<template>
    <div>
        <div class="popup">
            <div class="card-modal__dim" @mousedown="$emit('modalOff')"></div>
            <div class="popup-wrap pb-30">
                <button class="popup-close-btn" @mousedown="$emit('modalOff')"></button>
                <div class="popup-con scroll-sm">
                    <div class="calendar2__wrap">
                        <div class="calendar-month" v-if="!mode">
                            <h2 class="calendar-top mt-0">
                                <button class="prev-btn" @click="calendarData(-1)"></button>
                                <button class="year-btn" @click="setMode">{{ year }}. {{ month }}</button>
                                <button class="next-btn" @click="calendarData(1)"></button>
                                <button class="btn btn-primary btn-sm fr" @click="addScheduleModalOn">+ 추가</button>
                            </h2>
                            <table class="calendar-table">
                                <thead>
                                <th v-for="day in days" :key="day">{{ day }}</th>
                                </thead>
                                <tbody>
                                <tr v-for="(date, idx) in dates" :key="idx">
                                    <td
                                            v-for="(day, secondIdx) in date"
                                            @click="returnDate(day)"
                                            :key="secondIdx"
                                            :class="{
                                        'has-text-info-dark': idx === 0 && day >= lastMonthStart,
                                        'has-text-danger': dates.length - 1 === idx && nextMonthStart > day,
                                        'has-text-primary': day === today
                                        // 'has-text-primary': day === today && month === currentMonth && year === currentYear
                                    }"
                                    >
                                        <span>{{ day }}</span>
                                        <template v-for="(sDate, idx) in sDates[day-1]">
                                            <template v-if="sDates[day-1].data">
                                                <span v-for="(sd, ii) in sDate" class="schedule" v-if="ii < 3">
                                                    {{ String(sd.hour).padStart(2,'0') }}:{{ String(sd.min).padStart(2,'0')}} [{{sd.teamName}}] {{sd.content}}
                                                </span>
                                            </template>
                                            <span v-if="3 < sDates[day-1].data.length" class="more">+{{sDates[day-1].data.length - 3}}</span>
                                        </template>
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
                    <h4 class="popup-sub2">{{year}}.{{ String(month).padStart(2,'0')}}.{{String(today).padStart(2,'0')}} {{weekday}}</h4>
                    <ul class="schedule-list">
                        <template v-for="(sDate, idx) in sDates[today-1]">
                            <template v-if="sDates[today-1].data">
                                <li v-for="(sd, ii) in sDate" class="schedule-list__item">
                                    <span class="fc-blue">{{ String(sd.hour).padStart(2,'0') }}:{{ String(sd.min).padStart(2,'0')}}</span>
                                    <button class="btn" v-if="sd.teamName === '본인'" @click="scheduleUpdate(sd.id, sd.userId)">수정</button>
                                    <button class="btn" v-if="sd.teamName === '본인'" @click="deleteModalOn(sd.id, sd.userId)">삭제</button>
                                    [{{sd.teamName}}] {{sd.content}}
                                </li>
                            </template>
                        </template>
                    </ul>
                </div>
            </div>
        </div>

        <div class="popup" v-if="addScheduleModalUp">
            <div class="card-modal__dim" @mousedown="addScheduleModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con w-500 scroll-sm">
                  <h3 class="popup-tit">일정 <span v-if="scheduleUpdateState">수정</span><span v-else>추가</span></h3>
                    <ul class="card-modal__con p-0">
                        <li class="card-modal__con__item">
                            <label class="no-icon">날짜</label>
                            <input type="date" class="line" v-model="scheduleDate">
                        </li>
                        <li class="card-modal__con__item">
                            <label class="no-icon">시간</label>
                            <vue-picker class="select b-gray m-0" v-model="hour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            <span class="super ml-5">시 </span>
                            <vue-picker class="select b-gray" v-model="min">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            <span class="super ml-5">분 </span>
                        </li>
                        <li class="card-modal__con__item">
                            <label class="no-icon">내용</label>
                            <textarea placeholder="내용을 입력해주세요." rows="7" class="b-gray" v-model="scheduleContent"></textarea>
                        </li>
                    </ul>
                </div>
                <div class="confirm-btn">
                    <button class="btn w-50 fc-gray" @mousedown="addScheduleModalOff">취소</button>
                    <button class="btn w-50" @click="saveBtnClick">저장</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="saveModalUp">
            <div class="card-modal__dim" @mousedown="saveModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm t-center">
                    저장하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn w-50 fc-gray" @mousedown="saveModalOff">취소</button>
                    <button class="btn w-50" @click="addScheduleModalOff">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="deleteModalUp">
            <div class="card-modal__dim" @mousedown="deleteModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm t-center">
                    삭제하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn w-50 fc-gray" @mousedown="deleteModalOff">취소</button>
                    <button class="btn w-50" @click="deleteSchedule">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="confirmModalUp">
            <div class="card-modal__dim" @mousedown="confirmModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm t-center">
                    모든 내용을 입력해주세요.
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
    props: ['classObjectId', 'teamId', 'myTeams', 'user'],

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
            sDates: [],

            dates: [],
            currentYear: 0,
            currentMonth: 0,
            year: 0,
            hour: '-1',
            min: '-1',
            month: 0,
            lastMonthStart: 0,
            nextMonthStart: 0,
            today: 0,
            yearList: [],
            selectedYear: 0,
            selectedMonth: 0,
            selectedDay: 0,
            addScheduleModalUp: false,
            saveModalUp: false,
            confirmModalUp: false,
            deleteModalUp: false,
            deleteTarget: '',
            updateTarget: '',
            deleteUser: '',
            scheduleDate: '',
            scheduleContent: '',
            weekday:'',
            scheduleUpdateState:false,
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

            //데이터 펫퓌퓌ㅜ피취
            axios.get(window.location.origin + '/fetch/calendar/'+this.classObjectId+'/'+this.teamId+'/'+this.year+'/'+this.month).then(re => {
                if(re.data){
                    for(let i=0;i<re.data.length;i++){
                        const idx = Number((new Date(re.data[i].dateTime)).getDate()) - 1;
                        Vue.set(re.data[i], 'hour', (new Date(re.data[i].dateTime)).getHours());
                        Vue.set(re.data[i], 'min', (new Date(re.data[i].dateTime)).getMinutes());

                        if(re.data[i].userId === this.user.id){
                            Vue.set(re.data[i], 'teamName', '본인');
                        }
                        else if(re.data[i].teamId){
                            const target = re.data[i].teamId;
                            const idx = this.myTeams.findIndex(i => i.id === target);
                            Vue.set(re.data[i], 'teamName', this.myTeams[idx].name);
                        }
                        else{
                            Vue.set(re.data[i], 'teamName', '');
                        }

                        this.sDates[idx].data.push(re.data[i]);
                    }

                    for(let i=0;i<this.sDates.length;i++){
                        this.sDates[i].data.sort(function (a, b){
                            return a.dateTime < b.dateTime ? -1 : a.dateTime > b.dateTime ? 1 : 0;
                        })
                    }
                }
            })

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
            this.sDates = [];

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

                this.sDates.push({data:[]});
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
        addScheduleModalOn(){
            this.addScheduleModalUp = true;
        },
        addScheduleModalOff(){
            if(!(this.scheduleDate === '' || this.scheduleContent === '' || this.hour === '-1' || this.min === '-1')){
                const _date = this.scheduleDate+' '+this.hour+':'+this.min;
                const _content = this.scheduleContent;

                if(this.scheduleUpdateState){
                  const _date = this.scheduleDate+' '+this.hour+':'+this.min;
                  axios.post(window.location.origin + '/update/calendar', {
                    calendarId:this.updateTarget,
                    dateTime:_date,
                    content:_content
                  }).then(re => {
                    if(re.data) {
                      this.scheduleDate = '';
                      this.scheduleContent = '';
                      this.hour = '-1';
                      this.min = '-1';
                      this.deleteTarget = '';
                      this.scheduleUpdateState = false;

                      this.calendarData();
                    }
                  }).catch(error => console.log(error));
                  // // console.log(this.sDates);
                  // //
                  // this.sDates[this.today-1].data.forEach(data =>{
                  //   if(data.id === this.updateTarget){
                  //     data.content = _content;
                  //     data.dateTime = _date2;
                  //
                  //     Vue.set(data, 'dateTime', _date2);
                  //   }
                  // })
                  //
                  //
                  //
                  // for(let i=0;i<this.sDates[this.today-1].data.length;i++){
                  //   if(this.sDates[this.today-1].data[i].id === this.updateTarget){
                  //     console.log(this.sDates[this.today-1].data[i]);
                  //
                  //     Vue.set(this.sDates[this.today-1].data[i], 'content', _content)
                  //     Vue.set(this.sDates[this.today-1].data[i], 'dateTime', _date2);
                  //
                  //   }
                  // }
                  //
                  //
                  //
                  //
                  //
                  // // console.log(this.sDates[this.today-1].data);
                  // this.sDates[this.today-1].data.sort(function (a, b){
                  //   return a.dateTime < b.dateTime ? -1 : a.dateTime > b.dateTime ? 1 : 0;
                  // })
                  // this.scheduleDate = '';
                  // this.scheduleContent = '';
                  // this.hour = '-1';
                  // this.min = '-1';
                  // this.deleteTarget = '';
                  // this.scheduleUpdateState = false;
                }else{
                  axios.post(window.location.origin + '/post/calendar', {
                    classObjectId:this.classObjectId,
                    teamId:this.teamId,
                    dateTime:_date,
                    content:_content
                  }).then(re => {
                    if(re.data){
                      const _month = Number((new Date(re.data.dateTime)).getMonth()+1);
                      const _day = Number((new Date(re.data.dateTime)).getDate());

                      if(_month === this.month){
                        Vue.set(re.data, 'hour', (new Date(re.data.dateTime)).getHours());
                        Vue.set(re.data, 'min', (new Date(re.data.dateTime)).getMinutes());

                        if(re.data.userId === this.user.id){
                          Vue.set(re.data, 'teamName', '본인');
                        }
                        else if(re.data.teamId){
                          const target = re.data.teamId;
                          const idx = this.myTeams.findIndex(i => i.id === target);
                          Vue.set(re.data, 'teamName', this.myTeams[idx].name);
                        }
                        else{
                          Vue.set(re.data, 'teamName', '');
                        }

                        this.sDates[_day-1].data.push(re.data);

                        this.sDates[_day-1].data.sort(function (a, b){
                          return a.dateTime < b.dateTime ? -1 : a.dateTime > b.dateTime ? 1 : 0;
                        })
                      }

                      this.scheduleDate = '';
                      this.scheduleContent = '';
                      this.hour = '-1';
                      this.min = '-1';
                    }
                  })
                }
            }

            this.saveModalUp = false;
            this.addScheduleModalUp = false;
        },
        deleteSchedule(){
            axios.post('/delete/calendar/' + this.deleteTarget)
            .then(re => {
                if(re.data){
                  this.sDates[this.today-1].data.forEach((data, index) =>{
                    if(data.id === this.deleteTarget){
                      this.sDates[this.today-1].data.splice(index,1);
                    }
                  })

                  this.deleteModalUp = false;
                }
            }).catch(error => console.log(error));
        },
        calendarAddFetch(calendar){
            const _month = Number((new Date(calendar.dateTime)).getMonth()+1);
            const _day = Number((new Date(calendar.dateTime)).getDate());

            if(_month === this.month){
                Vue.set(calendar, 'hour', (new Date(calendar.dateTime)).getHours());
                Vue.set(calendar, 'min', (new Date(calendar.dateTime)).getMinutes());

                if(calendar.userId === this.user.id){
                    Vue.set(calendar, 'teamName', '본인');
                }
                else if(calendar.teamId){
                    const target = calendar.teamId;
                    const idx = this.myTeams.findIndex(i => i.id === target);
                    Vue.set(calendar, 'teamName', this.myTeams[idx].name);
                }
                else{
                    Vue.set(calendar, 'teamName', '');
                }

                this.sDates[_day-1].data.push(calendar);

                this.sDates[_day-1].data.sort(function (a, b){
                    return a.dateTime < b.dateTime ? -1 : a.dateTime > b.dateTime ? 1 : 0;
                })
            }
        },
        saveModalOn(){
            this.saveModalUp = true;
        },
        saveModalOff(){
            this.saveModalUp = false;
        },
        deleteModalOn(id, userId){
            this.deleteModalUp = true;
            this.deleteTarget = id;
            this.deleteUser = userId;
        },
        deleteModalOff(){
            this.deleteModalUp = false;
        },
        scheduleUpdate(id, userId){
            this.updateTarget = id;
            this.scheduleUpdateState = true;
            this.addScheduleModalUp = true;
            this.sDates[this.today-1].data.forEach((data, index) =>{
              if(data.id === id){
                let dateTime = data.dateTime;
                let arr = dateTime.split(' ');
                let _date = arr[0].split('-').join('-');
                let _time = arr[1].slice(0,2);
                let _minute = arr[1].slice(3,5);

                if(_time.slice(0,1) == 0){
                  _time = _time.slice(1,2);
                }
                if(_minute.slice(0,1) == 0){
                  _minute = _minute.slice(1,2);
                }

                this.scheduleContent = data.content;
                this.scheduleDate = _date;
                this.hour = _time;
                this.min = _minute;
              }
            });
        },
        confirmModalOn(){
            this.confirmModalUp = true;
        },
        confirmModalOff(){
            this.confirmModalUp = false;
        },
        saveBtnClick(){
            if(this.scheduleDate === '' || this.scheduleContent === '' || this.hour === '-1' || this.min === '-1'){
                this.confirmModalOn();
            }
            else{
                this.saveModalOn();
            }
            // (this.scheduleDate === '' || this.scheduleContent === '' || this.hour === '-1' || this.min === '-1') ? this.confirmModalOn() : this.saveModalOn();
        },
    },
}
</script>
