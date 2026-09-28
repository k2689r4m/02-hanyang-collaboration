<template>
  <div ref="lectureETCMember">
    <div class="popup__dim" @click="popClose"></div>
    <div class="popup-wrap">
      <div class="popup-con scroll-sm" >
      <h3 class="popup-tit">조교 및 외부전문가 설정</h3>
      <!--                <input type="checkbox" v-model="check.checked" />-->

      <h4 class="popup-sub mb-10">조교</h4>
      <button class="btn-addbtn mb-30" @click="userListUp(2)"></button>
      <ul class="user-list w-auto">
        <li class="user-list__item" v-for="(tl, idx) in classManagers" v-if="tl.selected">
          <span class="name">{{tl.name}}</span>
          <button class="user-deletebtn" @click="deleteUser(tl)"></button>
        </li>
      </ul>
      <h4 class="popup-sub mb-10">외부전문가</h4>
      <button class="btn-addbtn" @click="userListUp(3)"></button>
      <ul class="user-list w-auto">
        <li class="user-list__item" v-for="(tl, idx) in experts" v-if="tl.selected">
          <span class="name">{{tl.name}}</span>
          <button class="user-deletebtn" @click="deleteUser(tl)"></button>
        </li>
      </ul>
    </div>
      <div class="confirm-btn">
        <button class="btn w-50 fc-gray" @click="popClose">취소</button>
        <button class="btn w-50" @click="saveSetting">확인</button>
      </div>
    </div>

    <!--      </div>-->
    <!--    </div>-->

        <div class="popup" v-if="userPopup" @mousedown.stop>
          <div class="popup-wrap">
            <div class="popup-con">
              <h3 class="popup-tit mb-10">검색</h3>
              <div class="search__wrap mb-10">
                <input name="content" type="text" placeholder="검색어를 입력해주세요" v-model="searchData" class="search-input b-gray">
              </div>
              <table class="team-list last">
                <colgroup>
                  <col width="10%" />
                  <col width="25%" />
                  <col width="35%" />
                  <col width="20%" />
                  <col width="10%" />
                </colgroup>
                <tr>
                  <th>번호</th>
                  <th>이름</th>
                  <th>아이디</th>
                  <th>쓰기까지가능</th>
                  <th>선택</th>
                </tr>
                <tr v-for="(user, idx) in userList.filter( user => user.name.indexOf(this.searchData) > -1)">
                  <td><span class="bg">{{ idx+1 }}</span></td>
                  <td><span class="bg">{{ user.name }}</span></td>
                  <td><span class="bg">{{ user.email }}</span></td>
                  <td><span class="bg t-center pl-0"><input type="checkbox" v-model="user.write" /></span></td>
                  <td><span class="bg t-center pl-0"><input type="checkbox" v-model="user.selected" @change="selectCheck($event, idx)" /></span></td>
                </tr>
              </table>
            </div>
            <div class="confirm-btn">
              <button class="btn fc-gray w-50" @click="userPopup = false">취소</button>
              <button class="btn w-50" @click="userListOff(popupType)">확인</button>
            </div>
          </div>
        </div>

  </div>

</template>
<script>
export default {
  // props: ['user', 'cards', 'myPageId', 'myClass', 'myUserList'],

  props: ['classObjectId'],

  directives: {
    focus: {
      // 디렉티브 정의
      inserted: function (el) {
        el.focus()
      }
    }
  },
  data() {
    return {
      myIndex: 0,
      isClass: [],
      userPopup: false,
      userList: [],
      popupType: null,
      teamMasters: [],         //팀장
      classManagers: [],          //조교
      experts : [],        //외무전문가
      searchData : '',
    }
  },
  created() {     //렌더링이 되기전
    this.init();
    // console.log(this.myClass);
  },
  mounted() {     //렌더링이 되고 나서
    this.$nextTick(() => {
      // 모든 화면이 렌더링된 후 실행
    });
  },
  beforeUpdate() {    //data 값이 바뀌기는 전 순간에 호출
  },
  updated() {          //data 값이 바뀌고나서 호출
    //document.getElementById('externalModal').classList.add('d-none');
    this.$nextTick(() => {
      // 모든 화면이 렌더링된 후 실행
      // if(this.$refs.lectureETCMember.parentNode.parentNode.classList.contains('d-none')){
      //   console.log('xxxxxxxxxxx');
      // }
      // else{
      //   console.log('ooooooooooooo');
      // }
    });
  },
  methods: {
    init() {
      axios.get(window.location.origin + '/lectureList/lectureDetailMember/'+this.classObjectId+'/etcView').then(re => {
        console.log('+++++++++++++++++++++++++++');
        this.experts = _.cloneDeep(re.data.experts)
        this.classManagers = _.cloneDeep(re.data.classManagers);
      })
    },
    saveSetting(){
      // this.isClass.onSetting = true;
      // // this.isClass.classManagers = this.classManagers;
      //

      this.isClass.classManagers = [];
      for(let i=0;i<this.classManagers.length;i++){
        if(this.classManagers[i].selected){
          this.isClass.classManagers.push(this.classManagers[i]);
        }
      }

      this.isClass.experts = [];
      for(let i=0;i<this.experts.length;i++){
        if(this.experts[i].selected){
          this.isClass.experts.push(this.experts[i]);
        }
      }

      //
      // this.$emit('settingSave', _.cloneDeep(this.isClass));
      // this.$emit('modalOff');


      axios.post(window.location.origin + '/lectureList/lectureDetailMember/'+this.classObjectId+'/etcAdd',{
        experts: JSON.stringify(this.isClass.experts),
        classManagers: JSON.stringify(this.isClass.classManagers),
      }).then(re => {
        // this.$emit('modalOff');
        this.popClose();
      })
    },
    userListUp(type){
      this.popupType = type;

      if(type === 2){
        this.userList = _.cloneDeep(this.classManagers);
      }
      else if(type === 3){
        this.userList = _.cloneDeep(this.experts);
      }

      this.searchData = '';
      this.userPopup = true;
    },
    userListOff(type){
      if(type === 2){
        this.classManagers = _.cloneDeep(this.userList);
      }
      else if(type === 3){
        this.experts = _.cloneDeep(this.userList);
      }

      this.userPopup = false;
    },
    deleteUser(target){
      target.selected = false;
    },
    selectCheck(e, idx){
      const bgChangeTargetParent = e.target.parentElement.parentElement.parentElement;
      const bgChangeTarget = bgChangeTargetParent.childNodes;

      if(this.userList[idx].selected === true){
        for(let i=0; i<bgChangeTarget.length; i++){
          if(bgChangeTarget[i].nodeName == 'TD'){
            bgChangeTarget[i].firstChild.classList.add('bg-2');
          }
        }
      }
      else{
        for(let i=0; i<bgChangeTarget.length; i++){
          if(bgChangeTarget[i].nodeName == 'TD'){
            bgChangeTarget[i].firstChild.classList.remove('bg-2');
          }
        }
      }
    },
    popClose(){
      document.getElementById('externalModal').classList.add('d-none');
      this.init();
    }

  },
}
</script>
