@extends('layouts.layout')

<script>
    window.onload = () => {
        $("select").niceSelect();
    }
</script>

@section('content')
    <div class="container">
        <div class="container__wrap">
            @if ($classApply)
            <h3 class="container-tit"><span>{{$classApply->korName}}</span></h3>
            @endif
            {{--탭--}}
            <ul class="container-tab">
                <li class="active">정보</li>
            </ul>
@if($classApply->state == 'complete' && $myClassObjects)
{{--이전 소스 과목 코드 업데이트 기능--}}
            <div class="container-top">
                <form method="POST" action="{{ route('lectureDetailInfo', ['classApplyId' => $classApply->id]) }}">
                <div class="left">
                    <label>과목코드</label>
                        @csrf
                        <select class="custom-select" name="lectureCode">
                            <option value="" selected>선택</option>
                            @foreach($myClassObjects as $myClassObject)
                                <option value="{{ $myClassObject->suupNo }}" @if($myClassObject->suupNo == $classApply->code) selected @endif>{{ $myClassObject->suupNo.':'.$myClassObject->gwamokNm }}</option>
                            @endforeach
{{--                            @foreach($suupNumbers as $suupNumber)--}}
{{--                                <option value="{{ $suupNumber->suupNo }}" @if($suupNumber->suupNo == $classApply->code) selected @endif>{{ $suupNumber->suupNo.':'.$suupNumber->gwamokNm }}</option>--}}
{{--                            @endforeach--}}
                        </select>
                        @if($classApply->state == 'complete')
                            <button type="button" onclick="codeCheck()" class="btn btn-md btn-primary">저장</button>
                        @endif
                </div>
                <div class="popup" id="lecture_register_popup" style="display:none">
                    <div class="card-modal__dim" onclick="popupOff('lecture_register_popup')"></div>
                    <div class="popup-wrap">
                        <div class="popup-con scroll-sm t-center">
                            신청하시겠습니까?
                        </div>
                        <div class="confirm-btn">
                            <button type="button" onclick="popupOff('lecture_register_popup')" class="btn fc-gray w-50">취소</button>
                            <button class="btn w-50">확인</button>
                        </div>
                    </div>
                </div>
                <div class="popup" id="confirm_popup" style="display:none">
                    <div class="card-modal__dim" onclick="popupOff('confirm_popup')"></div>
                    <div class="popup-wrap">
                        <div class="popup-con scroll-sm t-center">
                            과목코드를 입력해주세요.
                        </div>
                        <div class="confirm-btn">
                            <button type="button" onclick="popupOff('confirm_popup')" class="btn w-100">확인</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
                @endif
            @if ($classApply)
                <div class="contents__wrap">
                    <h4 class="con-tit mb-20">
                        IC-PBL 교과목 세부 운영 계획서
                        <div class="tit-right d-none">
                            <select>
                                <option>참고자료(교과목명)</option>
                            </select>
                            <button class="btn-data">참고자료(과거)</button>
                        </div>
                    </h4>
                    <table class="table-input__wrap t-center readonly table mb-30">
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
                                    <input type="checkbox" name="type" value="type1" @if($classApply->type == 1) checked @endif onclick="return(false);" />
                                    전공기초
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" name="type" value="type2" @if($classApply->type == 2) checked @endif onclick="return(false);" />
                                    전공핵심(필수)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" name="type" value="type3" @if($classApply->type == 3) checked @endif onclick="return(false);" />
                                    전공핵심
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" name="type" value="type4" @if($classApply->type == 4) checked @endif onclick="return(false);" />
                                    전공심화
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>수강학년</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->grade == 1) checked @endif onclick="return(false);" />
                                    1학년
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->grade == 2) checked @endif onclick="return(false);" />
                                    2학년
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->grade == 3) checked @endif onclick="return(false);" />
                                    3학년
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->grade == 4) checked @endif onclick="return(false);" />
                                    4학년
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>수강규모</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->size1 == 1) checked @endif onclick="return(false);"  />
                                    10명~20명
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->size1 == 2) checked @endif onclick="return(false);" />
                                    21명~30명
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->size1 == 3) checked @endif onclick="return(false);" />
                                    30명초과 총<input type="text" placeholder="0" @if($classApply->size1 == 3) value="{{ $classApply->size2 }}" @endif readonly />명
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>교강사 수</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->proSize1 == 1) checked @endif onclick="return(false);" />
                                    단독운영
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->proSize1 == 2) checked @endif onclick="return(false);" />
                                    옴니버스 <input type="text" placeholder="0" @if($classApply->proSize1 == 2) value="{{ $classApply->proSize2 }}" @endif readonly />명
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->proSize1 == 3) checked @endif onclick="return(false);" />
                                    팀티칭 <input type="text" placeholder="0" @if($classApply->proSize1 == 3) value="{{ $classApply->proSize2 }}" @endif readonly />명
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>개설학과(부)<br>(전공)</th>
                            <td>
                                {{ $classApply->daehak }}/{{ $classApply->department }}<br />
                                @if($classApply->major)({{ $classApply->major }})@endif
                            </td>
                            <th>특수수업<br>(해당과목만 체크)</th>
                            <td>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->special == '1') checked @endif onclick="return(false);" />
                                    SMART-F
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->special2 == '1') checked @endif onclick="return(false);" />
                                    SMART-L
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->special3 == '1') checked @endif onclick="return(false);" />
                                    영어전용
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->special4 == '1') checked @endif onclick="return(false);" />
                                    제2외국어전용
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2">교과목명</th>
                            <td colspan="2">
                                &nbsp;<span class="fc-navy">(국문)</span>&nbsp;
                                <input type="text" class="input-text w-80" value="{{ $classApply->korName }}" readonly />
                            </td>
                            <th class="t-center">
                                학점&nbsp;&nbsp;-&nbsp;&nbsp;강의&nbsp;&nbsp;-&nbsp;&nbsp;실습
                            </th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;<span class="fc-navy">(영문)</span>&nbsp;
                                <input type="text" class="input-text w-80" value="{{ $classApply->engName }}" readonly />
                            </td>
                            <td class="t-center b-left">
                                <input type="text" class="input-text sm" placeholder="0" value="{{ $classApply->gradesPoint }}" readonly />  -
                                <input type="text" class="input-text sm" placeholder="0" value="{{ $classApply->lecturePoint }}" readonly />  -
                                <input type="text" class="input-text sm" placeholder="0" value="{{ $classApply->exercisePoint }}" readonly />
                            </td>
                        </tr>
                        <tr>
                            <th>교과목 개요</th>
                            <td colspan="3">
                                <textarea rows="4" placeholder="※ IC-PBL교과목으로서의 특징, 학습 목표, 기대효과 등을 간략하게 기재" readonly>{{ $classApply->description }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>IC-PBL<br />MECA유형</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->meca == 1) checked @endif onclick="return(false);" />
                                    M(Merge, 현장통합형)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->meca == 2) checked @endif onclick="return(false);" />
                                    E(Evaluate, 현장평가형)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->meca == 3) checked @endif onclick="return(false);" />
                                    C(Create, 문제해결형)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->meca == 4) checked @endif onclick="return(false);" />
                                    A(Anchor, 현장문제형)
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2">산업체 참여정보<br />(현장 연계 시)</th>
                            <td colspan="3">
                                소속 기관 : (<input type="text" class="input-text md" value="{{ $classApply->agency }}" readonly />)&nbsp;&nbsp;<br class="m-block" />
                                현장전문가 직급 및 업무분야 : <br class="m-block" />(<input type="text" class="input-text md" value="{{ $classApply->expert }}" readonly />)
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                참여 역할 : <br class="m-block" />
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->role1 == '1') checked @endif onclick="return(false);" />
                                    멘토
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->role3 == '1') checked @endif onclick="return(false);" />
                                    평가/심사
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->role4 == '1') checked @endif onclick="return(false);" />
                                    수업/특강
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if($classApply->role5 == '1') checked @endif onclick="return(false);" />
                                    기타
                                    <input type="text" class="lg" @if($classApply->role5 == '1') value="{{ $classApply->role2 }}" @endif readonly />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>예상수업결과물<br>(복수 선택 가능)</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" @if ($classApply->expected1 == '1') checked @endif onclick="return(false);"/>
                                    연구보고서
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if ($classApply->expected2 == '1') checked @endif onclick="return(false);"/>
                                    제안서
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if ($classApply->expected3 == '1') checked @endif onclick="return(false);"/>
                                    캠페인
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if ($classApply->expected4 == '1') checked @endif onclick="return(false);"/>
                                    프로토타입
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if ($classApply->expected5 == '1') checked @endif onclick="return(false);"/>
                                    소프트웨어
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if ($classApply->expected6 == '1') checked @endif onclick="return(false);"/>
                                    영상물
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" @if ($classApply->expected7 == '1') checked @endif onclick="return(false);"/>
                                    기타 <input type="text" class="lg" @if($classApply->expected7 == '1') value="{{ $classApply->expected8 }}" @endif readonly />
                                </label>
                            </td>
                        </tr>
                        @foreach($classApply->applicant as $key => $app)
                            <tr>
                                @if($key === 0)<th rowspan="{{count($classApply->applicant) * 4}}">신청자 정보</th>@endif
                                <th>성명</th>
                                <td colspan="2">
                                    <input type="text" class="input-text w-80" placeholder="※ 교수자가 여러 명일 경우 모든 사람의 정보 기재" value="{{$app['name']}}" readonly />
                                    <span class="fc-navy">(서명) {{$app['name']}}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>소속</th>
                                <td colspan="2">
                                    <input type="text" class="input-text" value="{{$app['org']}}" readonly />
                                </td>
                            </tr>
                            <tr>
                                <th>Tel</th>
                                <td colspan="2">
                                    <span class="fc-navy">연구실 :</span> <input type="text" class="input-text w-half" value="{{$app['tel']}}" readonly /><br class="m-block" />
                                    <span class="fc-navy">핸드폰 :</span> <input type="text" class="input-text w-half" value="{{$app['phone']}}" readonly />
                                </td>
                            </tr>
                            <tr>
                                <th>e-mail</th>
                                <td colspan="2">
                                    <input type="text" class="input-text" value="{{$app['email']}}" readonly />
                                </td>
                            </tr>
                        @endforeach
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
                                        <input type="checkbox" @if ($classApply->agree1 == '1') checked @endif onclick="return(false);" />
                                        동의함
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" @if ($classApply->agree1 != '1') checked @endif onclick="return(false);" />
                                        동의하지 않음
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </table>

                    @if($classApply->mode === "1")
                        <h5 class="sub-tit">※ 교육과정 개발 기여도 평가</h5>
                        <table class="table-input__wrap t-center readonly table mb-30">
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
                                    <input type="text" class="input-text" value="{{ $classApply->duration }} ~ {{ $classApply->duration2 }}" readonly />
                                </td>
                            </tr>
                            @foreach($classApply->contribute as $key => $con)
                                <tr>
                                    @if($key === 0)<th rowspan="{{count($classApply->contribute) * 2}}">기여구분</th>@endif
                                    <th class="p-0">성명</th>
                                    <th class="p-0" colspan="3">기여도</th>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" class="input-text" value="{{$con['name']}}" readonly />
                                    </td>
                                    <td colspan="3">
                                        <input type="text" class="input-text" placeholder="1인일 경우 100%, 4인일 경우 25%" value="{{$con['per']}}%" readonly />
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="5">
                                    <span class="fc-navy">내용</span>
                                    <textarea rows="7" readonly placeholder="예시 :
