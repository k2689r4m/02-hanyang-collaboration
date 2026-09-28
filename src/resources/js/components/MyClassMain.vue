<template>
    <div v-if="renderCom">
        <div class="card-top" :class="{'team':divTeam}">
            <span class="btn btn-icon divsion-class" v-if="divClass">수업</span>
            <span class="btn btn-icon divsion-team" v-if="divTeam">팀</span>

            <vue-picker v-model="year" class="sm mr-10" autofocus>
                <template v-for="(year, index) in ['2023','2022','2021','2020','2019','2018']">
                    <div @click="selectYear(index)">Q
                        <vue-picker-option :value="year+''">{{year}}년</vue-picker-option>
                    </div>
                </template>
            </vue-picker>
            <vue-picker v-model="semester" class="md mr-10" autofocus>
                <template v-for="(semester, index) in ['1학기','여름계절학기','2학기','겨울계절학기']">
                    <div @click="selectYear(index)">
                        <vue-picker-option :value="(Number(index)+1)+''">{{semester}}</vue-picker-option>
                    </div>
                </template>
            </vue-picker>
            <vue-picker v-model="classIndex" autofocus>
                <vue-picker-option :value="'-1'" disabled>
                    수업 선택
                </vue-picker-option>
                <template v-for="(obj, index) in myClassObjects2">
                    <div @click="selectClass(index)">
                        <vue-picker-option :value="(Number(index)+1)+''" >
                            {{ obj.class_object.class_apply.korName }}
                            <template v-if="user.authority === 4 || user.authority === 5">({{obj.user2.name}})</template>
                        </vue-picker-option>
                    </div>
                </template>
            </vue-picker>
            <!--            <template v-if="isPro">-->
            <template v-if="this.user.authority !== 1 && classIndex !== '-1'">
                <button class="btn btn-icon btn-calendar" @click="calendarModalOn">캘린더</button>
            </template>
            <template v-else>
                <button v-if="isTeamPage" class="btn btn-icon btn-calendar" @click="calendarModalOn">캘린더</button>
            </template>

            <!--            <button class="btn btn-icon btn-addtalk" @click="addNewTalk" v-if="!talkState">수업톡 추가</button>-->

            <!--            <button v-if="!talkState && !isTeamPage && isPro" class="btn btn-icon btn-addtalk" @click="addNewTalk">수업톡 추가</button>-->
<!--            <button v-if="!talkState && isTeamPage" class="btn btn-icon btn-addtalk" @click="addNewTalk">팀톡 추가</button>-->

            <span v-if="classIndex !== '-1'" :key="componentKey">
                <vue-picker v-model="teamIndex" autofocus>
                    <div @click="selectTeam(-1)">
                        <vue-picker-option :value="'-1'">수업 홈</vue-picker-option>
                    </div>
                    <template v-for="(team, index) in myTeams">
                        <div @click="selectTeam(index)">
                            <vue-picker-option :value="index+''">{{team.name}}</vue-picker-option>
                        </div>
                    </template>
                </vue-picker>
            </span>



            <button v-if="!myClass.onSetting && isPro && divClass && classIndex !== '-1'"
                    class="btn btn-icon btn-setting" @click="settingModalOn">설정</button>
            <button v-if="classIndex !== '-1'" class="btn btn-icon btn-user" @click="studentListModalOn">{{userList.length}}명</button>
        </div>
        <div class="card-wrap" ref="cardWrapRef">
            <button class="scroll-exist" @click="scrollRight" v-if="scrollExist"></button>

            <template v-if="classIndex !== '-1'">
                <draggable  v-model="cardsNum" :move="dragDefault" @end="moveCard" v-bind:disabled="isMobileCheck">
                    <template v-for="(id, index) in cardsNum" v-if="cards[id]">
                        <div v-if="cards[id].type === 5 || cards[id].type === 12" @dblclick.stop class="card" :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                            <div class="card-drag-po" :name="cards[id].type">
                                <label class="card-tit">{{ cards[id].title }}</label>
                                <ul class="talk-list scroll-sm" v-chat-scroll="{always: false}" id="chat_scroll">
                                    <li class="talk-list__item" v-for="(talk, index) in chat" :class="[{ left : talk.userId !== user.id}, { right : talk.userId === user.id}]">
                                        <span class="user">{{talk.name.slice(-2, talk.name.length)}}</span>
                                        <pre class="msg" v-text="talk.message"></pre>
                                    </li>
                                </ul>
                                <div class="talk-send">
                                    <textarea v-model="userMsg" placeholder="대화를 입력하세요" @keydown.enter="sendMessage($event, cards[id])" @keyup="resize" @keydown="resize" id="talkInput" />
                                    <button class="talk-sendbtn" @click="sendMessage($event, cards[id])"></button>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="cards[id].type === 6 || cards[id].type === 11" @dblclick.stop class="card" :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                            <div class="card-drag-po" :name="cards[id].type">
                                <label class="card-tit">{{ cards[id].title }}</label>
                                <draggable v-model="cards[id].itemsNum" :group="{name:'row'}" @end="moveItem($event)" v-bind:disabled="isMobileCheck">
                                    <template v-for="(_id, _index) in cards[id].itemsNum" v-if="items[_id]">
                                        <div class="card-con" :id="'item_'+items[_id].id"
                                             v-bind:class="{
                                             'stack-bg1':items[_id].label === '1',
                                             'stack-bg2':items[_id].label === '2',
                                             'stack-bg3':items[_id].label === '3',
                                             'stack-bg4':items[_id].label === '4',
                                             'stack-bg5':items[_id].label === '5',
                                             'stack-bg6':items[_id].label === '6',
                                             'stack-bg7':items[_id].label === '7',
                                             'stack-bg8':items[_id].label === '8',
                                             'new':items[_id].isNew
                                         }"
                                             @click="myItemModalOn(items[_id].id < 0 ? 'ADD' : 'VIEW', cards[id], items[_id].id)">

                                            <span class="item-tit">{{items[_id].title}}</span>

                                            <div v-if="items[_id].image" class="img-wrap">
                                                <img :src="items[_id].image.imgUrl"/>
                                            </div>

                                            <span v-if="items[_id].deadLine" class="dead-line">
                                            {{items[_id].deadLine.substr(2,2)}}.{{items[_id].deadLine.substr(5,2)}}.{{items[_id].deadLine.substr(8,2)}}
                                        </span>

                                            <span v-if="items[_id].comments.length" class="comment">
                                            {{items[_id].comments.length}}
                                        </span>

                                        </div>
                                    </template>
                                </draggable>

                                <button v-if="isTeamPage || cards[id].type === 6" @click.stop="myItemModalOn('ADD', cards[id], -1)" class="card-addbtn">
                                    추가
                                </button>
                            </div>
                        </div>
                        <div v-else-if="7 <= cards[id].type && cards[id].type <= 10" @dblclick.stop class="card" :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                            <div class="card-drag-po" :name="cards[id].type">
                                <label class="card-tit">{{ cards[id].title }}</label>
                                <div>
                                    <template v-for="(_id, _index) in cards[id].itemsNum" v-if="items[_id]">
                                        <div class="card-con" :id="'item_'+items[_id].id"
                                             v-bind:class="{
                                             'stack-bg1':items[_id].label === '1',
                                             'stack-bg2':items[_id].label === '2',
                                             'stack-bg3':items[_id].label === '3',
                                             'stack-bg4':items[_id].label === '4',
                                             'stack-bg5':items[_id].label === '5',
                                             'stack-bg6':items[_id].label === '6',
                                             'stack-bg7':items[_id].label === '7',
                                             'stack-bg8':items[_id].label === '8',
                                             'new':items[_id].isNew
                                         }"
                                             @click="myItemModalOn('VIEW', cards[id], items[_id].id)">

                                            <span class="item-tit" v-if="(items[_id].with_card && items[_id].with_card.with_team) && !isTeamPage">[{{items[_id].with_card.with_team.name}}] {{items[_id].title}}</span>
                                            <span class="item-tit" v-else>{{items[_id].title}}</span>

                                            <div v-if="items[_id].image" class="img-wrap">
                                                <img :src="items[_id].image.imgUrl"/>
                                            </div>

                                            <span v-if="items[_id].deadLine" class="dead-line">
                                            {{items[_id].deadLine.substr(2,2)}}.{{items[_id].deadLine.substr(5,2)}}.{{items[_id].deadLine.substr(8,2)}}
                                        </span>

                                            <span v-if="items[_id].comments.length" class="comment">
                                            {{items[_id].comments.length}}
                                        </span>
                                        </div>

                                    </template>
                                </div>

                                <button v-if="isTeamPage || cards[id].type === 6" @click.stop="myItemModalOn('ADD', cards[id], -1)" class="card-addbtn">
                                    추가
                                </button>
                            </div>
                        </div>
                        <div v-else class="card" @dblclick.stop :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                            <div class="card-drag-po handle" :name="cards[id].type">
                                <button v-if="cards[id].userId === user.id" class="card-deletebtn" @click="delCardModalOn(cards[id], index)"></button>

                                <label v-if="!cards[id].btnCardTitle || cards[id].userId !== user.id" @click="btnCardTitleOn(cards[id])" class="card-tit">
                                    {{cards[id].title}}
                                </label>
                                <input v-else
                                       v-focus
                                       onfocus="this.select()"
                                       v-model="cards[id].title"
                                       v-click-outside="btnCardTitleOff"
                                       v-on:keydown.esc="btnCardTitleOff"
                                       v-on:keydown.enter="btnCardTitleOff" class="card-modi"
                                >

                                <draggable v-model="cards[id].itemsNum" :group="{name:'row'}" @end="moveItem($event)" v-bind:disabled="isMobileCheck">
                                    <template v-for="(_id, _index) in cards[id].itemsNum" v-if="items[_id]">
                                        <div class="card-con" :id="'item_'+items[_id].id"
                                             v-bind:class="{
                                             'stack-bg1':items[_id].label === '1',
                                             'stack-bg2':items[_id].label === '2',
                                             'stack-bg3':items[_id].label === '3',
                                             'stack-bg4':items[_id].label === '4',
                                             'stack-bg5':items[_id].label === '5',
                                             'stack-bg6':items[_id].label === '6',
                                             'stack-bg7':items[_id].label === '7',
                                             'stack-bg8':items[_id].label === '8',
                                             'new':items[_id].isNew
                                         }"
                                             @click="myItemModalOn('VIEW', cards[id], items[_id].id)">

                                            <span class="item-tit">{{items[_id].title}}</span>

                                            <div v-if="items[_id].image" class="img-wrap">
                                                <img :src="items[_id].image.imgUrl"/>
                                            </div>

                                            <span v-if="items[_id].deadLine" class="dead-line">
                                            {{items[_id].deadLine.substr(2,2)}}.{{items[_id].deadLine.substr(5,2)}}.{{items[_id].deadLine.substr(8,2)}}
                                        </span>

                                            <span v-if="items[_id].comments.length" class="comment">
                                            {{items[_id].comments.length}}
                                        </span>

                                        </div>
                                    </template>
                                </draggable>

                                <button @click.stop="myItemModalOn('ADD', cards[id], -1)" class="card-addbtn">
                                    추가
                                </button>
                            </div>
                        </div>
                    </template>
                </draggable>

                <div class="card-add">
                    <div class="card-add__input"
                         @dblclick.stop
                         v-click-outside="btnAddCardOff"
                         v-bind:class="{'active':btnAddCard}">
                        <input
                                type="text"
                                v-if="btnAddCard"
                                v-focus
                                @dblclick.stop
                                v-on:keydown.esc="btnAddCardOff"
                                v-on:keydown.enter="btnAddCardOn"
                                v-model="cardData"
                                style="display: block;"
                                placeholder="flow명을 입력하세요."
                        />
                        <button class="card-addbtn" @click="btnAddCardOn" @dblclick.stop>
                            추가
                        </button>
                    </div>
                </div>
            </template>

        </div>

        <my-item-modal
                v-if="myItemModalUp"
                :flag="tempFlag"
                :cards="cards"
                :cardId="tempCard.id"
                :items="items"
                :itemId="itemId"
                :myUser="user"
                :myTeamId="myTeam.id"
                :myClassObjectId="myClassObject.id"
                :isMyPage="false"
                :isTeamPage="isTeamPage"
                :applyId="null"
                :daehaks="null"
                @socketEvent="socketEvent"
                @modalOff="myItemModalOff"
                ref="itemModal"
        >
        </my-item-modal>

        <my-class-setting
                v-if="settingModalUp"
                :user="user"
                :cards="cards"
                :myPageId="myPageId"
                :myClass="myClass"
                :myUserList="userList"
                @socketEvent="socketEvent"
                @modalOff="settingModalOff"
                @settingSave="settingSave"
        >

        </my-class-setting>

        <my-calendar-modal2
                v-if="calendarModalUp"
                :classObjectId="myClassObject.id"
                :teamId="myTeam.id ? myTeam.id : 0"
                :myTeams="myTeams"
                :user="user"
                @modalOff="calendarModalOff"
                ref="calendarModal"
        >
        </my-calendar-modal2>

        <div class="popup" v-if="studentListModalUp">
            <div class="card-modal__dim" @click="studentListModalOff"></div>
            <div class="popup-wrap">
                <div class="popup-con scroll-sm">
                    <h3 class="popup-tit">학생 목록</h3>
                    <table class="team-list">
                        <tr>
                            <th>번호</th>
                            <th>이름</th>
                            <th>아이디</th>
                            <th>팀</th>
                        </tr>

                        <tr v-for="(u, idx) in userList">
                            <td><span class="bg">{{idx + 1}}</span></td>
                            <td><span class="bg">{{u.name}}</span></td>
                            <td><span class="bg">{{u.email}}</span></td>
                            <td><span class="bg">{{u.team ? u.team : '&nbsp;'}}</span></td>
                        </tr>
                    </table>
                </div>
                <div class="confirm-btn">
                    <button class="btn w-100" @click="studentListModalOff">확인</button>
                </div>
            </div>
        </div>

        <div v-if="delCardModal" class="popup confirm">
            <div class="popup__dim" @click="delCardModal = false"></div>
            <div class="popup-wrap">
                <div class="confirm-txt">
                    flow를 삭제하면 세부 stack이 모두 삭제됩니다.<br />
                    flow를 삭제하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn w-50 fc-gray" @click="delCardModal = false">취소</button>
                    <button class="btn w-50" @click="addDelCard(delCardId, delCardIdx)">확인</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="module">
