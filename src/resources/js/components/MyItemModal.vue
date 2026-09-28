<template>
    <div id="cardModal" spellcheck="false" class="card-modal" @mousedown="$emit('modalOff')" :class="{'view-mode':viewMode || cards[cardId].type === 0}" :key="componentKey">
        <div class="card-modal__dim" @mousedown.stop style="width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.7); position: fixed; left: 0px; top: 0px;"></div>
        <div class="card-modal__wrap" @mousedown.stop>
            <div class="card-modal__con-wrap" id="modalConWrap">
                <div class="card-modal__tab">
                    <template v-if="cards[cardId].type === 0">
                        <button class="menu active">수업개설신청</button>
                    </template>
                    <template v-else>
                        <button class="menu" @click="btnMenu('default')"
                                v-bind:class="{'active':menuState.default}">기본</button>

                        <button v-if="Number(isCardItem.type) === 1 || isMenuType === 1" class="menu" @click="btnMenu('problemAnalysis')"
                                v-bind:class="{'active':menuState.problemAnalysis}">문제분석</button>

                        <button v-else-if="Number(isCardItem.type) === 2 || isMenuType === 2" class="menu" @click="btnMenu('reflectionLog')"
                                v-bind:class="{'active':menuState.reflectionLog}">성찰</button>

                        <button v-else-if="Number(isCardItem.type) === 3 || isMenuType === 3" class="menu" @click="btnMenu('brain')"
                                v-bind:class="{'active':menuState.brain}">브레인스토밍</button>

                        <button v-else-if="Number(isCardItem.type) === 5 || isMenuType === 5"
                                class="menu"
                                @click="btnMenu('teamActivity')"
                                v-bind:class="{'active':menuState.teamActivity}">
                          팀활동보고서
                        </button>

                        <button v-else-if="Number(isCardItem.type) === 7 || isMenuType === 7"
                                class="menu"
                                @click="btnMenu('evaluation')"
                                v-bind:class="{'active':menuState.evaluation}">
                          평가지
                        </button>

                        <button v-else-if="Number(isCardItem.type) === 8 || isMenuType === 8"
                                class="menu"
                                @click="btnMenu('operationResult')"
                                v-bind:class="{'active':menuState.operationResult}">
                          운영결과
                        </button>
                    </template>
                    <!--                    <button v-else-if="Number(isCardItem.type) === 5 || isMenuType === 5"-->
                    <!--                            class="menu"-->
                    <!--                            @click="btnMenu('detaileOperApply')"-->
                    <!--                            v-bind:class="{'active':menuState.detaileOperApply}">-->
                    <!--                        세부운영계획-->
                    <!--                    </button>-->
                </div>
                <ul v-if="menuState.brain" class="card-modal__con">
                    <li class="brain-board">
                        <draggable v-model="isCardItem.brain" @start="dragBrainInit" @end="moveBrain" v-bind:disabled="isMobileCheck">
                            <template v-for="(id, index) in isCardItem.brain" v-if="isCardItem.with_brains[id]">
                                <div class="brain-memo" :id="'brain_'+id" v-bind:class="'color'+isCardItem.with_brains[id].color" @click="editBrain(isCardItem.with_brains[id], id)" @click.stop>
                                    <button v-if="isCardItem.with_brains[id].userId === myUser.id" class="delete" @click="brainDeleteModalOn(index, isCardItem.with_brains[id])" @click.stop></button>
                                    <span class="name">{{ isCardItem.with_brains[id].name }}</span>
                                    <span class="date">{{ isCardItem.with_brains[id].date }}</span>
                                    <p class="con scroll-sm">
                                        {{ isCardItem.with_brains[id].content}}
                                    </p>
                                </div>
                            </template>
                        </draggable>
                    </li>
                    <li class="card-modal__con__item" v-click-outside="dragBrainInit">
                        <label class="label">작성</label>
                        <div class="label-list">
                            <label class="label-color__check color1_2">
                                <input type="radio" name="label_color2" value="1" v-model="brainColor">
                            </label>
                            <label class="label-color__check color2_2">
                                <input type="radio" name="label_color2" value="2" v-model="brainColor">
                            </label>
                            <label class="label-color__check color3_2">
                                <input type="radio" name="label_color2" value="3" v-model="brainColor">
                            </label>
                            <label class="label-color__check color4_2">
                                <input type="radio" name="label_color2" value="4" v-model="brainColor">
                            </label>
                            <label class="label-color__check color5_2">
                                <input type="radio" name="label_color2" value="5" v-model="brainColor">
                            </label>
                            <label class="label-color__check color6_2">
                                <input type="radio" name="label_color2" value="6" v-model="brainColor">
                            </label>
                            <label class="label-color__check color7_2">
                                <input type="radio" name="label_color2" value="7" v-model="brainColor">
                            </label>
                            <label class="label-color__check color8_2">
                                <input type="radio" name="label_color2" value="8" v-model="brainColor">
                            </label>
                        </div>
                        <div class="comment-list">
                            <textarea v-model="brainContent"
                                      placeholder="내용을 입력하세요" id="commentText_" @keyup="textAreaResize" rows="2"></textarea>
                            <button class="comment__btn" @click="addBrain"></button>
                        </div>
                    </li>
                </ul>
                <ul v-else-if="menuState.classApply && isMyPage" class="card-modal__con">
                    <li class="card-modal__con__item bg-white">
                        <h3 class="con-tit">
                            IC-PBL 교과목 개발 및 운영 계획서
                            <div class="tit-right">
                                <vue-picker class="select" v-model="classListSelet">
                                    <div @click="clearApply">
                                        <vue-picker-option value="-1">선택</vue-picker-option>
                                    </div>
                                    <template v-for="(classList_, idx) in classList">
                                        <div @click="fetchApply(classList_)">
                                            <vue-picker-option :value="classList_.id+''">
                                                {{classList_.korName}}
                                            </vue-picker-option>
                                        </div>
                                    </template>
                                </vue-picker>
                                <button class="btn-data">참고자료(과거)</button>
                            </div>
                        </h3>
                        <table class="table-input__wrap table th-center mb-30">
                            <colgroup>
                                <col width="20%" />
                                <col width="20%" />
                                <col width="20%" />
                                <col width="40%" />
                            </colgroup>
                            <tr>
                                <th>교과유형</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input ref="type" type="radio" name="apply_type" value="1" v-model="isCardItem.with_class_apply.type" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        전공기초
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_type" value="2" v-model="isCardItem.with_class_apply.type" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        전공핵심(필수)
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_type" value="3" v-model="isCardItem.with_class_apply.type" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        전공핵심
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_type" value="4" v-model="isCardItem.with_class_apply.type" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        전공심화
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>수강학년</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input ref="grade" type="radio" name="apply_grade" value="1" v-model="isCardItem.with_class_apply.grade" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        1학년
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_grade" value="2" v-model="isCardItem.with_class_apply.grade" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        2학년
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_grade" value="3" v-model="isCardItem.with_class_apply.grade" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        3학년
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_grade" value="4" v-model="isCardItem.with_class_apply.grade" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        4학년
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>수강규모</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input ref="size1" type="radio" name="apply_size1" value="1" v-model="isCardItem.with_class_apply.size1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        10명~20명
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_size1" value="2" v-model="isCardItem.with_class_apply.size1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        21명~30명
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_size1" value="3" v-model="isCardItem.with_class_apply.size1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        30명 초과 총
                                        <input ref="size2" type="number" :min="0"
                                               v-model="isCardItem.with_class_apply.size2"
                                               onKeyup="this.value=this.value.replace(/[^0-9]/g,'');"
                                               :placeholder="0"
                                               :disabled="isCardItem.with_class_apply.size1 !== '3' || isCardItem.with_class_apply.state !== 'wait'"> 명
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>교강사 수</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input ref="proSize1" type="radio" name="apply_proSize1" value="1" v-model="isCardItem.with_class_apply.proSize1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        단독운영
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_proSize1" value="2" v-model="isCardItem.with_class_apply.proSize1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        옴니버스
                                        <input ref="proSize2" type="number" :min="0"
                                               v-model="isCardItem.with_class_apply.proSize2"
                                               onKeyup="this.value=this.value.replace(/[^0-9]/g,'');"
                                               :placeholder="0"
                                               :disabled="isCardItem.with_class_apply.proSize1 !== '2' || isCardItem.with_class_apply.state !== 'wait'"> 명
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_proSize1" value="3" v-model="isCardItem.with_class_apply.proSize1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        팀티칭
                                        <input ref="proSize3" type="number" :min="0"
                                               v-model="isCardItem.with_class_apply.proSize3"
                                               onKeyup="this.value=this.value.replace(/[^0-9]/g,'');"
                                               :placeholder="0"
                                               :disabled="isCardItem.with_class_apply.proSize1 !== '3' || isCardItem.with_class_apply.state !== 'wait'"> 명
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>개설학과(부)<br />(전공)</th>
                                <td colspan="3">
                                    <vue-picker class="select w-50 border" v-model="isCardItem.with_class_apply.daehak" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        <vue-picker-option value="-1" disabled>선택</vue-picker-option>
                                        <template v-for="(daehak, key) in daehaks">
                                          <div @click="departmentListSet(key)">
                                            <vue-picker-option :value="key+''">{{key}}</vue-picker-option>
                                          </div>
                                        </template>
                                    </vue-picker>
                                    <vue-picker class="select w-50 border" v-model="tDepartment" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        <vue-picker-option value="-1" disabled>선택</vue-picker-option>
                                          <template v-for="(department_, index_) in daehaks">
                                            <template v-for="(department__, index__) in department_">
                                              <vue-picker-option v-show="department__.name == isCardItem.with_class_apply.daehak" :value="department__.department+''">{{department__.department}}</vue-picker-option>
                                            </template>
                                          </template>
<!--                                          <template v-for="(department_, index_) in daehaks[isCardItem.with_class_apply.daehak]">-->
<!--                                            <vue-picker-option :value="department_.department+''">{{department_.department}}</vue-picker-option>-->
<!--                                          </template>-->
                                    </vue-picker>
                                    <input ref="department" type="text" class="input-text middle" placeholder="전공 기재"
                                           v-model="isCardItem.with_class_apply.major" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                </td>
                            </tr>
                            <tr>
                                <th>특수수업<br />(해당과목만 체크)</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input ref="special" type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.special" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        SMART-F
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.special2" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        SMART-L
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.special3" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        영어전용
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.special4" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        제2외국어전용
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th rowspan="2">교과목명</th>
                                <td colspan="2">
                                    <span class="fc-navy">(국문)</span>
                                    <input ref="title" type="text" class="input-text w-80" v-model="isCardItem.with_class_apply.korName" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                </td>
                                <th>학점&nbsp;&nbsp;-&nbsp;&nbsp;강의&nbsp;&nbsp;-&nbsp;&nbsp;실습</th>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <span class="fc-navy">(영문)</span>
                                    <input ref="engName" type="text" class="input-text w-80" v-model="isCardItem.with_class_apply.engName" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                </td>
                                <td class="t-center b-left">
                                    <input ref="gradesPoint" type="number" :min="0" maxlength="1"
                                           class="input-text sm"
                                           v-model="isCardItem.with_class_apply.gradesPoint"
                                           :placeholder="0"
                                           @keyup="numberMaxLength($event,'gradesPoint')"
                                           :disabled="isCardItem.with_class_apply.state !== 'wait'">&nbsp;&nbsp;-&nbsp;&nbsp;
                                    <input ref="lecturePoint" type="number" :min="0" maxlength="1"
                                           class="input-text sm"
                                           v-model="isCardItem.with_class_apply.lecturePoint"
                                           :placeholder="0"
                                           @keyup="numberMaxLength($event,'lecturePoint')"
                                           :disabled="isCardItem.with_class_apply.state !== 'wait'">&nbsp;&nbsp;-&nbsp;&nbsp;
                                    <input ref="exercisePoint" type="number" :min="0" maxlength="1"
                                           class="input-text sm"
                                           v-model="isCardItem.with_class_apply.exercisePoint"
                                           :placeholder="0"
                                           @keyup="numberMaxLength($event,'exercisePoint')"
                                           :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                </td>
                            </tr>
                            <tr>
                                <th>교과목 개요</th>
                                <td colspan="3">
                                    <textarea ref="description" @keyup="textAreaResize" v-model="isCardItem.with_class_apply.description" rows="5" placeholder="※ IC-PBL교과목으로서의 특징, 학습 목표, 기대효과 등을 간략하게 기재" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>IC-PBL<br />MECA 유형</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input ref="meca" type="radio" name="apply_meca" value="1" v-model="isCardItem.with_class_apply.meca" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        M(Merge, 현장통합형)
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_meca" value="2" v-model="isCardItem.with_class_apply.meca" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        E(Evaluate, 현장평가형)
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_meca" value="3" v-model="isCardItem.with_class_apply.meca" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        C(Create, 문제해결형)
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="apply_meca" value="4" v-model="isCardItem.with_class_apply.meca" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        A(Anchor, 현장문제형)
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th rowspan="2">산업체 참여정보<br />(현장 연계 시)</th>
                                <td colspan="3">
                                    소속 기관 : (<input ref="agency" type="text" class="input-text md" v-model="isCardItem.with_class_apply.agency" :disabled="isCardItem.with_class_apply.state !== 'wait'">)&nbsp;&nbsp;
                                    현장전문가 직급 및 업무분야 : (<input ref="expert" type="text" class="input-text md" v-model="isCardItem.with_class_apply.expert" :disabled="isCardItem.with_class_apply.state !== 'wait'">)
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    참여 역할<br class="m-block" />
                                    <label class="custom-check">
                                        <input ref="role1" type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.role1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        멘토
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.role3" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        평가/심사
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.role4" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        수업/특강
                                    </label>
                                    <label class="custom-check ml-58">
                                        <input type="checkbox" value="1" true-value="1" false-value="0" v-model="isCardItem.with_class_apply.role5" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        기타
                                        <input ref="role2" type="text"
                                               class="lg"
                                               v-model="isCardItem.with_class_apply.role2"
                                               :placeholder="'입력하세요'"
                                               :disabled="isCardItem.with_class_apply.role5 != '1' || isCardItem.with_class_apply.state !== 'wait'">
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>예상수업결과물<br />(복수 선택 가능)</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input type="checkbox" ref="expected" value="1" v-model="isCardItem.with_class_apply.expected1" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        연구보고서
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_class_apply.expected2" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        제안서
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_class_apply.expected3" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        캠페인
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_class_apply.expected4" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        프로토타입
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_class_apply.expected5" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        소프트웨어
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_class_apply.expected6" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        영상물
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_class_apply.expected7" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                        기타
                                        <input type="text"
                                               class="lg"
                                               ref="expected8"
                                               v-model="isCardItem.with_class_apply.expected8"
                                               :placeholder="'입력하세요'"
                                               :disabled="isCardItem.with_class_apply.expected7 != 1 || isCardItem.with_class_apply.state !== 'wait'">
                                    </label>
                                </td>
                            </tr>
                            <template v-for="(applicant, index) in isCardItem.with_class_apply.applicant">
                                <template v-if="index === 0">
                                    <tr>
                                        <th :rowspan="isCardItem.with_class_apply.applicant.length*4">신청자 정보<br /><button type="button" v-if="isCardItem.with_class_apply.state === 'wait'" class="btn-addbtn mt-10" @click="btnApplicantList('ADD', null)"></button></th>
                                        <th>성명</th>
                                        <td colspan="2">
                                            <input :ref="index + '.name'" type="text"
                                                   class="input-text w-80"
                                                   v-model="applicant.name"
                                                   :placeholder="'교수자가 여러 명일 경우 모든 사람의 정보 기재'"
                                                   :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                            <span class="fc-navy">(서명) <span v-if="applicant.name">{{applicant.name}}</span><span v-else>{{myUser.name}}</span></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>소속</th>
                                        <td colspan="2">
                                            <input :ref="index + '.org'" type="text" class="input-text" v-model="applicant.org" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tel</th>
                                        <td colspan="2">
                                            <span class="fc-navy">연구실 :</span>
                                            <input :ref="index + '.tel'" type="text" class="input-text w-half" @keyup="addHyphen(index,'tel')" v-model="applicant.tel" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                            <span class="fc-navy">핸드폰 :</span>
                                            <input :ref="index + '.phone'" type="text" class="input-text w-half" @keyup="addHyphen(index,'phone')" v-model="applicant.phone" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>e-mail</th>
                                        <td colspan="2">
                                            <input :ref="index + '.email'" type="text" class="input-text" placeholder="email@example.com" v-model="applicant.email" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <th class="p-5 p-relative">성명 <button type="button" class="btn-addbtn delete sm fr" @click="btnApplicantList('DELETE', index)" v-if="isCardItem.with_class_apply.state === 'wait'"></button></th>
                                        <td colspan="2">
                                            <input :ref="index + '.name'" type="text"
                                                   class="input-text w-80"
                                                   v-model="applicant.name"
                                                   :placeholder="'교수자가 여러 명일 경우 모든 사람의 정보 기재'"
                                                   :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                            <span class="fc-navy">(서명) {{applicant.name}}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>소속</th>
                                        <td colspan="2">
                                            <input :ref="index + '.org'" type="text" v-model="applicant.org" class="input-text" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tel</th>
                                        <td colspan="2">
                                            <span class="fc-navy">연구실 :</span>
                                            <input :ref="index + '.tel'" type="text" class="input-text w-half" @keyup="addHyphen(index,'tel')" v-model="applicant.tel" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                            <span class="fc-navy">핸드폰 :</span>
                                            <input :ref="index + '.phone'"type="text" class="input-text w-half" @keyup="addHyphen(index,'phone')" v-model="applicant.phone" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>e-mail</th>
                                        <td colspan="2">
                                            <input :ref="index + '.email'" type="text" v-model="applicant.email" class="input-text"  placeholder="email@example.com" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                        </td>
                                    </tr>
                                </template>
                            </template>
                            <tr>
                                <td colspan="4">
                                    <div class="txt">
                                        <span class="fc-navy">※ 개인정보수집활용 동의</span><br>
                                        본인은 IC-PBL 교과목 개발 및 운영의 공모에 지원함에 있어
                                        제출한 인적사항 및 강의계획서 등의 자료가 IC-PBL 교과목
                                        개발 및 운영 지원을 위해 활용될 필요가 있다는 것을 이해하고 있으며,
                                        이를 위해 본인의 정보를 IC-PBL센터에 제공하는데 동의합니다.
                                    </div>
                                    <div class="agree-wrap">
                                        <label class="custom-check">
                                            <input ref="agree1" type="radio" name="apply_agree1" value="1" v-model="isCardItem.with_class_apply.agree1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                            동의함
                                        </label>
                                        <label class="custom-check">
                                            <input type="radio" name="apply_agree1" value="0" v-model="isCardItem.with_class_apply.agree1" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                            동의하지 않음
                                        </label>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <template v-if="isCardItem.with_class_apply.mode === '1'">
                            <h5 class="sub-tit">※ 교육과정 개발 기여도 평가</h5>
                            <table class="table-input__wrap table th-center mb-30">
                                <colgroup>
                                    <col width="15%" />
                                    <col width="40%" />
                                    <col width="15%" />
                                    <col width="15%" />
                                    <col width="15%" />
                                </colgroup>
                                <tr>
                                    <th>개발기간</th>
                                    <td colspan="4">
                                        <input ref="duration" type="date" v-model="isCardItem.with_class_apply.duration" :readonly="isCardItem.with_class_apply.state !== 'wait'" />
                                        <span>~</span> <input ref="duration2" type="date" :min="isCardItem.with_class_apply.duration" v-model="isCardItem.with_class_apply.duration2" :readonly="isCardItem.with_class_apply.state !== 'wait'" />
                                        <!--                                        <input ref="duration" type="text" class="input-text" v-model="isCardItem.with_class_apply.duration" :disabled="isCardItem.with_class_apply.state !== 'wait'">-->
                                    </td>
                                </tr>
                                <template v-for="(contribute, index) in isCardItem.with_class_apply.contribute">
                                    <template v-if="index === 0">
                                        <tr>
                                            <th :rowspan="isCardItem.with_class_apply.contribute.length*2">기여구분<br /><button type="button" class="btn-addbtn mt-10" @click="btnContributeList('ADD', null)" v-if="isCardItem.with_class_apply.state === 'wait'"></button></th>
                                            <th class="p-5">성명</th>
                                            <th class="p-5" colspan="3">기여도</th>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input :ref="index + '.name'" type="text" class="input-text" v-model="contribute.name" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                            </td>
                                            <td colspan="3">
                                                <div class="percent">
                                                    <input :ref="index + '.per'" type="number" class="input-text" v-model="contribute.per" :disabled="isCardItem.with_class_apply.state !== 'wait'" placeholder="1인일 경우 100%, 4인일 경우 25%">
                                                    <span>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template v-else>
                                        <th class="p-5 p-relative">성명 <button type="button" class="btn-addbtn delete sm fr" @click="btnContributeList('DELETE', index)" v-if="isCardItem.with_class_apply.state === 'wait'"></button></th>
                                        <th class="p-5" colspan="3">기여도</th>
                                        <tr>
                                            <td>
                                                <input :ref="index + '.name'" type="text" class="input-text" v-model="contribute.name" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                            </td>
                                            <td colspan="3">
                                                <div class="percent">
                                                    <input :ref="index + '.per'" type="number" class="input-text" v-model="contribute.per" :disabled="isCardItem.with_class_apply.state !== 'wait'" placeholder="1인일 경우 100%, 4인일 경우 25%">
                                                    <span>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <!--                                <tr>-->
                                <!--                                    <th rowspan="2">기여구분<br /><button type="button" class="btn-addbtn mt-10"></button></th>-->
                                <!--                                    <th class="p-5">성명</th>-->
                                <!--                                    <th class="p-5" colspan="3">기여도</th>-->
                                <!--                                </tr>-->
                                <!--                                <tr>-->
                                <!--                                    <td>-->
                                <!--                                        <input ref="conName" type="text" class="input-text" v-model="isCardItem.with_class_apply.conName" :disabled="isCardItem.with_class_apply.state !== 'wait'">-->
                                <!--                                    </td>-->
                                <!--                                    <td colspan="3">-->
                                <!--                                        <div class="percent">-->
                                <!--                                            <input ref="conPer" type="number" class="input-text" v-model="isCardItem.with_class_apply.conPer" :disabled="isCardItem.with_class_apply.state !== 'wait'" placeholder="1인일 경우 100%, 4인일 경우 25%">-->
                                <!--                                            <span>%</span>-->
                                <!--                                        </div>-->
                                <!--                                    </td>-->
                                <!--                                </tr>-->
                                <tr>
                                    <td colspan="5">
                                        <span class="fc-navy">내용</span>
                                        <textarea ref="conDescription" @keyup="textAreaResize" rows="7" v-model="isCardItem.with_class_apply.conDescription" :disabled="isCardItem.with_class_apply.state !== 'wait'" placeholder="예시 :
