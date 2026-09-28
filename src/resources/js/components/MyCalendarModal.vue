<template>
  <div>
    <div class="popup">
      <div class="card-modal__dim" @mousedown="$emit('modalOff')"></div>
      <div class="popup-wrap">
        <div class="popup-con scroll-sm">
          <h3 class="popup-tit">기초교육신청<span class="fr">본인은 기초교육 수강 <span class="fc-blue">대상자</span> 입니다.</span></h3>
          <my-calendar></my-calendar>
          <h4 class="popup-sub2">2020.12.08 화요일</h4>
          <ul class="request-list">
            <li class="request-list__item">
              <div class="con-wrap">
                <span class="name">우종현</span>
                <span class="con">기초교육의 이해1</span>
              </div>
              <button class="btn btn-primary btn-line" @click="saveModalOn">신청</button>
            </li>
            <li class="request-list__item">
              <div class="con-wrap">
                <span class="name">강성일</span>
                <span class="con">기초교육의 이해1. 16:00 ~ 17:00</span>
              </div>
              <button class="btn btn-primary btn-line" disabled>신청완료</button>
            </li>
            <li class="request-list__item">
              <div class="con-wrap">
                <span class="name">김대일</span>
                <span class="con">기초디자인의 이해 및 기초학습과정1. 17:00 ~ 18:00</span>
              </div>
              <button class="btn btn-gray" disabled>신청불가</button>
            </li>
          </ul>
        </div>
        <div class="confirm-btn">
          <button class="btn w-50 fc-gray" @mousedown="$emit('modalOff')">취소</button>
          <button class="btn w-50">확인</button>
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
  // props: [],

  data() {
    return {
      saveModalUp: false,
      confirmModalUp: false,
    }
  },
  created() {     //렌더링이 되기전
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
    saveModalOn(){
      this.saveModalUp = true;
    },
    saveModalOff(){
      this.saveModalUp = false;
    },
    confirmModalOn(){
      this.saveModalUp = false;
      this.confirmModalUp = true;
    },
    confirmModalOff(){
      this.confirmModalUp = false;
    },
  },
}
</script>