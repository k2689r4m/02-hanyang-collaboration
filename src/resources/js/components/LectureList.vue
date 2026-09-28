<!-- 수업관리 VUE 파일-->

<template>
  <div>
    Lecture List
  </div>
</template>
<script>
export default {
  data() {
    return {
      ah: 0,
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
    init(){

    },
  },
}
</script>