1. 해당 교과목에 IC-PBL을 적용하여 달성하고자 하는 목표 수립
2. 주차별 교육 내용 및 방법 계획 수립
3. 주차별 세부 교육 내용 및 방법 마련 및 검토
4. 수업 진행 후 기대하는 최종 결과물 구상
">
                                      </textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <div class="txt">
                                            <span class="fc-navy">※ IC-PBL 교과목 운영 지원 서약</span><br>
                                            본 공모에 선정된 {{s_year}}학년도
                                            <span v-if="s_semester === '1'">1</span><span v-if="s_semester === '2'">여름계절</span><span v-if="s_semester === '3'">2</span><span v-if="s_semester === '4'">겨울계절</span>학기 IC-PBL 교과목은 대학혁신지원사업 기간 종료 후에도 지속적으로 운영할 것을 서약합니다.
                                        </div>
                                        <div class="agree-wrap">
                                            <label class="custom-check">
                                                <input ref="agree2" type="radio" name="apply_agree2" value="1" v-model="isCardItem.with_class_apply.agree2" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                                동의함
                                            </label>
                                            <label class="custom-check">
                                                <input type="radio" name="apply_agree2" value="0" v-model="isCardItem.with_class_apply.agree2" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                                동의하지 않음
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5">
                                        <div class="txt">
                                            <span class="fc-navy">※ 대학혁신지원사업 교과목 개발</span><br>
                                            대학혁신지원사업비로 개발된 본 과목은 {{s_year}}년
                                            <span v-if="s_semester === '1'">1</span><span v-if="s_semester === '2'">여름계절</span><span v-if="s_semester === '3'">2</span><span v-if="s_semester === '4'">겨울계절</span>학기에 개설하여 운영될 것이며 교과목 운영 종료 후
                                            개발비를 지급받을 것입니다. 또한 본 과목 개발과 관련하여 대학혁신지원사업이 아닌 타 국고 사업의
                                            이중수혜 적발 시 개발비 지급중단 및 환수조치가 될 수 있음을 확인합니다.
                                        </div>
                                        <div class="agree-wrap">
                                            <label class="custom-check">
                                                <input ref="agree3" type="radio" name="apply_agree3" value="1" v-model="isCardItem.with_class_apply.agree3" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                                확인함
                                            </label>
                                            <label class="custom-check">
                                                <input type="radio" name="apply_agree3" value="0" v-model="isCardItem.with_class_apply.agree3" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                                확인하지 않음
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </template>

                        <h3 class="con-tit">IC-PBL 교과목 세부 운영 계획서</h3>
                        <h5 class="sub-tit">1. 수업 계획 세부 내용</h5>
                        <div class="pl-10 mb-20 lh-1">
                            1) 기본정보<br />
                            <br />
                            ※ 작성 지침<br />
                            적정수업 크기, 튜터 활용 여부, 학습자 특성, 교실환경, 교재, 현장연계 계획 등 IC-PBL수업의 기본 정보들을 기술하시면 됩니다.<br />
                            <span class="fc-gray">(아래 회색 글씨는 예시입니다.)</span>
                        </div>
                        <table class="table-input__wrap t-center table mb-30">
                            <colgroup>
                                <col width="5%" />
                                <col width="25%" />
                                <col width="70%" />
                            </colgroup>
                            <tr>
                                <th>1</th>
                                <th>적정 수업 크기</th>
                                <td>
                                    <input ref="basic1" type="text" v-model="isCardItem.with_class_apply.basic1" placeholder="20~30명, 3~4명 팀 구성" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                </td>
                            </tr>
                            <tr>
                                <th>2</th>
                                <th>튜터 활용 여부와 활용 계획</th>
                                <td>
                                    <input ref="basic2" type="text" v-model="isCardItem.with_class_apply.basic2" placeholder="활용하지 않음" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                </td>
                            </tr>
                            <tr>
                                <th>3</th>
                                <th>학습자 특성</th>
                                <td>
                                    <textarea ref="basic3" @keyup="textAreaResize" v-model="isCardItem.with_class_apply.basic3" placeholder="다문화사회전문가 자격취득에 필요한 필수 교과목을 이수한 이민·다문화 전공 3~4학년 학생" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>4</th>
                                <th>교실환경(H/W, S/W)</th>
                                <td>
                                    <input ref="basic4" type="text" v-model="isCardItem.with_class_apply.basic4" placeholder="인터넷 사용 및 동영상 시청이 가능한 강의실" :disabled="isCardItem.with_class_apply.state !== 'wait'">
                                </td>
                            </tr>
                            <tr>
                                <th>5</th>
                                <th>교재 및 수업자료 활용 계획</th>
                                <td>
                                    <textarea ref="basic5" @keyup="textAreaResize" v-model="isCardItem.with_class_apply.basic5" placeholder="· 지정 교과서 없음
· 교수 강의노트 및 현장 전문가 특강 자료 활용" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>6</th>
                                <th>현장 연계 계획</th>
                                <td>
                                  <textarea ref="basic6" @keyup="textAreaResize" rows="4" v-model="isCardItem.with_class_apply.basic6" placeholder="· 유네스코 아시아태평양 국제이해교육원 방문
· 국내 다문화교육 기관(다솜학교, 서울소재 주한 외국인학교, 국내소재 국제학교, 다문화가정 자녀 다수 재학 일반학교, 무지개청소년 셍터 등) 방문
· 세계시민교육 워크숍 참여" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                        </table>

                        <div class="pl-10 mb-20 lh-1">
                            2) 평가 계획<br />
                            <br />
                            ※ 작성 지침<br />
                            평가 세부 항목, 팀 평가와 개인 평가 비율, 평가 시행 시기 및 방법, 평가 기준, 평가 주체 등을 자유롭게 기술하시면 됩니다.<br>
                            평가 양식이나 루브릭 평가 기준표 등을 함께 계획하실 것을 권장합니다.
                        </div>

                        <table class="table-input__wrap t-center table mb-30">
                            <tr>
                                <td>
                                    <textarea ref="basicPlan" @keyup="textAreaResize" rows="3" v-model="isCardItem.with_class_apply.basicPlan" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                        </table>

                        <h5 class="sub-tit">2. IC-PBL 문제 (시나리오)</h5>
                        <div class="pl-10 mb-20 lh-1">
                            (※ IC-PBL 모듈 개수만큼 작성, 예를 들어 IC-PBL 모듈이 2개일 경우 2개의 IC-PBL문제 개발)
                        </div>

                        <table class="table-input__wrap t-center table mb-30">
                            <colgroup>
                                <col width="20%" />
                                <col width="20%" />
                                <col width="60%" />
                            </colgroup>
                            <tr>
                                <th>구분</th>
                                <th colspan="2">내용</th>
                            </tr>
                            <tr>
                                <th>학습 내용</th>
                                <td colspan="2">
                                  <textarea ref="sceContent" v-model="isCardItem.with_class_apply.sceContent" rows="2" @keyup="textAreaResize" placeholder="※ 본 IC-PBL 모듈의 학습내용
(교과목 전체 내용이 아님)" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>핵심 학습 목표</th>
                                <td colspan="2">
                                  <textarea ref="sceGoal" v-model="isCardItem.with_class_apply.sceGoal" rows="2" @keyup="textAreaResize" placeholder="※ 본 IC-PBL 모듈을 통해 성취하고자 하는 학습목표
(교과목 전체 목표가 아님)" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th rowspan="3">문제 상황 시나리오</th>
                                <th>시나리오 제목 :</th>
                                <td><textarea ref="sceTitle" v-model="isCardItem.with_class_apply.sceTitle" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea></td>
                            </tr>
                            <tr>
                                <th>문제 상황 속<br />학습자(주인공) 역할 :</th>
                                <td><textarea ref="sceRole" v-model="isCardItem.with_class_apply.sceRole" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                  <textarea ref="sceDetail" v-model="isCardItem.with_class_apply.sceDetail" rows="2" placeholder="※ 해당 학습내용 및 학습목표를 반영한
