<template>
    <div v-if="renderCom">
        <div class="card-top">
            <template v-if="!isConsult">
                <span class="btn btn-icon divsion-pro">수업준비</span>
                <vue-picker v-model="year" class="sm mr-10" autofocus>
                    <template v-for="(year, index) in ['2023','2022','2021','2020','2019','2018']">
                        <div @click="selectYear(index)">
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
            </template>
        </div>
        <div class="card-wrap" @dblclick="dblClickAddCardOn" ref="cardWrapRef">
            <button class="scroll-exist" @click="scrollRight" v-if="scrollExist"></button>
            <draggable v-model="cardsNum" handle=".handle" :move="dragDefault" @end="moveCard" v-bind:disabled="isMobileCheck">
                <template v-for="(id, index) in cardsNum" v-if="cards[id]">
                    <div v-if="cards[id].type === 0" class="card" @dblclick.stop :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                        <div class="card-drag-po" :name="cards[id].type">
                            <label v-if="!cards[id].btnCardTitle || cards[id].userId !== user.id" class="card-tit">
                                {{cards[id].title}}
                            </label>

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
                                         }"
                                         @click="myItemModalOn(items[_id].id < 0 ? 'ADD' : 'VIEW', cards[id], items[_id].id)">

                                        <template v-if="items[_id].with_class_apply">
                                            <span v-if="items[_id].with_class_apply.state === 'wait' && !(items[_id].id < -10)" class="badge wait">대기</span>
                                            <span v-else-if="items[_id].with_class_apply.state === 'approval'" class="badge approval">승인</span>
                                            <span v-else-if="items[_id].with_class_apply.state === 'complete'" class="badge complete">완료</span>
                                        </template>
                                        <span class="item-tit">{{items[_id].title}}</span>

                                        <div v-if="items[_id].image" class="img-wrap">
                                            <img v-if="isConsult" :src="items[_id].image.imgUrl+'/consultant/'+myPageId"/>
                                            <img v-else :src="items[_id].image.imgUrl"/>
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

                            <button v-if="!isConsult" @click.stop="myItemModalOn('ADD', cards[id], -1)" class="card-addbtn">
                                추가
                            </button>
                        </div>
                    </div>
                    <div v-else-if="cards[id].type === 1 || cards[id].type === 2" class="card" @dblclick.stop :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                        <div class="card-drag-po" :name="cards[id].type">
                            <label v-if="!cards[id].btnCardTitle || cards[id].userId !== user.id" class="card-tit">
                                {{cards[id].title}}
                            </label>

                            <button v-if="!isConsult" class="card-request-btn" @click="calendarModalOn(cards[id].type)">신청</button>

                            <div>
                                <template v-for="(_id, _index) in cards[id].itemsNum" v-if="items[_id]">
                                    <div v-if="items[_id].type === 1001 || items[_id].type === 1002" class="card-con" :id="'item_'+items[_id].id"
                                         v-bind:class="{
                                             'stack-bg1':items[_id].label === '1',
                                             'stack-bg2':items[_id].label === '2',
                                             'stack-bg3':items[_id].label === '3',
                                             'stack-bg4':items[_id].label === '4',
                                             'stack-bg5':items[_id].label === '5',
                                             'stack-bg6':items[_id].label === '6',
                                             'stack-bg7':items[_id].label === '7',
                                             'stack-bg8':items[_id].label === '8',
                                         }"
                                         @click="myItemBCModalOn(items[_id])">

                                        <template v-if="items[_id].with_basic_apply">
                                            <span v-if="items[_id].with_basic_apply.state === 0" class="badge wait">대기</span>
                                            <span v-else-if="items[_id].with_basic_apply.state === 1 && (
                                                    // new Date(items[_id].with_basic_apply.basic.startDateTime) <= new Date() &&
                                                    new Date() <= new Date(items[_id].with_basic_apply.basic.endDateTime))"
                                                  class="badge approval">승인</span>
                                            <span v-else class="badge complete">종료</span>