// import Card from './restful/Card.js';
export default {
    props: {
        user : Object,
        myPageId : Number,
        myClassObjects: Array,
        reClassId: Number,
    },

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
            renderCom: true,
            componentKey: 1,
            componentKey_: 1,
            myVersion: 0,
            isPro: false,
            myIndex: 0,
            myChannel: '',
            myTeamChannel: '',
            myClassObject: [],
            myClass: [],
            myTeams: [],
            myTeam: [],

            isClassPage: true,
            isTeamPage: false,


            classIndex: '-1',
            teamIndex: '',

            userList: [],
            users: [],
            render: [],
            myItemIndex: 0,

            cards: {},
            items: {},
            chat: [],
            cardsNum: [],

            tempCard: [],
            itemId: -1,
            tempFlag: '',
            isCardItem: '',
            cardData: '',
            cardItem: null,
            dblClickAddCard: false,
            btnCardItem: false,
            btnAddCard: false,
            settingModalUp: false,
            calendarModalUp: false,
            myItemModalUp: false,
            studentListModalUp: false,
            userMsg: '',
            talkState: false,
            isMobileCheck: false,
            delCardModal: false,
            delCardId: '',
            delCardIdx: '',
            divTeam: false,
            divClass: true,
            scrollExist: false,
            year: '2021',
            semester: '1',
            myClassObjects2: null,
        }
    },
    created() {     //렌더링이 되기전
        // this.myClassObjects2 = _.cloneDeep(this.myClassObjects);
        axios.get(window.location.origin + '/myClass/classList/'+this.year+'/'+this.semester).then(re => {
            if(re.data){
                this.myClassObjects2 = re.data;
                this.init();
            }
        })

        // this.init();
        window.addEventListener("resize", this.resizeWindow);
    },
    mounted() {     //렌더링이 되고 나서
        this.$nextTick(() => {
            // 모든 화면이 렌더링된 후 실행
            if(this.isMobile()){
                this.isMobileCheck = true;
            }
        });
    },
    beforeUpdate() {    //data 값이 바뀌기는 전 순간에 호출
    },
    updated() {          //data 값이 바뀌고나서 호출
        // console.log(Echo.connector.channels);
        //document.getElementsByClassName('card-wrap')[0].scrollWidth > document.getElementsByClassName('card-wrap')[0].clientWidth ? this.scrollExist = true : this.scrollExist = false;
        document.getElementsByClassName('card-wrap')[0] ? this.scrollExistCheck() : '';
    },
    methods: {
        reRender(){
            this.renderCom = false;

            this.$nextTick(() => {
                this.renderCom = true;
            })
        },
        resizeWindow() {
            document.getElementsByClassName('card-wrap')[0].scrollWidth > document.getElementsByClassName('card-wrap')[0].clientWidth ? this.scrollExist = true : this.scrollExist = false;
        },
        scrollRight() {
            document.getElementsByClassName('card-wrap')[0].scrollLeft = document.getElementsByClassName('card-wrap')[0].scrollWidth;
        },
        initContents(){
            this.cards = {};
            this.items = {};
            this.chat = [];
            this.cardsNum = [];
        },
        dragDefault(e){
            // return (this.cards[e.relatedContext.element].type === 4);
        },
        forceRerender() {
            this.componentKey += 1;
            this.componentKey_ += 1;
        },
        init() {
            this.initContents();
            if(this.myClassObjects2.length){
                this.classIndex = '-1';
                let idx = -1;    //'-1' 밑예 -1일꼉우 처리 필요

                if(this.reClassId){
                    for(let i=0;i<this.myClassObjects2.length;i++){
                        if(this.myClassObjects2[i].class_object.my_class.id === this.reClassId){
                            idx = i;
                            this.classIndex = i+'';
                            break;
                        }
                    }
                }

                this.teamIndex = '-1';
                this.myTeam = [];

                // if(this.myClassObjects2[idx].class_object.team_info){
                //     this.teamIndex = '-1';
                //     console.log('--------init data-------');
                //     console.log(this.myClassObjects2);
                //     console.log('------------------------');
                //
                //     this.myClassObject = this.myClassObjects2[idx].class_object;
                //     this.myClass = this.myClassObjects2[idx].class_object.my_class;
                //     this.myTeams = this.myClassObjects2[idx].class_object.team_info;
                //     console.log('--------team data-------');
                //     console.log(this.myTeams);
                //     console.log('------------------------');
                //
                //     this.myTeam = [];
                // }
                // else{
                //     this.myClassObject = this.myClassObjects2[idx].myClass;
                //     this.myClass = this.myClassObjects2[idx].myClass.with_my_classes;
                //     this.myTeams = [];
                //     this.myTeam = [];
                //     this.teamIndex = '-1';
                // }
                //
                // axios.get(window.location.origin + '/fetch/class/members/'+this.myClassObject.id).then(re => {
                //     if(re.data){
                //         this.userList = re.data;
                //         // console.log('USSERLIST::: ');
                //         // console.log(this.userList);
                //     }
                // })

            }
            else{
                if(this.reClassId){
                    for(let i=0;i<Object.keys(this.myClassObjects2).length;i++){
                        if(this.myClassObjects2[i].class_object.my_class.id === this.reClassId){
                            this.classIndex = (i+1)+'';
                            this.teamIndex = '-1';
                            this.myTeam = [];
                            this.selectClass(i);
                            break;
                        }
                    }
                }
                else{
                    this.classIndex = '-1';
                }



                this.teamIndex = '-1';
                this.myTeam = [];
                // alert('non myClasses')
                // window.location.href = "http://49.172.70.81:8000/myPage";
            }
            //
            // this.initCard(this.myClassObject.id, 'classObject');
            // this.socketInit('MYCLASS_', this.myClassObject.id);
            //
            // if(!this.myClass.onTeamAccess && this.user.authority !== 1){
            //     this.supperSocketInit('SUPER_', this.myClassObject.id);
            // }
            //
            // this.myChannel = 'MYCLASS_'+this.myClassObject.id;

            this.componentKey++;
        },
        initCard(isId, isType){
            window.Rest.fetch(
                    {
                        id: isId,
                        types: 'card',
                        subTypes: isType,
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            // console.log('#########################');
                            // console.log(_result);
                            // console.log('#########################');

                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                this.initContents();

                                let _card = _result.cards;
                                let _cardNum = _result.cardsNum;
                                this.cardsNum = _result.cardsNum;

                                if(!_result.cardsNum){
                                    this.cardsNum = [];
                                    _card = [];
                                    _cardNum = [];
                                }

                                this.talkState = false;

                                if(_cardNum){
                                    for(let i=0;i<_card.length;i++){
                                        _card[i].item = {};
                                        if(_card[i].type === 5 || _card[i].type === 12){
                                            _card[i].item = [];
                                            this.chatInit(_card[i]);
                                            this.talkState = true;
                                        }
                                        else if(_card[i].itemsNum){
                                            for(let j=0;j<_card[i].with_items.length;j++){
                                                if(_card[i].with_items[j].with_comments){
                                                    _card[i].with_items[j].comments = _.cloneDeep(_card[i].with_items[j].with_comments);
                                                }
                                                else{
                                                    _card[i].with_items[j].comments = [];
                                                }


                                                if(_card[i].with_items[j].images && _card[i].with_items[j].images.length){
                                                    for(let x=0;x<_card[i].with_items[j].with_images.length;x++){
                                                        if(_card[i].with_items[j].with_images[x].id === _card[i].with_items[j].images[0]){
                                                            _card[i].with_items[j].image = _card[i].with_items[j].with_images[x];
                                                            break;
                                                        }
                                                    }
                                                }

                                                _card[i].with_items[j].isNew = false;
                                                Vue.set(this.items, _card[i].with_items[j].id, _card[i].with_items[j]);
                                                // this.items[_card[i].with_items[j].id] = _card[i].with_items[j];
                                            }
                                        }
                                        else{
                                            _card[i].itemsNum = [];
                                        }

                                        Vue.set(this.cards, _card[i].id, _card[i]);
                                    }
                                }

                                this.initTempCard();

                                if(this.myClass.userId === this.user.id){
                                    this.isPro = true;
                                }
                                else{
                                    this.isPro = false;
                                }

                            }
                            // this.forceRerender();
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        initTempCard() {
          const tempCardData = JSON.parse(localStorage.getItem('tempCard'));

          if (typeof tempCardData === 'object') {
            for (const key in tempCardData) {
              if (this.cardsNum.includes(tempCardData[key].cardId)) {
                let itemsNum = this.cards[tempCardData[key].cardId].itemsNum;
                itemsNum = typeof itemsNum === 'object' || typeof itemsNum === 'array' ? itemsNum : [];
                const _itemsNum = [tempCardData[key].id];
                this.cards[tempCardData[key].cardId].itemsNum = _itemsNum.concat(itemsNum);

                Vue.set(this.items, tempCardData[key].id, tempCardData[key]);
                console.log(this.items);
              }
            }
          }
        },
        cardItemCommentFetch(_data){
            window.Rest.fetch(
                    {
                        id: _data.itemId,
                        types: 'comment',
                        subTypes: 'comment'
                    }
            ).then(
                    (re) => {
                        if(re){
                            // const _result = JSON.parse(re);
                            const _result = re.data;

                            let tempComments = [];

                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                _result.forEach(com => {
                                    let date_ = new Date(com.updated_at);
                                    let year_ = date_.getFullYear();
                                    let month_ = (1 + date_.getMonth());
                                    let day_ = date_.getDate();
                                    let hour_ = date_.getHours();
                                    let minutes_ = date_.getMinutes();
                                    month_ = month_ >= 10 ? month_ : '0' + month_;
                                    day_ = day_ >= 10 ? day_ : '0' + day_;
                                    hour_ = hour_ >= 10 ? hour_ : '0' + hour_;
                                    minutes_ = minutes_ >= 10 ? minutes_ : '0' + minutes_;

                                    tempComments.push({
                                        id: com.id,
                                        itemId: com.itemId,
                                        userId: com.with_user.id,
                                        userName: com.with_user.name,
                                        date: year_ + '.' + month_ + '.' +day_ + '. ' + hour_ + ':' +minutes_,
                                        content: com.content,
                                        fileName: com.fileName,
                                        fileUrl: window.Rest.uri + '/download/' +  com.id,
                                        file: '',
                                        commentMenu: false,
                                        editMode: false,
                                    });
                                })

                                let cardIdx = -1;
                                let itemIdx = -1;

                                if(this.isTeamPage !== _data.isTeamPage){
                                    let cardType = -1;

                                    if(_data.cardType === 1){
                                        cardType = 10;
                                    }else if(_data.cardType === 2){
                                        cardType = 7;
                                    }

                                    if(cardType !== -1){
                                        for(let i=0;i<this.cards.length;i++){
                                            if(this.cards[i].type === cardType){
                                                cardIdx = i;
                                                break;
                                            }
                                        }
                                    }
                                    else{
                                        console.log('?????');
                                    }
                                }
                                // else{
                                //     for(let i=0;i<this.cards.length;i++){
                                //         if(this.cards[i].id === _data.cardId){
                                //             cardIdx = i;
                                //             break;
                                //         }
                                //     }
                                // }
                                //
                                // for(let i=0;i<this.cards[cardIdx].item.length;i++){
                                //     if(this.cards[cardIdx].item[i].id === _data.itemId){
                                //         itemIdx = i;
                                //         break;
                                //     }
                                // }

                                // this.cards[cardIdx].item[itemIdx].comments = tempComments;
                                this.items[_data.itemId].comments = tempComments;

                                console.log('TTTTTTTT');
                                if(this.myItemModalUp) {
                                    if(!(cardIdx === -1 || itemIdx === -1)){

                                        if (this.itemId === _data.itemId) {

                                            this.$refs.itemModal.cardItemCommentFetch('UPDATE');
                                        }
                                    }
                                }

                                this.cards.push('1');
                                this.cards.pop();
                                // this.forceRerender();
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        dragCallBack(e){
            if(this.myClass.userId !== this.user.id){
                return false;
            }
        },
        selectClass(idx){
            if(this.isClassPage){
                console.log('LEAVE :'+ this.myChannel);
                Echo.leave('MYCLASS_'+this.myClassObject.id);

                if(!this.myClass.onTeamAccess && this.user.authority !== 1){
                    console.log('LEAVE : SUPER_'+ this.myClassObject.id);
                    Echo.leave('SUPER_'+this.myClassObject.id);
                }
            }

            if(this.isTeamPage){
                console.log('LEAVE :'+ this.myTeamChannel);
                Echo.leave('MYTEAM_'+this.myTeam.id);
            }

            this.myClassObject = this.myClassObjects2[idx].class_object;
            this.myClass = this.myClassObjects2[idx].class_object.my_class;

            console.log('=============')
            console.log(this.myClassObjects2)

            window.Rest.fetch(
                    {
                        id: this.myClass.id,
                        types: 'myClass',
                        subTypes: 'classObject',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result[0]){
                                    this.myClass = _result[0];
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )

            this.myTeams = this.myClassObjects2[idx].class_object.team_info;
            this.myTeam = '';

            axios.get(window.location.origin + '/fetch/class/members/'+this.myClassObject.id).then(re => {
                if(re.data){
                    this.userList = re.data;
                }
            })

            this.teamIndex = '-1';
            this.isClassPage = true;
            this.isTeamPage = false;


            this.initCard(this.myClassObject.id, 'classObject');



            if(!Echo.connector.channels.hasOwnProperty('presence-MYCLASS_'+this.myClassObject.id)){
                this.socketInit('MYCLASS_', this.myClassObject.id);
            }

            if(!Echo.connector.channels.hasOwnProperty('presence-SUPER_'+this.myClassObject.id)){
                if(!this.myClass.onTeamAccess && this.user.authority !== 1){
                    this.supperSocketInit('SUPER_', this.myClassObject.id);
                }
            }

            this.myChannel = 'MYCLASS_'+this.myClassObject.id;

            this.divTeam = false;
            this.divClass = true;

            this.reRender();
        },
        selectTeam(idx){
            if(idx === -1){
                Object.keys(this.myClassObjects2).forEach((ii)=>{
                    if(this.myClassObject.id === this.myClassObjects2[ii].class_object.id){
                        this.selectClass(ii);
                        return true;
                    }
                })
                return false;
            }

            if(this.isClassPage){
                console.log('LEAVE :'+ this.myChannel);
                Echo.leave('MYCLASS_'+this.myClassObject.id);

                if(!this.myClass.onTeamAccess && this.user.authority !== 1){
                    console.log('LEAVE :SUPER_'+ this.myClassObject.id);
                    Echo.leave('SUPER_'+this.myClassObject.id);
                }
            }
            if(this.isTeamPage){
                console.log('LEAVE :'+ this.myTeamChannel);
                Echo.leave('MYTEAM_'+this.myTeam.id);
            }

            this.isClassPage = false;
            this.isTeamPage = true;
            this.myTeam =  _.cloneDeep(this.myTeams[idx]);

            axios.get(window.location.origin + '/fetch/team/members/'+this.myTeam.id).then(re => {
                if(re.data){
                    this.userList = re.data;
                }
            })

            this.initCard(this.myTeam.id, 'team');

            this.socketInit('MYTEAM_', this.myTeam.id);
            this.myTeamChannel = 'MYTEAM_'+this.myTeam.id;

            this.forceRerender();

            this.divTeam = true;
            this.divClass = false;

            this.reRender();
        },
        showDate(data){
            this.selectData = data;
        },
        btnAddCardOn() {
            if (this.btnAddCard | this.dblClickAddCard) {
                if (this.cardData) {
                    this.addNewCard(this.cardData);
                    this.btnAddCard = false;
                    this.dblClickAddCard = false;
                }
                else {

                }
            }
            else {
                this.btnAddCard = true;
            }
        },
        dblClickAddCardOn(e){
            if(!this.isMobile()){
                if(!this.dblClickAddCard){
                    if(this.$refs.cardWrapRef.clientWidth - 20 < e.clientX + 260){
                        this.$refs.inputDom.style.left = (this.$refs.cardWrapRef.clientWidth - 20 - 260) + 'px';
                    }
                    else{
                        this.$refs.inputDom.style.left = e.clientX + 'px';
                    }

                    if(this.$refs.cardWrapRef.clientHeight - 20 < e.clientY + 87){
                        this.$refs.inputDom.style.top = (this.$refs.cardWrapRef.clientHeight) + 'px';
                    }
                    else{
                        this.$refs.inputDom.style.top = e.clientY + 'px';
                    }

                    this.dblClickAddCard = true;
                }
            }
        },      //더블클릭시 카드 추가 창
        dblClickAddCardOff(){
            if(this.dblClickAddCard){
                this.dblClickAddCard = false;
            }
        },
        btnAddCardOff() {
            if (this.btnAddCard) {
                this.btnAddCard = false;
            }
        },
        btnCardTitleOn(card) {
            if (!card.btnCardTitle) {
                if (this.tempCard.btnCardTitle) {
                    this.tempCard.btnCardTitle = false;
                    this.tempCard = '';
                }
                card.btnCardTitle = true;
                this.tempCard = card;
            }
        },
        btnCardTitleOff() {
            if (this.tempCard.btnCardTitle) {
                window.Rest.update(
                        {
                            id: this.tempCard.id,
                            types: 'card',
                            subTypes: 'title',
                            title: this.tempCard.title,
                        }
                ).then(
                        (re) => {
                            if(re){
                                const _result = re.data;
                                if(_result.hasOwnProperty('fail')){
                                    alert(_result.fail);
                                }
                                else{
                                    if(_result.success){

                                    }

                                    this.tempCard.btnCardTitle = false;
                                    this.tempCard = '';
                                }
                            }
                            else{
                                console.log('FETCH FAIL');
                            }
                        }
                )
            }
        },
        settingSave(config){
            console.log('-----SETTING-------');
            console.log(config);
            console.log('-------------------');

            window.Rest.classSetting(config).then(
                    (re) => {
                        const _result = re.data;
                        if(_result.hasOwnProperty('fail')){
                            alert(_result.fail);
                        }
                        else{
                            if(_result.success){
                                if(this.isTeamPage){
                                    this.initCard(this.myTeam.id, 'team');
                                }
                                else{
                                    this.initCard(this.myClassObject.id, 'classObject');
                                }

                                this.socketConfigInit();

                                this.scrollExistCheck();
                            }
                        }
                    }
            )
        },
        addNewTalk(){
            window.Rest.create(
                    {
                        id: this.isTeamPage ? this.myTeam.id : this.myClassObject.id,
                        types: 'talk',
                        subTypes: this.isTeamPage ? 'team' : 'classObject',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                console.log('-----NEW TALK------');
                                console.log(_result[0]);
                                console.log('--------------------');

                                // this.cards.push(_result[0]);
                                if(this.isTeamPage){
                                    this.myTeam.onTeamTalk = true;
                                    this.initCard(this.myTeam.id, 'team');
                                }
                                else{
                                    this.myClass.onClassTalk = true;
                                    this.initCard(this.myClassObject.id, 'classObject');
                                }

                                this.scrollExistCheck();

                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )

            // this.cards.push({
            //     id: this.myIndex++,
            //     userId: this.user.id,
            //     myPageId: this.myPageId,
            //
            //     type: 5,
            //
            //     title: '수업톡',
            //     talks: [],
            //
            //     btnCardTitle: false,
            // })


        },
        addNewCard(title) {
            window.Rest.create(
                    {
                        id: this.isTeamPage ? this.myTeam.id : this.myClassObject.id,
                        types: 'card',
                        subTypes: this.isTeamPage ? 'team' : 'classObject',
                        title: title,
                        type: 4,
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result[0]){
                                    _result[0].item = [];
                                    _result[0].itemsNum = [];
                                    Vue.set(this.cards, _result[0].id, _result[0]);
                                    this.cardsNum.push(_result[0].id);
                                }
                                this.cardData = '';

                                this.scrollExistCheck();
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        addDelCard(card,idx){
            if(card.userId !== this.user.id){
                return false;
            }

            if(card.btnCardTitle){
                return false;
            }

            window.Rest.delete(
                    {
                        id: card.id,
                        types: 'card',
                        subTypes: this.isTeamPage ? 'team' : 'classObject',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result.success){
                                    // this.cards.splice(idx, 1);
                                    delete this.cards[this.cardsNum[idx]];
                                    this.cardsNum.splice(idx, 1);
                                    this.delCardModal = false;

                                    this.scrollExistCheck();
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )

        },
        settingModalOn(){
            // this.myClass = this.myClasses[Number(this.classIndex)];

            window.Rest.fetch(
                    {
                        id: this.myClass.id,
                        types: 'myClass',
                        subTypes: 'classObject',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result[0]){
                                    this.myClass = _result[0];
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )


            this.settingModalUp = true;
        },
        settingModalOff(){
            this.settingModalUp = false;
        },
        calendarModalOn(){
            document.getElementsByTagName('body')[0].setAttribute('class', 'not_scroll'); //바닥 스크롤 막기
            this.calendarModalUp = true;
        },
        calendarModalOff(){
            document.getElementsByTagName('body')[0].setAttribute('class', ''); //바닥 스크롤 풀기
            this.calendarModalUp = false;
        },
        myItemModalOn(flag, card, itemId){
            document.getElementsByTagName('body')[0].setAttribute('class', 'not_scroll'); //바닥 스크롤 막기
            this.tempCard = card;
            this.itemId = itemId;
            this.tempFlag = flag;

            this.myItemModalUp = true;
        },
        myItemModalOff(){
            document.getElementsByTagName('body')[0].setAttribute('class', ''); //바닥 스크롤 풀기
            this.myItemModalUp = false;
        },
        studentListModalOn(){
            //학생 불러오기
            // axios.get(window.location.origin + `/fetch/${isClassPage ? 'class' : 'team'}/members/${this.myClassObject.id}`).then(re => {
            //   this.sDatas = re.data.consultings;
            //   this.isBasicTarget = re.data.isConsultingTarget;
            // })

            this.studentListModalUp = true;
        },
        studentListModalOff(){
            this.studentListModalUp = false;
        },
        calendarOff(date){
            alert(date);
        },
        moveCard(e){
            // const newCardId = e.to.offsetParent.id.split('_')[1];
            const oldCardId = e.clone.id.split('_')[1];
            const newCardId = e.item.id.split('_')[1];

            if(oldCardId === newCardId){
                return false;
            }

            window.Rest.update(
                    {
                        id: oldCardId,
                        types: 'card',
                        destinationPosition: e.newIndex,
                        subTypes: this.isTeamPage ? 'team' : 'classObject',
                        subId: this.isTeamPage ? this.myTeam.id : this.myClassObject.id,
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result.success){

                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        moveItem(e){
            const newCardId = e.to.offsetParent.id.split('_')[1];
            const newCardIndex = e.to.offsetParent.id.split('_')[2];
            const oldCardId = e.from.offsetParent.id.split('_')[1];
            const oldCardIndex = e.from.offsetParent.id.split('_')[2];
            const oldItemId = this.cards[this.cardsNum[Number(newCardIndex)]].itemsNum[e.newIndex];

            if(newCardIndex === oldCardIndex && e.newIndex === e.oldIndex){
                return false;
            }

            window.Rest.update(
                    {
                        id: oldItemId,
                        types: 'item',
                        destinationPosition: e.newIndex,
                        subTypes: 'move',
                        subId: newCardId,
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result.success){
                                    this.items[oldItemId].cardId = newCardId;
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        sendMessage(event, card){
            if(event.shiftKey === false) {
                if(event.key === 'Enter' || event.target.type === 'submit'){
                    if(this.userMsg === ''){
                        event.preventDefault();
                    }
                    else{
                        event.preventDefault();
                        if(this.userMsg){
                            this.socketChatSend(card, this.userMsg);
                            document.getElementById('talkInput').style.height = `32px`;
                        }
                    }
                }
            }
        },
        resize(event) {
            if(event.key !== "Enter"){
                event.target.style.height = `${event.target.scrollHeight}px`;
            }
        },
        chatInit(card){
            window.Rest.fetch(
                    {
                        id: card.id,
                        types: 'chat',
                        subTypes: this.isTeamPage ? 'team' : 'classObject',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result){

                                    for(let i=0;i<_result.length;i++){
                                        // card.item.push(_result[i]);
                                        this.chat.push(_result[i]);
                                    }


                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        socketChatSend(card, msg){
            window.Rest.chat(
                    {
                        id: card.id,
                        types: this.isTeamPage ? 'team' : 'classObject',
                        subId: this.isTeamPage ? this.myTeam.id : this.myClassObject.id,
                        message: msg,
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result.success){
                                    if(this.userMsg){
                                        this.chat.push({
                                            userId: this.user.id,
                                            name: this.user.name,
                                            message: this.userMsg,
                                        })
                                    }
                                    this.userMsg = '';
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }

            )

            let cs = document.getElementById('chat_scroll')
            cs.scrollTop = cs.scrollHeight;
        },
        socketEvent(data_){
            if(data_.type === 'TEAM_UPDATE_ITEM' || data_.type === 'TEAM_UPDATE_ITEM_COMMENT' || data_.type === 'TEAM_DEL_ITEM'){
                //TEAM -> MYCLASS
                console.log('TEAM -> MYCLASS');

                Echo.join(this.myChannel)
                        .whisper('updateCall', data_);
            }
            else if(data_.type === 'MYCLASS_UPDATE_ITEM' || data_.type === 'MYCLASS_UPDATE_ITEM_COMMENT' || data_.type === 'MYCLASS_DEL_ITEM'){
                // MYCLASS -> TEAM
                //////////////////
                console.log('MYCLASS -> TEAM');
                console.log(this.myTeams);
                console.log('CALL:::: '+data_.teamId);


                for(let i=0;this.myTeams.length;i++){
                    if(this.myTeams[i].id === data_.teamId){
                        console.log('CALL:::: '+'MYTEAM_'+this.myTeams[i].id);
                        console.log('CALL:::: '+data_.teamId);
                        Echo.join('MYTEAM_'+this.myTeams[i].id)
                                .whisper('updateCall', data_);
                        break;
                    }
                }
            }
            else if(this.isTeamPage){
                //TEAM -> TEAM

                //TEAM
                console.log(this.myTeamChannel);
                Echo.join(this.myTeamChannel)
                        .whisper('updateCall', data_);
            }
            else{
                // MYCLASS -> MYCLASS
                console.log(this.myChannel);
                Echo.join(this.myChannel)
                        .whisper('updateCall', data_);
            }
        },
        socketInit(channel, id){
            console.log('JOIN :'+channel+id);
            Echo.join(channel+id)
                    .here(user => {
                        this.users = user;
                        // console.log(Echo.socketId());

                        // console.log(Echo.connector.pusher.config);
                        // Echo.connector.pusher.config.auth.headers.setProperty('X-Socket-ID', Echo.socketId());
                        // Echo.connector.pusher.config['X-Socket-ID'] = Echo.socketId();

                        // console.log(user);
                    })
                    .joining(user => {
                        this.users.push(user);
                    })
                    .leaving(user => {
                        this.users = this.users.filter(u => u.id !== user.id);
                    })
                    .listenForWhisper('updateCall', (_data) =>{
                        console.log('socket!!');
                        if(this.isTeamPage === _data.isTeamPage){
                            if(_data.type === 'MOVE_CARD'){
                                let oldCardIndex = -1;
                                for(let i=0;i<this.cards.length;i++){
                                    if(this.cards[i].id === _data.oldCardId){
                                        oldCardIndex = i;
                                        break;
                                    }
                                }

                                if(!(oldCardIndex === -1)){
                                    if(_data.newCardIndex === 0){
                                        this.cards.unshift(this.cards[oldCardIndex]);
                                        this.cards.splice(oldCardIndex + 1, 1);
                                    }
                                    else if(_data.newCardIndex === (this.cards.length - 1)){
                                        this.cards.push(this.cards[oldCardIndex]);
                                        this.cards.splice(oldCardIndex, 1);
                                    }
                                    else{
                                        if(oldCardIndex < _data.newCardIndex){
                                            this.cards.splice(_data.newCardIndex + 1, 0, this.cards[oldCardIndex]);
                                            this.cards.splice(oldCardIndex, 1);
                                        }
                                        else if(_data.newCardIndex < oldCardIndex){
                                            this.cards.splice(_data.newCardIndex, 0, this.cards[oldCardIndex]);
                                            this.cards.splice(oldCardIndex + 1, 1);
                                        }
                                    }
                                }
                                else{
                                    // 옮길곳을 못찾으면 패치 요청 필요
                                }
                            }
                            else if(_data.type === 'ADD_CARD'){
                                window.Rest.fetch(
                                        {
                                            id: _data.cardId,
                                            types: 'card',
                                            subTypes: 'id',
                                        }
                                ).then(
                                        (re) => {
                                            if(re){
                                                const _result = re.data;

                                                if(_result.hasOwnProperty('fail')){
                                                    // 옮길곳을 못찾으면 패치 요청 필요
                                                    alert(_result.fail);
                                                }
                                                else{
                                                    _result.item = [];
                                                    this.cards.push(_result);
                                                }
                                            }
                                            else{
                                                // 옮길곳을 못찾으면 패치 요청 필요
                                                console.log('FETCH FAIL');
                                            }
                                        }
                                )
                            }
                            else if(_data.type === 'DEL_CARD'){
                                let cardIndex = -1;

                                for(let i=0;i<this.cards.length;i++){
                                    if(this.cards[i].id === _data.cardId){
                                        cardIndex = i;
                                        break;
                                    }
                                }

                                if(cardIndex !== -1){
                                    if(this.myItemModalUp) {
                                        for(let i=0;i<this.cards[cardIndex].item.length;i++){
                                            if (this.itemId === this.cards[cardIndex].item[i].id) {
                                                this.myItemModalOff();
                                                break;
                                            }
                                        }
                                    }

                                    this.cards.splice(cardIndex, 1);
                                }
                                else{
                                    // 옮길곳을 못찾으면 패치 요청 필요
                                }
                            }
                            else if(_data.type === 'UPDATE_CARD'){
                                let cardIndex = -1;

                                for(let i=0;i<this.cards.length;i++){
                                    if(this.cards[i].id === _data.cardId){
                                        cardIndex = i;
                                        break;
                                    }
                                }

                                if(cardIndex !== -1){
                                    this.cards[cardIndex].title = _data.title;
                                    this.cards.push('1');
                                    this.cards.pop();
                                }
                                else{
                                    // 옮길곳을 못찾으면 패치 요청 필요
                                }
                            }
                            else if(_data.type === 'MOVE_ITEM'){
                                let oldCardIndex = -1;
                                let newCardIndex = -1;
                                let oldItemIndex = -1;

                                for(let i=0;i<this.cards.length;i++){
                                    if(this.cards[i].id === _data.oldCardId){
                                        oldCardIndex = i;
                                        break;
                                    }
                                }
                                for(let i=0;i<this.cards.length;i++){
                                    if(this.cards[i].id === _data.newCardId){
                                        newCardIndex = i;
                                        break;
                                    }
                                }

                                if(!(oldCardIndex === -1 || newCardIndex === -1)){
                                    for(let i=0;i<this.cards[oldCardIndex].item.length;i++){
                                        if(this.cards[oldCardIndex].item[i].id === _data.oldItemId){
                                            oldItemIndex = i;
                                            break;
                                        }
                                    }


                                    if(oldItemIndex !== -1){
                                        this.cards[oldCardIndex].item[oldItemIndex].id = -1;
                                        const tempItem = _.cloneDeep(this.cards[oldCardIndex].item[oldItemIndex]);
                                        tempItem.id = _data.oldItemId;

                                        for(let i=0;i<this.cards[oldCardIndex].item.length;i++){
                                            if(this.cards[oldCardIndex].item[i].id === -1){
                                                this.cards[oldCardIndex].item.splice(i,1);
                                                break;
                                            }
                                        }
                                        if(_data.newItemIndex === 0){
                                            this.cards[newCardIndex].item.unshift(tempItem);
                                        }
                                        else if(_data.newItemIndex === this.cards[newCardIndex].item.length){
                                            this.cards[newCardIndex].item.push(tempItem);
                                        }
                                        else{
                                            this.cards[newCardIndex].item.splice(_data.newItemIndex,0,tempItem);
                                        }

                                        this.cards.push('1');
                                        this.cards.pop();
                                    }
                                    else{
                                        // 옮길곳을 못찾으면 패치 요청 필요
                                    }
                                }
                                else{
                                    // 옮길곳을 못찾으면 패치 요청 필요
                                }


                            }
                            else if(_data.type === 'ADD_ITEM'){
                                window.Rest.fetch(
                                        {
                                            id: _data.itemId,
                                            types: 'item',
                                            subTypes: 'item'
                                        }
                                ).then(
                                        (re) => {
                                            if(re){
                                                const _result = re.data;

                                                if(_result[0]){
                                                    let __result = _.cloneDeep(_result[0]);
                                                    if(_result[0].images){
                                                        let imagesNum = _.cloneDeep(_result[0].images);
                                                        let images = _.cloneDeep(_result[0].with_images);
                                                        __result.images = [];
                                                        for(let i=0;i<imagesNum.length;i++){
                                                            for(let j=0;j<images.length;j++){
                                                                if(images[j].id === imagesNum[i]){
                                                                    __result.images.push(images[j]);
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }

                                                    for(let i=0;i<this.cards.length;i++){
                                                        if(this.cards[i].id === _result[0].cardId){
                                                            __result.comments = [];
                                                            this.cards[i].item.unshift(__result);
                                                            // this.cards[i].item.push(__result);
                                                            break;
                                                        }
                                                    }

                                                }
                                            }
                                            else{
                                                console.log('FETCH FAIL');
                                            }
                                        }
                                )

                            }
                            else if(_data.type === 'DEL_ITEM'){
                                let cardIdx = -1;
                                let itemIdx = -1;

                                for(let i=0;i<this.cards.length;i++){
                                    if(this.cards[i].id === _data.cardId){
                                        cardIdx = i;
                                        break;
                                    }
                                }

                                for(let i=0;i<this.cards[cardIdx].item.length;i++){
                                    if(this.cards[cardIdx].item[i].id === _data.itemId){
                                        itemIdx = i;
                                        break;
                                    }
                                }

                                if(!(cardIdx === -1 || itemIdx === -1)){
                                    this.cards[cardIdx].item.splice(itemIdx, 1);

                                    if(this.myItemModalUp) {
                                        if (this.itemId === _data.itemId) {
                                            this.myItemModalOff();
                                        }
                                    }
                                }
                            }
                            else if(_data.type === 'UPDATE_ITEM'){
                                console.log('UPDATE!!!!!!!!')
                                console.log(_data)

                                window.Rest.fetch(
                                        {
                                            id: _data.itemId,
                                            types: 'item',
                                            subTypes: 'item'
                                        }
                                ).then(
                                        (re) => {
                                            if(re){
                                                const _result = re.data;
                                                if(_result[0]){
                                                    let __result = _.cloneDeep(_result[0]);
                                                    if(_result[0].images){
                                                        let imagesNum = _.cloneDeep(_result[0].images);
                                                        let images = _.cloneDeep(_result[0].with_images);
                                                        __result.images = [];
                                                        for(let i=0;i<imagesNum.length;i++){
                                                            for(let j=0;j<images.length;j++){
                                                                if(images[j].id === imagesNum[i]){
                                                                    __result.images.push(images[j]);
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                    }

                                                    let cardIndex = -1;

                                                    for(let i=0;i<this.cards.length;i++){
                                                        if(this.cards[i].id === _result[0].cardId){
                                                            cardIndex = i;
                                                            break;
                                                        }
                                                    }

                                                    if(cardIndex !== -1){
                                                        for(let i=0;i<this.cards[cardIndex].item.length;i++){
                                                            if(this.cards[cardIndex].item[i].id === _result[0].id){
                                                                // __result.comments = _.cloneDeep(this.cardItemCommentFetch(cardIndex,i,this.cards[cardIndex].item[i].id));
                                                                __result.comments = __result.with_comments;
                                                                this.cards[cardIndex].item[i] = __result;

                                                                this.cards.push('1');
                                                                this.cards.pop();
                                                                break;
                                                            }
                                                        }

                                                        if(this.myItemModalUp){
                                                            if(this.itemId === _result[0].id){
                                                                this.$refs.itemModal.cardItemFetch('UPDATE');
                                                            }
                                                        }
                                                    }
                                                    else{
                                                    }

                                                }
                                            }
                                            else{
                                                console.log('FETCH FAIL');
                                            }
                                        }
                                )
                            }
                            else if(_data.type === 'UPDATE_ITEM_COMMENT'){
                                this.cardItemCommentFetch(_data);
                            }
                            else{
                                if(this.isTeamPage){
                                    this.initCard(this.myTeam.id, 'team');
                                }
                                else{
                                    this.initCard(this.myClassObject.id, 'classObject');
                                }
                            }
                        }
                        else{
                            console.log(_data.type);
                            if(_data.type === 'TEAM_DEL_ITEM' || _data.type === 'MYCLASS_DEL_ITEM'){
                                let cardIdx = -1;
                                let itemIdx = -1;
                                let cardType = -1;

                                if(_data.itemType === 1){
                                    cardType = 10;
                                }else if(_data.itemType === 2){
                                    cardType = 7;
                                }

                                if(cardType !== -1){
                                    for(let i=0;i<this.cards.length;i++){
                                        if(this.cards[i].type === cardType){
                                            cardIdx = i;
                                            break;
                                        }
                                    }
                                }
                                else{

                                }

                                for(let i=0;i<this.cards[cardIdx].item.length;i++){
                                    if(this.cards[cardIdx].item[i].id === _data.itemId){
                                        itemIdx = i;
                                        break;
                                    }
                                }

                                if(!(cardIdx === -1 || itemIdx === -1)){
                                    this.cards[cardIdx].item.splice(itemIdx, 1);

                                    if(this.myItemModalUp) {
                                        if (this.itemId === _data.itemId) {
                                            this.myItemModalOff();
                                        }
                                    }
                                }
                            }
                            else if(_data.type === 'TEAM_UPDATE_ITEM' || _data.type === 'MYCLASS_UPDATE_ITEM'){
                                window.Rest.fetch(
                                        {
                                            id: _data.itemId,
                                            types: 'item',
                                            subTypes: 'item'
                                        }
                                ).then(
                                        (re) => {
                                            if(re){
                                                const _result = re.data;
                                                if(_result[0]){
                                                    let __result = _.cloneDeep(_result[0]);
                                                    if(_result[0].images){
                                                        let imagesNum = _.cloneDeep(_result[0].images);
                                                        let images = _.cloneDeep(_result[0].with_images);
                                                        __result.images = [];
                                                        for(let i=0;i<imagesNum.length;i++){
                                                            for(let j=0;j<images.length;j++){
                                                                if(images[j].id === imagesNum[i]){
                                                                    __result.images.push(images[j]);
                                                                    break;
                                                                }
                                                            }
                                                        }

                                                    }

                                                    let cardIndex = -1;
                                                    let cardType = -1;

                                                    if(__result.type === 1){
                                                        cardType = 10;
                                                    }else if(__result.type === 2){
                                                        cardType = 7;
                                                    }

                                                    if(cardType !== -1){
                                                        for(let i=0;i<this.cards.length;i++){
                                                            if(this.cards[i].type === cardType){
                                                                cardIndex = i;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    else{

                                                    }



                                                    if(cardIndex !== -1){
                                                        for(let i=0;i<this.cards[cardIndex].item.length;i++){
                                                            if(this.cards[cardIndex].item[i].id === _result[0].id){
                                                                // __result.comments = _.cloneDeep(this.cardItemCommentFetch(cardIndex,i,this.cards[cardIndex].item[i].id));
                                                                __result.comments = __result.with_comments;
                                                                this.cards[cardIndex].item[i] = __result;

                                                                console.log(this.cards[cardIndex].item[i]);

                                                                this.cards.push('1');
                                                                this.cards.pop();
                                                                break;
                                                            }
                                                        }

                                                        if(this.myItemModalUp){
                                                            if(this.itemId === _result[0].id){
                                                                this.$refs.itemModal.cardItemFetch('UPDATE');
                                                            }
                                                        }
                                                    }
                                                    else{
                                                    }
                                                }
                                            }
                                            else{
                                                console.log('FETCH FAIL');
                                            }
                                        }
                                )
                            }
                            else if(_data.type === 'TEAM_UPDATE_ITEM_COMMENT' || _data.type === 'MYCLASS_UPDATE_ITEM_COMMENT'){
                                this.cardItemCommentFetch(_data);
                            }
                        }
                    })
                    .listenForWhisper('updateConfigCall', (_data) =>{
                        console.log('socket:: update config!!');
                        if(_data){
                            this.myClass = _data.config;
                        }
                    })
                    .listen('.fixedCardItemEvent.create', (re)=>{
                        console.log('fixedCardItemEvent.create');
                        console.log(re);

                        if(re){
                            let itemType = -1;
                            if(Number(re.item.type) === 1){
                                itemType = 10;
                            }
                            else if(Number(re.item.type) === 2){
                                itemType = 7;
                            }
                            else if(Number(re.item.type) === 5){
                                itemType = 9;
                            }

                            for(let key in this.cards){
                                if(this.cards[key].type === itemType){
                                    re.item.comments = [];
                                    if(re.item.images){
                                        for(let i=0;i<re.item.with_images.length;i++){
                                            if(re.item.with_images[i].id === re.item.images[0]){
                                                re.item.image = re.item.with_images[i];
                                                break;
                                            }
                                        }
                                    }

                                    // const idx = re.itemsNum.indexOf(Number(re.item.id));

                                    this.cards[key].itemsNum = re.itemsNum;
                                    re.item.isNew = true;
                                    Vue.set(this.items, Number(re.item.id), re.item)
                                    // this.cards[key].itemsNum.splice(idx, 0, re.item);

                                    console.log(this.cards[key].itemsNum);
                                    console.log(this.items);

                                    break;
                                }
                            }
                        }
                    })
                    .listen('.chat.received', (_data)=>{
                        if(_data){
                            this.chat.push({
                                id: _data.message.id,
                                userId: _data.message.userId,
                                name: _data.message.name,
                                message: _data.message.message,
                            })
                        }
                    })
                    .listen('.card.add', (re)=>{
                        console.log('socket add');
                        if(re){
                            console.log('socket add');
                            re.data.itemsNum = [];
                            Vue.set(this.cards, re.data.id, re.data)
                            this.cardsNum.push(re.data.id);
                        }
                    })
                    .listen('.card.update', (re)=>{
                        if(re){
                            if(this.cards[re.data.id]){
                                console.log('socket update');
                                this.cards[re.data.id].title = re.data.title;
                            }
                        }
                    })
                    .listen('.card.move', (re)=>{
                        if(re){
                            console.log('socket move');
                            this.cardsNum = re.data;
                        }
                    })
                    .listen('.card.del', (re)=>{
                        if(re){
                            if(this.cards[re.data]){
                                console.log('socket del');
                                delete this.cards[re.data];
                                this.cardsNum.splice(this.cardsNum.indexOf(Number(re.data)),1);

                                if(this.myItemModalUp){
                                    if(this.items[this.itemId].cardId === Number(re.data)){
                                        this.myItemModalOff();
                                    }
                                }
                            }
                        }
                    })
                    .listen('.item.add', (re)=>{
                        if(re){
                            if(this.cards[re.data.cardId]){
                                console.log('socket item add');

                                re.data.item.comments = [];
                                if(re.data.item.images){
                                    for(let i=0;i<re.data.item.with_images.length;i++){
                                        if(re.data.item.with_images[i].id === re.data.item.images[0]){
                                            re.data.item.image = re.data.item.with_images[i];
                                            break;
                                        }
                                    }
                                }

                                re.data.item.isNew = true;
                                Vue.set(this.items, re.data.item.id, re.data.item)
                                this.cards[re.data.cardId].itemsNum.unshift(re.data.item.id);

                                // console.log(this.items[re.data.item.id]);

                                // this.forceRerender();

                            }
                        }
                    })
                    .listen('.item.update', (re)=>{
                        if(re){
                            if(this.items[re.data.id]){
                                console.log('socket item update');

                                if(!re.data.comments){
                                    re.data.comments = [];
                                }

                                if(re.data.images){
                                    for(let i=0;i<re.data.images.length;i++){
                                        if(re.data.images[i].id === re.data.imagesNum[0]){
                                            re.data.image = re.data.images[i];
                                            break;
                                        }
                                    }
                                }

                                re.data.comments = _.cloneDeep(this.items[re.data.id].comments);

                                if(this.myItemModalUp){
                                    if(this.itemId === re.data.id){
                                        this.$refs.itemModal.cardItemFetch('UPDATE');
                                    }
                                    else{
                                        re.data.isNew = true;
                                    }
                                }
                                else{
                                    re.data.isNew = true;
                                }

                                this.items[re.data.id] = re.data;
                            }
                        }
                    })
                    .listen('.item.move', (re)=>{
                        if(re){
                            console.log('socket item move');

                            this.cards[re.data.oldCard.id].itemsNum = re.data.oldCard.itemsNum;

                            for(let i=0;i<this.cards[re.data.oldCard.id].itemsNum.length;i++){
                                const _itemId = this.cards[re.data.oldCard.id].itemsNum[i];
                                this.items[_itemId].cardId = re.data.oldCard.id;
                                // console.log(this.items[_itemId].cardId + ' = ' + re.data.oldCard.id);
                                console.log('idx:'+ i);


                                // Vue.set(this.items[_itemId], 'cardId', re.data.oldCard.id);
                            }

                            this.cards[re.data.newCard.id].itemsNum = re.data.newCard.itemsNum;
                            for(let i=0;i<this.cards[re.data.newCard.id].itemsNum.length;i++){
                                const _itemId = this.cards[re.data.newCard.id].itemsNum[i];
                                this.items[_itemId].cardId = re.data.newCard.id;
                            }
                        }
                    })
                    .listen('.item.del', (re)=>{
                        if(re){
                            if(this.items[re.data.id]){
                                console.log('socket item del');
                                // delete this.cards[re.data];

                                if(Number(re.data.type) === 1 || Number(re.data.type) === 2 || Number(re.data.type) === 5) {
                                    for (let key in this.cards) {
                                        if (this.cards[key].itemsNum) {
                                            for (let j = 0; j < this.cards[key].itemsNum.length; j++) {
                                                const itemId = this.cards[key].itemsNum[j];
                                                if (Number(this.items[itemId].id) === Number(re.data.id)) {
                                                    this.cards[key].itemsNum.splice(j, 1);
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }
                                else{
                                    const itemIdx = this.cards[re.data.cardId].itemsNum.indexOf(Number(re.data.id));
                                    this.cards[re.data.cardId].itemsNum.splice(itemIdx, 1);
                                }

                                delete this.items[re.data.id];

                                if(this.myItemModalUp){
                                    if(this.itemId === Number(re.data.id)){
                                        this.myItemModalOff();
                                    }
                                }
                            }
                        }
                    })
                    .listen('.card.setting', (re)=>{
                        if(re){
                            console.log(re.data);
                            console.log(this.myClass);
                            this.myClass = _.cloneDeep(re.data);
                            if(this.isTeamPage){
                                this.initCard(this.myTeam.id, 'team');
                            }
                            else{
                                this.initCard(this.myClassObject.id, 'classObject');
                            }
                        }
                    })
                    .listen('.brain.move', (re)=>{
                        if(re){
                            if(this.myItemModalUp){
                                if(this.itemId === re.data.itemId){
                                    this.$refs.itemModal.brainMoveFetch(re.data.brain);
                                }
                            }
                        }
                    })
                    .listen('.brain.add', (re)=>{
                        if(re){
                            if(this.myItemModalUp){
                                if(this.itemId === re.data.itemId){
                                    this.$refs.itemModal.brainAddFetch(re.data.brains);

                                }
                                else{
                                    Vue.set(this.items[re.data.itemId], 'isNew', true);
                                }
                            }
                            else{
                                Vue.set(this.items[re.data.itemId], 'isNew', true);
                            }
                        }
                    })
                    .listen('.brain.update', (re)=>{
                        if(re){
                            if(this.myItemModalUp){
                                if(this.itemId === re.data.itemId){
                                    this.$refs.itemModal.brainUpdateFetch(re.data.brains);
                                }
                                else{
                                    Vue.set(this.items[re.data.itemId], 'isNew', true);
                                }
                            }
                            else{
                                Vue.set(this.items[re.data.itemId], 'isNew', true);
                            }
                        }
                    })
                    .listen('.brain.del', (re)=>{
                        if(re){
                            if(this.myItemModalUp){
                                if(this.itemId === re.data.itemId){
                                    this.$refs.itemModal.brainDelFetch(re.data.brainId);
                                }
                            }
                        }
                    })
                    .listen('.comment.add', (re)=>{
                        if(re){
                            this.items[re.data.itemId].comments.push(re.data.comment);

                            if(this.myItemModalUp){
                                if(this.itemId === re.data.itemId){
                                    this.$refs.itemModal.commentAddFetch(re.data.comment);
                                }
                                else{
                                    Vue.set(this.items[re.data.itemId], 'isNew', true);
                                }
                            }
                            else{
                                Vue.set(this.items[re.data.itemId], 'isNew', true);
                            }
                        }
                    })
                    .listen('.comment.update', (re)=>{
                        if(re){
                            if(this.myItemModalUp){
                                if(this.itemId === re.data.itemId){
                                    this.$refs.itemModal.commentUpdateFetch(re.data.comment);
                                }
                                else{
                                    Vue.set(this.items[re.data.itemId], 'isNew', true);
                                }
                            }
                            else{
                                Vue.set(this.items[re.data.itemId], 'isNew', true);
                            }
                        }
                    })
                    .listen('.comment.del', (re)=>{
                        if(re){
                            for(let i=0;i<this.items[re.data.itemId].comments.length;i++){
                                if(this.items[re.data.itemId].comments[i].id === re.data.commentId) {
                                    this.items[re.data.itemId].comments.splice(i, 1);
                                    break;
                                }
                            }

                            if(this.myItemModalUp){
                                if(this.itemId === re.data.itemId){
                                    this.$refs.itemModal.commentDelFetch(re.data.commentId);
                                }
                            }
                        }
                    })
                    .listen('.calendar.add', (re)=>{
                        if(re){
                            if(this.calendarModalUp){
                                this.$refs.calendarModal.calendarAddFetch(re.data.calendar);
                            }
                        }
                    })
        },
        supperSocketInit(channel, id){
            console.log('JOIN :'+channel+id);
            Echo.join(channel+id)
                    .here(user => {
                    })
                    .joining(user => {
                    })
                    .leaving(user => {
                    })
                    .listen('.super.add', (re)=>{
                        console.log('socket .super.add');
                        console.log(re);

                        if(re){
                            if(this.cards[re.cardId]){
                                re.data.comments = [];
                                if(re.data.images){
                                    for(let i=0;i<re.data.with_images.length;i++){
                                        if(re.data.with_images[i].id === re.data.images[0]){
                                            re.data.image = re.data.with_images[i];
                                            break;
                                        }
                                    }
                                }

                                re.data.isNew = true;
                                Vue.set(this.items, re.data.id, re.data)
                                this.cards[re.cardId].itemsNum.unshift(re.data.id);
                            }
                        }
                    })
                    .listen('.super.update', (re)=>{
                        console.log('socket .super.update');
                        if(re){
                            if(this.items[re.data.id]){
                                console.log('socket item update');

                                if(!re.data.comments){
                                    re.data.comments = [];
                                }

                                if(re.data.images){
                                    for(let i=0;i<re.data.images.length;i++){
                                        if(re.data.images[i].id === re.data.imagesNum[0]){
                                            re.data.image = re.data.images[i];
                                            break;
                                        }
                                    }
                                }

                                re.data.comments = _.cloneDeep(this.items[re.data.id].comments);

                                if(this.myItemModalUp){
                                    if(this.itemId === re.data.id){
                                        this.$refs.itemModal.cardItemFetch('UPDATE');
                                    }
                                    else{
                                        re.data.isNew = true;
                                    }
                                }
                                else{
                                    re.data.isNew = true;
                                }

                                this.items[re.data.id] = re.data;
                            }
                        }
                    })
                    .listen('.super.comment.add', (re)=>{
                        if(re){
                            this.items[re.data.id].comments.push(re.data.comment);

                            if(this.myItemModalUp){
                                if(this.itemId === re.data.id){
                                    this.$refs.itemModal.commentAddFetch(re.data.comment);
                                }
                                else{
                                    Vue.set(this.items[re.data.id], 'isNew', true);
                                }
                            }
                            else{
                                Vue.set(this.items[re.data.id], 'isNew', true);
                            }
                        }
                    })
                    .listen('.super.comment.update', (re)=>{
                        console.log(re);

                        if(re){
                            if(this.myItemModalUp){
                                if(this.itemId === re.data.id){
                                    this.$refs.itemModal.commentUpdateFetch(re.data.comment);
                                }
                                else{
                                    Vue.set(this.items[re.data.id], 'isNew', true);
                                }
                            }
                            else{
                                Vue.set(this.items[re.data.id], 'isNew', true);
                            }
                        }
                    })
                    .listen('.super.comment.del', (re)=>{
                        console.log(re);

                        if(re){
                            for(let i=0;i<this.items[re.data.id].comments.length;i++){
                                if(this.items[re.data.id].comments[i].id === re.data.commentId) {
                                    this.items[re.data.id].comments.splice(i, 1);
                                    break;
                                }
                            }

                            if(this.myItemModalUp){
                                if(this.itemId === re.data.id){
                                    this.$refs.itemModal.commentDelFetch(re.data.commentId);
                                }
                            }
                        }
                    })
        },
        socketConfigInit(){
            window.Rest.fetch(
                    {
                        id: this.myClass.id,
                        types: 'myClass',
                        subTypes: 'classObject',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                if(_result[0]){
                                    this.myClass = _result[0];

                                    // Echo.join(this.myChannel)
                                    //     .whisper('updateConfigCall',{config:_result[0]});
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        isMobile(){
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },
        delCardModalOn(card,idx){
            this.delCardModal = true;
            this.delCardId = card;
            this.delCardIdx = idx;
        },
        selectYear(){
            axios.get(window.location.origin + '/myClass/classList/'+this.year+'/'+this.semester).then(re => {
                if(re.data){
                    if(this.classIndex !== '-1'){
                        if(this.isClassPage){
                            console.log('LEAVE :'+ this.myChannel);
                            Echo.leave('MYCLASS_'+this.myClassObject.id);

                            if(!this.myClass.onTeamAccess && this.user.authority !== 1){
                                console.log('LEAVE : SUPER_'+ this.myClassObject.id);
                                Echo.leave('SUPER_'+this.myClassObject.id);
                            }
                        }

                        if(this.isTeamPage){
                            console.log('LEAVE :'+ this.myTeamChannel);
                            Echo.leave('MYTEAM_'+this.myTeam.id);
                        }


                    }



                    this.myClassObjects2 = _.cloneDeep(re.data)
                    this.init();


                    // console.log(this.myClassObjects);
                    // console.log(re.data);
                    this.reRender();


                }
            })
        },
        scrollExistCheck(){
            document.getElementsByClassName('card-wrap')[0].scrollWidth > document.getElementsByClassName('card-wrap')[0].clientWidth ? this.scrollExist = true : this.scrollExist = false;
        },
    },

}
</script>
