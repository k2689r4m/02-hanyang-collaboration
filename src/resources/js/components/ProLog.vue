<template>
    <div>
        <h4 class="sub-tit">{{user.authority === 1 ? '피드백' : '새 알림'}}</h4>
        <ul class="alarm-list scroll-sm">
            <li v-for="(log, idx) in logs" class="alarm-list__item" v-bind:class="{'center':log.authority !== 1}">
                <span class="name">{{log.userName}}</span>
                <template v-if="log.eventState === 10">
                    가 {{log.title}} {{ getType(log.eventType) }} 신청을 승인했습니다.
                </template>
                <template v-else-if="log.eventState === 20">
                    가 {{ getType(log.eventType) }} 이 종료되었습니다.
                </template>
                <template v-else-if="log.eventState === 30">
                    {{log.authority !==1 ? '가' : '이'}} {{log.title}} 수업에서 "{{log.eventTitle ? log.eventTitle : ''}}"에 {{user.name}}님을 태그했습니다.
                </template>
                <template v-else-if="log.eventState === 40">
                    {{log.authority !==1 ? '가' : '이'}} {{log.title}} 수업에서 {{ getType(log.eventType) }}에 "<span class="tit">{{log.eventTitle ? log.eventTitle : ''}}</span>" 댓글을 추가 했습니다.
                </template>
                <template v-else-if="log.eventState === 50">
                    {{log.authority !==1 ? '가' : '이'}} {{ getType(log.eventType) }}에 "{{log.eventTitle ? log.eventTitle : ''}}" 댓글을 수정 했습니다.
                </template>
                <span class="date">{{log.date}}</span>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    props: ['user'],

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
            axios.get(window.location.origin + '/dashboardPro/proLog').then(re => {
                console.log('[PRO] INIT DATA:::');
                console.log(re.data);

                for(let i=0;i<re.data.length;i++){
                    const week = ['일', '월', '화', '수', '목', '금', '토'];
                    const _year = new Date(re.data[i].created_at).getFullYear();
                    const _month = String(new Date(re.data[i].created_at).getMonth() + 1);
                    const _day = String(new Date(re.data[i].created_at).getDate());
                    let _hour = String(new Date(re.data[i].created_at).getHours());
                    let _min = String(new Date(re.data[i].created_at).getMinutes());
                    const _date = new Date(_year, _month - 1, _day).getDay();
                    // padStart(2,'0');
                    const _weekday = week[_date];

                    re.data[i].eventType = Number(re.data[i].eventType);
                    re.data[i].eventState = Number(re.data[i].eventState);

                    _hour < 10 ? _hour = '0' + _hour : '';
                    _min < 10 ? _min = '0' + _min : '';

                    re.data[i].date = _month + '/' + _day + ' ' + _weekday + ', ' + _hour + ':' + _min;

                    if(re.data[i].eventState === 40 || re.data[i].eventState === 50){
                        if(re.data[i].eventTitle){
                            re.data[i].eventTitle = re.data[i].eventTitle.replace(/<(\/)?([a-zA-Z]*)(\s[a-zA-Z]*=[^>]*)?(\s)*(\/)?>/ig, "");
                        }
                    }
                }

                this.logs = _.cloneDeep(re.data);
            })

            Echo.join('PRO_'+this.user.id)
                    .here(user => {
                        // this.users = user;
                        console.log('[PRO] SOCKET INIT');
                    })
                    .joining(user => {
                        // this.users.push(user);
                    })
                    .leaving(user => {
                        // this.users = this.users.filter(u => u.id !== user.id);
                    })
                    .listen('.pro.log', (re)=>{
                        console.log('SCOKET PRO LOG::');
                        console.log(re.data);
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
                            re.data.eventState = Number(re.data.eventState);

                            re.data.date = _month + '/' + _day + ' ' + _weekday + ', ' + _hour + ':' + _min;

                            if(re.data.eventState === 40 || re.data.eventState === 50){
                                if(re.data.eventTitle) {
                                    re.data.eventTitle = re.data.eventTitle.replace(/<(\/)?([a-zA-Z]*)(\s[a-zA-Z]*=[^>]*)?(\s)*(\/)?>/ig, "");
                                }
                            }

                            this.logs.unshift(re.data);
                        }
                    })
        },
        getType(type){

            if(type === 10){
                return '내 문제분석';
            }
            else if(type === 20){
                return '내 성찰일지';
            }
            else if(type === 30){
                return '내 브레인';
            }
            else if(type === 40){
                return '수업개설';
            }
            else if(type === 50){
                return '내 팀활동';
            }
            else if(type === 60){
                return '---';
            }
            else if(type === 70){
                return '평가';
            }
            else if(type === 80){
                return '운영결과';
            }
            else if(type === 1000){
                return '기초교육';
            }
            else if(type === 1010){
                return '컨설팅';
            }
            else{
                return '내 스택';
            }
        },
    },
}
</script>
