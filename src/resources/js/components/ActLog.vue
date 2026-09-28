<template>
    <ul class="activity-list scroll-sm">
        <li v-for="(log, idx) in logs" class="activity-list__item" v-bind:class="{'team1':log.teamId === 1,'team2':log.teamId === 2,'fc-blue':log.userId == user.id}">
            <span class="name" v-if="log.userId != user.id">{{log.teamName ? '('+log.teamName+')' : ''}} {{log.userName}} {{getType(log.authority)}}</span>
            [{{log.eventType === 10 ? '문제분석' : log.eventType === 20 ? '성찰일지' : '팀활동'}}] ‘{{ log.eventTitle }}’
            <p class="detail">
            {{ log.classObjectName }} {{ log.date }}
            </p>
        </li>
    </ul>
</template>
<script>
export default {
    props: ['user'],

    data() {
        return {
            logs: [],
            myActCheck: false,
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
            axios.get(window.location.origin + '/dashboardPro/actLog').then(re => {
                console.log('[ACT] INIT DATA:::');
                console.log(re.data);

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

                    re.data[i].date = _month + '/' + _day + ' ' + _weekday + ', ' + (_hour < 10 ? '0' + _hour : _hour) + ':' + (_min < 10 ? '0' + _min : _min);
                }

                this.logs = _.cloneDeep(re.data);
            })

            Echo.join('ACT_'+this.user.id)
                    .here(user => {
                        // this.users = user;
                        console.log('[ACT] SOCKET INIT');
                    })
                    .joining(user => {
                        // this.users.push(user);
                    })
                    .leaving(user => {
                        // this.users = this.users.filter(u => u.id !== user.id);
                    })
                    .listen('.act.log', (re)=>{
                        console.log('SCOKET ACT LOG::');
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

                            re.data.date = _month + '/' + _day + ' ' + _weekday + ', ' + (_hour < 10 ? '0' + _hour : _hour) + ':' + (_min < 10 ? '0' + _min : _min);

                            this.logs.unshift(re.data);
                        }
                    })
        },
        getType(type){
            if(type === 1){
                return '학생'
            }
            else if(type === 2){
                return '교수'
            }
            else if(type === 3){
                return 'PBL센터'
            }
            else if(type === 4){
                return '컨설턴트'
            }
            else if(type === 5){
                return '외부전문가'
            }
            else if(type === 5){
                return '조교'
            }
        },
    },
}
</script>