1. 해당 교과목에 IC-PBL을 적용하여 달성하고자 하는 목표 수립
2. 주차별 교육 내용 및 방법 계획 수립
3. 주차별 세부 교육 내용 및 방법 마련 및 검토
4. 수업 진행 후 기대하는 최종 결과물 구상
">{{ $classApply->conDescription }}
                              </textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div class="txt">
                                        <span class="fc-navy">※ IC-PBL 교과목 운영 지원 서약</span><br>
                                        본 공모에 선정된 2020학년도 2학기 IC-PBL 교과목은 대학혁신지원사업 기간 종료 후에도 지속적으로 운영할 것을 서약합니다.
                                    </div>
                                    <div class="agree-wrap">
                                        <label class="custom-check">
                                            <input type="checkbox" @if ($classApply->agree2 == '1') checked @endif onclick="return(false);" />
                                            동의함
                                        </label>
                                        <label class="custom-check">
                                            <input type="checkbox" @if ($classApply->agree2 == '0') checked @endif onclick="return(false);" />
                                            동의하지 않음
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div class="txt">
                                        <span class="fc-navy">※ 대학혁신지원사업 교과목 개발</span><br>
                                        대학혁신지원사업비로 개발된 본 과목은 2021년 2학기에 개설하여 운영될 것이며 교과목 운영 종료 후
                                        개발비를 지급받을 것입니다. 또한 본 과목 개발과 관련하여 대학혁신지원사업이 아닌 타 국고 사업의
                                        이중수혜 적발 시 개발비 지급중단 및 환수조치가 될 수 있음을 확인합니다.
                                    </div>
                                    <div class="agree-wrap">
                                        <label class="custom-check">
                                            <input type="checkbox" @if ($classApply->agree3 == '1') checked @endif onclick="return(false);" />
                                            확인함
                                        </label>
                                        <label class="custom-check">
                                            <input type="checkbox" @if ($classApply->agree3 == '0') checked @endif onclick="return(false);" />
                                            확인하지 않음
                                        </label>
                                    </div>
                                    {{--                                <div class="date-wrap">--}}
                                    {{--                                    20<input type="text" class="input-text sm" placeholder="00" value="{{ $classApply->applicantYear }}" readonly />년--}}
                                    {{--                                    <input type="text" class="input-text sm" placeholder="00" value="{{ $classApply->applicantMonth }}" readonly />월--}}
                                    {{--                                    <input type="text" class="input-text sm" placeholder="00" value="{{ $classApply->applicantDay }}" readonly />일--}}
                                    {{--                                </div>--}}
                                </td>
                            </tr>
                        </table>
                    @endif

                <!-- 세부운영계획 -->
                    <h5 class="sub-tit">1. 수업 계획 세부 내용</h5>
                    <div class="pl-10 mb-20 lh-1">
                        1) 기본정보<br />
                        <br />
                        ※ 작성 지침<br />
                        적정수업 크기, 튜터 활용 여부, 학습자 특성, 교실환경, 교재, 현장연계 계획 등 IC-PBL수업의 기본 정보들을 기술하시면 됩니다.
                    </div>
                    <table class="table-input__wrap readonly t-center table mb-30">
                        <colgroup>
                            <col width="5%" />
                            <col width="25%" />
                            <col width="70%" />
                        </colgroup>
                        <tr>
                            <th>1</th>
                            <th>적정 수업 크기</th>
                            <td>
                                <textarea rows="1" readonly>{{ $classApply->basic1 }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>2</th>
                            <th>튜터 활용 여부와 활용 계획</th>
                            <td>
                                <textarea rows="1" readonly>{{ $classApply->basic2 }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>3</th>
                            <th>학습자 특성</th>
                            <td>
                                <textarea rows="2" readonly>{{ $classApply->basic3 }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>4</th>
                            <th>교실환경(H/W, S/W)</th>
                            <td>
                                <textarea rows="1" readonly>{{ $classApply->basic4 }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>5</th>
                            <th>교재 및 수업자료활용 계획</th>
                            <td>
                                <textarea rows="2" readonly>{{ $classApply->basic5 }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>6</th>
                            <th>현장 연계 계획</th>
                            <td>
                                <textarea rows="3" readonly>{{ $classApply->basic6 }}</textarea>
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
                    <table class="table-input__wrap readonly t-center table mb-30">
                        <tr>
                            <td>
                                <textarea rows="3" readonly>{{ $classApply->basicPlan }}</textarea>
                            </td>
                        </tr>
                    </table>
                    <h5 class="sub-tit">2. IC-PBL 문제 (시나리오)</h5>
                    <div class="pl-10 mb-20 lh-1">
                        (※ IC-PBL 모듈 개수만큼 작성, 예를 들어 IC-PBL 모듈이 2개일 경우 2개의 IC-PBL문제 개발)
                    </div>
                    <table class="table-input__wrap readonly t-center table mb-30">
                        <colgroup>
                            <col width="15%" />
                            <col width="15%" />
                            <col width="70%" />
                        </colgroup>
                        <tr>
                            <th>구분</th>
                            <th colspan="2">내용</th>
                        </tr>
                        <tr>
                            <th>학습 내용</th>
                            <td colspan="2">
                            <textarea rows="2" placeholder="※ 본 IC-PBL 모듈의 학습내용
(교과목 전체 내용이 아님)" readonly>{{ $classApply->sceContent }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>핵심 학습 목표</th>
                            <td colspan="2">
                            <textarea rows="2" placeholder="※ 본 IC-PBL 모듈을 통해 성취하고자 하는 학습목표
(교과목 전체 목표가 아님)" readonly>{{ $classApply->sceGoal }}</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="3">문제 상황 시나리오</th>
                            <th>시나리오 제목 :</th>
                            <td><textarea rows="1" readonly>{{ $classApply->sceTitle }}</textarea></td>
                        </tr>
                        <tr>
                            <th>문제 상황 속<br />학습자(주인공) 역할 :</th>
                            <td><textarea rows="1" readonly>{{ $classApply->sceRole }}</textarea></td>
                        </tr>
                        <tr>
                            <td colspan="2"><textarea rows="4" placeholder="※ 해당 학습내용 및 학습목표를 반영한
실제적이고 시의성 있는 문제 상황을 기술" readonly>{{ $classApply->sceDetail }}</textarea></td>
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
                        @if($classApply->planDetail)
                            @foreach($classApply->planDetail as $key => $pD)
                                <tr>
                                    <th>{{$key + 1}}</th>
                                    <td><textarea rows="4" readonly>{{$pD['content']}}</textarea></td>
                                    <td><textarea rows="4" readonly>{{$pD['level']}}</textarea></td>
                                    <td><textarea rows="4" readonly>{{$pD['stuContent']}}</textarea></td>
                                    <td><textarea rows="4" readonly>{{$pD['subject']}}</textarea></td>
                                    <td>
                                        <label class="custom-check">
                                            <input type="checkbox" @if ($pD['method1']) checked @endif onclick="return(false);" />
                                            비대면 실시간강의
                                        </label>
                                        <label class="custom-check">
                                            <input type="checkbox" @if ($pD['method2']) checked @endif onclick="return(false);" />
                                            비대면 녹화 강의
                                        </label>
                                        <label class="custom-check">
                                            <input type="checkbox" @if ($pD['method3']) checked @endif onclick="return(false);" />
                                            대면강의
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </table>

                    {{--                    <ul class="file-list">--}}
                    {{--                        @foreach($classApply->progressPlan as $key => $progressPlan)--}}
                    {{--                            <li class="file-list__item">--}}
                    {{--                                @if ($loop->first)--}}
                    {{--                                    <span>*첨부 파일</span>--}}
                    {{--                                @endif--}}
                    {{--                                <a href="{{ route('downloadApplyFile', ['applyId' => $classApply->id, 'pathName' => $classApply->progressPlan[$key]['filePathName']]) }}" class="name">{{ $classApply->progressPlan[$key]['fileName'] }}</a>--}}
                    {{--                            </li>--}}
                    {{--                        @endforeach--}}
                    {{--                    </ul>--}}
                </div>
            @else
                수강신청을 거치지 않고 만들어진 수업입니다.
            @endif
        </div>
    </div>

@endsection

<script type="text/javascript">
    function fileListAdd(){
        let listLength = document.querySelectorAll('.file-list__item').length;
        let addList = document.createElement('li');
        addList.classList.add('file-list__item');
        addList.innerHTML = '<input type="file" class="input" onchange="fileUpload(this, 2)" id="file_add' + (listLength + 1) + '" />'
            + '<label for="file_add' + (listLength + 1) + '" class="name"></label>'
            + '<button class="btn-addbtn delete" onclick="fileListDelete(this)"></button>';
        if(listLength > 4){
            return 0;
        }else{
            document.querySelector('.file-list').append(addList);
        }
    }
    function fileListDelete(e){
        let deleteList = e.parentNode;
        document.querySelector('.file-list').removeChild(deleteList)
    }
    function fileUpload(e, type){
        let fileName = e.files[0].name;
        let inputTarget = e.nextSibling;
        type === 1 ? inputTarget.nextSibling.innerText = fileName : inputTarget.innerText = fileName;
    }
    function codeCheck(){
        if(document.getElementsByName('lectureCode')[0].value.length){
            popupOn('lecture_register_popup');
        }else{
            popupOn('confirm_popup');
        }
    }
    function popupOn(id){
        document.getElementById(id).style.display = 'block';
    }
    function popupOff(id){
        document.getElementById(id).style.display = 'none';
    }
</script>