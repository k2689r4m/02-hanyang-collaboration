<template>
    <div>
        <h4 class="sub-tit">주요 일정</h4>
        <a href="" class="btn-more">더보기</a>
        <ul class="schedule-list">
            <li class="schedule-list__item tit">{{todayDate}}</li>
            <li v-for="(t, idx) in myToday" class="schedule-list__item">
                <span class="tit">{{ t.content }}</span>
                <span class="detail">
                    <span class="subject">{{t.class_apply.korName}}</span>
                    ~ {{String(new Date(t.dateTime).getHours()).padStart(2,'0')}}:{{String(new Date(t.dateTime).getMinutes()).padStart(2,'0')}}
                </span>
            </li>
        </ul>
        <ul class="schedule-list color2">
            <li class="schedule-list__item tit color2">{{nextDate}}</li>
            <li v-for="(n, idx) in myNext" class="schedule-list__item">
                <span class="tit">{{ n.content }}</span>
                <span class="detail"><span class="subject">{{n.class_apply.korName}}</span>
                    ~ {{String(new Date(n.dateTime).getHours()).padStart(2,'0')}}:{{String(new Date(n.dateTime).getMinutes()).padStart(2,'0')}}
                </span>
            </li>
        </ul>
    </div>
</template>
<script>
export default {
    props: ['user', 'today', 'next'],

    data() {
        return {
            logs: [],
            todayDate: null,
            nextDate: null,
            myToday: [],
            myNext: [],
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
            const _month = String(new Date().getMonth() + 1);
            const _day = String(new Date().getDate());
            const _nextDay = String(new Date().getDate()+1);

            this.todayDate =  '오늘('+_month.padStart(2,'0') + '/' + _day.padStart(2,'0') + ')';
            this.nextDate =  '내일('+_month.padStart(2,'0') + '/' + _nextDay.padStart(2,'0') + ')';

            if(this.today){
                this.myToday = _.cloneDeep(this.today);
            }
            if(this.next){
                this.myNext = _.cloneDeep(this.next);
            }

            

            Echo.join('SCH_'+this.user.id)
                    .here(user => {
                        // this.users = user;
                        console.log('[SCH] SOCKET INIT');
                    })
                    .joining(user => {
                        // this.users.push(user);
                    })
                    .leaving(user => {
                        // this.users = this.users.filter(u => u.id !== user.id);
                    })
                    .listen('.sch.log', (re)=>{
                        console.log('SCOKET SCH LOG::');
                        if(re.data){
                            if(re.data.today){
                                for(let i=0; i<2; i++){
                                  if(new Date(this.myToday[i].dateTime).getTime() > new Date(re.data.dateTime).getTime()) {
                                    this.myToday.splice(i, 0, re.data);
                                    if(3 <= this.myNext.length){
                                      this.myNext.pop();
                                    }
                                    break;
                                  }
                                  else {
                                    this.myToday.push(re.data);
                                    if(3 <= this.myNext.length){
                                      this.myNext.pop();
                                    }
                                  }
                                }
                            }
                            else{
                                for(let i=0; i<2; i++){
                                  if(new Date(this.myNext[i].dateTime).getTime() > new Date(re.data.dateTime).getTime()) {
                                    this.myNext.splice(i, 0, re.data);
                                    if(3 <= this.myNext.length){
                                      this.myNext.pop();
                                    }
                                    break;
                                  }
                                  else {
                                    this.myNext.push(re.data);
                                    if(3 <= this.myNext.length){
                                      this.myNext.pop();
                                    }
                                  }
                                }
                            }
                        }
                    })
        },
    },
}
</script>
