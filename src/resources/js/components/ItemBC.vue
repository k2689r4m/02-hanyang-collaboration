<template>
    <div>
        <div class="popup">

            <div class="card-modal__dim" @mousedown="$emit('modalOff')"></div>
            <div class="popup-wrap">
                <div v-if="item.type === 1001" class="popup-con w-500 scroll-sm overflow-visible">
                    <h3 class="popup-tit">{{basic.title}} 기초교육</h3>
                    <ul class="card-modal__con p-0">
                        <li class="card-modal__con__item">
                            <label class="no-icon">시작 날짜</label>
                            <input type="date" class="line" v-model="sDate" readonly>
                            <span class="mr-10"></span>
                            <vue-picker class="select b-gray m-0 readonly" v-model="sHour">
                              <vue-picker-option value="-1">선택</vue-picker-option>
                              <template v-for="h in 24">
                                <vue-picker-option :value="Number(h-1)+''">
                                  {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                </vue-picker-option>
                              </template>
                            </vue-picker>
                            <span class="ml-5">시 </span>
                            <vue-picker class="select b-gray readonly" v-model="sMin">
                              <vue-picker-option value="-1">선택</vue-picker-option>
                              <template v-for="m in 60">
                                <vue-picker-option :value="Number(m-1)+''">
                                  {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                </vue-picker-option>
                              </template>
                            </vue-picker>
                            <span class="ml-5">분 </span>
                        </li>

                        <li class="card-modal__con__item">
                            <label class="no-icon">종료 날짜</label>
                            <input type="date" class="line" v-model="eDate" readonly>
                            <span class="mr-10"></span>
                            <vue-picker class="select b-gray m-0 readonly" v-model="eHour">
                              <vue-picker-option value="-1">선택</vue-picker-option>
                              <template v-for="h in 24">
                                <vue-picker-option :value="Number(h-1)+''">
                                  {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                </vue-picker-option>
                              </template>
                            </vue-picker>
                            <span class="ml-5">시 </span>
                            <vue-picker class="select b-gray readonly" v-model="eMin">
                              <vue-picker-option value="-1">선택</vue-picker-option>
                              <template v-for="m in 60">
                                <vue-picker-option :value="Number(m-1)+''">
                                  {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                </vue-picker-option>
                              </template>
                            </vue-picker>
                            <span class="ml-5">분 </span>
                        </li>


<!--                        <li class="card-modal__con__item">-->
<!--                            <label class="no-icon">컨설팅명</label>-->
<!--                            <textarea placeholder="내용을 입력해주세요." rows="1" class="b-gray" v-model="basic.title"></textarea>-->
<!--                        </li>-->
                    </ul>
                </div>
                <div v-else-if="item.type === 1002" class="popup-con w-500 scroll-sm">
                    <h3 class="popup-tit">{{consulting.title}} 컨설팅 </h3>
                    <ul class="card-modal__con p-0">
                        <li class="card-modal__con__item">
                          <label class="no-icon">수업</label>
                          <vue-picker class="select b-gray w-200 m-0" v-model="isClass" v-bind:class="{'readonly':!editedMode}">
                            <vue-picker-option value="-1">선택</vue-picker-option>
                            <template v-for="(cls, idx) in myClasses">
                              <vue-picker-option :value="cls.classObjectId+''">
                                {{ cls.korName }}
                              </vue-picker-option>
                            </template>
                          </vue-picker>
                        </li>

                        <li class="card-modal__con__item">
                            <label class="no-icon">시작 날짜</label>
                            <input type="date" class="line" v-model="sDate" readonly>
                            <span class="mr-10"></span>
                            <vue-picker class="select b-gray m-0 readonly" v-model="sHour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            <span class="ml-5">시 </span>
                            <vue-picker class="select b-gray readonly" v-model="sMin">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            <span class="ml-5">분 </span>
                        </li>

                        <li class="card-modal__con__item">
                            <label class="no-icon">종료 날짜</label>
                            <input type="date" class="line" v-model="eDate" readonly>
                            <span class="mr-10"></span>
                            <vue-picker class="select b-gray m-0 readonly" v-model="eHour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            <span class="ml-5">시 </span>
                            <vue-picker class="select b-gray readonly" v-model="eMin">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            <span class="ml-5">분 </span>
                        </li>

                        <li class="card-modal__con__item"></li>

<!--                                    <li class="card-modal__con__item">-->
<!--                                        <label class="no-icon">컨설팅명</label>-->
<!--                                        <textarea placeholder="내용을 입력해주세요." rows="1" class="b-gray" v-model="basic.title"></textarea>-->
<!--                                    </li>-->
                    </ul>
                </div>

                <div v-if="item.type === 1001" class="confirm-btn">
                    <button class="btn w-100" @click="$emit('modalOff')">확인</button>
                </div>
                <div v-else-if="item.type === 1002" class="confirm-btn">
                    <template v-if="editedMode">
                        <button class="btn w-50 fc-gray" @mousedown="$emit('modalOff')">취소</button>
                        <button class="btn w-50" @click="saveData">수정</button>
                    </template>
                    <template v-else>
                        <button class="btn w-100" @click="$emit('modalOff')">확인</button>
                    </template>

                </div>
            </div>

        </div>
    </div>

</template>
<script>
export default {
    props: ['item','myClasses'],

    data() {
        return {
            basic: '',
            consulting: '',
            isClass: '',

            editedMode: false,

            sDate: '',
            sHour: '-1',
            sMin: '-1',

            eDate: '',
            eHour: '-1',
            eMin: '-1',
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
            console.log(this.myClasses);


            if(this.item.type === 1001){
                this.basic = this.item.with_basic_apply.basic;

                const sYear = (new Date(this.basic.startDateTime)).getFullYear();
                const sMonth = (new Date(this.basic.startDateTime)).getMonth() + 1;
                const sDay = (new Date(this.basic.startDateTime)).getDate();

                this.sDate = sYear+'-'+String(sMonth).padStart(2,'0')+'-'+String(sDay).padStart(2,'0');
                this.sHour = (new Date(this.basic.startDateTime)).getHours() + '';
                this.sMin = (new Date(this.basic.startDateTime)).getMinutes() + '';

                const eYear = (new Date(this.basic.endDateTime)).getFullYear();
                const eMonth = (new Date(this.basic.endDateTime)).getMonth() + 1;
                const eDay = (new Date(this.basic.endDateTime)).getDate();

                this.eDate = eYear+'-'+String(eMonth).padStart(2,'0')+'-'+String(eDay).padStart(2,'0');
                this.eHour = (new Date(this.basic.endDateTime)).getHours() + '';
                this.eMin = (new Date(this.basic.endDateTime)).getMinutes() + '';
            }
            else if(this.item.type === 1002){
                console.log(this.item.with_consulting_apply);
                this.consulting = this.item.with_consulting_apply.consulting;
                this.isClass = this.item.with_consulting_apply.class_apply.classObjectId + '';

                const sYear = (new Date(this.consulting.startDateTime)).getFullYear();
                const sMonth = (new Date(this.consulting.startDateTime)).getMonth() + 1;
                const sDay = (new Date(this.consulting.startDateTime)).getDate();

                this.sDate = sYear+'-'+String(sMonth).padStart(2,'0')+'-'+String(sDay).padStart(2,'0');
                this.sHour = (new Date(this.consulting.startDateTime)).getHours() + '';
                this.sMin = (new Date(this.consulting.startDateTime)).getMinutes() + '';

                const eYear = (new Date(this.consulting.endDateTime)).getFullYear();
                const eMonth = (new Date(this.consulting.endDateTime)).getMonth() + 1;
                const eDay = (new Date(this.consulting.endDateTime)).getDate();

                this.eDate = eYear+'-'+String(eMonth).padStart(2,'0')+'-'+String(eDay).padStart(2,'0');
                this.eHour = (new Date(this.consulting.endDateTime)).getHours() + '';
                this.eMin = (new Date(this.consulting.endDateTime)).getMinutes() + '';


                if(this.item.with_consulting_apply.state === 0){
                    this.editedMode =  true;
                }
            }

        },
        saveData(){
            axios.post(window.location.origin + '/myPage/consulting/apply/modi/' + this.item.with_consulting_apply.id +'/'+this.isClass).then(re => {
                console.log(re);
                if (re.data) {
                    console.log(re.data);

                    for(let i=0;i<this.myClasses.length;i++){
                        if(Number(this.myClasses[i].classObjectId)  === Number(this.isClass)){
                            this.item.with_consulting_apply.class_apply.classObjectId = this.myClasses[i].classObjectId;
                            break;
                        }
                    }

                    this.$emit('modalOff')
                }
            })
        },
    },
}
</script>
