<template>
    <div class="dash-top">
        <div class="dash-top__img">
            <img :src="origin + '/avatar/' + user.id" alt="" onerror="this.remove()" />
            <div></div>
        </div>

        <div class="dash-top__txt">
            <span class="name">{{user.name}}</span>
            <span v-if="user.authority === 1">학생,</span>
            <span v-else-if="user.authority === 2">교수님, </span>
            <span v-else="user.authority === 2">님, </span>
            환영합니다!<br>새 활동을 확인해보세요.<br>
            <template v-if="user.authority === 2">
            <span class="badge badge-gray m-none" v-if="Number(user.basicTarget) === 0">기초교육 비대상자</span>
            <span class="badge badge-primary m-none" v-else>기초교육 대상자</span>
            <span class="badge badge-gray m-none" v-if="Number(user.consultingTarget) === 0">컨설팅 비대상자</span>
            <span class="badge badge-primary m-none" v-else>컨설팅 대상자</span>
            </template>
        </div>

        <div class="m-block m-badge">
            <template v-if="user.authority === 2">
            <span class="badge badge-gray" v-if="Number(user.basicTarget) === 0">기초교육 비대상자</span>
            <span class="badge badge-primary" v-else>기초교육 대상자</span>
            <span class="badge badge-gray" v-if="Number(user.consultingTarget) === 0">컨설팅 비대상자</span>
            <span class="badge badge-primary" v-else>컨설팅 대상자</span>
            </template>
        </div>
        <div class="admin-slick__wrap m-none" v-if="user.authority === 1 || user.authority === 2">
            <VueSlickCarousel :arrows="true" :slidesToShow="2" :infinite="false" v-if="myClasses.length > 0">
                <div class="dash-top__admin__wrap" v-for="(myClass, idx) in myClasses">
                    <div class="dash-top__admin">
                        <a class="admin-btn" :href="origin + '/lectureList/lectureDetailInfo/' + myClass.id" v-if="user.authority === 2">관리</a>
                        <a class="admin-btn stu" :href="origin + '/myClass?myClassId=' + myClass.id" v-if="user.authority === 1">&gt;</a>
                        <div class="img-wrap">
                            <span class="m" v-if="Number(myClass.class_apply.meca) === 1"></span>
                            <span class="e" v-else-if="Number(myClass.class_apply.meca) === 2"></span>
                            <span class="c" v-else-if="Number(myClass.class_apply.meca) === 3"></span>
                            <span class="a" v-else-if="Number(myClass.class_apply.meca) === 4"></span>
                            <span class="badge">
                                <template v-if="Number(myClass.class_apply.meca) === 1">현장통합형</template>
                                <template v-else-if="Number(myClass.class_apply.meca) === 2">현장평가형</template>
                                <template v-else-if="Number(myClass.class_apply.meca) === 3">문제해결형</template>
                                <template v-else-if="Number(myClass.class_apply.meca) === 4">현장문제형</template>
                            </span>
                        </div>
                        <span class="name">{{myClass.class_apply.korName}}</span>
                        <table class="con">
                            <colgroup>
                                <col width="50%" />
                                <col width="50%" />
                            </colgroup>
                            <tr>
                                <td><strong>{{myClass.class_members.length}}</strong><span>명</span></td>
                                <td><strong>{{myClass.teamCount}}</strong><span>개</span></td>
                            </tr>
                            <tr>
                                <td>학생</td>
                                <td>팀</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </VueSlickCarousel>
        </div>
        <div class="dash-top__admin__wrap m-block scroll-sm" v-if="user.authority === 1 || user.authority === 2">
            <div class="dash-top__admin" v-for="(myClass, idx) in myClasses">
                <a class="admin-btn" :href="origin + '/lectureList/lectureDetailInfo/' + myClass.class_apply.id" v-if="user.authority === 2">관리</a>
                <a class="admin-btn stu" :href="origin + '/myClass?myClassId=' + myClass.id" v-if="user.authority === 1">&gt;</a>
                <div class="img-wrap">
                    <span class="m" v-if="Number(myClass.class_apply.meca) === 1"></span>
                    <span class="e" v-else-if="Number(myClass.class_apply.meca) === 2"></span>
                    <span class="c" v-else-if="Number(myClass.class_apply.meca) === 3"></span>
                    <span class="a" v-else-if="Number(myClass.class_apply.meca) === 4"></span>
                    <span class="badge">
                        <template v-if="Number(myClass.class_apply.meca) === 1">현장통합형</template>
                        <template v-else-if="Number(myClass.class_apply.meca) === 2">현장평가형</template>
                        <template v-else-if="Number(myClass.class_apply.meca) === 3">문제해결형</template>
                        <template v-else-if="Number(myClass.class_apply.meca) === 4">현장문제형</template>
                    </span>
                </div>
                <span class="name">{{myClass.class_apply.korName}}</span>
                <table class="con">
                    <colgroup>
                        <col width="50%" />
                        <col width="50%" />
                    </colgroup>
                    <tr>
                        <td><strong>{{myClass.class_members.length}}</strong><span>명</span></td>
                        <td><strong>{{myClass.teamCount}}</strong><span>개</span></td>
                    </tr>
                    <tr>
                        <td>학생</td>
                        <td>팀</td>
                    </tr>
                </table>
            </div>
        </div>

        <ul v-if="user.authority === 4" class="alarm-list scroll-sm">
          <li class="alarm-list__item">
            <span class="name">박재환 학생</span>이 문화콘텐츠창작소개개발론 수업에서 내 스택에 "<span class="tit">감사합니다 @ 김태형 @ 송민지 @ 박기수 </span>" 댓글을 추가 했습니다.
            <span class="date">5/14 금, 10:31</span>
          </li>
          <li class="alarm-list__item">
            <span class="name">박재환 학생</span>이 문화콘텐츠창작소개개발론 수업에서 내 스택에 "<span class="tit">감사합니다 @ 김태형 @ 송민지 @ 박기수 </span>" 댓글을 추가 했습니다.
            <span class="date">5/14 금, 10:31</span>
          </li>
          <li class="alarm-list__item">
            <span class="name">박재환 학생</span>이 문화콘텐츠창작소개개발론 수업에서 내 스택에 "<span class="tit">감사합니다 @ 김태형 @ 송민지 @ 박기수 </span>" 댓글을 추가 했습니다.
            <span class="date">5/14 금, 10:31</span>
          </li>
        </ul>
    </div>
</template>
<script>
export default {
    props: ['user', 'myClasses'],

    data() {
        return {
            origin: window.location.origin,
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
            // console.log(this.myClasses);
        },
        moveClass(myClassId){
          document.location.href = "{!! route('myClassView', ['myClassId' => '']); !!}" + myClassId;
        },
    },
}
</script>
