<template>
    <div>
        asdasd1asasd
        
    </div>
</template>
<script>
export default {
    props: ['date'],
    data() {
        return {
            days: [
                '일요일',
                '월요일',
                '화요일',
                '수요일',
                '목요일',
                '금요일',
                '토요일',
            ],
            dates: [],
            currentYear: 0,
            currentMonth: 0,
            year: 0,
            month: 0,
        }
    },
    created() {     //렌더링이 되기전
        this.year = this.date.getFullYear();
        this.month = this.date.getMonth() + 1;
        this.calendarData();
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
        calendarData() {
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
        getMonthOfDays(monthFirstDay,monthLastDate,prevMonthLastDate,) {
            let day = 1;
            let prevDay = (prevMonthLastDate - monthFirstDay) + 1;
            const dates = [];
            let weekOfDays = [];
            while (day <= monthLastDate) {
                if (day === 1) {
                    // 1일이 어느 요일인지에 따라 테이블에 그리기 위한 지난 셀의 날짜들을 구할 필요가 있다.
                    for (let j = 0; j < monthFirstDay; j += 1) {
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
            return dates;
        },
    },
}
</script>