실제적이고 시의성 있는 문제 상황을 기술" @keyup="textAreaResize" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea>
                                </td>
                            </tr>
                        </table>

                        <h5 class="sub-tit">3. 세부 수업 진행 계획</h5>

                        <table class="table-input__wrap t-center table mb-30">
                            <colgroup>
                                <col width="6%" />
                                <col width="19%" />
                                <col width="14%" />
                                <col width="19%" />
                                <col width="19%" />
                                <col width="23%" />
                            </colgroup>
                            <tr>
                                <th>주차</th>
                                <th>수업 내용</th>
                                <th>IC-PBL 단계</th>
                                <th>학습자 활동 내용</th>
                                <th>학습과제</th>
                                <th>수업방식</th>
                            </tr>
                            <template v-for="(pD, idx) in isCardItem.with_class_apply.planDetail">
                                <tr>
                                    <th>{{idx+1}}</th>
                                    <td><textarea rows="4" v-model="pD.content" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea></td>
                                    <td><textarea rows="4" v-model="pD.level" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea></td>
                                    <td><textarea rows="4" v-model="pD.stuContent" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea></td>
                                    <td><textarea rows="4" v-model="pD.subject" :disabled="isCardItem.with_class_apply.state !== 'wait'"></textarea></td>
                                    <td>
                                        <label class="custom-check">
                                            <!--                                            <input :name="'apply_method_'+idx" type="radio" value="1" v-model="pD.method1" />-->
                                            <input type="checkbox" value="1" v-model="pD.method1" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                            비대면 실시간강의
                                        </label>
                                        <label class="custom-check">
                                            <input type="checkbox" value="1" v-model="pD.method2" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                            비대면 녹화 강의
                                        </label>
                                        <label class="custom-check">
                                            <input type="checkbox" value="1" v-model="pD.method3" true-value="1" false-value="0" :disabled="isCardItem.with_class_apply.state !== 'wait'" />
                                            대면강의
                                        </label>
                                    </td>
                                </tr>
                            </template>
                        </table>
                    </li>
                </ul>
                <ul v-else-if="menuState.detaileOperApply && isMyPage" class="card-modal__con">
                    <li class="card-modal__con__item bg-white">

                    </li>
                </ul>
                <ul v-else-if="menuState.problemAnalysis" class="card-modal__con">
                    <li class="card-modal__con__item bg-white">
                        <h3 class="con-tit">문제분석지</h3>
                        <table class="table-input__wrap text">
                            <tr>
                                <th>학번</th>
                                <td>
                                    <input ref="studentId" type="text" v-model="isCardItem.with_problem_analysis.studentId" :readonly="isCardItem.userId !== myUser.id" />
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap text">
                            <tr>
                                <th>이름</th>
                                <td>
                                    <input v-if="isCardItem.with_problem_analysis.name" type="text" :value="isCardItem.with_problem_analysis.name" readonly/>
                                    <input v-else type="text" :value="myUser.name" readonly/>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    ① 사실(fact) = As-Is<br>
                                    문제에 제시된 사실과 학습자가 알고 있는 문제해결과 관련된 사실 확인
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content1" v-model="isCardItem.with_problem_analysis.content1" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    ② 생각(idea/hypothesis) = To-Be<br>
                                    문제의 원인, 결과, 가능한 해결안에 관한 학습자의 가설이나 추측 검토
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content2" v-model="isCardItem.with_problem_analysis.content2" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    ③ 학습과제(learning issues)<br>
                                    문제를 해결하기 위해 학습자가 학습 해야할 필요가 있는 학습내용을 선정
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content3" v-model="isCardItem.with_problem_analysis.content3" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    ④ 실천계획(action plans)<br>
                                    문제를 해결하기 위해 학습자가 이후에 해야할 일 또는 실천 계획
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content4" v-model="isCardItem.with_problem_analysis.content4" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="guide">
                            [①→②→③→④] 순서대로 진행 ⇒ ①·②를 근거로 ③의 학습과제를 도출하고 수행하다보면 또 다른 가설(②)이 세워질 수도 있고 가설이 사실(①)로 확인되기도 한다. ⇒ 그렇게 되면 다시 ①·②·③이 수정되거나 보완되면서 반복적으로 수정 보완을 통해 훨씬 더 정교화된 문제 분석결과가 나온다. ⇒ 보통 두 번 정도 이러한 사이클이 이루어진 후 ①·②·③이 확정되면 이를 바탕으로 실천계획(④)을 세운다.
                        </div>

                    </li>
                </ul>
                <ul v-else-if="menuState.teamActivity" class="card-modal__con">
                    <li class="card-modal__con__item bg-white">
                        <h3 class="con-tit">팀활동보고서</h3>
                        <table class="table-input__wrap t-center table">
                            <colgroup>
                                <col width="13%" />
                                <col width="13%" />
                                <col width="28%" />
                                <col width="18%" />
                                <col width="28%" />
                            </colgroup>
                            <tr>
                                <th rowspan="3">미팅<br />개요</th>
                                <th>일시</th>
                                <td>
                                    <input ref="dateTime" type="date" v-model="isCardItem.with_team_activity.dateTime" :readonly="isCardItem.userId !== myUser.id" />
                                    <!--                                    <input ref="dateTime" type="text" v-model="isCardItem.with_team_activity.dateTime" :readonly="isCardItem.userId !== myUser.id" />-->
                                </td>
                                <th>문제해결과정</th>
                                <td>
                                    <input ref="problemSolvingProcess" type="text" v-model="isCardItem.with_team_activity.problemSolvingProcess" :readonly="isCardItem.userId !== myUser.id" />
                                </td>
                            </tr>
                            <tr>
                                <th>참석자</th>
                                <td colspan="3">
                                    <input ref="attendees" type="text" v-model="isCardItem.with_team_activity.attendees" :readonly="isCardItem.userId !== myUser.id" />
                                </td>
                            </tr>
                            <tr>
                                <th>본 미팅의<br />주요활동</th>
                                <td colspan="3">
                                    <textarea ref="mainActivities" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_team_activity.mainActivities" :readonly="isCardItem.userId !== myUser.id"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap t-center table mt-10">
                            <colgroup>
                                <col width="13%" />
                                <col width="13%" />
                                <col width="55%" />
                                <col width="19%" />
                            </colgroup>
                            <tr>
                                <th rowspan="3">진행<br />사항</th>
                                <th>구분</th>
                                <th>활동 내용</th>
                                <th>조치 사항</th>
                            </tr>
                            <tr>
                                <th>이번<br />미팅에서<br />한 일</th>
                                <td>
                                    <textarea ref="task1" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_team_activity.task1" :readonly="isCardItem.userId !== myUser.id"></textarea>
                                </td>
                                <td>
                                    <textarea ref="task2" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_team_activity.task2" :readonly="isCardItem.userId !== myUser.id"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>논의 사항</th>
                                <td colspan="2">
                                    <input ref="discuss1" type="text" v-model="isCardItem.with_team_activity.discuss1" :readonly="isCardItem.userId !== myUser.id"/>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap t-center table mt-10">
                            <colgroup>
                                <col width="13%" />
                                <col width="13%" />
                                <col width="26%" />
                                <col width="24%" />
                                <col width="24%" />
                            </colgroup>
                            <tr>
                                <th rowspan="3">추후<br />계획</th>
                                <th>구분</th>
                                <th>활동 내용</th>
                                <th>역할 분담</th>
                                <th>조치 사항</th>
                            </tr>
                            <tr>
                                <th>다음<br />미팅에서<br />해야 할 일</th>
                                <td>
                                    <textarea ref="schedule1" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_team_activity.schedule1" :readonly="isCardItem.userId !== myUser.id"></textarea>
                                </td>
                                <td>
                                    <textarea ref="schedule2" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_team_activity.schedule2" :readonly="isCardItem.userId !== myUser.id"></textarea>
                                </td>
                                <td>
                                    <textarea ref="schedule3" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_team_activity.schedule3" :readonly="isCardItem.userId !== myUser.id"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>기타</th>
                                <td colspan="3">
                                    <input ref="schedule4" type="text" v-model="isCardItem.with_team_activity.schedule4" :readonly="isCardItem.userId !== myUser.id"/>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap t-center table mt-10">
                            <colgroup>
                                <col width="13%" />
                                <col width="13%" />
                                <col width="74%" />
                            </colgroup>
                            <tr>
                                <th>피드백</th>
                                <th>교수님의<br />피드백<br />사항</th>
                                <td>
                                    <textarea rows="2" @keyup="textAreaResize" v-model="isCardItem.with_team_activity.feedback" :readonly="myUser.authority === 1"></textarea>
                                </td>
                            </tr>
                        </table>
                        <button v-if="myUser.authority === 2 && viewMode" @click="saveFeedModal = true" class="btn btn-primary btn-md float-right">피드백 저장</button>
                    </li>
                </ul>
                <ul v-else-if="menuState.evaluation" class="card-modal__con">
                    <li class="card-modal__con__item bg-white">
                        <h3 class="con-tit">평가지</h3>
                        <h4 class="sub-tit">⊙ PBL 활동 평가 모음 (선택 가능)</h4>
                        <div class="lh-1">
                            [최종 문제 해결안 평가] 항목 별로 점수를 기입하세요.<br />
                            ※ 교수자, 학습자 모두 활용 가능<br />
                            ※ 1 : 매우 부족함 / 2 : 부족함 / 3 : 보통 / 4 : 우수함 / 5 : 매우 우수함
                        </div>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="5%" />
                                <col width="60%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                            </colgroup>
                            <tr>
                                <th rowspan="2" colspan="2">내용</th>
                                <th colspan="3">평가 대상 팀 이름</th>
                                <td colspan="2"><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                            </tr>
                            <tr>
                                <th rowspan="6">보고서</th>
                                <td>문제에서 요구하는 사항이 무엇인지 분명히 파악하고 접근하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_1" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_1" /></label></td>
                            </tr>
                            <tr>
                                <td>문제에 포함된 주요 개념, 절차, 원리 등을 분명히 이해하고 있다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_2" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_2" /></label></td>
                            </tr>
                            <tr>
                                <td>문제 해결을 위해 자료가 충분히 검토되었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_3" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_3" /></label></td>
                            </tr>
                            <tr>
                                <td>신뢰할 만한 자료를 인용 또는 참고하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_4" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_4" /></label></td>
                            </tr>
                            <tr>
                                <td>충분한 설명, 세부 사항, 적절한 예를 포함하고 있다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_5" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_5" /></label></td>
                            </tr>
                            <tr>
                                <td>실천 가능한 해결안을 제시하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_6" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_6" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_6" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_6" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_6" /></label></td>
                            </tr>
                            <tr>
                                <th rowspan="7">발표</th>
                                <td>문제에서 요구하는 최종 해결안의 형식에 맞게 작성되었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_7" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_7" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_7" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_7" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_7" /></label></td>
                            </tr>
                            <tr>
                                <td>발표에 중요한 내용이 충분히 제시되었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_8" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_8" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_8" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_8" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_8" /></label></td>
                            </tr>
                            <tr>
                                <td>발표 내용이 논리적으로 잘 조직되었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_9" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_9" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_9" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_9" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_9" /></label></td>
                            </tr>
                            <tr>
                                <td>발표 자료가 매력 있게 구성되었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_10" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_10" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_10" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_10" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_10" /></label></td>
                            </tr>
                            <tr>
                                <td>발표 내용이 청중이 이해하기 쉽게 제시되었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_11" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_11" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_11" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_11" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_11" /></label></td>
                            </tr>
                            <tr>
                                <td>발표 내용이 다른 학습자의 학습에 도움이 되었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_12" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_12" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_12" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_12" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_12" /></label></td>
                            </tr>
                            <tr>
                                <td>발표자가 내용을 분명하게 전달하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio1_13" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_13" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_13" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_13" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio1_13" /></label></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="t-center">점수 합계</td>
                                <td colspan="5" class="t-center">15</td>
                            </tr>
                        </table>
                        <div class="lh-1 mt-30">
                            [성찰일지 평가] 항목 별로 점수를 기입하세요.<br />
                            ※ 교수자 활용 가능<br />
                            ※ 1 : 매우 부족함 / 2 : 부족함 / 3 : 보통 / 4 : 우수함 / 5 : 매우 우수함
                        </div>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="65%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                            </colgroup>
                            <tr>
                                <th rowspan="2">내용</th>
                                <th colspan="3">평가 대상<br />학생 이름</th>
                                <td colspan="2"><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                            </tr>
                            <tr>
                                <td>문제를 통해 학습해야 할 주요 개념, 원리 및 절차에 대해 정확하게 이해하고 있다.</td>
                                <td><label class="check-only"><input type="radio" name="radio2_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_1" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_1" /></label></td>
                            </tr>
                            <tr>
                                <td>주요 학습 내용과 관련하여 자신의 생각이나 느낀 점을 잘 진술하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio2_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_2" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_2" /></label></td>
                            </tr>
                            <tr>
                                <td>학습 과정에서의 자신의 경험이 잘 드러나도록 진술하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio2_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_3" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_3" /></label></td>
                            </tr>
                            <tr>
                                <td>학습 내용에 자신의 현재 및 미래의 일을 잘 연결 지어 진술하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio2_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_4" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_4" /></label></td>
                            </tr>
                            <tr>
                                <td>팀 활동의 기여 정도는 학습자들이 서로에게 부여한 점수를 반영하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio2_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_5" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio2_5" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">점수 합계</td>
                                <td colspan="5" class="t-center">15</td>
                            </tr>
                        </table>
                        <h4 class="sub-tit mt-30">⊙ 동료 평가지</h4>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="10%" />
                                <col width="50%" />
                                <col width="10%" />
                                <col width="30%" />
                            </colgroup>
                            <tr>
                                <th>과제명</th>
                                <td><input type="text" /></td>
                                <th>학번</th>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>조 이름</th>
                                <td><input type="text" /></td>
                                <th>이름</th>
                                <td><input type="text" /></td>
                            </tr>
                        </table>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="5%" />
                                <col width="75%" />
                                <col width="20%" />
                            </colgroup>
                            <tr>
                                <th colspan="2">평가 요소</th>
                                <th>조원 이름</th>
                            </tr>
                            <tr>
                                <td class="t-center">1</td>
                                <td>문제해결 활동에 적극적으로  참여한 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">2</td>
                                <td>문제해결 활동에 도움이 되는 발언이나 태도를 보인 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">3</td>
                                <td>다른 사람의 발언을 적극적으로 경청한 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">4</td>
                                <td>문제를 다각적으로 분석했던 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">5</td>
                                <td>해결안/아이디어를 논리적으로 도출한 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">6</td>
                                <td>비판적이고 창의적인 의견을 제시한 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">7</td>
                                <td>학습 결과물을 충실하게 낸 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">8</td>
                                <td>다양한 정보를 수집하고 활용하고자 했던 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">9</td>
                                <td>자기주도적으로 학습을 수행했던 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">10</td>
                                <td>(선택) 온라인 상에서 적극적으로 상호작용한 사람은 누구입니까?</td>
                                <td><input type="text" /></td>
                            </tr>
                        </table>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                            </colgroup>
                            <tr>
                                <th colspan="6">조별 점수 추가(자기 조 포함, 5점 만점)</th>
                            </tr>
                            <tr>
                                <th>이름</th>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>점수</th>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>조별 점수를 주게 된 근거 (구체적 사실과 논거 제시)</th>
                                <td colspan="5"><textarea rows="3" @keyup="textAreaResize"></textarea></td>
                            </tr>
                        </table>
                        <h4 class="sub-tit mt-30">⊙ 발표 동료 평가지 (1)</h4>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="20%" />
                                <col width="40%" />
                                <col width="10%" />
                                <col width="30%" />
                            </colgroup>
                            <tr>
                                <th>발표조 및 발표자 명</th>
                                <td><input type="text" /></td>
                                <th>학번</th>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>과제명(일시)</th>
                                <td><input type="text" /></td>
                                <th>평가자</th>
                                <td><input type="text" /></td>
                            </tr>
                        </table>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="5%" />
                                <col width="60%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                            </colgroup>
                            <tr>
                                <th colspan="2" rowspan="2">평가 요소</th>
                                <th colspan="5">점수</th>
                            </tr>
                            <tr>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                            </tr>
                            <tr>
                                <td class="t-center">1</td>
                                <td>주제가 분명하게 제시됨</td>
                                <td><label class="check-only"><input type="radio" name="radio3_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_1" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_1" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">2</td>
                                <td>주제와 관련된 사실과 데이터들이 충분히 조사되었음</td>
                                <td><label class="check-only"><input type="radio" name="radio3_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_2" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_2" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">3</td>
                                <td>발표자는 관련 문제에 대한 내용을 충분히 이해하고 있음</td>
                                <td><label class="check-only"><input type="radio" name="radio3_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_3" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_3" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">4</td>
                                <td>발표가 청중들에게 새롭고 유용한 정보를 제시하였음</td>
                                <td><label class="check-only"><input type="radio" name="radio3_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_4" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_4" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">5</td>
                                <td>발표의 내용이 흥미롭고 의미 있는 정보가 제시되었음</td>
                                <td><label class="check-only"><input type="radio" name="radio3_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_5" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio3_5" /></label></td>
                            </tr>
                        </table>
                        <h4 class="sub-tit mt-30">⊙ 발표 동료 평가지 (2)</h4>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="20%" />
                                <col width="40%" />
                                <col width="10%" />
                                <col width="30%" />
                            </colgroup>
                            <tr>
                                <th>발표조 및 발표자 명</th>
                                <td><input type="text" /></td>
                                <th>학번</th>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>과제명(일시)</th>
                                <td><input type="text" /></td>
                                <th>평가자</th>
                                <td><input type="text" /></td>
                            </tr>
                        </table>
                        <p class="t-right fc-gray mt-10">※ 각 요소별 5단계 점수 부여</p>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="5%" />
                                <col width="45%" />
                                <col width="10%" />
                                <col width="10%" />
                                <col width="10%" />
                                <col width="10%" />
                                <col width="10%" />
                            </colgroup>
                            <tr>
                                <th colspan="2">평가 요소</th>
                                <th colspan="5">학생명</th>
                            </tr>
                            <tr>
                                <td class="t-center">1</td>
                                <td>[준비성] 다양한 자료 수집과 질적으로 좋은 정보 제공</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">2</td>
                                <td>[책임감] 맡은 역할을 충실히 수행</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">3</td>
                                <td>[참여도] 토론이나 과제 해결을 위해 적극적으로 참여</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">4</td>
                                <td>[협동적 자세] 다른 사람들과 협력하고 리더십을 보임</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">5</td>
                                <td>[의사소통] 조원들의 의견을 경청하고 자신의 의견을 적절히 표현함</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">6</td>
                                <td>[전문성] 정보의 다각적 분석과 토론에 적극 참여하며 환류 수용</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">7</td>
                                <td>[반성적 태도] 타인을 비난하지 않고 자신의 능력 및 한계를 인정함</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">8</td>
                                <td>[비판적 사고] 가설 생성 및 지식 적용, 논리적 추론 등 비판적 사고력</td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                        </table>
                        <h4 class="sub-tit mt-30">⊙ 조별 평가지</h4>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="20%" />
                                <col width="40%" />
                                <col width="10%" />
                                <col width="30%" />
                            </colgroup>
                            <tr>
                                <th>과제명</th>
                                <td><input type="text" /></td>
                                <th>학번</th>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>조 이름</th>
                                <td><input type="text" /></td>
                                <th>이름</th>
                                <td><input type="text" /></td>
                            </tr>
                        </table>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="5%" />
                                <col width="75%" />
                                <col width="20%" />
                            </colgroup>
                            <tr>
                                <th colspan="2">평가 요소</th>
                                <th colspan="5">조 이름</th>
                            </tr>
                            <tr>
                                <td class="t-center">1</td>
                                <td>문제와 관련된 정보를 가장 많이 제시한 조는 어느 조인가?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">2</td>
                                <td>다양한 학습 자료를 수집, 분석하여 합리적인 근거와 이유를 들어 의견을 제시하고 학습 결과를 이해하기 쉽게 보고한 조는 어느 조인가?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">3</td>
                                <td>흥미를 가지고 학습에 가장 적극적으로 참여한 조는 어느 조인가?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">4</td>
                                <td>조원 간 의견을 존중하고 서로 도우며 학습을 이끈 조는 어느 조인가?</td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <td class="t-center">5</td>
                                <td>다른 조에게 방해가 되지 않게 열심히 학습에 임한 조는 어느 조인가?</td>
                                <td><input type="text" /></td>
                            </tr>
                        </table>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                                <col width="16%" />
                            </colgroup>
                            <tr>
                                <th colspan="6">조별 점수 추가(자기 조 포함, 5점 만점)</th>
                            </tr>
                            <tr>
                                <th>조 이름</th>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>점수</th>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                                <td><input type="text" /></td>
                            </tr>
                            <tr>
                                <th>조별 점수를 주게 된 근거 (구체적 사실과 논거 제시)</th>
                                <td colspan="5"><textarea rows="3" @keyup="textAreaResize"></textarea></td>
                            </tr>
                        </table>
                        <h4 class="sub-tit mt-30">⊙ 교수자 자기평가지</h4>
                        <table class="table-input__wrap table th-center mt-10 check-table">
                            <colgroup>
                                <col width="5%" />
                                <col width="60%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                                <col width="7%" />
                            </colgroup>
                            <tr>
                                <th colspan="2" rowspan="2">평가 요소</th>
                                <th colspan="5">점수</th>
                            </tr>
                            <tr>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                                <th>4</th>
                                <th>5</th>
                            </tr>
                            <tr>
                                <td class="t-center">1</td>
                                <td>학습자들이 수업에 흥미를 가질 수 있도록 동기 유발을 위하여 노력하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_1" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_1" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_1" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">2</td>
                                <td>학습자들이 아무 것도 모른다는 것을 받아들일 수 있는 분위기를 조성하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_2" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_2" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_2" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">3</td>
                                <td>학습자들이 주어진 문제에 대하여 논리적으로 사고하도록 유도하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_3" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_3" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_3" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">4</td>
                                <td>학습자들이 각각의 논쟁점 간의 관계를 인식하도록 노력하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_4" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_4" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_4" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">5</td>
                                <td>학습자들이 관련 개념과 용어에 익숙해지도록 노력하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_5" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_5" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_5" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">6</td>
                                <td>학습자들이 교수자에게 의존하지 않도록 학습자 스스로 답을 찾을 수 있도록 유도하였다</td>
                                <td><label class="check-only"><input type="radio" name="radio4_6" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_6" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_6" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_6" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_6" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">7</td>
                                <td>학습자들이 의사 표현을 보다 정확하고 세련되게 할 수 있도록 유도하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_7" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_7" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_7" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_7" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_7" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">8</td>
                                <td>집단 활동 과정에서 각 학습자의 태도와 분위기를 주의 깊게 관찰하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_8" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_8" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_8" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_8" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_8" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">9</td>
                                <td>집단 내에 문제가 발생하였을 경우 학습자 스스로 해결할 수 있도록 유도하였다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_9" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_9" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_9" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_9" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_9" /></label></td>
                            </tr>
                            <tr>
                                <td class="t-center">10</td>
                                <td>모든 학습자가 집단 활동에 열심히 참여하도록 도와주었다.</td>
                                <td><label class="check-only"><input type="radio" name="radio4_10" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_10" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_10" checked /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_10" /></label></td>
                                <td><label class="check-only"><input type="radio" name="radio4_10" /></label></td>
                            </tr>
                        </table>
                    </li>
                </ul>
                <ul v-else-if="menuState.reflectionLog" class="card-modal__con">
                    <li class="card-modal__con__item bg-white">
                        <h3 class="con-tit">성찰</h3>
                        <table class="table-input__wrap text">
                            <tr>
                                <th>학번</th>
                                <td>
                                    <input ref="studentId" type="text" v-model="isCardItem.with_reflection_log.studentId" :readonly="isCardItem.userId !== myUser.id" />
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap text">
                            <tr>
                                <th>이름</th>
                                <td>
                                    <input v-if="isCardItem.with_reflection_log.name" type="text" :value="isCardItem.with_reflection_log.name" readonly/>
                                    <input v-else type="text" :value="myUser.name" readonly/>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    1. 이번 학습을 통해 무엇을 배웠나요?
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content1" v-model="isCardItem.with_reflection_log.content1" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    2. 수업에서 어려웠던 활동은 무엇이었나요?
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content2" v-model="isCardItem.with_reflection_log.content2" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    3. 앞으로 내가 더 알고 싶은 내용은 무엇인가요?
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content3" v-model="isCardItem.with_reflection_log.content3" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    4. 학습한 내용을 적용할 수 있는 것은 무엇인가요?
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content4" v-model="isCardItem.with_reflection_log.content4" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    5. 이 수업에서 나의 부족한 부분은 무엇인가요?
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content5" v-model="isCardItem.with_reflection_log.content5" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    6. 이 수업의 학습과정을 통해 무엇을 느꼈나요?
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content6" v-model="isCardItem.with_reflection_log.content6" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                        <table class="table-input__wrap">
                            <tr>
                                <th>
                                    7. 기타 느낀 점을 자유롭게 기술하세요.
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    <textarea ref="content7" v-model="isCardItem.with_reflection_log.content7" rows="4" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>
                                </td>
                            </tr>
                        </table>
                    </li>
                </ul>
                <ul v-else-if="menuState.operationResult && isMyPage" class="card-modal__con">
                    <li class="card-modal__con__item bg-white">
                        <h3 class="con-tit">IC-PBL 및 IC-PBL+ 교과목 운영결과 요약보고서</h3>
                        <table class="table-input__wrap table t-center">
                            <colgroup>
                                <col width="15%" />
                                <col width="21%" />
                                <col width="16%" />
                                <col width="27%" />
                                <col width="21%" />
                            </colgroup>
                            <tr>
                                <th>개설학기</th>
                                <td>
                                    <input ref="semester" type="text" v-model="isCardItem.with_operation_result.semester">
                                </td>
                                <th>교과구분</th>
                                <td>
                                    <label class="custom-check w-50">
                                        <input ref="division" type="radio" name="curriculum_div" value="1" v-model="isCardItem.with_operation_result.division" />
                                        IC-PBL
                                    </label>
                                    <label class="custom-check w-50">
                                        <input type="radio" name="curriculum_div" value="2" v-model="isCardItem.with_operation_result.division" />
                                        IC-PBL+
                                    </label>
                                </td>
                                <th>IC-PBL 유형</th>
                            </tr>
                            <tr>
                                <th>개설단과대학</th>
                                <td>
                                    <input ref="college" type="text" v-model="isCardItem.with_operation_result.college">
                                </td>
                                <th>학과(전공)/학년</th>
                                <td>
                                    <input ref="grades" type="text" v-model="isCardItem.with_operation_result.grades">
                                </td>
                                <td rowspan="3">
                                    <label class="custom-check">
                                        <input ref="icpblType" type="radio" name="icpbl_type" value="1" v-model="isCardItem.with_operation_result.icpblType" />
                                        M (현장통합형)
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="icpbl_type" value="2" v-model="isCardItem.with_operation_result.icpblType" />
                                        E (현장평가형)
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="icpbl_type" value="3" v-model="isCardItem.with_operation_result.icpblType" />
                                        C (문제해결형)
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="icpbl_type" value="4" v-model="isCardItem.with_operation_result.icpblType" />
                                        A (현장문제형)
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>교과목명</th>
                                <td>
                                    <input ref="lectureName" type="text" v-model="isCardItem.with_operation_result.lectureName">
                                </td>
                                <th>교강사명</th>
                                <td>
                                    <input ref="professor" type="text" v-model="isCardItem.with_operation_result.professor">
                                </td>
                            </tr>
                            <tr>
                                <th>학점-강의-실습</th>
                                <td>
                                    <input ref="grade" type="text" v-model="isCardItem.with_operation_result.grade">
                                </td>
                                <th>수강인원</th>
                                <td>
                                    <input ref="size" type="text" v-model="isCardItem.with_operation_result.size">
                                </td>
                            </tr>
                        </table>
                        <p class="t-right fc-gray fs-sm mb-30">※본 요약보고서는 수업 운영 특성에 따라 양식을 변형하여 사용할 수 있음</p>
                        <h4 class="sub-tit">⊙ IC-PBL 교과목 운영 결과</h4>
                        <div class="pl-10 lh-1">▪ 수업설계</div>
                        <table class="table-input__wrap table t-center mt-10 mb-30">
                            <colgroup>
                                <col width="15%" />
                                <col width="20%" />
                                <col width="65%" />
                            </colgroup>
                            <tr>
                                <th rowspan="4">기본 사항</th>
                                <th>교과목 개요</th>
                                <td>
                                    <input ref="summary" type="text" v-model="isCardItem.with_operation_result.summary">
                                </td>
                            </tr>
                            <tr>
                                <th>수업 목표</th>
                                <td>
                                    <input ref="classGoal" type="text" v-model="isCardItem.with_operation_result.classGoal">
                                </td>
                            </tr>
                            <tr>
                                <th>운영방식</th>
                                <td>
                                    <input ref="method" type="text" v-model="isCardItem.with_operation_result.method">
                                </td>
                            </tr>
                            <tr>
                                <th>평가 방법</th>
                                <td>
                                    <input ref="basicPlan" type="text" v-model="isCardItem.with_operation_result.basicPlan">
                                </td>
                            </tr>
                            <tr>
                                <th rowspan="3">문제 시나리오</th>
                                <th>제목</th>
                                <td>
                                    <input ref="title" type="text" v-model="isCardItem.with_operation_result.title">
                                </td>
                            </tr>
                            <tr>
                                <th>문제 상황 속<br />학습자(주인공) 역할</th>
                                <td>
                                    <textarea ref="role" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_operation_result.role"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <textarea ref="scenario" rows="2" @keyup="textAreaResize" v-model="isCardItem.with_operation_result.scenario"></textarea>
                                </td>
                            </tr>
                        </table>
                        <div class="pl-10 lh-1">▪ 수업운영과정</div>
                        <table class="table-input__wrap table t-center mt-10 mb-30">
                            <colgroup>
                                <col width="20%" />
                                <col width="10%" />
                                <col width="30%" />
                                <col width="30%" />
                            </colgroup>
                            <tr>
                                <th>IC-PBL 단계</th>
                                <th>주차</th>
                                <th>수업 내용</th>
                                <th>학습 과제 및 수업 방식</th>
                            </tr>
                            <tr v-for="(sc, idx) in isCardItem.with_operation_result.process">
                                <td>
                                    <input type="text" v-model="sc.level">
                                </td>
                                <td>
                                    <input type="text" v-model="sc.week">
                                </td>
                                <td>
                                    <input type="text" v-model="sc.content">
                                </td>
                                <td>
                                    <input type="text" v-model="sc.method">
                                </td>
                            </tr>
                        </table>
                        <div class="pl-10 lh-1">▪ 수업 결과</div>
                        <table class="table-input__wrap table t-center mt-10 mb-30">
                            <colgroup>
                                <col width="20%" />
                                <col width="80%" />
                            </colgroup>
                            <tr>
                                <th>결과물 유형<br />(복수 선택 가능)</th>
                                <td>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_operation_result.outputType1" true-value="1" false-value="0" />
                                        연구보고서
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_operation_result.outputType2" true-value="1" false-value="0" />
                                        제안서
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_operation_result.outputType3" true-value="1" false-value="0" />
                                        캠페인
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_operation_result.outputType4" true-value="1" false-value="0" />
                                        프로토타입
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_operation_result.outputType5" true-value="1" false-value="0" />
                                        소프트웨어
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_operation_result.outputType6" true-value="1" false-value="0" />
                                        영상물
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" value="1" v-model="isCardItem.with_operation_result.outputType7" true-value="1" false-value="0" />
                                        기타&nbsp;&nbsp;<input type="text"
                                                             class="input-text w-200"
                                                             v-model="isCardItem.with_operation_result.outputType8"
                                                             :placeholder="'입력하세요'"
                                                             :disabled="isCardItem.with_operation_result.outputType7 != 1">
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>최종 수업 결과물</th>
                                <td>
                                    <textarea ref="finalOutput" v-model="isCardItem.with_operation_result.finalOutput"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th>주요 학생성찰</th>
                                <td>
                                    <textarea ref="mainStudent" v-model="isCardItem.with_operation_result.mainStudent"></textarea>
                                </td>
                            </tr>
                        </table>
                        <h4 class="sub-tit">⊙ 현장연계 <span class="fs-md fc-gray">※현장연계가 있는 M, E, A 유형 경우에만 작성</span></h4>
                        <table class="table-input__wrap table t-center mt-10 mb-30">
                            <colgroup>
                                <col width="17%" />
                                <col width="33%" />
                                <col width="17%" />
                                <col width="33%" />
                            </colgroup>
                            <tr>
                                <th>현장명</th>
                                <td>
                                    <input ref="sTitle" type="text" v-model="isCardItem.with_operation_result.sTitle">
                                </td>
                                <th>현장전문가 성명<br />직급 및 업무분야</th>
                                <td>
                                    <input ref="sName" type="text" v-model="isCardItem.with_operation_result.sName">
                                </td>
                            </tr>
                            <tr>
                                <th>참여역할</th>
                                <td colspan="3">
                                    <label class="custom-check">
                                        <input ref="sRole1" type="radio" name="role_part" value="1" v-model="isCardItem.with_operation_result.sRole1" />
                                        멘토링
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="role_part" value="2" v-model="isCardItem.with_operation_result.sRole1" />
                                        평가/심사
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="role_part" value="3" v-model="isCardItem.with_operation_result.sRole1" />
                                        수업/특강
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="role_part" value="4" v-model="isCardItem.with_operation_result.sRole1" />
                                        현장견학안내
                                    </label>
                                    <label class="custom-check">
                                        <input type="radio" name="role_part" value="5" v-model="isCardItem.with_operation_result.sRole1" />
                                        기타&nbsp;&nbsp;<input
                                            ref="sRole2"
                                            type="text"
                                            class="input-text w-200"
                                            placeholder="입력하세요"
                                            v-model="isCardItem.with_operation_result.sRole2"
                                            :disabled="isCardItem.with_operation_result.sRole1 != 5" />
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th>현장연계사항</th>
                                <td colspan="3">
                                    <input ref="sLink" type="text" v-model="isCardItem.with_operation_result.sLink">
                                </td>
                            </tr>
                            <tr>
                                <th>환류/성과</th>
                                <td colspan="3">
                                    <input ref="sFeedback" type="text" v-model="isCardItem.with_operation_result.sFeedback">
                                </td>
                            </tr>
                            <tr>
                                <th>현장의견</th>
                                <td colspan="3">
                                    <input ref="sOpinion" type="text" v-model="isCardItem.with_operation_result.sOpinion">
                                </td>
                            </tr>
                        </table>
                        <h4 class="sub-tit">⊙ 교수자 성찰</h4>
                        <table class="table-input__wrap table t-center mt-10 mb-30">
                            <colgroup>
                                <col width="17%" />
                                <col width="17%" />
                                <col width="66%" />
                            </colgroup>
                            <tr>
                                <th colspan="2">항목</th>
                                <th>성찰 내용</th>
                            </tr>
                            <tr>
                                <th rowspan="2">수업설계</th>
                                <th>교과 선택</th>
                                <td>
                                    <input ref="pr1" type="text" v-model="isCardItem.with_operation_result.pr1">
                                </td>
                            </tr>
                            <tr>
                                <th>문제 개발</th>
                                <td>
                                    <input ref="pr2" type="text" v-model="isCardItem.with_operation_result.pr2">
                                </td>
                            </tr>
                            <tr>
                                <th rowspan="3">수업운영</th>
                                <th>팀 구성</th>
                                <td>
                                    <input ref="pr3" type="text" v-model="isCardItem.with_operation_result.pr3">
                                </td>
                            </tr>
                            <tr>
                                <th>퍼실리테이팅</th>
                                <td>
                                    <input ref="pr4" type="text" v-model="isCardItem.with_operation_result.pr4">
                                </td>
                            </tr>
                            <tr>
                                <th>평가 방식</th>
                                <td>
                                    <input ref="pr5" type="text" v-model="isCardItem.with_operation_result.pr5">
                                </td>
                            </tr>
                            <tr>
                                <th>수업성과</th>
                                <th>기타 총평</th>
                                <td>
                                    <input ref="pr6" type="text" v-model="isCardItem.with_operation_result.pr6">
                                </td>
                            </tr>
                        </table>
                    </li>
                </ul>
                <ul v-else class="card-modal__con">
                    <li class="card-modal__con__item">
                        <label class="tit">제목</label>
                        <template v-if="isMyPage && cards[cardId].type === 0">
                            <input ref="title" type="text" placeholder="수업명으로 자동 입력됩니다." v-model="isCardItem.title" v-if="Number(isMenuType) !== 4">
                            <input ref="title" type="text" placeholder="수업명으로 자동 입력됩니다." v-model="isCardItem.with_class_apply.korName" readonly v-else-if="Number(isMenuType) === 4">
                        </template>
                        <input ref="title" type="text" placeholder="제목을 입력하세요" v-model="isCardItem.title" :readonly="(isCardItem.userId !== myUser.id) || Number(isCardItem.type) === 4" v-else>
                    </li>
                    <li class="card-modal__con__item">
                        <label class="text">상세</label>
                        <div class="editor">
                            <editor-menu-bubble class="menububble" :editor="editor" v-click-outside="hideLinkMenu" v-slot="{ commands, isActive, getMarkAttrs, menu }">
                                <div
                                        class="menububble"
                                        :class="{ 'is-active': menu.isActive }"
                                        :style="`left: ${menu.left}px; bottom: ${menu.bottom}px;`"
                                >

                                    <form class="menububble__form" v-if="linkMenuIsActive" @submit.prevent="setLinkUrl(commands.link, linkUrl)">
                                        <input class="menububble__input" type="text" v-model="linkUrl" placeholder="https://" ref="linkInput" @keydown.esc="hideLinkMenu"/>
                                        <button class="menububble__button btn" @click="setLinkUrl(commands.link, linkUrl)" type="button">확인</button>
                                        <button class="menububble__button btn" @click="hideLinkMenu" type="button">취소</button>
                                    </form>

                                    <template v-else>
                                        <button
                                                class="menububble__button"
                                                @click="showLinkMenu(getMarkAttrs('link'))"
                                                :class="{ 'is-active': isActive.link() }"
                                        >
                                            <span>{{ isActive.link() ? 'Update Link' : 'Add Link'}}</span>
                                            <!--                                            <icon name="link" />-->
                                        </button>
                                    </template>

                                </div>
                            </editor-menu-bubble>

                            <editor-content class="editor__content" :editor="editor" :readonly="isCardItem.userId !== myUser.id" />
                        </div>
                        <!--                        <label class="text">상세</label>-->
                        <!--                        <textarea rows="2" placeholder="내용을 입력하세요" v-model="isCardItem.content" :readonly="isCardItem.userId !== myUser.id" @keyup="textAreaResize"></textarea>-->
                    </li>
                    <li v-if="isCardItem.activationImage" class="card-modal__con__item">
                        <label class="img">이미지</label>
                        <label for="input_img" class="btn-addbtn" v-if="isCardItem.userId === myUser.id"></label>
                        <input type="file" class="input-img" accept="image/png, image/jpeg" id="input_img" @change="uploadImage">
                        <ul class="img-list">
                            <draggable v-model="isCardItem.images" :move="dragCallBack" v-bind:disabled="isMobileCheck">
                                <template v-if="viewMode && isCardItem.userId != myUser.id">
                                    <li v-for="(imgObj, index) in isCardItem.images" class="img-list__item" v-if="index < 4">
                                        <button class="img-deletebtn" @click="deleteImage(index)" v-if="isCardItem.userId === myUser.id"></button>

                                        <img v-if="applyId && viewMode" :src="imgObj.imgUrl+'/consultant/'+applyId" :alt="imgObj.fileName" @click="imgSlideOn(index)"/>
                                        <img v-else :src="imgObj.imgUrl" :alt="imgObj.fileName" @click="imgSlideOn(index)"/>

                                        <button v-if="index === 3 && isCardItem.images.length > 4" class="more" @click="imgSlideOn(index)">+더보기({{isCardItem.images.length - 3}}개)</button>
                                    </li>
                                </template>
                                <template v-else>
                                    <li v-for="(imgObj, index) in isCardItem.images" class="img-list__item">
                                        <button class="img-deletebtn" @click="deleteImage(index)" v-if="isCardItem.userId === myUser.id"></button>

                                        <img v-if="applyId && viewMode" :src="imgObj.imgUrl+'/consultant/'+applyId" :alt="imgObj.fileName" @click="imgSlideOn(index)"/>
                                        <img v-else :src="imgObj.imgUrl" :alt="imgObj.fileName" @click="imgSlideOn(index)"/>
                                    </li>
                                </template>
                            </draggable>
                        </ul>
                    </li>
                    <li v-if="isCardItem.activationFile" class="card-modal__con__item">
                        <label class="file">파일</label>
                        <label for="input_file2" class="btn-addbtn" v-if="isCardItem.userId === myUser.id"></label>
                        <input type="file" class="input-img" id="input_file2" @change="itemUploadFile">
                        <ul class="user-list">
                            <li v-for="(file, idx) in isCardItem.files" class="user-list__item">
                                <a :href="file.imgUrl"><span class="name">{{file.fileName}}</span></a>
                                <button class="user-deletebtn" @click="itemDeleteFile(idx)"></button>
                            </li>
                        </ul>
                    </li>
                    <li v-if="isCardItem.activationDeadline" class="card-modal__con__item time">
                        <label class="time">마감기한</label>
                        <!--                        <span class="date-time">2021.02.22 13:00</span>-->
                        <template v-if="isCardItem.userId !== myUser.id">
                            <span class="date-time">
                            {{year.split('-')[0]}}.{{year.split('-')[1]}}.{{year.split('-')[2]}}
                            {{10 <= Number(hour) ? Number(hour)+'' : '0'+Number(hour) }}:
                            {{10 <= Number(min) ? Number(min)+'' : '0'+Number(min) }}
                            </span>
                        </template>
                        <template v-else>
                            <input ref="deadLine" type="date" v-model="year"/>
                            <vue-picker class="select" v-model="hour">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="h in 24">
                                    <vue-picker-option :value="Number(h-1)+''">
                                        {{10 <= Number((h-1)) ? Number(h-1) + '' : '0'+(Number(h-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;&nbsp;<span>시</span>
                            <vue-picker class="select" v-model="min">
                                <vue-picker-option value="-1">선택</vue-picker-option>
                                <template v-for="m in 60">
                                    <vue-picker-option :value="Number(m-1)+''">
                                        {{10 <= Number((m-1)) ? Number(m-1) + '' : '0'+(Number(m-1))}}
                                    </vue-picker-option>
                                </template>
                            </vue-picker>
                            &nbsp;&nbsp;<span>분</span>
                        </template>
                    </li>
                    <li v-if="isCardItem.activationParty" class="card-modal__con__item">
                        <label class="user">참여자</label>
                        <button class="btn-addbtn" @click="partyListOn" v-if="isCardItem.userId === myUser.id"></button>

                        <div v-if="userListMode" class="select-list__wrap user-select">
                            <ul class="select-list" v-for="(user_, index) in pUserList">
                                <li class="select-list__item" @click="addParty(index)">{{ user_.name }}</li>
                            </ul>
                        </div>

                        <ul class="user-list">
                            <li v-for="(user_, index) in isCardItem.party" class="user-list__item" >
                                <span class="name">{{ user_.name }}</span>
                                <button class="user-deletebtn" @click="delParty(index)" v-if="isCardItem.userId === myUser.id"></button>
                            </li>
                            <!--                            <li class="user-list__item">-->
                            <!--                                <span class="name">김수현</span>-->
                            <!--                                <button class="user-deletebtn"></button>-->
                            <!--                            </li>-->
                            <!--                            <li class="user-list__item">-->
                            <!--                                <span class="name">이민수</span>-->
                            <!--                                <button class="user-deletebtn"></button>-->
                            <!--                            </li>-->
                        </ul>
                    </li>
                    <li v-if="isCardItem.activationLabel" class="card-modal__con__item">
                        <label class="label">라벨</label>
                        <div class="label-list">
                            <label class="label-color__check color0">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="0"
                                       :checked="0 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color1">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="1"
                                       :checked="1 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color2">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="2"
                                       :checked="2 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color3">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="3"
                                       :checked="3 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color4">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="4"
                                       :checked="4 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color5">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="5"
                                       :checked="5 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color6">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="6"
                                       :checked="6 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color7">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="7"
                                       :checked="7 === isCardItem.label" :disabled="isCardItem.userId !== myUser.id"/>
                            </label>
                            <label class="label-color__check color8">
                                <input type="radio" name="label_color" v-model="isCardItem.label" value="8"
                                       :checked="8 === isCardItem.label"/>
                            </label>
                        </div>
                    </li>
                    <li v-if="isCardItem.activationCheck" class="card-modal__con__item check">
                        <label class="check">체크리스트</label>
                        <div class="check-list__wrap">
                            <draggable v-model="isCardItem.checks" :move="dragCallBack" v-bind:disabled="isMobileCheck">
                                <div v-for="(check, index) in isCardItem.checks" class="check-list">
                                    <div class="check-list__con">
                                        <input type="checkbox" v-model="check.checked" v-if="isCardItem.userId === myUser.id" />
                                        <input type="checkbox" v-model="check.checked" v-if="isCardItem.userId !== myUser.id" onclick="return false" />
                                        <input ref="checkList" type="text" placeholder="내용을 입력하세요." v-model="check.content" :readonly="isCardItem.userId !== myUser.id" />
                                    </div>
                                    <button class="check-list__btn delete" @click="btnCardItemMenuCheck('DELETE', index)" v-if="isCardItem.userId === myUser.id"></button>
                                </div>
                            </draggable>
                        </div>
                        <div class="check-list" style="display: block;">
                            <button class="check-list__btn" @click="btnCardItemMenuCheck('ADD', null)" v-if="isCardItem.userId === myUser.id"></button>
                        </div>
                    </li>
                </ul>
                <ul v-if="viewMode && !isMyPage && itemId > -10" class="card-modal__con comment">
                    <li class="card-modal__con__item">
                        <label class="comment">댓글</label>
                        <div class="comment-list">
                            <div class="container_">
                                <editor-menu-bar :editor="editor2" v-slot="{ commands }">
                                    <div class="menubar">
                                        <button class="mentionbtn" @click="editorPopupOn($event, commands)">
                                            멘션
                                        </button>
                                    </div>
                                </editor-menu-bar>

                                <div id="textarea-input" class="editor textarea-input scroll-sm">
                                    <editor-content class="editor__content" :editor="editor2" />
                                </div>

                                <div class="suggestion-list scroll-sm" v-show="showSuggestions" ref="suggestions">
                                    <template v-if="hasResults">
                                        <div
                                                v-for="(user, index) in filteredUsers"
                                                :key="user.id"
                                                class="suggestion-list__item"
                                                :class="{ 'is-selected': navigatedUserIndex === index }"
                                                @click="selectUser(user)"
                                        >
                                            {{ user.name }}
                                        </div>
                                    </template>
                                    <div v-else class="suggestion-list__item is-empty">
                                        검색 결과 없음
                                    </div>
                                </div>

                                <label for="input_file" class="filebtn">{{ commentFileName }}</label>
                                <input type="file" class="input-img"
                                       accept=".doc, .docx, .dotx, .xlsx, .xls, .pdf, .hwp, .ppt, .pptx, .egg, .zip, .7z, .tar, .zipx"
                                       id="input_file" @change="uploadFile">
                                <button class="comment__btn"  @click="addComment(this)"></button>
                            </div>
                        </div>
                        <div v-for="(comment, index) in isCardItem.comments" class="comment-list">
                            <div class="comment-top">
                                <span class="name">{{ comment.userName }}</span>
                                <span class="date">{{ comment.date }}</span>

                                <template v-if="!comment.editMode">
                                    <button class="option" v-if="comment.userId === myUser.id" :class="{'on':comment.commentMenu}" @click="btnCommentMenuOn(comment)"></button>
                                    <div v-if="comment.commentMenu" class="select-list__wrap option-select" v-click-outside="btnCommentMenuOff">
                                        <ul class="select-list">
                                            <li class="select-list__item" @click="editCommentBtnOn(comment)">수정</li>
                                            <li class="select-list__item" @click="delCommentConfirm(comment, index)">삭제</li>
                                        </ul>
                                    </div>
                                </template>
                            </div>
                            <template v-if="comment.editMode">
                                <div v-click-outside="editCommentBtnOff" >
                                    <editor-menu-bar :editor="editor3" v-slot="{ commands }">
                                        <div class="menubar">
                                            <button class="mentionbtn" @click="editorPopupOn($event, commands)">
                                                멘션
                                            </button>
                                        </div>
                                    </editor-menu-bar>

                                    <div id="textarea-edit-input" class="editor textarea-input scroll-sm">
                                        <editor-content class="editor__content" :editor="editor3" />
                                    </div>

                                    <div class="suggestion-list scroll-sm" v-show="showSuggestions2" ref="suggestions2" @click.stop>
                                        <template v-if="hasResults2">
                                            <div
                                                    v-for="(user, index) in filteredUsers2"
                                                    :key="user.id"
                                                    class="suggestion-list__item"
                                                    :class="{ 'is-selected': navigatedUserIndex2 === index }"
                                                    @click="selectUser2(user)"
                                            >
                                                {{ user.name }}
                                            </div>
                                        </template>
                                        <div v-else class="suggestion-list__item is-empty">
                                            검색 결과 없음
                                        </div>
                                    </div>

                                    <label for="input_file_edit" class="filebtn">{{ commentEdit.fileName }}</label>
                                    <input type="file" class="input-img"
                                           accept=".doc, .docx, .dotx, .xlsx, .xls, .pdf, .hwp, .ppt, .pptx, .egg, .zip, .7z, .tar, .zipx"
                                           id="input_file_edit" @change="editUploadFile($event, commentEdit)">

                                    <button class="comment__btn"  @click="editComment(index, commentEdit)"></button>
                                </div>
                            </template>
                            <template v-else>
                                <div class="comment-con" v-html="comment.content"></div>
                                <button @click="fileDownloadConfirmOn(comment.fileUrl)" class="filebtn w-auto" v-if="comment.fileUrl">{{comment.fileName}}</button>
                            </template>
                        </div>
                    </li>
                </ul>
<!--                <div class="t-center mb-30" v-if="tempSaveBtn || cards[cardId].type === 0">-->
<!--                  <button class="btn btn-lg btn-primary" @click="tempSave()">임시저장</button>-->
<!--                </div>-->
            </div>

            <ul v-if="!viewMode && cards[cardId].type !== 0" class="card-modal__menu scroll-sm">
                <li class="card-modal__menu__item">
                    <h4 class="tit">메뉴 추가</h4>
                </li>
                <li class="card-modal__menu__item m-w-100">
                    <button class="fold" :class="{'active':!defaultMenu}" @click="btnDefaultMenuFold">기본메뉴</button>
                </li>
                <li class="card-modal__menu__item" v-if="defaultMenu">
                    <button class="image" @click="btnCardItemMenuOn('IMAGE')">이미지</button>
                    <button class="file" @click="btnCardItemMenuOn('FILE')">파일</button>
                    <button class="time" @click="btnCardItemMenuOn('DEADLINE')">마감기한</button>
                    <button v-if="!isMyPage" class="user" @click="btnCardItemMenuOn('PARTY')">참여자</button>
                    <button class="label" @click="btnCardItemMenuOn('LABEL')">라벨</button>
                    <button class="check" @click="btnCardItemMenuOn('CHECK')">체크리스트</button>
                </li>
                <li v-if="!isMyPage" class="card-modal__menu__item m-w-50">
                    <button class="etc" @click="btnMenu('brain')"
                            v-bind:class="{'on':menuState.brain}"
                    >브레인스토밍</button>
                </li>
                <li v-if="isMyPage && cards[cardId].type === 0" class="card-modal__menu__item m-w-50">
                    <button v-if="!menuState.classApply" class="etc" @click="classApplyModal = true"
                            v-bind:class="{'on':menuState.classApply}"
                    >수업개설신청</button>
                    <button v-else class="etc" @click="btnMenu('classApply')"
                            v-bind:class="{'on':menuState.classApply}"
                    >수업개설신청</button>
                </li>
                <!--                <li v-if="isMyPage && card.type === 0" class="card-modal__menu__item">-->
                <!--                    <button class="etc" @click="btnMenu('detaileOperApply')"-->
                <!--                            v-bind:class="{'on':menuState.detaileOperApply}"-->
                <!--                    >세부운영계획</button>-->
                <!--                </li>-->
                <li v-if="!isMyPage" class="card-modal__menu__item m-w-50">
                    <button class="etc" @click="btnMenu('problemAnalysis')"
                            v-bind:class="{'on':menuState.problemAnalysis}"
                    >문제분석</button>
                </li>
                <li v-if="!isMyPage" class="card-modal__menu__item m-w-50">
                    <button class="etc" @click="btnMenu('teamActivity')"
                            v-bind:class="{'on':menuState.teamActivity}"
                    >팀활동보고서</button>
                </li>
                <li v-if="cards[cardId].type !== 0" class="card-modal__menu__item m-w-50">
                    <button class="etc" @click="btnMenu('evaluation')"
                            v-bind:class="{'on':menuState.evaluation}"
                    >평가지</button>
                </li>
                <li v-if="!isMyPage" class="card-modal__menu__item m-w-50">
                    <button class="etc" @click="btnMenu('reflectionLog')"
                            v-bind:class="{'on':menuState.reflectionLog}"
                    >성찰</button>
                </li>
                <li v-if="isMyPage && cards[cardId].type === 13" class="card-modal__menu__item m-w-50">
                    <button class="etc" @click="btnMenu('operationResult')"
                            v-bind:class="{'on':menuState.operationResult}"
                    >운영결과<br class="m-none">요약보고서</button>
                </li>
            </ul>

            <div class="card-modal__btn">
                <template v-if="cards[cardId].type === 0">
                    <button class="btn-cancel w-50" @click="classCon(0)">취소</button>
                    <button class="btn-confirm w-50" @click="classCon(1)">확인</button>
                </template>
                <template v-else-if="viewMode">
                    <template v-if="isCardItem.userId === myUser.id">
                        <button class="btn-cancel" @click="$emit('modalOff')">취소</button>
                        <button class="btn-delete" @click="deleteModal=true">삭제</button>
                        <button class="btn-confirm" @click="updateModal=true">확인</button>
                    </template>
                    <template v-else-if="isCardItem.userId === myUser.id">
                        <button class="btn-cancel w-50" @click="$emit('modalOff')">취소</button>
                        <button class="btn-confirm w-50" @click="saveModal=true">확인</button>
                    </template>
                    <template v-else>
                        <button class="btn-confirm w-50" @click="$emit('modalOff')">확인</button>
                    </template>
                </template>
                <template v-else>
<!--                    <button class="btn-cancel w-50" @click="tempSaveBtn ? tempSaveModal=true : cancelModal=true">취소</button>-->
                    <button class="btn-cancel w-50" @click="cancelModal=true">취소</button>
                    <button class="btn-confirm w-50" @click="saveModal=true">확인</button>
                </template>
            </div>
        </div>

        <div class="popup" v-if="classApplyModal && itemId > -11" @mousedown.stop>
            <div class="card-modal__dim"></div>
            <div class="popup-wrap pb-40">
                <div class="popup-con scroll-sm t-center">
                    <button class="btn btn-blue btn-sm btn-full mb-10" @click="contentsInit('classApply', '1')">신규</button>
                    <button class="btn btn-blue btn-sm btn-full" @click="contentsInit('classApply', '2')">재운영</button>
                </div>
<!--                <div class="confirm-btn">-->
<!--                    <button class="btn w-100" @click="classApplyModal = false">닫기</button>-->
<!--                </div>-->
            </div>
        </div>

        <!-- 취소 -->
        <div class="popup" v-if="cancelModal" @mousedown.stop>
            <div class="popup__dim" @click="cancelModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    작성한 내용이 저장되지 않습니다.<br />
                    작성을 취소하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="cancelModal=false">취소</button>
                    <button class="btn w-50" @click="$emit('modalOff')">확인</button>
                </div>
            </div>
        </div>

        <!-- 삭제 -->
        <div class="popup" v-if="deleteModal" @mousedown.stop>
            <div class="popup__dim" @click="deleteModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    삭제하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="deleteModal=false">취소</button>
                    <button class="btn w-50" @click="cardItemDelete">확인</button>
                </div>
            </div>
        </div>

        <!-- 확인 -->
        <div class="popup" v-if="saveModal" @mousedown.stop>
            <div class="popup__dim" @click="saveModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    저장하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="saveModal=false">취소</button>
                    <button class="btn w-50" @click="cardItemAdd">확인</button>
                </div>
            </div>
        </div>

        <!-- 기여도 -->
        <div class="popup" v-if="perModal" @mousedown.stop>
            <div class="popup__dim" @click="perModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    기여도의 합계는 100%여야 합니다.
                </div>
                <div class="confirm-btn">
                    <button class="btn w-100" @click="perModal=false">확인</button>
                </div>
            </div>
        </div>

        <!-- update -->
        <div class="popup" v-if="updateModal" @mousedown.stop>
            <div class="popup__dim" @click="updateModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    수정하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="updateModal=false">취소</button>
                    <button class="btn w-50" @click="cardItemUpdate">확인</button>
                </div>
            </div>
        </div>

        <!-- brain save -->
        <div class="popup" v-if="brainSaveModal" @mousedown.stop>
            <div class="popup__dim" @click="brainSaveModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    저장하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="brainSaveModal=false">취소</button>
                    <button class="btn w-50" @click="addBrain">확인</button>
                </div>
            </div>
        </div>

        <!-- brain delete -->
        <div class="popup" v-if="brainDeleteModal" @mousedown.stop>
            <div class="popup__dim" @click="brainDeleteModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    삭제하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="brainDeleteModal=false">취소</button>
                    <button class="btn w-50" @click="delBrain(brainDeleteId)">확인</button>
                </div>
            </div>
        </div>

        <!-- alert -->
        <div class="popup" v-if="alertModal" @mousedown.stop>
            <div class="popup__dim" @click="alertModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    {{alertMsg}}
                </div>
                <div class="confirm-btn">
                    <button class="btn w-100" @click="focusEvent">확인</button>
                </div>
            </div>
        </div>

        <!-- 피드백 저장 -->
        <div class="popup" v-if="saveFeedModal" @mousedown.stop>
            <div class="popup__dim" @click="saveFeedModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    피드백을 저장하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="saveFeedModal=false">취소</button>
                    <button class="btn w-50" @click="saveFeedBack">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="saveFeedModalComplete" @mousedown.stop>
            <div class="popup__dim" @click="saveFeedModalComplete=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    피드백이 저장되었습니다.
                </div>
                <div class="confirm-btn">
                    <button class="btn w-100" @click="saveFeedModalComplete=false">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="fileDownloadConfirm" @mousedown.stop>
            <div class="popup__dim" @click="fileDownloadConfirm=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    다운 받으시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="fileDownloadConfirm=false">취소</button>
                    <a class="btn w-50" :href="fileDownloadLink" @click="fileDownloadConfirm=false">확인</a>
                </div>
            </div>
        </div>

        <div class="popup" v-if="delCommentConfirmModal" @mousedown.stop>
            <div class="popup__dim" @click="delCommentConfirmModal=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    삭제하시겠습니까?
                </div>
                <div class="confirm-btn">
                    <button class="btn fc-gray w-50" @click="delCommentConfirmModal=false">취소</button>
                    <button class="btn w-50" @click="delComment">확인</button>
                </div>
            </div>
        </div>

        <div class="popup" v-if="tempSaveModal" @mousedown.stop>
          <div class="popup__dim" @click="tempSaveModal=false"></div>
          <div class="popup-wrap">
            <div class="popup-con t-center">
              임시저장하시겠습니까?
            </div>
            <div class="confirm-btn">
              <button class="btn fc-gray w-50" @click="$emit('modalOff')">아니오</button>
              <button class="btn w-50" @click="tempSave()">네</button>
            </div>
          </div>
        </div>

        <div class="popup" v-if="tempSaveConfirmModal" @mousedown.stop>
          <div class="popup__dim" @click="$emit('modalOff')"></div>
          <div class="popup-wrap">
            <div class="popup-con t-center">
              임시저장되었습니다.
            </div>
            <div class="confirm-btn">
              <button class="btn w-100" @click="$emit('modalOff')">확인</button>
            </div>
          </div>
        </div>

        <div class="popup slide-pop" v-if="imgSlidePopup" @mousedown.stop>
            <div class="popup__dim" @click="imgSlidePopup=false"></div>
            <div class="popup-wrap">
                <div class="popup-con t-center">
                    <template>
                        <hooper :settings="imgSlide">
                            <slide v-for="(imgObj, index) in isCardItem.images">
                                <img v-if="applyId && viewMode" :src="imgObj.imgUrl+'/consultant/'+applyId" :alt="imgObj.fileName" />
                                <img v-else :src="imgObj.imgUrl" :alt="imgObj.fileName" />
                            </slide>
                            <hooper-navigation slot="hooper-addons"></hooper-navigation>
                        </hooper>
                    </template>
                </div>
            </div>
        </div>

        <div v-if="editorPopup"
             @mousedown.stop
             @click.stop
             v-bind:style="{ left: editorPopup.popX + 'px', top: editorPopup.popY + 'px' }"
             class="suggestion-list scroll-sm editor-pop" v-click-outside="editorPopupOff">
            <div
                    v-for="(user, index) in userList.filter(u => u.id !== this.myUser.id)"
                    :key="user.id"
                    class="suggestion-list__item"
                    @click="editorPopup.mention({ id: user.id, label: ' '+user.name+' ' }), editorPopup = null"
            >
                {{ user.name }}
            </div>
        </div>

    </div>
</template>
<script>
export default {
    props: [
        'flag',
        'cards',
        'cardId',
        'items',
        'itemId',
        'myUser',
        'myTeamId',
        'myClassObjectId',
        'isMyPage',
        'isTeamPage',
        'applyId',
        's_year',
        's_semester',
        'daehaks',
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
            test_: '',
            editor: new Editor({
                extensions: [
                    new Blockquote(),
                    new BulletList(),
                    new CodeBlock(),
                    new HardBreak(),
                    new Heading({ levels: [1, 2, 3] }),
                    new ListItem(),
                    new OrderedList(),
                    new TodoItem(),
                    new TodoList(),
                    new Link(),
                    new Bold(),
                    new Code(),
                    new Italic(),
                    new History(),
                ],
                content: '',
                editable: true,
            }),
            editor2: new Editor({
                extensions: [
                    new HardBreak(),
                    new Heading({ levels: [1, 2, 3] }),
                    new Mention({
                        // a list of all suggested items
                        items: async () => {
                            await new Promise(resolve => {
                                setTimeout(resolve, 500)
                            })
                            return this.userList.filter(u => u.id !== this.myUser.id);
                            // return [
                            //     { id: 1, name: 'Sven Adlung' },
                            //     { id: 2, name: 'Patrick Baber' },
                            //     { id: 3, name: 'Nick Hirche' },
                            //     { id: 4, name: 'Philip Isik' },
                            //     { id: 5, name: 'ㅂ' },
                            //     { id: 6, name: 'Philipp Kühn' },
                            //     { id: 7, name: 'Hans Pagel' },
                            //     { id: 8, name: 'Sebastian Schrama' },
                            // ]
                        },
                        // is called when a suggestion starts
                        onEnter: ({
                                      items, query, range, command, virtualNode,
                                  }) => {

                            this.query = query
                            this.filteredUsers = items

                            this.suggestionRange = range
                            this.vNode = virtualNode.getBoundingClientRect();
                            this.renderPopup(virtualNode)
                            // we save the command for inserting a selected mention
                            // this allows us to call it inside of our custom popup
                            // via keyboard navigation and on click
                            this.insertMention = command
                        },
                        // is called when a suggestion has changed
                        onChange: ({
                                       items, query, range, virtualNode,
                                   }) => {
                            this.query = query
                            this.filteredUsers = items
                            this.suggestionRange = range
                            this.navigatedUserIndex = 0
                            this.renderPopup(virtualNode)
                        },
                        // is called when a suggestion is cancelled
                        onExit: () => {
                            // reset all saved values
                            this.query = null
                            this.filteredUsers = []
                            this.suggestionRange = null
                            this.navigatedUserIndex = 0
                            this.destroyPopup()
                        },
                        // is called on every keyDown event while a suggestion is active
                        onKeyDown: ({ event }) => {
                            if (event.key === 'ArrowUp') {
                                this.upHandler()
                                return true
                            }
                            if (event.key === 'ArrowDown') {
                                this.downHandler()
                                return true
                            }
                            if (event.key === 'Enter') {
                                this.enterHandler()
                                return true
                            }
                            return false
                        },
                        // is called when a suggestion has changed
                        // this function is optional because there is basic filtering built-in
                        // you can overwrite it if you prefer your own filtering
                        // in this example we use fuse.js with support for fuzzy search
                        onFilter: async (items, query) => {
                            if (!query) {
                                return items
                            }
                            await new Promise(resolve => {
                                setTimeout(resolve, 500)
                            })
                            const fuse = new Fuse(items, {
                                threshold: 0.2,
                                keys: ['name'],
                            })
                            return fuse.search(query).map(item => item.item)
                        },
                    }),
                    new Code(),
                    new Bold(),
                    new Italic(),
                ],
                content: '',
            }),
            editor3: new Editor({
                extensions: [
                    new HardBreak(),
                    new Heading({ levels: [1, 2, 3] }),
                    new Mention({
                        // a list of all suggested items
                        items: async () => {
                            await new Promise(resolve => {
                                setTimeout(resolve, 500)
                            })
                            return this.userList.filter(u => u.id !== this.myUser.id);
                        },
                        // is called when a suggestion starts
                        onEnter: ({
                                      items, query, range, command, virtualNode,
                                  }) => {
                            this.query2 = query
                            this.filteredUsers2 = items

                            this.suggestionRange2 = range
                            this.vNode2 = virtualNode.getBoundingClientRect();
                            this.renderPopup2(virtualNode)
                            // we save the command for inserting a selected mention
                            // this allows us to call it inside of our custom popup
                            // via keyboard navigation and on click
                            this.insertMention2 = command
                        },
                        // is called when a suggestion has changed
                        onChange: ({
                                       items, query, range, virtualNode,
                                   }) => {
                            this.query2 = query
                            this.filteredUsers2 = items
                            this.suggestionRange2 = range
                            this.navigatedUserIndex2 = 0

                            this.renderPopup2(virtualNode)
                        },
                        // is called when a suggestion is cancelled
                        onExit: () => {
                            // reset all saved values
                            this.query2 = null
                            this.filteredUsers2 = []
                            this.suggestionRange2 = null
                            this.navigatedUserIndex2 = 0
                            this.destroyPopup2()
                        },
                        // is called on every keyDown event while a suggestion is active
                        onKeyDown: ({ event }) => {
                            if (event.key === 'ArrowUp') {
                                this.upHandler2()
                                return true
                            }
                            if (event.key === 'ArrowDown') {
                                this.downHandler2()
                                return true
                            }
                            if (event.key === 'Enter') {
                                this.enterHandler2()
                                return true
                            }
                            return false
                        },
                        // is called when a suggestion has changed
                        // this function is optional because there is basic filtering built-in
                        // you can overwrite it if you prefer your own filtering
                        // in this example we use fuse.js with support for fuzzy search
                        onFilter: async (items, query) => {
                            if (!query) {
                                return items
                            }
                            await new Promise(resolve => {
                                setTimeout(resolve, 500)
                            })
                            const fuse = new Fuse(items, {
                                threshold: 0.2,
                                keys: ['name'],
                            })
                            return fuse.search(query).map(item => item.item)
                        },
                    }),
                    new Code(),
                    new Bold(),
                    new Italic(),
                ],
                content: '',
            }),
            linkUrl: null,
            linkMenuIsActive: false,

            query: null,
            suggestionRange: null,
            filteredUsers: [],
            navigatedUserIndex: 0,
            insertMention: () => {},
            popup: null,
            editorPopup: null,
            vNode: null,

            query2: null,
            suggestionRange2: null,
            filteredUsers2: [],
            navigatedUserIndex2: 0,
            insertMention2: () => {},
            popup2: null,
            vNode2: null,

            myIndex: 0,
            componentKey: 0,
            render: [],
            isCardItem: '',
            year: '',
            hour: '-1',
            min: '-1',
            menuState: {
                default: true,
                brain: false,                  //브레인스토밍
                classApply: false,             //수업신청          [교수만 노출]
                detaileOperApply: false,       //세부운영계획서     [교수만 노출]
                problemAnalysis: false,        //문제분석
                teamActivity: false,           //팀활동보고서
                evaluation: false,             //평가지
                reflectionLog: false,          //성찰일지
                operationResult: false,        //운영결과 요약보고서 [교수만 노출]
            },
            commentContent: '',
            commentContent_: '',
            //브레인스토밍
            brainContent: '',
            brainColor: '1',
            brainIndex: -1,
            userList: [],
            pUserList: [],
            userListMode: false,
            defaultMenu:true,


            //index
            commentFileName : '파일',
            commentFileUrl : '',
            commentFile : '',
            commentMenu : '',

            commentEdit : '',

            referenceData : '-1',

            isMenuType: 0,
            viewMode: false,
            reg: /(?!\s*@[0-9a-zA-Z가-힣ㄱ-ㅎ]+\s*<\/a>)(@[0-9a-zA-Z가-힣ㄱ-ㅎ]+)/g,
            radioFlag: false,
            classApplyModal: false,
            cancelModal: false,
            deleteModal: false,
            saveModal: false,
            updateModal: false,
            brainSaveModal: false,
            brainDeleteModal: false,
            brainDeleteId: '',
            alertModal: false,
            alertMsg: '',
            isMobileCheck: false,
            focusTarget: '',
            imgSlidePopup: false,
            imgSlide: {
                itemsToShow: 1,
                centerMode: true,
                trimWhiteSpace: true,
            },
            mentionPopup: false,
            saveFeedModal: false,
            saveFeedModalComplete: false,
            classList:[],
            classListSelet:'-1',
            applyMode: null,
            fileDownloadConfirm: false,
            fileDownloadLink: '',
            delCommentConfirmModal: false,
            delCommentTarget: '',
            delCommentInx: '',
            perModal: false,
            focusState: '',
            department: [],
            tDepartment: '-1',
            isTD: false,
            tempSaveBtn: false,
            tempSaveModal: false,
            tempSaveConfirmModal: false,
        }
    },
    created() {     //렌더링이 되기전
        this.init();
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
        if(this.viewMode){
            // console.log('UPDATE cardID:: '+this.items[this.itemId].cardId);
            // console.log('UPDATE itemID:: '+this.itemId);
            // console.log('isCardItem:: ' + this.isCardItem.type);


        }

        // console.log(this.department);
        // console.log(this.tDepartment);
        if (this.isTD && this.isMyPage) {
          this.isCardItem.with_class_apply != undefined ? this.tDepartment = this.isCardItem.with_class_apply.department : '';
          this.isTD = false;
        }
      // const temp = this.tDepartment;
      //   this.tDepartment = 0;
      // this.tDepartment = temp;
    },
    beforeDestroy() {
        this.editor.destroy();
        this.editor2.destroy();
        this.editor3.destroy();
    },
    computed: {
        hasResults() {
            return this.filteredUsers.length;
        },
        showSuggestions() {
            return this.query || this.hasResults;
        },

        hasResults2() {
            return this.filteredUsers2.length;
        },
        showSuggestions2() {
            return this.query2 || this.hasResults2;
        },
    },
    methods: {
        editorPopupOn(e,com){
            // console.log(e.target.getBoundingClientRect());
            // console.log(e.target.clientY);
            this.editorPopup = com;
            this.editorPopup.popX = e.target.getBoundingClientRect().x;
            this.editorPopup.popY = e.target.getBoundingClientRect().y;
        },
        editorPopupOff(){
            this.editorPopup = null;
        },
        upHandler() {
            this.navigatedUserIndex = ((this.navigatedUserIndex + this.filteredUsers.length) - 1) % this.filteredUsers.length;
        },
        downHandler() {
            this.navigatedUserIndex = (this.navigatedUserIndex + 1) % this.filteredUsers.length;
        },
        enterHandler() {
            const user = this.filteredUsers[this.navigatedUserIndex];
            if (user) {
                this.selectUser(user);
            }
        },
        selectUser(user) {
            this.insertMention({
                range: this.suggestionRange,
                attrs: {
                    id: user.id,
                    label: ' ' + user.name + ' ',
                },
            })
        },
        renderPopup(node) {
            const { x, y } = node.getBoundingClientRect();

            if (x === 0 && y === 0) {
                return
            }

            if (this.popup) {
                return
            }

            // ref: https://atomiks.github.io/tippyjs/v6/all-props/
            this.popup = tippy('.card-modal', {
                // getReferenceClientRect: () => node.getBoundingClientRect(),
                getReferenceClientRect: () => this.vNode,
                appendTo: () => document.body,
                interactive: true,
                sticky: true, // make sure position of tippy is updated when content changes
                plugins: [sticky],
                content: this.$refs.suggestions,
                trigger: 'mouseenter', // manual
                showOnCreate: true,
                theme: 'dark',
                placement: 'top-start',
                inertia: true,
                duration: [400, 200],
            })
        },
        destroyPopup() {
            if (this.popup) {
                this.popup[0].destroy();
                this.popup = null;
            }
        },
        beforeDestroy() {
            this.destroyPopup();
        },
        upHandler2() {
            this.navigatedUserIndex2 = ((this.navigatedUserIndex2 + this.filteredUsers2.length) - 1) % this.filteredUsers2.length;
        },
        downHandler2() {
            this.navigatedUserIndex2 = (this.navigatedUserIndex2 + 1) % this.filteredUsers2.length;
        },
        enterHandler2() {
            const user = this.filteredUsers2[this.navigatedUserIndex2];
            if (user) {
                this.selectUser2(user);
            }
        },
        selectUser2(user) {
            this.insertMention2({
                range: this.suggestionRange2,
                attrs: {
                    id: user.id,
                    label: ' ' + user.name + ' ',
                },
            })
            this.editor3.focus();
        },
        renderPopup2(node) {
            const { x, y } = node.getBoundingClientRect();
            if (x === 0 && y === 0) {
                return
            }

            if (this.popup2) {
                return
            }

            // ref: https://atomiks.github.io/tippyjs/v6/all-props/

            this.popup2 = tippy('#textarea-edit-input', {
                // getReferenceClientRect: () => node.getBoundingClientRect(),
                getReferenceClientRect: () => this.vNode2,
                appendTo: () => document.body,
                interactive: true,
                sticky: true, // make sure position of tippy is updated when content changes
                plugins: [sticky],
                content: this.$refs.suggestions2[0],
                trigger: 'mouseenter', // manual
                showOnCreate: true,
                theme: 'dark',
                placement: 'top-start',
                inertia: true,
                duration: [400, 200],
            })
        },
        destroyPopup2() {
            if (this.popup2) {
                this.popup2[0].destroy();
                this.popup2 = null;
            }
        },
        showLinkMenu(attrs) {
            this.linkUrl = attrs.href
            this.linkMenuIsActive = true
            this.$nextTick(() => {
                this.$refs.linkInput.focus()
            })
        },
        hideLinkMenu() {
            this.linkUrl = null
            this.linkMenuIsActive = false
        },
        setLinkUrl(command, url) {
            command({ href: url })
            this.hideLinkMenu()
        },
        dragCallBack(){
            if(this.isCardItem.userId !== this.myUser.id){
                return false;
            }
        },
        forceRerender() {
            this.componentKey += 1;
        },
        init() {
          console.log(this.itemId);
            if(this.flag === 'ADD'){
                if (this.itemId > -10) {
                  this.isCardItem = {
                    id: null,
                    userId: this.myUser.id,
                    cardId: this.cardId,
                    title: '',
                    content: '',
                    brain: [],                          //브레인스토밍 순서
                    images: [],
                    files: [],
                    imagesNum: [],

                    comments: [],
                    deadLine: '',
                    party: [],
                    label: '0',
                    checks: [
                      {
                        checked: false,
                        content: '',
                      },
                    ],

                    activationDeadline: false,  //마감기한
                    activationImage: true,      //이미지
                    activationFile: false,       //파일
                    activationParty: false,     //참여자
                    activationLabel: false,     //라벨
                    activationCheck: false,     //체크리스트

                    detaileOperApply: [],       //세부운영계획서     [교수만 노출]
                    detailOperApplyFiles: [
                      {
                        fileName: '',
                      },
                    ],

                    with_problem_analysis: '',                         //문제분석
                    with_team_activity: '',           //팀활동보고서
                    with_reflection_log: '',          //성찰일지
                    with_class_apply: '',             //수업신청          [교수만 노출]
                    with_operation_result: '',               //운영결과
                    with_brains: {},                  //브레인스토밍

                    evaluation: [],             //평가지

                    operationResult: [],        //운영결과 요약보고서 [교수만 노출]userId: this.myUser.id,
                  }
                }
                else {
                  this.isCardItem = this.items[this.itemId];
                }

                this.userFetch();

                if(this.cards[this.cardId].type === 0){
                    this.menuState.default = false;
                    this.menuState.classApply = true;
                    this.isMenuType = 4;

                    this.classApplyModal = true;
                }
            }
            else if(this.flag === 'VIEW'){
                if(this.cards[this.cardId].type === 0){
                    this.menuState.default = false;
                }

                this.viewMode = true;
                this.items[this.itemId].isNew = false;

                this.cardItemFetch('INIT');
            }
        },
        cardItemFetch(type){
            window.Rest.fetch(
                    {
                        id: this.itemId,
                        types: 'item',
                        subTypes: 'item',
                        subId: this.applyId,
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

                                __result.files = [];
                                //스택 파일
                                if(_result[0].with_files){
                                    for(let i=0;i<_result[0].with_files.length;i++){
                                        _result[0].with_files[i].imgFile = '',
                                                __result.files.push(_result[0].with_files[i]);
                                    }
                                }

                                if(__result.type === 3){     //브레인일경우 타입 가공
                                    let _brains = _.cloneDeep(__result.with_brains);
                                    __result.with_brains = {};

                                    if(__result.brain){
                                        for(let i=0;i<__result.brain.length;i++){
                                            for(let j=0;j<_brains.length;j++){
                                                if(_brains[j].id === __result.brain[i]){
                                                    const _id = __result.brain[i];
                                                    const _year = new Date(_brains[j].updated_at).getFullYear();
                                                    const _month = String(new Date(_brains[j].updated_at).getMonth() + 1).padStart(2,'0');
                                                    const _day = String(new Date(_brains[j].updated_at).getDate()).padStart(2,'0');
                                                    const _hour = String(new Date(_brains[j].updated_at).getHours()).padStart(2,'0')
                                                    const _min = String(new Date(_brains[j].updated_at).getMinutes()).padStart(2,'0')

                                                    Vue.set(_brains[j], 'name', _brains[j].with_user.name);
                                                    Vue.set(_brains[j], 'date', _year+'-'+_month+'-'+_day+' '+_hour+':'+_min);
                                                    Vue.set(__result.with_brains, _id, _brains[j]);
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }

                                if(type === 'INIT'){
                                    this.isCardItem = __result;
                                    this.isCardItem.comments = [];

                                    if(!this.isMyPage && this.itemId > -10){
                                        this.cardItemCommentFetch(type);
                                    }

                                    if(this.isCardItem.userId !== this.myUser.id){
                                        this.editor.setOptions({
                                            editable: false,
                                        })
                                    }
                                }
                                else if(type === 'UPDATE'){
                                    __result.comments = this.isCardItem.comments;
                                    this.isCardItem = __result;
                                    this.items[this.itemId].cardId = __result.cardId;
                                }

                                this.userFetch();

                                if(!this.isCardItem.images){
                                    this.isCardItem.images = [];
                                    this.isCardItem.imagesNum = [];
                                }

                                if(!this.isCardItem.files){
                                    this.isCardItem.files = [];
                                }

                                if(this.isCardItem.activationDeadline){
                                    const date_ =  this.isCardItem.deadLine.split(' ');
                                    const ymd = date_[0];
                                    const hm = date_[1].split(':');

                                    this.year = ymd;
                                    this.hour = Number(hm[0]) + '';
                                    this.min = Number(hm[1]) + '';
                                }

                                this.editor.setContent(this.isCardItem.content);

                                console.log('ITEM----------------');
                                console.log(this.isCardItem);
                                console.log('--------------------');

                                if(this.cards[this.cardId].type === 0){
                                    this.menuState.default = false;
                                    this.menuState.classApply = true;

                                }

                                // this.department = this.daehaks[this.isCardItem.with_class_apply.daehak];
                                this.isTD = true;
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        cardItemCommentFetch(type){
            window.Rest.fetch(
                    {
                        id: this.isCardItem.id,
                        types: 'comment',
                        subTypes: 'comment'
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;

                            if(_result.hasOwnProperty('fail')){
                                alert(_result.fail);
                            }
                            else{
                                let tempComments = [];
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

                                    if(type === 'INIT'){
                                        this.isCardItem.comments.push({
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
                                    }
                                    else if(type === 'UPDATE'){
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
                                    }
                                })

                                if(type === 'UPDATE'){
                                    this.isCardItem.comments = tempComments;
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        cardItemAdd(){
            const backItem = _.cloneDeep(this.isCardItem);

            //DeadLine
            if(this.isCardItem.activationDeadline){
                this.isCardItem.deadLine = this.year + ' ' + this.hour + ':' + this.min;
            }
            else{
                this.isCardItem.deadLine = '';
            }

            //content
            this.isCardItem.content = this.editor.getHTML();

            //Label
            if(!this.isCardItem.activationLabel){
                this.isCardItem.label = '0';
            }

            if(!this.isCardItem.activationCheck){
                this.isCardItem.checks = [{
                    checked: '',
                    content: '',
                }]
            }

            let fileForm = [];
            for(let i=0;i<this.isCardItem.images.length;i++){
                if(this.isCardItem.images[i].id === -1){
                    fileForm.push(this.isCardItem.images[i].imgFile);
                }
            }

            let itemFileForm = [];
            for(let i=0;i<this.isCardItem.files.length;i++){
                if(this.isCardItem.files[i].id === -1){
                    itemFileForm.push(this.isCardItem.files[i].imgFile);
                }
            }


            let applyFiles = [];

            if(this.isMenuType === 3){
                let _brains = [];
                for(let i=0;i<this.isCardItem.brain.length;i++){
                    const _id = this.isCardItem.brain[i];
                    _brains.push(this.isCardItem.with_brains[_id])
                }

                this.isCardItem.with_brains = [];
                if(_brains.length){
                    this.isCardItem.with_brains = _.cloneDeep(_brains);
                }

            }

            this.isCardItem.images = fileForm;


            if(this.isMyPage){
                this.isCardItem.with_class_apply.department = this.tDepartment;
            }


            const data__ = {
                id: this.cardId,
                subId: this.applyId,
                type: this.isMenuType,
                types: 'item',
                title: this.isCardItem.title,
                content: this.isCardItem.content,
                deadLine: this.isCardItem.deadLine,
                label: this.isCardItem.label,
                checks: this.isCardItem.checks,
                images: fileForm,
                files: itemFileForm,
                applyFiles: applyFiles,

                party: this.isCardItem.party,
                brain: this.isCardItem.brain,

                activationDeadline: this.isCardItem.activationDeadline,  //마감기한
                activationImage: this.isCardItem.activationImage,     //이미지
                activationFile: this.isCardItem.activationFile,       //파일
                activationParty: this.isCardItem.activationParty,     //참여자
                activationLabel: this.isCardItem.activationLabel,     //라벨
                activationCheck: this.isCardItem.activationCheck,     //체크리스트

                problemAnalysis: this.isCardItem.with_problem_analysis,
                reflectionLog: this.isCardItem.with_reflection_log,
                classApply: this.isCardItem.with_class_apply,
                teamActivity: this.isCardItem.with_team_activity,
                operationResult: this.isCardItem.with_operation_result,
                brains: this.isCardItem.with_brains,
            };

            if(this.isMenuType === 4){
                data__.title = data__.classApply.korName;
            }

            console.log('TEST----------------');
            console.log(data__);
            console.log('--------------------');

            window.Rest.create(
                    data__
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;

                            // validation alert
                            if(_result.sub){
                                for (const [key, value] of Object.entries(_result[0])) {
                                    this.alertMsg = `${value}`;
                                    this.focusTarget = `${key}`;
                                    if(`${key}`.indexOf('.') === 1){
                                      this.focusState = 'several';
                                      if(`${key}`.indexOf('name') > 0){
                                        this.alertMsg = '성명을 입력해 주세요.'
                                      }
                                      else if(`${key}`.indexOf('org') > 0){
                                        this.alertMsg = '소속을 입력해 주세요.'
                                      }
                                      else if(`${key}`.indexOf('tel') > 0){
                                        this.alertMsg = '연구실 번호를 입력해 주세요.'
                                      }
                                      else if(`${key}`.indexOf('phone') > 0){
                                        this.alertMsg = '핸드폰 번호를 입력해 주세요.'
                                      }
                                      else if(`${key}`.indexOf('email') > 0){
                                        this.alertMsg = '이메일을 입력해 주세요.'
                                      }
                                      else if(`${key}`.indexOf('per') > 0){
                                        this.alertMsg = '기여도를 입력해 주세요.'
                                      }
                                    }

                                    this.alertModal = true;
                                    this.saveModal = false;
                                    break;
                                }
                                document.getElementsByClassName('menu')[2] !== undefined ? document.getElementsByClassName('menu')[2].click() : '';
                            }
                            else{
                               this.cards[this.cardId].type !== 0 ? this.btnMenu('default') : '';
                            }

                            if(_result.hasOwnProperty('fail')){
                                if(_result[0].title && !_result.sub){
                                    this.alertMsg = '제목을 입력해 주세요.';
                                    this.focusTarget = 'title';
                                }else if(_result[0].deadLine && !_result.sub){
                                    this.alertMsg = '마감기한을 설정해 주세요.';
                                    this.focusTarget = 'deadLine';
                                }else if(_result[0].checks && !_result.sub){
                                    this.alertMsg = '체크리스트 내용을 전부 입력해 주세요.';
                                    this.focusTarget = 'checkList';
                                    this.focusState = 'several';
                                }
                                else{
                                    // this.alertMsg = _result.fail;
                                }
                                this.isCardItem = backItem;
                                this.alertModal = true;
                                this.saveModal = false;
                                // alert(_result.fail);
                            }
                            else{
                                if(_result[0]){
                                    // if(!(this.cards[this.cardId].type === 10 || this.cards[this.cardId].type === 7 || this.cards[this.cardId].type === 9)){

                                    let __result = _.cloneDeep(_result[0]);

                                    if(_result[0].images){
                                        let imagesNum = _.cloneDeep(_result[0].images);
                                        let images = _.cloneDeep(_result[0].with_images);
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
                                    console.log(__result);
                                    this.cards[this.cardId].itemsNum.unshift(__result.id);
                                    // }

                                    this.$emit('modalOff');
                                }
                            }
                        }
                        else{
                            this.isCardItem = backItem;
                            console.log('FETCH FAIL');
                        }
                    }
            )
            console.log('-------------------');
        },
        tempSave(){
          const backItem = _.cloneDeep(this.isCardItem);

          //DeadLine
          if(this.isCardItem.activationDeadline){
            this.isCardItem.deadLine = this.year + ' ' + this.hour + ':' + this.min;
          }
          else{
            this.isCardItem.deadLine = '';
          }

          //content
          this.isCardItem.content = this.editor.getHTML();

          //Label
          if(!this.isCardItem.activationLabel){
            this.isCardItem.label = '0';
          }

          if(!this.isCardItem.activationCheck){
            this.isCardItem.checks = [{
              checked: '',
              content: '',
            }]
          }

          let fileForm = [];
          for(let i=0;i<this.isCardItem.images.length;i++){
            if(this.isCardItem.images[i].id === -1){
              fileForm.push(this.isCardItem.images[i].imgFile);
            }
          }

          let itemFileForm = [];
          for(let i=0;i<this.isCardItem.files.length;i++){
            if(this.isCardItem.files[i].id === -1){
              itemFileForm.push(this.isCardItem.files[i].imgFile);
            }
          }

          let applyFiles = [];

          this.isCardItem.images = fileForm;

          if(this.isMyPage){
            if (typeof this.isCardItem.with_class_apply != 'string') {
              this.isCardItem.with_class_apply.department = this.tDepartment;
            }
          }

          // let keyName = this.cardId;
          // let data__ = {};
          // data__[this.cardId] = {

          const len = JSON.parse(localStorage.getItem('tempCard'));

          let data__ = {
            id:  -10 + (JSON.parse(localStorage.getItem('tempCard')) != null ? -1 * (1 + (len ? len.length : 0)) : -1), //아이템아이디
            cardId: this.cardId,
            classId: this.myClassObjectId, //수업아이디
            userId: this.myUser.id, //작성자아이디
            type: this.isMenuType,
            types: 'item',
            title: '[임시저장]' + this.isCardItem.title,
            content: this.isCardItem.content,
            deadLine: this.isCardItem.deadLine,
            label: this.isCardItem.label,
            checks: this.isCardItem.checks,
            images: [],//fileForm,
            files: [],//itemFileForm,
            // applyFiles: applyFiles,

            party: this.isCardItem.party,
            // brain: this.isCardItem.brain,

            activationDeadline: this.isCardItem.activationDeadline,  //마감기한
            activationImage: this.isCardItem.activationImage,     //이미지
            activationFile: this.isCardItem.activationFile,       //파일
            activationParty: this.isCardItem.activationParty,     //참여자
            activationLabel: this.isCardItem.activationLabel,     //라벨
            activationCheck: this.isCardItem.activationCheck,     //체크리스트

            // problemAnalysis: this.isCardItem.with_problem_analysis,
            // reflectionLog: this.isCardItem.with_reflection_log,
            // classApply: this.isCardItem.with_class_apply,
            // teamActivity: this.isCardItem.with_team_activity,
            // operationResult: this.isCardItem.with_operation_result,

            basicApplyId: null,
            consultingApplyId: null,

            comments: [],

            with_basic_apply: null,
            with_brains: [],
            with_class_apply: this.isCardItem.with_class_apply,
            with_comments: [],
            with_consulting_apply: null,
            with_images: [],
            with_operation_result: null,
            with_problem_analysis: null,
            with_reflection_log: null,
            with_team_activity: null,
            // brains: this.isCardItem.with_brains,
          };

          if(this.isMenuType === 4){
            data__.title = '[임시저장]' + data__.with_class_apply.korName;
          }

          let tempCard = [];

          JSON.parse(localStorage.getItem('tempCard')) != null ? tempCard = JSON.parse(localStorage.getItem('tempCard')) : '';
          tempCard.push(data__);
          localStorage.setItem('tempCard',JSON.stringify(tempCard));

          this.tempSaveModal = false;
          this.tempSaveConfirmModal = true;
        },
        cardItemDelete(){
            window.Rest.delete(
                    {
                        id: this.isCardItem.id,
                        types: 'item',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                this.alertMsg = _result.fail;
                                this.alertModal = true;
                                this.deleteModal = false;
                                //alert(_result.fail);
                            }
                            else{
                                if(_result.success){
                                    if(this.isCardItem.type === 1 || this.isCardItem.type === 2){

                                        for(let key in this.cards){
                                            if(this.cards[key].itemsNum){
                                                for(let j=0;j<this.cards[key].itemsNum.length;j++){
                                                    const itemId = this.cards[key].itemsNum[j];
                                                    if(this.items[itemId].id === this.isCardItem.id){
                                                        this.cards[key].itemsNum.splice(j, 1);
                                                        break;
                                                    }
                                                }
                                            }
                                        }

                                    }else{
                                        const cId = this.items[this.itemId].cardId;
                                        const idx = this.cards[cId].itemsNum.indexOf(this.itemId, 0);
                                        this.cards[cId].itemsNum.splice(idx, 1);
                                    }

                                    delete this.items[re.data.id];



                                    this.$emit('modalOff');
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        cardItemUpdate(){
            const backItem = _.cloneDeep(this.isCardItem);

            //DeadLine
            if(this.isCardItem.activationDeadline){
                this.isCardItem.deadLine = this.year + ' ' + this.hour + ':' + this.min;
            }
            else{
                this.isCardItem.deadLine = '';
            }

            //content
            this.isCardItem.content = this.editor.getHTML();


            //Label
            if(!this.isCardItem.activationLabel){
                this.isCardItem.label = '0';
            }

            if(!this.isCardItem.activationCheck){
                this.isCardItem.checks = [{
                    checked: '',
                    content: '',
                }]
            }

            let imagesList = [];
            let imagesNum = [];

            if(this.isCardItem.images){
                for(let i=0;i<this.isCardItem.images.length;i++){
                    if(this.isCardItem.images[i].id === -1){
                        imagesList.push(this.isCardItem.images[i].imgFile);
                        imagesNum.push(0);
                    }
                    else{
                        imagesNum.push(this.isCardItem.images[i].id);
                    }
                }
            }

            this.isCardItem.images = imagesList;
            this.isCardItem.imagesNum = imagesNum;


            let itemFileList = [];
            let itemFileNum = [];
            if(this.isCardItem.files){
                for(let i=0;i<this.isCardItem.files.length;i++){
                    if(this.isCardItem.files[i].id === -1){
                        itemFileList.push(this.isCardItem.files[i].imgFile);
                        itemFileNum.push(0);
                    }
                    else{
                        itemFileNum.push(this.isCardItem.files[i].id);
                    }
                }
            }

            this.isCardItem.files = itemFileList;
            this.isCardItem.filesNum = itemFileNum;

            console.log('ITEM UPDATE ------');
            console.log(this.isCardItem);

            if(this.isMyPage){
                this.isCardItem.with_class_apply.department = this.tDepartment;
            }


            let __data = {
                id: this.isCardItem.id,
                subId: this.applyId,
                type: this.isCardItem.type,
                types: 'item',
                subTypes: 'edit',
                title: this.isCardItem.title,
                content: this.isCardItem.content,
                deadLine: this.isCardItem.deadLine,
                label: this.isCardItem.label,
                checks: this.isCardItem.checks,
                images: this.isCardItem.images,
                imagesNum: this.isCardItem.imagesNum,
                files: this.isCardItem.files,
                filesNum: this.isCardItem.filesNum,

                party: this.isCardItem.party,
                brain: this.isCardItem.brain,

                activationDeadline: this.isCardItem.activationDeadline,  //마감기한
                activationImage: this.isCardItem.activationImage,     //이미지
                activationFile: this.isCardItem.activationFile,       //파일
                activationParty: this.isCardItem.activationParty,     //참여자
                activationLabel: this.isCardItem.activationLabel,     //라벨
                activationCheck: this.isCardItem.activationCheck,     //체크리스트

                problemAnalysis: this.isCardItem.with_problem_analysis,
                reflectionLog: this.isCardItem.with_reflection_log,
                teamActivity: this.isCardItem.with_team_activity,
                operationResult: this.isCardItem.with_operation_result,
            }

            if(this.isCardItem.type === 4){
                if(this.isCardItem.with_class_apply.state === 'wait'){
                    __data.classApply = this.isCardItem.with_class_apply;
                    __data.title = __data.classApply.korName;
                }
            }


            window.Rest.update(__data).then(
                    (re) => {
                        if(re){
                            const _result = re.data;

                            // validation alert
                            if(_result.sub){
                                for (const [key, value] of Object.entries(_result[0])) {
                                    this.alertMsg = `${value}`;
                                    this.focusTarget = `${key}`;
                                    if(`${key}`.indexOf('.') === 1){
                                        this.focusState = 'several';
                                        if(`${key}`.indexOf('name') > 0){
                                          this.alertMsg = '성명을 입력해 주세요.'
                                        }
                                        else if(`${key}`.indexOf('org') > 0){
                                          this.alertMsg = '소속을 입력해 주세요.'
                                        }
                                        else if(`${key}`.indexOf('tel') > 0){
                                          this.alertMsg = '연구실 번호를 입력해 주세요.'
                                        }
                                        else if(`${key}`.indexOf('phone') > 0){
                                          this.alertMsg = '핸드폰 번호를 입력해 주세요.'
                                        }
                                        else if(`${key}`.indexOf('email') > 0){
                                          this.alertMsg = '이메일을 입력해 주세요.'
                                        }
                                        else if(`${key}`.indexOf('per') > 0){
                                          this.alertMsg = '기여도를 입력해 주세요.'
                                        }
                                    }

                                  this.alertModal = true;
                                  this.saveModal = false;
                                  break;
                              }
                              document.getElementsByClassName('menu')[2] !== undefined ? document.getElementsByClassName('menu')[2].click() : '';
                            }
                            else{
                              this.cards[this.cardId].type !== 0 ? this.btnMenu('default') : '';
                            }

                            if(_result.hasOwnProperty('fail')){
                                if(_result[0].title && !_result.sub){
                                    this.alertMsg = '제목을 입력해 주세요.';
                                    this.focusTarget = 'title';
                                }else if(_result[0].deadLine && !_result.sub){
                                    this.alertMsg = '마감기한을 설정해 주세요.';
                                    this.focusTarget = 'deadLine';
                                }
                                else if(_result[0].checks && !_result.sub){
                                  this.alertMsg = '체크리스트 내용을 전부 입력해 주세요.';
                                  this.focusTarget = 'checkList';
                                  this.focusState = 'several';
                                }
                                else{
                                    // this.alertMsg = _result.fail;
                                }
                                this.isCardItem = backItem;
                                this.alertModal = true;
                                this.updateModal = false;
                                //alert(_result.fail);
                            }
                            else{
                                if(_result[0]){
                                    // const po = this.searchIndex('item',_result[0].id);

                                    // const tempData = _.cloneDeep(this.cards[po.cIdx].item[po.iIdx].with_class_apply);
                                    const tempData = _.cloneDeep(this.items[this.itemId].with_class_apply);
                                    const tempData2 = _.cloneDeep(this.isCardItem.comments);

                                    this.items[this.itemId] = _.cloneDeep(_result[0]);

                                    if(_result[0].imagesNum) {
                                        this.items[this.itemId].images = [];
                                        this.items[this.itemId].imagesNum = _result[0].imagesNum;

                                        for (let i = 0; i < _result[0].imagesNum.length; i++) {
                                            for (let j = 0; j < _result[0].images.length; j++) {
                                                if (_result[0].images[j].id === _result[0].imagesNum[i]) {
                                                    this.items[this.itemId].images.push(_result[0].images[j]);
                                                    if(i===0)
                                                        this.items[this.itemId].image = _result[0].images[j];
                                                    break;
                                                }
                                            }
                                        }
                                    }

                                    this.items[this.itemId].with_class_apply = tempData;
                                    this.items[this.itemId].comments = tempData2;

                                    //1. 무엇을 기준으로 잡고 파라미터를 더 날려줄 것인가.
                                    if(this.isCardItem.type === 1 || this.isCardItem.type === 2){

                                    }
                                    else{

                                    }
                                }

                                this.updateModal = false;
                                this.$emit('modalOff');
                            }
                        }
                        else{
                            this.isCardItem = backItem;
                            console.log('FETCH FAIL');
                        }
                    }
            )
            console.log('-------------------');
        },
        btnCardItemMenuOn(type){
            if(type === 'DEADLINE'){
                if(this.isCardItem.activationDeadline){
                    this.isCardItem.activationDeadline = false;
                }
                else{

                    this.isCardItem.activationDeadline = true;
                }
            }
            else if(type === 'IMAGE'){
                if(this.isCardItem.activationImage){
                    this.isCardItem.activationImage = false;
                }
                else{
                    this.isCardItem.activationImage = true;
                }
            }
            else if(type === 'FILE'){
                if(this.isCardItem.activationFile){
                    this.isCardItem.activationFile = false;
                }
                else{
                    this.isCardItem.activationFile = true;
                }
            }
            else if(type === 'PARTY'){
                if(this.isCardItem.activationParty){
                    this.isCardItem.activationParty = false;
                }
                else{
                    this.isCardItem.activationParty = true;
                }
            }
            else if(type === 'LABEL'){
                if(this.isCardItem.activationLabel){
                    this.isCardItem.label = '0';
                    this.isCardItem.activationLabel = false;
                }
                else{
                    this.isCardItem.label = '0';
                    this.isCardItem.activationLabel = true;
                }
            }
            else if(type === 'CHECK'){
                if(this.isCardItem.activationCheck){
                    this.isCardItem.activationCheck = false;
                }
                else{
                    this.isCardItem.activationCheck = true;
                }
            }


            //다른 탭 메뉴 다 꺼주기
            // for (let key in this.menuState){
            //     this.menuState[key] = false;
            // }

        },
        uploadImage(e){
            this.isCardItem.images.push({
                id: -1,
                imgUrl: URL.createObjectURL(e.target.files[0]),
                imgFile: e.target.files[0],
                fileName: e.target.files[0].name,
            });

            this.forceRerender();
        },
        deleteImage(idx){
            // for(let i=0;i<this.isCardItem.imagesNum.length;i++){
            //     if(this.isCardItem.imagesNum[i] === this.isCardItem.images[idx].id){
            //         this.isCardItem.imagesNum.splice(i,1);
            //         this.isCardItem.imagesNum.splice(i,1);
            //         break;
            //     }
            // }

            this.isCardItem.images.splice(idx,1);
        },
        itemUploadFile(e){
            this.isCardItem.files.push({
                id: -1,
                imgUrl: URL.createObjectURL(e.target.files[0]),
                imgFile: e.target.files[0],
                fileName: e.target.files[0].name,
            });

            this.forceRerender();
        },
        itemDeleteFile(idx){
            this.isCardItem.files.splice(idx,1);
        },
        btnCardItemMenuCheck(flag, idx){
            if(flag === 'ADD'){
                this.isCardItem.checks.push({
                    checked: false,
                    content: '',
                });
            }
            else if(flag === 'DELETE'){
                this.isCardItem.checks.splice(idx, 1);
            }
        },
        btnApplicantList(flag, idx){
            if(flag === 'ADD'){
                this.isCardItem.with_class_apply.applicant.push({
                    name: '',
                    org: '',
                    tel: '',
                    phone: '',
                    email: '',
                });
            }
            else if(flag === 'DELETE'){
                this.isCardItem.with_class_apply.applicant.splice(idx, 1);
            }
        },
        btnContributeList(flag, idx){
            if(flag === 'ADD'){
                this.isCardItem.with_class_apply.contribute.push({
                    name: '',
                    per: '',
                });
            }
            else if(flag === 'DELETE'){
                this.isCardItem.with_class_apply.contribute.splice(idx, 1);
            }
        },
        btnMenu(type, mode){
            if(!this.menuState[type]){
                if(type === 'default'){
                    for (let key in this.menuState){
                        this.menuState[key] = false;
                    }

                    this.menuState[type] = true;
                }
                else{
                    if(this.menuState[type]){
                        this.menuState[type] = false;
                        this.menuState['default'] = true;
                    }
                    else{
                        for (let key in this.menuState){
                            this.menuState[key] = false;
                        }

                        this.contentsInit(type, mode);

                        this.menuState[type] = true;
                    }
                }

                if(type === 'brain'){
                  this.tempSaveBtn = false;
                }
                else{
                  this.tempSaveBtn = true;
                }
            }
        },
        contentsInit(type, mode){
            switch (type){
                case 'problemAnalysis':{
                    if(!this.isCardItem.with_problem_analysis && this.itemId > -10){
                        this.isCardItem.with_problem_analysis = {
                            studentId:this.myUser.gaeinNo,
                            name:this.myUser.name,
                            content1:'',
                            content2:'',
                            content3:'',
                            content4:'',
                        };
                    }
                    else{
                        this.isCardItem = this.items[this.itemId];
                    }

                    this.isMenuType = 1;
                    break;
                }
                case 'reflectionLog':{
                    if(!this.isCardItem.with_reflection_log && this.itemId > -10){
                        this.isCardItem.with_reflection_log = {
                            userId: this.myUser.id,
                            studentId:this.myUser.gaeinNo,
                            name:this.myUser.name,
                            content1:'',
                            content2:'',
                            content3:'',
                            content4:'',
                            content5:'',
                            content6:'',
                            content7:'',
                        };
                    }
                    else{
                        this.isCardItem = this.items[this.itemId];
                    }

                    this.isMenuType = 2;

                    break;
                }
                case 'brain':{
                    if(!this.isCardItem.brain){
                        this.isCardItem.with_brains = {};
                        this.isCardItem.brain = [];
                    }

                    this.isMenuType = 3;

                    break;
                }
                case 'classApply':{
                    //수정해야함
                    if(!this.isCardItem.with_class_apply){
                        this.isCardItem.with_class_apply = {
                            userId: this.myUser.id,
                            state: 'wait',
                            mode: mode,
                            type: '',
                            grade: '',
                            size1: '',
                            size2: '',
                            proSize1: '',
                            proSize2: '',
                            proSize3: '',
                            daehak: '-1',
                            department: '-1',
                            major: '',
                            special: '0',
                            special2: '0',
                            special3: '0',
                            special4: '0',
                            korName: '',
                            engName: '',
                            gradesPoint: '',
                            lecturePoint: '',
                            exercisePoint: '',
                            description: '',
                            meca: '',
                            agency: '',
                            expert: '',
                            role1: '0',
                            role2: '',
                            role3: '0',
                            role4: '0',
                            role5: '0',
                            expected1: '0',
                            expected2: '0',
                            expected3: '0',
                            expected4: '0',
                            expected5: '0',
                            expected6: '0',
                            expected7: '0',
                            expected8: '',
                            aplName: '',
                            aplSign: '',
                            aplOrg: '',
                            aplTel: '',
                            aplPhone: '',
                            aplEmail: '',
                            applicant: [
                              {
                                name: '',
                                org: '',
                                tel: '',
                                phone: '',
                                email: '',
                              },
                            ],
                            agree1: '',
                            duration: '',
                            duration2: '',
                            conName: '',
                            conPer: '',
                            contribute: [
                              {
                                name: '',
                                per: '',
                              }
                            ],
                            conDescription: '',
                            agree2: '',
                            agree3: '',
                            basic1: '',
                            basic2: '',
                            basic3: '',
                            basic4: '',
                            basic5: '',
                            basic6: '',
                            basicPlan: '',
                            sceContent: '',
                            sceGoal: '',
                            sceTitle: '',
                            sceRole: '',
                            sceDetail: '',
                            planDetail: [],
                        };

                        for(let i=0;i<16;i++){
                            this.isCardItem.with_class_apply.planDetail.push({
                                content: '',
                                level: '',
                                stuContent: '',
                                subject: '',
                                method1: 0,
                                method2: 0,
                                method3: 0,
                            })
                        }

                        this.applyMode = mode;


                        axios.get(window.location.origin + '/myPage/oldApply/'+mode).then(re => {
                            console.log('MODE'+ mode);
                            console.log(re);

                            if (re.data) {
                                this.classList = _.cloneDeep(re.data);
                            }

                        })

                        this.classApplyModal = false;
                    }
                    else if(mode === '1' || mode === '2'){
                        this.isCardItem.with_class_apply.mode = mode;
                        this.classApplyModal = false;
                    }
                    else {

                    }



                    this.isMenuType = 4;

                    break;
                }
                case 'teamActivity':{
                    if(!this.isCardItem.with_team_activity && this.itemId > -10){
                        this.isCardItem.with_team_activity = {
                            dateTime:'',
                            problemSolvingProcess:'',
                            attendees:'',
                            mainActivities:'',
                            task1:'',
                            task2:'',
                            discuss1:'',
                            schedule1:'',
                            schedule2:'',
                            schedule3:'',
                            schedule4:'',
                            feedback:'',
                        };
                    }
                    else{
                        this.isCardItem = this.items[this.itemId];
                    }

                    this.isMenuType = 5;

                    break;
                }
                case 'detaileOperApply':{
                    this.isMenuType = 6;

                    break;
                }
                case 'evaluation':{
                    this.isMenuType = 7;

                    break;
                }
                case 'operationResult':{
                    if(!this.isCardItem.with_operation_result && this.itemId > -10){
                        this.isCardItem.with_operation_result = {
                            itemId: '',
                            semester: '',
                            college: '',
                            lectureName: '',
                            grade: '',
                            division: '',
                            grades: '',
                            professor: '',
                            size: '',
                            icpblType: '',
                            summary: '',
                            classGoal: '',
                            method: '',
                            basicPlan: '',
                            title: '',
                            role: '',
                            scenario: '',
                            process: [],
                            outputType1: '0',
                            outputType2: '0',
                            outputType3: '0',
                            outputType4: '0',
                            outputType5: '0',
                            outputType6: '0',
                            outputType7: '0',
                            outputType8: '',
                            finalOutput: '',
                            mainStudent: '',
                            sTitle: '',
                            sName: '',
                            sRole1: '',
                            sRole2: '',
                            sLink: '',
                            sFeedback: '',
                            sOpinion: '',
                            pr1: '',
                            pr2: '',
                            pr3: '',
                            pr4: '',
                            pr5: '',
                            pr6: '',
                        };

                        for(let i=0;i<4;i++){
                            this.isCardItem.with_operation_result.process.push({
                                level: '',
                                week: '',
                                content: '',
                                method: '',
                            })
                        }
                    }
                    else{
                        this.isCardItem = this.items[this.itemId];
                    }


                    this.isMenuType = 8;
                    break;
                }
            }
        },
        addComment(){
            if(this.editor2.getHTML().length !== 7) {
                //멘션 누구에게 하는지 찾기
                let tempMentions = [];
                this.editor2.getJSON().content.forEach(function(tms){
                    if(tms.content){
                        tms.content.forEach(function(tm){
                            if(tm.type === 'mention' && tm.attrs){
                                tempMentions.push(Number(tm.attrs.id));
                            }
                        })
                    }
                });

                //ES6
                tempMentions = _.cloneDeep(Array.from(new Set(tempMentions)));
                console.log(tempMentions);

                window.Rest.update(
                        {
                            id: this.isCardItem.id,
                            types: 'item',
                            subTypes: 'comment',
                            content: this.editor2.getHTML(),
                            file: this.commentFile,
                            mentions: tempMentions,
                        }
                ).then(
                        (re) => {
                            if(re){
                                const _result = re.data;

                                if(_result.hasOwnProperty('fail')){
                                    this.alertMsg = '댓글 내용을 입력해 주세요.';
                                    this.alertModal = true;
                                    //alert(_result.fail);
                                }
                                else{
                                    if(_result[0]){
                                        let date_ = new Date(_result[0].created_at);
                                        let year_ = date_.getFullYear();
                                        let month_ = (1 + date_.getMonth());
                                        let day_ = date_.getDate();
                                        let hour_ = date_.getHours();
                                        let minutes_ = date_.getMinutes();
                                        month_ = month_ >= 10 ? month_ : '0' + month_;
                                        day_ = day_ >= 10 ? day_ : '0' + day_;
                                        hour_ = hour_ >= 10 ? hour_ : '0' + hour_;
                                        minutes_ = minutes_ >= 10 ? minutes_ : '0' + minutes_;

                                        let fileUrl = '';
                                        if(_result[0].filePathName){
                                            fileUrl = window.Rest.uri + '/download/' +  _result[0].id;
                                        }

                                        this.isCardItem.comments.unshift({
                                            id: _result[0].id,
                                            itemId: _result[0].itemId,
                                            userId: _result[0].with_user.id,
                                            userName: _result[0].with_user.name,
                                            date: year_ + '.' + month_ + '.' +day_ + '. ' + hour_ + ':' +minutes_,
                                            content: _result[0].content,
                                            fileName: _result[0].fileName,
                                            fileUrl: fileUrl,
                                            file: '',
                                            commentMenu: false,
                                            editMode: false,
                                        });

                                        // const po = this.searchIndex('item',_result[0].itemId);

                                        this.items[this.itemId].comments = _.cloneDeep(this.isCardItem.comments);

                                        // document.getElementById('textarea-input').lastChild.childNodes[0].childNodes[0].innerText = '';
                                        this.editor2.setContent('');

                                        this.commentFileName = '파일';
                                        this.commentFile = '';
                                        this.commentFileUrl = '';

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
        uploadFile(e){
            if(e.target.files[0] === undefined){
                this.commentFileName = '파일';
                this.commentFile = '';
                this.commentFileUrl = '';
            }else{
                let pathpoint = e.target.files[0].name.lastIndexOf('.');
                let filepoint = e.target.files[0].name.substring(pathpoint+1,e.target.files[0].name.length);
                let filetype = filepoint.toLowerCase();

                if(filetype=='doc' || filetype=='docx' || filetype=='dotx' || filetype=='xlsx' || filetype=='xls' || filetype=='hwp' || filetype=='ppt'
                        || filetype=='pptx' || filetype=='egg' || filetype=='zip' || filetype=='7z' || filetype=='tar' || filetype=='zipx' || filetype=='pdf'){
                    this.commentFileName = e.target.files[0].name;
                    this.commentFile = e.target.files[0];
                    this.commentFileUrl = URL.createObjectURL(e.target.files[0]);
                }else {
                    alert('해당 유형의 파일은 선택할 수 없습니다.');
                    return false;
                }
            }
        },
        editUploadFile(e, com){
            if(e.target.files[0] === undefined){
                com.fileName = '파일';
                com.file = '';
                com.fileUrl = '';
            }else{
                let pathpoint = e.target.files[0].name.lastIndexOf('.');
                let filepoint = e.target.files[0].name.substring(pathpoint+1,e.target.files[0].name.length);
                let filetype = filepoint.toLowerCase();

                if(filetype=='doc' || filetype=='docx' || filetype=='dotx' || filetype=='xlsx' || filetype=='xls' || filetype=='hwp' || filetype=='ppt'
                        || filetype=='pptx' || filetype=='egg' || filetype=='zip' || filetype=='7z' || filetype=='tar' || filetype=='zipx' || filetype=='pdf'){
                    com.fileName = e.target.files[0].name;
                    com.file = e.target.files[0];
                    com.fileUrl = URL.createObjectURL(e.target.files[0]);
                }else {
                    alert('해당 유형의 파일은 선택할 수 없습니다.');
                    return false;
                }
            }
        },
        editCommentBtnOn(comment){
            if(!comment.editMode){
                console.log(this.editor3);
                this.editor3.focus();

                this.commentEdit = _.cloneDeep(comment);

                if(!this.commentEdit.fileName){
                    this.commentEdit.fileName = '파일'
                }

                comment.editMode = true;

                // this.editor3.content = comment.content;

                this.editor3.setContent(comment.content);
            }
            else{
                console.log('NO')
            }
        },
        editCommentBtnOff(){
            if(this.commentEdit){
                for(let i=0;i<this.isCardItem.comments.length;i++){
                    if(this.isCardItem.comments[i].id === this.commentEdit.id){
                        this.isCardItem.comments[i].editMode = false;
                        this.commentEdit = '';

                        if(this.commentMenu){
                            if(this.commentMenu.commentMenu){
                                this.commentMenu.commentMenu = false;
                                this.commentMenu = '';
                            }
                        }
                        break;
                    }
                }
            }
        },
        editComment(idx, comment_){
            if(this.editor3.getHTML().length !== 7) {
                //멘션 누구에게 하는지 찾기
                let tempMentions = [];
                this.editor3.getJSON().content.forEach(function(tms){
                    if(tms.content){
                        tms.content.forEach(function(tm){
                            if(tm.type === 'mention' && tm.attrs){
                                tempMentions.push(Number(tm.attrs.id));
                            }
                        })
                    }
                });

                //ES6
                tempMentions = Array.from(new Set(tempMentions));

                window.Rest.update(
                        {
                            id: comment_.id,
                            types: 'comment',
                            subTypes: 'comment',
                            content: this.editor3.getHTML(),
                            file: comment_.file,
                            mentions: tempMentions,
                        }
                ).then(
                        (re) => {
                            if(re){
                                const _result = re.data;

                                if(_result.hasOwnProperty('fail')){
                                    this.alertMsg = _result.fail;
                                    this.alertModal = true;
                                    //alert(_result.fail);
                                }
                                else{
                                    if(_result[0]){
                                        let date_ = new Date(_result[0].updated_at);
                                        let year_ = date_.getFullYear();
                                        let month_ = (1 + date_.getMonth());
                                        let day_ = date_.getDate();
                                        let hour_ = date_.getHours();
                                        let minutes_ = date_.getMinutes();
                                        month_ = month_ >= 10 ? month_ : '0' + month_;
                                        day_ = day_ >= 10 ? day_ : '0' + day_;
                                        hour_ = hour_ >= 10 ? hour_ : '0' + hour_;
                                        minutes_ = minutes_ >= 10 ? minutes_ : '0' + minutes_;

                                        let fileUrl = '';
                                        if(_result[0].filePathName){
                                            fileUrl = window.Rest.uri + '/download/' +  _result[0].id;
                                        }

                                        this.isCardItem.comments[idx] = {
                                            id: _result[0].id,
                                            itemId: comment_.itemId,
                                            userId: comment_.userId,
                                            userName: comment_.userName,
                                            date: year_ + '.' + month_ + '.' +day_ + '. ' + hour_ + ':' +minutes_,
                                            content: _result[0].content,
                                            fileName: _result[0].fileName,
                                            fileUrl: fileUrl,
                                            file: '',
                                            commentMenu: false,
                                            editMode: false,
                                        }
                                        this.editor3.setContent('');

                                        this.isCardItem.comments[idx].editMode = false;
                                        this.commentEdit = '';
                                        this.commentMenu.commentMenu = false;
                                        this.commentMenu = '';
                                        // this.editCommentBtnOff();


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
        delCommentConfirm(comment_, idx){
            this.delCommentConfirmModal = true;
            this.delCommentTarget = comment_;
            this.delCommentInx = idx;
        },
        delComment(){
            if(this.delCommentTarget.userId === this.myUser.id) {
                window.Rest.delete(
                        {
                            id: this.delCommentTarget.id,
                            types: 'comment',
                        }
                ).then(
                        (re) => {
                            if(re){
                                const _result = re.data;

                                if(_result.hasOwnProperty('fail')){
                                    this.alertMsg = _result.fail;
                                    this.alertModal = true;
                                    //alert(_result.fail);
                                }
                                else{
                                    if(_result.success){
                                        this.isCardItem.comments.splice(this.delCommentInx, 1);
                                        this.commentMenu = '';

                                        // const po = this.searchIndex('item',comment_.itemId);
                                        this.items[this.itemId].comments = _.cloneDeep(this.isCardItem.comments);
                                    }
                                }
                                this.delCommentConfirmModal = false;
                            }
                            else{
                                console.log('FETCH FAIL');
                            }
                        }
                )


            }

        },
        commentAddFetch(comment){
            let date_ = new Date(comment.created_at);
            let year_ = date_.getFullYear();
            let month_ = (1 + date_.getMonth());
            let day_ = date_.getDate();
            let hour_ = date_.getHours();
            let minutes_ = date_.getMinutes();
            month_ = month_ >= 10 ? month_ : '0' + month_;
            day_ = day_ >= 10 ? day_ : '0' + day_;
            hour_ = hour_ >= 10 ? hour_ : '0' + hour_;
            minutes_ = minutes_ >= 10 ? minutes_ : '0' + minutes_;

            this.isCardItem.comments.unshift({
                id: comment.id,
                itemId: comment.itemId,
                userId: comment.with_user.id,
                userName: comment.with_user.name,
                date: year_ + '.' + month_ + '.' +day_ + '. ' + hour_ + ':' +minutes_,
                content: comment.content,
                fileName: comment.fileName,
                fileUrl: window.Rest.uri + '/download/' +  comment.id,
                file: '',
                commentMenu: false,
                editMode: false,
            });
        },
        commentUpdateFetch(comment){
            let idx_ = -1;
            let date_ = new Date(comment.updated_at);
            let year_ = date_.getFullYear();
            let month_ = (1 + date_.getMonth());
            let day_ = date_.getDate();
            let hour_ = date_.getHours();
            let minutes_ = date_.getMinutes();
            month_ = month_ >= 10 ? month_ : '0' + month_;
            day_ = day_ >= 10 ? day_ : '0' + day_;
            hour_ = hour_ >= 10 ? hour_ : '0' + hour_;
            minutes_ = minutes_ >= 10 ? minutes_ : '0' + minutes_;

            const tempComment = {
                id: comment.id,
                itemId: comment.itemId,
                userId: comment.with_user.id,
                userName: comment.with_user.name,
                date: year_ + '.' + month_ + '.' +day_ + '. ' + hour_ + ':' +minutes_,
                content: comment.content,
                fileName: comment.fileName,
                fileUrl: window.Rest.uri + '/download/' +  comment.id,
                file: '',
                commentMenu: false,
                editMode: false,
            }

            for(let i=0;i<this.isCardItem.comments.length;i++){
                if(this.isCardItem.comments[i].id === comment.id){
                    this.isCardItem.comments[i] = [];
                    this.isCardItem.comments[i] = tempComment;
                    this.isCardItem.comments.push('1');
                    this.isCardItem.comments.pop();
                    break;
                }
            }
        },
        commentDelFetch(commentId){
            if(this.isCardItem.comments){
                for(let i=0;i<this.isCardItem.comments.length;i++){
                    if(this.isCardItem.comments[i].id === commentId){
                        this.isCardItem.comments.splice(i, 1);
                        break;
                    }
                }
            }
        },
        addBrain(){
            let date_ = new Date();
            let year_ = date_.getFullYear();
            let month_ = (1 + date_.getMonth());
            let day_ = date_.getDate();
            let hour_ = date_.getHours();
            let minutes_ = date_.getMinutes();

            month_ = month_ >= 10 ? month_ : '0' + month_;
            day_ = day_ >= 10 ? day_ : '0' + day_;
            hour_ = hour_ >= 10 ? hour_ : '0' + hour_;
            minutes_ = minutes_ >= 10 ? minutes_ : '0' + minutes_;

            let tempBrain = {
                userId: this.myUser.id,
                name: this.myUser.name,
                content: this.brainContent,
                date: year_ + '-' + month_ + '-' +day_ + ' ' + hour_ + ':' +minutes_,
                color: this.brainColor,
            }

            this.brainContent = '';
            this.brainColor = '1';

            if(this.brainIndex !== -1){     //수정
                if(this.viewMode){
                    window.Rest.updateBrain(
                            {
                                id: this.brainIndex,
                                type: 'brain',
                                content: tempBrain.content,
                                color: tempBrain.color,
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
                                            this.brainSaveModal = false;
                                            Vue.set(this.isCardItem.with_brains, this.brainIndex, tempBrain);
                                        }
                                    }
                                }
                                else{
                                    console.log('FETCH FAIL');
                                }
                            }
                    )
                }
                else{
                    Vue.set(this.isCardItem.with_brains, this.brainIndex, tempBrain);
                    this.brainIndex = -1;
                    this.brainSaveModal = false;
                }
            }
            else{       //추가
                // this.isCardItem.with_brains.push(tempBrain);
                if(this.viewMode){
                    // Vue.set(this.isCardItem.with_brains, this.myIndex, tempBrain)
                    // this.isCardItem.brain.push(this.myIndex);
                    // this.myIndex++;
                    window.Rest.updateBrain(
                            {
                                id: this.isCardItem.id,
                                type: 'item',
                                content: tempBrain.content,
                                color: tempBrain.color,
                            }
                    ).then(
                            (re) => {
                                if(re){
                                    const _result = re.data;
                                    if(_result.hasOwnProperty('fail')){
                                        this.alertMsg = _result.fail;
                                        this.alertModal = true;
                                        this.brainSaveModal = false;
                                        // alert(_result.fail);
                                    }
                                    else{
                                        if(_result.brainId){
                                            Vue.set(tempBrain, 'id', _result.brainId)
                                            Vue.set(this.isCardItem.with_brains, _result.brainId, tempBrain)
                                            this.isCardItem.brain.push(_result.brainId);
                                            this.brainSaveModal = false;
                                        }
                                    }
                                }
                                else{
                                    console.log('FETCH FAIL');
                                }
                            }
                    )
                }
                else{
                    Vue.set(this.isCardItem.with_brains, this.myIndex, tempBrain)
                    this.isCardItem.brain.push(this.myIndex);
                    this.myIndex++;
                    this.brainSaveModal = false;
                }


            }
        },
        editBrain(brain_, idx){
            if(brain_.userId === this.myUser.id){
                this.brainContent = brain_.content;
                this.brainColor = brain_.color;
                this.brainIndex = idx;
            }
        },
        delBrain(brain_){
            if(!this.viewMode){
                this.isCardItem.brain.splice(this.brainDeleteId,1);
                this.brainDeleteModal = false;
                return;
            }

            window.Rest.updateBrain(
                    {
                        id: brain_.id,
                        type: 'delete',
                        content: '-',
                        color: '1',
                    }
            ).then(
                    (re) => {
                        if(re){
                            const _result = re.data;
                            if(_result.hasOwnProperty('fail')){
                                this.alertMsg = _result.fail;
                                this.alertModal = true;
                                this.brainDeleteModal = false;
                                // alert(_result.fail);
                            }
                            else{
                                if(_result.success){
                                    delete this.isCardItem.with_brains[brain_.id];
                                    this.isCardItem.brain.splice(this.isCardItem.brain.indexOf(Number(brain_.id)),1);
                                    this.brainDeleteModal = false;
                                }
                            }
                        }
                        else{
                            console.log('FETCH FAIL');
                        }
                    }
            )
        },
        moveBrain(e){
            // const newCardId = e.to.offsetParent.id.split('_')[1];
            const oldBrainId = e.clone.id.split('_')[1];
            // const newCardId = e.item.id.split('_')[1];

            if(e.oldIndex === e.newIndex){
                return false;
            }

            if(!this.viewMode){
                return;
            }

            window.Rest.moveBrain(
                    {
                        id: oldBrainId,
                        destPosition: e.newIndex,
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
        brainMoveFetch(nums){
            if(this.isCardItem.brain){
                this.isCardItem.brain = nums;
            }
        },
        brainAddFetch(brains){
            if(this.isCardItem.brain){
                const _year = new Date(brains.updated_at).getFullYear();
                const _month = String(new Date(brains.updated_at).getMonth() + 1).padStart(2,'0');
                const _day = String(new Date(brains.updated_at).getDate()).padStart(2,'0');
                const _hour = String(new Date(brains.updated_at).getHours()).padStart(2,'0')
                const _min = String(new Date(brains.updated_at).getMinutes()).padStart(2,'0')

                Vue.set(brains, 'name', brains.with_user.name);
                Vue.set(brains, 'date', _year+'-'+_month+'-'+_day+' '+_hour+':'+_min);

                Vue.set(this.isCardItem.with_brains, brains.id, brains)
                this.isCardItem.brain.push(brains.id);
            }
        },
        brainUpdateFetch(brains){
            if(this.isCardItem.brain){
                const _year = new Date(brains.updated_at).getFullYear();
                const _month = String(new Date(brains.updated_at).getMonth() + 1).padStart(2,'0');
                const _day = String(new Date(brains.updated_at).getDate()).padStart(2,'0');
                const _hour = String(new Date(brains.updated_at).getHours()).padStart(2,'0')
                const _min = String(new Date(brains.updated_at).getMinutes()).padStart(2,'0')

                Vue.set(brains, 'name', brains.with_user.name);
                Vue.set(brains, 'date', _year+'-'+_month+'-'+_day+' '+_hour+':'+_min);

                Vue.set(this.isCardItem.with_brains, brains.id, brains)
            }
        },
        brainDelFetch(brainId){
            if(this.isCardItem.brain){
                // Vue.set(this.isCardItem.with_brains, brains.id, brains)
                delete this.isCardItem.with_brains[brainId];
                this.isCardItem.brain.splice(this.isCardItem.brain.indexOf(Number(brainId)),1);
            }
        },
        dragBrainInit(e){
            this.brainContent = '';
            this.brainColor = '1';
            this.brainIndex = -1;
        },
        btnDefaultMenuFold(){
            this.defaultMenu = !this.defaultMenu;
        },
        btnCommentMenuOn(com){
            if(!this.commentMenu){
                this.commentMenu = com;
                this.commentMenu.commentMenu = true;

                this.isCardItem.comments.push('1');
                this.isCardItem.comments.pop();
            }
            else{
                this.commentMenu.commentMenu = false;
                this.commentMenu = '';
            }
        },
        btnCommentMenuOff(){
            if(this.commentMenu){
                if(this.commentMenu.commentMenu){
                    this.commentMenu.commentMenu = false;
                    this.commentMenu = '';
                }
            }
        },
        partyListOn(){
            //'myTeamId', 'myClassObjectId', 'isTeamPage'
            // console.log(this.isTeamPage ? this.myTeamId+'' : this.myClassObjectId+'');
            // console.log('types: user');
            // console.log(this.isTeamPage ? 'team' : 'classObject');

            this.userListMode = !this.userListMode;
        },
        addParty(idx){
            this.isCardItem.party.push(this.pUserList[idx]);
            this.pUserList.splice(idx,1);
        },
        delParty(idx){
            this.pUserList.push(this.isCardItem.party[idx]);
            this.isCardItem.party.splice(idx, 1);
        },
        detailOperApplyFileList(flag, idx){
            if(flag === 'ADD'){
                this.isCardItem.with_class_apply.progressPlan.push({
                    id: -2,
                    filePathName: '',
                    fileFile: '',
                    fileName: '',
                });
            }
            else if(flag === 'DELETE'){
                this.isCardItem.with_class_apply.progressPlan.splice(idx, 1);
            }
        },  //수정해야함 with_class_apply
        onClickFileUpload(){
            this.$refs.fileInput.click();
        },
        detailOperApplyFilePreview(e, idx){
            // let fileName = e.target.files[0].name;
            // document.querySelector('#' + e.target.id + '+ label').innerHTML = fileName;

            const _file = {
                id: -1,
                filePathName: URL.createObjectURL(e.target.files[0]),
                fileFile: e.target.files[0],
                fileName: e.target.files[0].name,
            };

            this.isCardItem.with_class_apply.progressPlan[idx] = _file;

            this.isCardItem.with_class_apply.progressPlan.push('1');
            this.isCardItem.with_class_apply.progressPlan.pop();

            console.log(this.isCardItem.with_class_apply.progressPlan);
            //
            // if(e.target.files[0] === undefined){
            //     com.fileName = '파일';
            //     com.file = '';
            //     com.fileUrl = '';
            // }else{
            //     let pathpoint = e.target.files[0].name.lastIndexOf('.');
            //     let filepoint = e.target.files[0].name.substring(pathpoint+1,e.target.files[0].name.length);
            //     let filetype = filepoint.toLowerCase();
            //
            //     if(filetype=='doc' || filetype=='docx' || filetype=='dotx' || filetype=='xlsx' || filetype=='xls' || filetype=='hwp' || filetype=='ppt'
            //         || filetype=='pptx' || filetype=='egg' || filetype=='zip' || filetype=='7z' || filetype=='tar' || filetype=='zipx' || filetype=='pdf'){
            //         com.fileName = e.target.files[0].name;
            //         com.file = e.target.files[0];
            //         com.fileUrl = URL.createObjectURL(e.target.files[0]);
            //     }else {
            //         alert('해당 유형의 파일은 선택할 수 없습니다.');
            //         return false;
            //     }
            // }
        },  //수정해야함 with_class_apply
        textAreaResize(event) {
            event.target.style.minHeight = (event.target.rows * 19 + 2) + 'px';
            event.target.style.height = "1px";
            event.target.style.height = (12 + event.target.scrollHeight)+"px";

            // let container = document.getElementById('item_con');
            // console.log(container.innerHTML);
        },
        clickRadio(val, val_){
            if(val == val_ && !this.radioFlag){
                event.target.checked = false;
                val_ = '';
                this.radioFlag = true;
            }else{
                this.radioFlag = false;
            }
        },
        imgOpen(imgUrl){
            window.open(imgUrl)
        },
        isMobile(){
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        },
        userFetch(){
            if(!this.isMyPage){
                window.Rest.fetch(
                        {
                            id: this.isTeamPage ? this.myTeamId : this.myClassObjectId,
                            types: 'user',
                            itemId: this.isCardItem.id,
                            teamId: this.myTeamId ? this.myTeamId : null,
                            subTypes: this.isTeamPage ? 'team' : 'classObject',
                        }
                ).then(
                        (re) => {
                            if(re){
                                const _result = re.data;
                                if(_result.hasOwnProperty('fail') && this.itemId > -10){
                                    alert(_result.fail);
                                }
                                else{
                                    if(_result){
                                        this.userList = _.cloneDeep(_result);

                                        for(let j=0;j<this.isCardItem.party.length;j++){
                                            for(let i=0;i<_result.length;i++){
                                                if(this.isCardItem.party[j].id === _result[i].id){
                                                    _result.splice(i,1);
                                                    break;
                                                }
                                            }
                                        }

                                        this.pUserList = _.cloneDeep(_result);
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
        brainDeleteModalOn(idx, id){
            this.viewMode ? this.brainDeleteId = id : this.brainDeleteId = idx;
            this.brainDeleteModal = true;
        },
        focusEvent(){
            this.alertModal = false;
            if(this.focusState === 'several'){
              console.log();
              this.$refs[this.focusTarget] ? (this.$refs[this.focusTarget][0].value === '' ? this.$refs[this.focusTarget][0].focus() : this.$refs[this.focusTarget][1].focus()) : '';
            }
            else{
              this.$refs[this.focusTarget] ? this.$refs[this.focusTarget].focus() : '';
            }
        },
        imgSlideOn(index){
            this.imgSlide.initialSlide = index;
            this.imgSlidePopup = true;
        },
        numberMaxLength(e,type){
            switch (type){
              case 'gradesPoint' :
                if(this.isCardItem.with_class_apply.gradesPoint.length > e.target.maxLength){
                  this.isCardItem.with_class_apply.gradesPoint = this.isCardItem.with_class_apply.gradesPoint.slice(0, e.target.maxLength);
                }
                break;
              case 'lecturePoint' :
                if(this.isCardItem.with_class_apply.lecturePoint.length > e.target.maxLength){
                  this.isCardItem.with_class_apply.lecturePoint = this.isCardItem.with_class_apply.lecturePoint.slice(0, e.target.maxLength);
                }
                break;
              case 'exercisePoint' :
                if(this.isCardItem.with_class_apply.exercisePoint.length > e.target.maxLength){
                  this.isCardItem.with_class_apply.exercisePoint = this.isCardItem.with_class_apply.exercisePoint.slice(0, e.target.maxLength);
                }
                break;
            }
        },
        saveFeedBack(){
            axios.post(window.location.origin + '/lectureList/lectureDetailTeam/Report/' + this.myClassObjectId + '/'+ this.isCardItem.with_team_activity.id, {
                feedback: this.isCardItem.with_team_activity.feedback,
                check: true
            }).then(re => {
                if (re.data) {
                    this.saveFeedModal = false;
                    this.saveFeedModalComplete = true;
                }
            })
        },
        fetchApply(ca){
            axios.get(window.location.origin + '/myPage/fetchApply/' + ca.id).then(re => {
                console.log(re);
                if (re.data) {
                    // const backItem = _.cloneDeep(this.isCardItem.with_class_apply)
                    // this.department = this.daehaks[re.data.daehak];
                    this.isCardItem.with_class_apply = _.cloneDeep(re.data);
                    this.isCardItem.with_class_apply.userId = this.myUser.id;
                    this.isCardItem.with_class_apply.state = 'wait';

                    // this.tDepartment = this.isCardItem.with_class_apply.department;
                    this.isTD = true;

                    // console.log(this.tDepartment);
                }
            })
        },
        clearApply(){
            if(this.applyMode){
                this.isCardItem.with_class_apply = {
                    userId: this.myUser.id,
                    state: 'wait',
                    mode: this.applyMode,
                    type: '',
                    grade: '',
                    size1: '',
                    size2: '',
                    proSize1: '',
                    proSize2: '',
                    proSize3: '',
                    daehak: '-1',
                    department: '-1',
                    major: '',
                    special: '0',
                    special2: '0',
                    special3: '0',
                    special4: '0',
                    korName: '',
                    engName: '',
                    gradesPoint: '',
                    lecturePoint: '',
                    exercisePoint: '',
                    description: '',
                    meca: '',
                    agency: '',
                    expert: '',
                    role1: '0',
                    role2: '',
                    role3: '0',
                    role4: '0',
                    role5: '0',
                    expected1: '0',
                    expected2: '0',
                    expected3: '0',
                    expected4: '0',
                    expected5: '0',
                    expected6: '0',
                    expected7: '0',
                    expected8: '',
                    aplName: '',
                    aplSign: '',
                    aplOrg: '',
                    aplTel: '',
                    aplPhone: '',
                    aplEmail: '',
                    applicant: [
                      {
                        name: '',
                        org: '',
                        tel: '',
                        phone: '',
                        email: '',
                      },
                    ],
                    agree1: '',
                    duration: '',
                    duration2: '',
                    conName: '',
                    conPer: '',
                    contribute: [
                      {
                        name: '',
                        per: '',
                      }
                    ],
                    conDescription: '',
                    agree2: '',
                    agree3: '',
                    basic1: '',
                    basic2: '',
                    basic3: '',
                    basic4: '',
                    basic5: '',
                    basic6: '',
                    basicPlan: '',
                    sceContent: '',
                    sceGoal: '',
                    sceTitle: '',
                    sceRole: '',
                    sceDetail: '',
                    planDetail: [],
                };

                for(let i=0;i<16;i++){
                    this.isCardItem.with_class_apply.planDetail.push({
                        content: '',
                        level: '',
                        stuContent: '',
                        subject: '',
                        method1: 0,
                        method2: 0,
                        method3: 0,
                    })
                }
            }
        },
        fileDownloadConfirmOn(fileLink){
            this.fileDownloadConfirm = true;
            this.fileDownloadLink = fileLink;
            console.log(this.fileDownloadLink);
        },
        addHyphen(i, type){
            if(type === 'tel'){
                this.isCardItem.with_class_apply.applicant[i].tel = this.isCardItem.with_class_apply.applicant[i].tel.replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-");
            }
            else if(type === 'phone'){
                this.isCardItem.with_class_apply.applicant[i].phone = this.isCardItem.with_class_apply.applicant[i].phone.replace(/[^0-9]/g, "").replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})$/,"$1-$2-$3").replace("--", "-");
            }
        },
        classCon(type){
            if(type === 0){
                if(this.viewMode){
                    this.$emit('modalOff');
                }
                else{
                    // this.tempSaveModal=true;
                    this.cancelModal=true;
                }
            }
            else{
                if(this.viewMode){
                    if(this.isCardItem.with_class_apply.state === 'complete'){
                        this.$emit('modalOff');
                    }
                    else{
                        this.updateModal=true;
                    }
                }
                else{
                    this.saveModal=true;
                }
            }
        },
        departmentListSet(key){
            this.tDepartment = '-1';
            // this.department = _.cloneDeep(this.daehaks[key]);
        },
    },
}
</script>