<!--                                            <span v-else-if="items[_id].with_basic_apply.state === 1 && !(-->
<!--                                                    new Date(items[_id].with_basic_apply.basic.startDateTime) <= new Date() &&-->
<!--                                                    new Date() <= new Date(items[_id].with_basic_apply.basic.endDateTime))"-->
<!--                                                  class="badge complete">종료</span>-->
                                        </template>
                                        <template v-else-if="items[_id].with_consulting_apply">
                                            <span v-if="items[_id].with_consulting_apply.state === 0" class="badge wait">대기</span>
                                            <span v-else-if="items[_id].with_consulting_apply.state === 1 && (
                                                    // new Date(items[_id].with_consulting_apply.consulting.startDateTime) <= new Date() &&
                                                    new Date() <= new Date(items[_id].with_consulting_apply.consulting.endDateTime))"
                                                  class="badge approval">승인</span>
                                            <span v-else class="badge complete">종료</span>
<!--                                            <span v-else-if="items[_id].with_consulting_apply.state === 1 && !(-->
<!--                                                    new Date(items[_id].with_consulting_apply.consulting.startDateTime) <= new Date() &&-->
<!--                                                    new Date() <= new Date(items[_id].with_consulting_apply.consulting.endDateTime))"-->
<!--                                                  class="badge complete">종료</span>-->
                                        </template>

                                        <span class="item-tit">{{items[_id].title}}</span>

                                        <div v-if="items[_id].image" class="img-wrap">
                                            <img v-if="isConsult" :src="items[_id].image.imgUrl+'/consultant/'+myPageId"/>
                                            <img v-else :src="items[_id].image.imgUrl"/>
                                        </div>

                                        <span v-if="items[_id].deadLine" class="dead-line">
                                            {{items[_id].deadLine.substr(2,2)}}.{{items[_id].deadLine.substr(5,2)}}.{{items[_id].deadLine.substr(8,2)}}
                                        </span>

                                        <span v-if="items[_id].comments.length" class="comment">
                                            {{items[_id].comments.length}}
                                        </span>
                                    </div>
                                    <div v-else class="card-con" :id="'item_'+items[_id].id"
                                         v-bind:class="{
                                             'stack-bg1':items[_id].label === '1',
                                             'stack-bg2':items[_id].label === '2',
                                             'stack-bg3':items[_id].label === '3',
                                             'stack-bg4':items[_id].label === '4',
                                             'stack-bg5':items[_id].label === '5',
                                             'stack-bg6':items[_id].label === '6',
                                             'stack-bg7':items[_id].label === '7',
                                             'stack-bg8':items[_id].label === '8',
                                         }"
                                         @click="myItemModalOn('VIEW', cards[id], items[_id].id)">

                                        <span class="item-tit">{{items[_id].title}}</span>

                                        <div v-if="items[_id].image" class="img-wrap">
                                            <img v-if="isConsult" :src="items[_id].image.imgUrl+'/consultant/'+myPageId"/>
                                            <img v-else :src="items[_id].image.imgUrl"/>
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

                            <button v-if="cards[id].type === 1 && !isConsult" @click.stop="myItemModalOn('ADD', cards[id], -1)" class="card-addbtn">
                                추가
                            </button>
                            <button v-else-if="cards[id].type === 2" @click.stop="myItemModalOn('ADD', cards[id], -1)" class="card-addbtn">
                                추가
                            </button>
                        </div>
                    </div>
                    <div v-else-if="cards[id].type === 13" class="card" @dblclick.stop :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                        <div class="card-drag-po" :name="cards[id].type">
                            <label v-if="!cards[id].btnCardTitle || cards[id].userId !== user.id" class="card-tit">
                                {{cards[id].title}}
                            </label>

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
                                         }"
                                         @click="myItemModalOn('VIEW', cards[id], items[_id].id)">

                                        <span class="item-tit">{{items[_id].title}}</span>

                                        <div v-if="items[_id].image" class="img-wrap">
                                            <img v-if="isConsult" :src="items[_id].image.imgUrl+'/consultant/'+myPageId"/>
                                            <img v-else :src="items[_id].image.imgUrl"/>
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

                            <button @click.stop="myItemModalOn('ADD', cards[id], -1)" class="card-addbtn">
                                추가
                            </button>
                        </div>
                    </div>
                    <div v-else class="card" @dblclick.stop :id="'card_'+cards[id].id+'_'+index" :name="cards[id].type">
                        <div class="card-drag-po handle" :name="cards[id].type">
                            <!--                            <button v-if="cards[id].userId === user.id" class="card-deletebtn" @click="addDelCard(cards[id], index)"></button>-->
                            <button v-if="!isConsult" class="card-deletebtn" @click="delCardModalOn(cards[id], index)"></button>

                            <label v-if="!cards[id].btnCardTitle || (cards[id].userId !== user.id && isConsult)" @click="btnCardTitleOn(cards[id])" class="card-tit">
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
                                         }"
                                         @click="myItemModalOn('VIEW', cards[id], items[_id].id)">

                                        <span class="item-tit">{{items[_id].title}}</span>

                                        <div v-if="items[_id].image" class="img-wrap">
                                            <img v-if="isConsult" :src="items[_id].image.imgUrl+'/consultant/'+myPageId"/>
                                            <img v-else :src="items[_id].image.imgUrl"/>
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

            <div class="card-add" ref="inputDom" :style="{position: 'absolute'}" v-bind:class="{'d-none': !dblClickAddCard}">
                <div class="card-add__input active">
                    <input
                            id="xxxx"
                            type="text"
                            ref="inputRef"
                            v-click-outside="dblClickAddCardOff"
                            v-on:keydown.esc="btnAddCardOn"
                            v-on:keydown.enter="btnAddCardOn"
                            v-model="cardData"
                            style="display: block;"
                            placeholder="flow명을 입력하세요."
                    />
                    <button class="card-addbtn" @click="btnAddCardOn">
                        추가
                    </button>
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

        <!--        <button @click="calendarModalOn">신청</button>-->
        <my-item-modal
                v-if="myItemModalUp"
                :flag="tempFlag"
                :cards="cards"
                :cardId="tempCard.id"
                :items="items"
                :itemId="itemId"
                :myUser="user"
                :myTeamId="false"
                :myClassObjectId="false"
                :isMyPage="true"
                :isTeamPage="false"
                :applyId="isConsult ? myPageId : null"
                :s_year="year"
                :s_semester="semester"
                :daehaks="daehaks"
                @socketEvent="socketEvent"
                @modalOff="myItemModalOff"
                ref="itemModal"
        >
        </my-item-modal>

        <my-page-calendar-modal
                v-if="calendarModalUp"
                :calendarType="calendarType"
                :myClasses="myClasses"
                :myPage="myPage"
                @remoteItem="remoteItem"
                @modalOff="calendarModalOff"
        ></my-page-calendar-modal>

        <my-bc
                v-if="myItemBCModal"
                :item="bcData"
                :myClasses="myClasses"
                @modalOff="myItemBCModalOff"
                ref="myBC"
        >
        </my-bc>
    </div>
