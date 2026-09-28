<template>
    <ul class="dash-top__list scroll-sm">
        <li v-for="(log, idx) in logs" class="dash-top__list__item">
            <span class="text-primary">{{log.userName}}</span>가 <span class="text-primary">"{{log.eventTitle}}"</span>

            <template v-if="10 <= log.eventType && log.eventType < 20 ">
                <template v-if="11 === log.eventType" >
                    수업을 <span class="text-primary">개설 신청</span>했습니다.
                </template>
                <template v-else-if="12 === log.eventType" >
                    수업이 <span class="text-primary">시작</span>되었습니다.
                </template>
                <template v-else-if="13 === log.eventType" >
                    수업이 <span class="text-primary">종료</span>되었습니다.
                </template>
            </template>
            <template v-if="20 <= log.eventType && log.eventType < 30 ">
                <template v-if="21 === log.eventType" >
                    수업 <span class="text-primary">기초교육</span>이 <span class="text-primary">시작</span>되었습니다.
                </template>
                <template v-else-if="22 === log.eventType" >
                    수업 <span class="text-primary">기초교육</span>이 <span class="text-primary">완료</span>되었습니다.
                </template>
            </template>
            <template v-if="30 <= log.eventType && log.eventType < 40 ">
                <template v-if="31 === log.eventType" >
                    수업 <span class="text-primary">컨설팅</span>이 <span class="text-primary">시작</span>되었습니다.
                </template>
                <template v-else-if="32 === log.eventType" >
                    수업 <span class="text-primary">컨설팅</span>이 <span class="text-primary">완료</span>되었습니다.
                </template>
            </template>

            <span class="date">{{ log.date }}</span>
        </li>
    </ul>
</template>
<script>
export default {
    // props: ['calendarType'],

    data() {
        return {
            logs: [],
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
            axios.get(window.location.origin + '/admin/dashboard/noti').then(re => {
                console.log('INIT DATA:::');

                for(let i=0;i<re.data.length;i++){
                    const week = ['일', '월', '화', '수', '목', '금', '토'];
                    const _year = new Date(re.data[i].created_at).getFullYear();
                    const _month = String(new Date(re.data[i].created_at).getMonth() + 1);
                    const _day = String(new Date(re.data[i].created_at).getDate());
                    const _hour = String(new Date(re.data[i].created_at).getHours());
                    const _min = String(new Date(re.data[i].created_at).getMinutes());
                    const _date = new Date(_year, _month - 1, _day).getDay();
                    // padStart(2,'0');
                    const _weekday = week[_date];

                    re.data[i].eventType = Number(re.data[i].eventType);

                    re.data[i].date = _month + '/' + _day + ' ' + _weekday + ', ' + _hour + ':' + _min;
                }

                this.logs = _.cloneDeep(re.data);
            })

            Echo.join('ADMIN')
                .here(user => {
                    // this.users = user;
                    console.log('SOCKET INIT');
                })
                .joining(user => {
                    // this.users.push(user);
                })
                .leaving(user => {
                    // this.users = this.users.filter(u => u.id !== user.id);
                })
                .listen('.admin.log', (re)=>{
                    console.log('SCOKET ADMIN LOG');
                    if(re){
                        const week = ['일', '월', '화', '수', '목', '금', '토'];
                        const _year = new Date(re.data.created_at).getFullYear();
                        const _month = String(new Date(re.data.created_at).getMonth() + 1);
                        const _day = String(new Date(re.data.created_at).getDate());
                        const _hour = String(new Date(re.data.created_at).getHours());
                        const _min = String(new Date(re.data.created_at).getMinutes());
                        const _date = new Date(_year, _month - 1, _day).getDay();
                        const _weekday = week[_date];

                        re.data.eventType = Number(re.data.eventType);

                        re.data.date = _month + '/' + _day + ' ' + _weekday + ', ' + _hour + ':' + _min;

                        this.logs.unshift(re.data);
                    }
                })
        },
    },
}
</script>