</template>

<script type="module">
export default {
    // props: {
    //     user : Object,
    //     myPageId : Number,
    //     myClasses : Object,
    // },

    props: [
        'user',
        'myPageId',
        'classes',
        'daehaks'
        // 'myClasses'
    ],

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
            myVersion: 0,
            componentKey: 0,
            myIndex: 0,
            myItemIndex: 0,
            cards: {},
            tempCards: {},
            items: {},
            cardsNum: [],
            tempCard: [],
            myClasses: [],
            myPage: [],
            itemId: -1,
            isConsult: false,

            itemIndex: -1,
            tempFlag: '',
            isCardItem: '',
            cardData: '',
            cardItem: null,
            dblClickAddCard: false,
            btnCardItem: false,
            btnAddCard: false,
            myItemModalUp: false,
            calendarModalUp: false,
            btnTest: false,
            year: '',
            semester: '',
            calendarType: null,
            isMobileCheck: false,
            delCardModal: false,
            delCardId: '',
            delCardIdx: '',
            scrollExist: false,
            myItemBCModal: false,
            bcData: '',
        }
    },
    created() {     //렌더링이 되기전
        this.init();
        window.addEventListener("resize", this.resizeWindow);
    },
    mounted() {     //렌더링이 되고 나서
        this.$nextTick(() => {
            // 모든 화면이 렌더링된 후 실행
            if(this.isMobile()) {
                this.isMobileCheck = true;
            }
        });
    },
    beforeUpdate() {    //data 값이 바뀌기는 전 순간에 호출this.date

    },
    updated() {          //data 값이 바뀌고나서 호출
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
        forceRerender() {
            this.componentKey += 1;
        },
        initContents(){
            this.cards = {};
            this.items = {};
            this.cardsNum = [];
        },
        init() {
            //2021
            //1,2,3,4
            axios.get(window.location.origin + '/myPage/proList/0/0').then(re => { //연도 학기 세팅
                if(re.data){
                    console.log('-----------------------');
                    console.log(re.data);

                    this.year = re.data.year;
                    this.semester = re.data.semester;
                    this.myClasses = re.data.myClasses;
                    this.myPage = re.data.myPage;

                    this.initCard(null,null);
                    // this.initTempCard();
                }
            })

            //컨설턴트 인지 확인
            if(this.user.authority === 4){
                this.isConsult = true;
            }
        },
        initCard(isId, isType){
            window.Rest.fetch(
                    {
                        // id: this.myPageId,
                        id: this.myPage.id,
                        types: 'card',
                        subTypes: 'myPage',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;

                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                this.initContents();
                                const _card = _result.cards;
                                const _cardNum = _result.cardsNum;
                                this.cardsNum = _result.cardsNum;
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

                                                if(_card[i].with_items[j].type === 1001){
                                                    const end = _card[i].with_items[j].with_basic_apply.basic.endDateTime;
                                                    const start = _card[i].with_items[j].with_basic_apply.basic.startDateTime;
                                                    if(new Date(start) <= new Date() && new Date() <= new Date(end)){
                                                        console.log('포함');
                                                    }

                                                    // console.log(_card[i].with_items[j].with_basic_apply.basic.endDateTime);
                                                    // _card[i].with_items[j].with_basic_apply.basic.endDateTime
                                                }

                                                Vue.set(this.items, _card[i].with_items[j].id, _card[i].with_items[j]);
                                            }
                                        }
                                        else{
                                            _card[i].itemsNum = [];
                                        }
                                        Vue.set(this.cards, _card[i].id, _card[i])
                                    }

                                    // console.log(this.items);
                                }

                                this.initTempCard();

                                this.reRender();
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )

        },
        initTempCard(){
          const tempCardData = JSON.parse(localStorage.getItem('tempCard'));

          if (typeof tempCardData === 'object') {
            for(const key in tempCardData){
              if (this.cardsNum.includes(tempCardData[key].cardId)) {
                let itemsNum = this.cards[tempCardData[key].cardId].itemsNum;
                itemsNum = typeof itemsNum === 'object'  || typeof itemsNum === 'array' ? itemsNum : [];
                const _itemsNum = [tempCardData[key].id];
                this.cards[tempCardData[key].cardId].itemsNum = _itemsNum.concat(itemsNum);

                Vue.set(this.items, tempCardData[key].id, tempCardData[key]);
              }
            }
          }

          // this.initContents();
          // const _card = _result.cards;
          // const _cardNum = _result.cardsNum;
          // this.cardsNum = _result.cardsNum;
          // this.talkState = false;
          //
          // if(_cardNum){
          //   for(let i=0;i<_card.length;i++){
          //     _card[i].item = {};
          //     _card[i].itemsNum = [];
          //     Vue.set(this.cards, _card[i].id, _card[i])
          //   }
          // }
          //
          // this.reRender();
        },
        dragDefault(e){
            return (this.cards[e.relatedContext.element].type === 4);
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
        },
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
            document.getElementsByClassName('card-addbtn').forEach(function(e){
              e.disabled = true;
            });
            document.getElementsByClassName('card-con').forEach(function(e){
              e.classList.add('disabled');
            });
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
            document.getElementsByClassName('card-addbtn').forEach(function(e){
              e.disabled = false;
            });
            document.getElementsByClassName('card-con').forEach(function(e){
              e.classList.remove('disabled');
            });
            if (this.tempCard.btnCardTitle) {
                window.Rest.update(
                        {
                            id: this.tempCard.id,
                            subId: this.myPage.id,
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
                                        this.tempCard.btnCardTitle = false;
                                        this.tempCard = '';
                                    }
                                }
                            }
                            else{
                                console.log('FETCH FAIL');
                            }
                        }
                )
            }
        },
        addNewCard(title) {
            window.Rest.create(
                    {
                        id: this.myPage.id,
                        types: 'card',
                        subTypes: 'myPage',
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
            if(card.btnCardTitle){
                return false;
            }

            window.Rest.delete(
                    {
                        id: card.id,
                        types: 'card',
                        subTypes: 'myPage',
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
        moveCard(e){
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
                        subTypes: 'myPage',
                        subId: this.myPage.id,
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
                                    // this.socketEvent();
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
                        _subId: this.myPage.id,
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
        calendarModalOn(type){
            this.calendarType = type
            this.calendarModalUp = true;
        },
        calendarModalOff(){
            this.calendarType = null;
            this.calendarModalUp = false;
        },
        socketEvent(){
        },
        goClass(id){
            if(!this.isConsult){
                window.location.href = 'lectureList/lectureDetailInfo/'+id
            }
        },
        isMobile(){
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },
        delCardModalOn(card,idx){
            this.delCardModal = true;
            this.delCardId = card;
            this.delCardIdx = idx;
        },
        remoteItem(_data){
            console.log('remoteItem');
            let __result = _.cloneDeep(_data);

            if(_data.images){

                let imagesNum = _.cloneDeep(_data.images);
                let images = _.cloneDeep(_data.with_images);
                __result.images = [];

                for(let i=0;i<imagesNum.length;i++){
                    for(let j=0;j<images.length;j++){
                        if(images[j].id === imagesNum[i]){
                            __result.images.push(images[j]);

                            if(i===0)
                                __result.image = images[j];

                            break;
                        }
                    }
                }
            }

            __result.comments = [];
            Vue.set(this.items, __result.id, __result);
            this.cards[_data.cardId].itemsNum.unshift(__result.id);
        },
        myItemBCModalOn(data){
            this.bcData = data;
            this.myItemBCModal = true;
        },
        myItemBCModalOff(){
            this.myItemBCModal = false;
        },
        selectYear(){
            axios.get(window.location.origin + '/myPage/proList/'+this.year+'/'+this.semester).then(re => {
                if(re.data){
                    // console.log('-----------------------');
                    // console.log(re.data);

                    // this.year = re.data.year;
                    // this.semester = re.data.semester;
                    this.myClasses = re.data.myClasses;
                    this.myPage = re.data.myPage;

                    this.initCard(null,null);
                }
            })
        },
        scrollExistCheck(){
            document.getElementsByClassName('card-wrap')[0].scrollWidth > document.getElementsByClassName('card-wrap')[0].clientWidth ? this.scrollExist = true : this.scrollExist = false;
        }
    },

}
</script>
