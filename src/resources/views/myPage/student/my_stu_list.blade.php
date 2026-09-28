@extends('layouts.layout')

@section('script')

    <script>
        var applyData = [];

        window.onload = () =>{
            $(".custom-select").niceSelect();

            // document.getElementById('btnModalConfirmBtn').addEventListener('click',function(){
            //    document.getElementById('infoModal').classList.add('d-none');
            // });
{{--            console.log({!! $classLists[0]->applyInfo  !!});--}}
{{--            console.log(`{!! $classLists !!}`)--}}
            applyData = {!! '['.json_encode($classLists).']' !!}[0].data;
        }
        function onClickedModalBtn(applyInfoKey) {
            // console.log(applyData[applyInfoKey].apply_info);

            const data = applyData[applyInfoKey].apply_info;
            console.log(data);
            if (data) {
                Object.keys(data).forEach((key) => {
                    //일치하는 id에 값을 넣어줌
                    const element = document.getElementById(key);
                    if (element) {
                        element.value = data[key];
                        if(key === 'expected1' || key === 'expected2' || key === 'expected3' || key === 'expected4'
                            || key === 'expected5' || key === 'expected6' || key === 'expected7' ){
                            if(data['expected1'] === '1'){
                                document.getElementById('expected1').checked = true;
                            }
                            if(data['expected2'] === '1'){
                                document.getElementById('expected2').checked = true;
                            }
                            if(data['expected3'] === '1'){
                                document.getElementById('expected3').checked = true;
                            }
                            if(data['expected4'] === '1'){
                                document.getElementById('expected4').checked = true;
                            }
                            if(data['expected5'] === '1'){
                                document.getElementById('expected5').checked = true
                            }
                            if(data['expected6'] === '1'){
                                document.getElementById('expected6').checked = true;
                            }
                            if(data['expected7'] === '1'){
                                document.getElementById('expected7').checked = true;
                            }
                            if(data['expected8'] !== ''){
                                document.getElementById('expected8').value = data[key];
                            }
                        }

                    }else if(key === 'type'|| key === 'grade' || key === 'size1' || key === 'special' || key === 'meca'
                        || key === 'role1' || key === 'proSize1'){
                        switch (data[key]){
                            case '1':
                                const keyWord1 = key+'1';
                                document.getElementById(keyWord1).checked = true;
                                break
                            case '2':
                                const keyWord2 = key+'2';
                                document.getElementById(keyWord2).checked = true;
                                break
                            case '3':
                                const keyWord3 = key+'3';
                                document.getElementById(keyWord3).checked = true;
                                break
                            case '4':
                                const keyWord4 = key+'4';
                                document.getElementById(keyWord4).checked = true;
                                break
                        }
                    }else if(key === 'size2') {
                        document.getElementById('size2').value = data[key];
                    }




                })
            }
            document.getElementById('modalCon').classList.remove('d-none')
        }

        function onClickedConfirmBtn(){
            document.getElementById('modalCon').classList.add('d-none')
        }


    </script>
@endsection

@section('content')

    @error('content_length')
    <div class="popup confirm" id="errorModal">
        <div class="popup__dim" onclick="document.getElementById('errorModal').remove()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                {{ $message }}
            </div>
            <div class="confirm-btn">
                <button id="btnCommitPeed" class="btn w-100" onclick="document.getElementById('errorModal').remove()">확인</button>
            </div>
        </div>
    </div>
    @enderror
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>수업관리</span></h3>
            <ul class="container-tab">
                <a href="{{ route('myPageView') }}"><li class="active">수업목록</li></a>
                <a href="{{ route('myStuCom') }}"><li>수강후기 공모전</li></a>
            </ul>

            <div class="container-top">
                <form method="GET" action="{{ route('myPageView') }}" >
                    <div class="left">
                        <select class="custom-select" name="type">
                            <option value="classObjectName" selected>수업명</option>
                            <option value="professor">교수명</option>
                        </select>
                        <div class="search__wrap">
                            <input name="content" class="search-input" type="text" placeholder="검색어를 두자 이상 입력하세요"/>
                            <button class="search-btn"></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="contents__wrap">
                <table class="list-table">
                    <colgroup>
                        <col width="10%" />
                        <col width="30%" />
                        <col width="13%" />
                        <col width="15%" />
                        <col width="10%" />
                        <col width="11%" />
                        <col width="11%" />

                    </colgroup>
                    <thead>
                    <tr>
                        <th>번호</th>
                        <th>수업명</th>
                        <th>교수명</th>
                        <th>학기</th>
                        <th>인원</th>
                        <th>상태</th>
                        <th>정보</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $index = 1;
                    @endphp

                    @foreach($classLists as $key=>$classList)
                        <tr>
                            <td>{{ ($classLists->currentPage() - 1) * $classLists->perPage() + ($key + 1) }}</td>
                            <td><a href="{{ route('myClassView', ['myClassId' => $classList->id]) }}">{{ $classList->applyInfo->korName }}</a></td>
                            <td>{{ $classList->classObject()->myclass()->first()->user()->name }}</td>
                            <td>{{ $classList->classObject()->suupYear }}년
                                @switch($classList->classObject()->suupTerm)
                                    @case(10)
                                1
                                    @break
                                    @case(15)
                                여름
                                    @break
                                    @case(20)
                                2
                                    @break
                                    @case(25)
                                겨울
                                    @break
                                @endswitch
                                학기</td>
                            <td>{{ $classList->classObject()->classListCount2() }}</td>
                            <td>
                                @if(($classList->classObject()->suupTerm === 10 && date('m') >= 7) || ($classList->classObject()->suupTerm === 20 && date('Y') > $classList->classObject()->suupYear))
                                완료
                                @else
                                진행중
                                @endif
                            </td>
                            <td>
{{--                                <button name="infoBtn" class="btn btn-primary btn-line btn-sm" onclick="onClickedModalBtn(`{{ $key }}`)">확인</button>--}}
                                <button class="btn btn-primary btn-line btn-sm" onclick="location.href='{{ route('myStuListDetail',['classListId' => $classList->id])  }}'">확인</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $classLists->withQueryString()->links(('vendor.pagination.tailwind')) }}
            </div>
        </div>
    </div>
    <div id="modalCon" class="card-modal view-mode d-none">
        <div class="card-modal__dim" onclick="onClickedConfirmBtn()"></div>
        <div class="card-modal__wrap">

            <div class="card-modal__con-wrap">

                <div class="card-modal__con">
                    <div class="mb-20">
                        <label class="mr-5">과목코드</label>
                        <div class="search__wrap">
                            <input class="form-control" type="text" disabled/>
                        </div>
                    </div>

                    <h4 class="con-tit mb-20">
                        IC-PBL 교과목 개발 및 운영 계획서
                    </h4>

                    <table class="table-input__wrap table table-bordered m-b-30">
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
                                    <input type="checkbox" id="type1" name="type" value="type1" onclick="return(false);" />
                                    전공기초
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="type2" name="type" value="type2" onclick="return(false);" />
                                    전공핵심(필수)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="type3" name="type3" value="type3" onclick="return(false);" />
                                    전공핵심
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="type4" name="type4" value="type4" onclick="return(false);" />
                                    전공심화
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <th>수강학년</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" id="grade1" onclick="return(false);" />
                                    1학년
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="grade2" onclick="return(false);" />
                                    2학년
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="grade3" onclick="return(false);" />
                                    3학년
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="grade4" onclick="return(false);" />
                                    4학년
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>수강규모</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" id="size11" onclick="return(false);"  />
                                    10명~20명
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="size12" onclick="return(false);" />
                                    21명~30명
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="size13" onclick="return(false);" />
                                    30명초과 <input type="text" placeholder="0" id="size2" readonly />명
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>교강사 수</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" id="proSize11" onclick="return(false);" />
                                    단독운영
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="proSize12" onclick="return(false);" />
                                    옴니버스 <input type="text" id="proSize2" placeholder="0" value="" readonly />명
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="proSize13" onclick="return(false);" />
                                    팀티칭 <input type="text" id="proSize3" placeholder="0" value="" readonly />명
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>개설학과(부)<br>(전공)</th>
                            <td><input type="text" id="department" class="input-text middle" value="" readonly /></td>
                            <th>특수수업<br>(해당과목만 체크)</th>
                            <td>
                                <label class="custom-check">
                                    <input type="checkbox" id="special1" onclick="return(false);" />
                                    SMART-F
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="special2" onclick="return(false);" />
                                    SMART-L
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="special3" onclick="return(false);" />
                                    영어전용
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="special4" onclick="return(false);" />
                                    제2외국어전용
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2">교과목명</th>

                            <th class="t-center">
                                학점&nbsp;&nbsp;-&nbsp;&nbsp;강의&nbsp;&nbsp;-&nbsp;&nbsp;실습
                            </th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                &nbsp;
                            </td>
                            <td class="t-center">
                                <input type="text" id="gradesPoint" class="input-text sm" placeholder="0" value="" readonly />
                                <input type="text" id="lecturePoint"class="input-text sm" placeholder="0" value="" readonly />
                                <input type="text" id="exercisePoint" class="input-text sm" placeholder="0" value="" readonly />
                            </td>
                        </tr>
                        <tr>
                            <th>교과목 개요</th>
                            <td colspan="3">
                                <textarea id="description" rows="4" placeholder="※ IC-PBL교과목으로서의 특징, 학습 목표, 기대효과 등을 간략하게 기재" readonly></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th>IC-PBL<br />MECA유형</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" id="meca1"onclick="return(false);" />
                                    M(Merge, 현장통합형)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="meca2"onclick="return(false);" />
                                    E(Evaluate, 현장평가형)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="meca3"onclick="return(false);" />
                                    C(Create, 문제해결형)
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="meca4"onclick="return(false);" />
                                    A(Anchor, 현장문제형)
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="2">산업체 참여정보<br />(현장 연계 시)</th>
                            <td colspan="3">
                                소속 기관 : (<input type="text" id="agency" class="input-text md" value="" readonly />)&nbsp;&nbsp;
                                현장전문가 직급 및 업무분야 : (<input type="text" id="expert" class="input-text md" value="" readonly />)
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                참여 역할 : <br class="m-block" />
                                <label class="custom-check">
                                    <input type="checkbox" id="role11" onclick="return(false);" />
                                    멘토
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="role12" onclick="return(false);" />
                                    평가/심사
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="role13" onclick="return(false);" />
                                    수업/특강
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="role14" onclick="return(false);" />
                                    기타
                                    <input type="text" class="lg" id="role2" readonly />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th>예상수업결과물<br>(복수 선택 가능)</th>
                            <td colspan="3">
                                <label class="custom-check">
                                    <input type="checkbox" id="expected1" onclick="return(false);"/>
                                    연구보고서
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="expected2" onclick="return(false);"/>
                                    제안서
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="expected3" onclick="return(false);"/>
                                    캠페인
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="expected4" onclick="return(false);"/>
                                    프로토타입
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="expected5" onclick="return(false);"/>
                                    소프트웨어
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="expected6" onclick="return(false);"/>
                                    영상물
                                </label>
                                <label class="custom-check">
                                    <input type="checkbox" id="expected7" onclick="return(false);"/>
                                    기타 <input type="text" class="lg" id="expected8" value="" readonly />
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th rowspan="4">신청자 정보</th>
                            <th>성명</th>
                            <td colspan="2">
                                <input type="text" id="aplName" class="input-text w-80" placeholder="※ 교수자가 여러 명일 경우 모든 사람의 정보 기재" value="" readonly />
                                <span class="fc-navy">(서명)</span>
                            </td>
                        </tr>
                        <tr>
                            <th>소속</th>
                            <td colspan="2">
                                <input type="text" id="aplOrg" class="input-text" value="" readonly />
                            </td>
                        </tr>
                        <tr>
                            <th>Tel</th>
                            <td colspan="2">
                                <span class="fc-navy">연구실 :</span> <input type="text" id="aplTel" class="input-text w-half" value="" readonly />
                                <span class="fc-navy">핸드폰 :</span> <input type="text" id="aplPhone" class="input-text w-half" value="" readonly />
                            </td>
                        </tr>
                        <tr>
                            <th>e-mail</th>
                            <td colspan="2">
                                <input type="text" id="aplEmail" class="input-text" value="" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="txt">
                                    <span class="fc-navy">※ 개인정보수집활용 동의(필수)</span><br>
                                    본인은 IC-PBL 교과목 개발 및 운영의 공모에 지원함에 있어
                                    제출한 인적사항 및 강의계획서 등의 자료가 IC-PBL 교과목
                                    개발 및 운영 지원을 위해 활용될 필요가 있다는 것을 이해하고 있으며,
                                    이를 위해 본인의 정보를 IC-PBL센터에 제공하는데 동의합니다.
                                </div>
                                <div class="agree-wrap text-right">
                                    <label class="custom-check">
                                        <input type="checkbox" checked onclick="return(false);" />
                                        동의함
                                    </label>
                                    <label class="custom-check">
                                        <input type="checkbox" onclick="return(false);" />
                                        동의하지 않음
                                    </label>
                                </div>
                            </td>
                        </tr>

                        <h5 class="sub-tit">1. 수업 계획 세부 내용</h5>
                        <div class="pl-10 mb-20 lh-1">
                            1) 기본정보<br />
                            <br />
                            ※ 작성 지침<br />
                            적정수업 크기, 튜터 활용 여부, 학습자 특성, 교실환경, 교재, 현장연계 계획 등 IC-PBL수업의 기본 정보들을 기술하시면 됩니다.
                        </div>

                    </table>
                    <div>
                        과목명 한글
                            &nbsp;<span class="fc-navy">(국문)</span>&nbsp;
                            <input type="text" class="input-text w-80" value="" id="korName" readonly /><br>
                        과목명 영어
                            <span class="fc-navy">(영문)</span>&nbsp;
                            <input type="text" class="input-text w-80" value="" id="engName" readonly />
                    </div>

                    <div>
                        수강 제한 인원
                    </div>

                    <div>
                        개설학기
                    </div>

                    <div>
                        개설구분
                    </div>


                </div>
            </div>
            <div class="card-modal__btn">
                <button class="btn-confirm" onclick="onClickedConfirmBtn()">확인</button>
            </div>
        </div>
    </div>




{{--    <div id="modalCon" class="card-modal view-mode d-none">--}}
{{--        <div class="card-modal__dim" onclick="onClickedConfirmBtn()"></div>--}}
{{--        <div class="card-modal__wrap">--}}

{{--            <div class="card-modal__con-wrap">--}}

{{--                <div class="card-modal__con">--}}
{{--                    <div class="mb-20">--}}
{{--                        <label class="mr-5">과목코드</label>--}}
{{--                        <div class="search__wrap">--}}
{{--                            <input class="form-control" type="text" disabled/>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <h4 class="con-tit mb-20">--}}
{{--                        IC-PBL 교과목 개발 및 운영 계획서--}}
{{--                    </h4>--}}

{{--                    <table class="table-input__wrap table table-bordered m-b-30">--}}
{{--                        <colgroup>--}}
{{--                            <col width="20%" />--}}
{{--                            <col width="20%" />--}}
{{--                            <col width="20%" />--}}
{{--                            <col width="40%" />--}}
{{--                        </colgroup>--}}
{{--                        <tr>--}}
{{--                            <th>교과유형</th>--}}
{{--                            <td colspan="3">--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="type1" name="type" value="type1" onclick="return(false);" />--}}
{{--                                    전공기초--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="type2" name="type" value="type2" onclick="return(false);" />--}}
{{--                                    전공핵심(필수)--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="type3" name="type3" value="type3" onclick="return(false);" />--}}
{{--                                    전공핵심--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="type4" name="type4" value="type4" onclick="return(false);" />--}}
{{--                                    전공심화--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}

{{--                        <tr>--}}
{{--                            <th>수강학년</th>--}}
{{--                            <td colspan="3">--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="grade1" onclick="return(false);" />--}}
{{--                                    1학년--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="grade2" onclick="return(false);" />--}}
{{--                                    2학년--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="grade3" onclick="return(false);" />--}}
{{--                                    3학년--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="grade4" onclick="return(false);" />--}}
{{--                                    4학년--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>수강규모</th>--}}
{{--                            <td colspan="3">--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="size11" onclick="return(false);"  />--}}
{{--                                    10명~20명--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="size12" onclick="return(false);" />--}}
{{--                                    21명~30명--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="size13" onclick="return(false);" />--}}
{{--                                    30명초과 <input type="text" placeholder="0" id="size2" readonly />명--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>교강사 수</th>--}}
{{--                            <td colspan="3">--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="proSize11" onclick="return(false);" />--}}
{{--                                    단독운영--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="proSize12" onclick="return(false);" />--}}
{{--                                    옴니버스 <input type="text" id="proSize2" placeholder="0" value="" readonly />명--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="proSize13" onclick="return(false);" />--}}
{{--                                    팀티칭 <input type="text" id="proSize3" placeholder="0" value="" readonly />명--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>개설학과(부)<br>(전공)</th>--}}
{{--                            <td><input type="text" id="department" class="input-text middle" value="" readonly /></td>--}}
{{--                            <th>특수수업<br>(해당과목만 체크)</th>--}}
{{--                            <td>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="special1" onclick="return(false);" />--}}
{{--                                    SMART-F--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="special2" onclick="return(false);" />--}}
{{--                                    SMART-L--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="special3" onclick="return(false);" />--}}
{{--                                    영어전용--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="special4" onclick="return(false);" />--}}
{{--                                    제2외국어전용--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th rowspan="2">교과목명</th>--}}
{{--                            <td colspan="2">--}}
{{--                                &nbsp;<span class="fc-navy">(국문)</span>&nbsp;--}}
{{--                                <input type="text" class="input-text w-80" value="" id="korName" readonly />--}}
{{--                            </td>--}}
{{--                            <th class="t-center">--}}
{{--                                학점&nbsp;&nbsp;-&nbsp;&nbsp;강의&nbsp;&nbsp;-&nbsp;&nbsp;실습--}}
{{--                            </th>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <td colspan="2">--}}
{{--                                &nbsp;<span class="fc-navy">(영문)</span>&nbsp;--}}
{{--                                <input type="text" class="input-text w-80" value="" id="engName" readonly />--}}
{{--                            </td>--}}
{{--                            <td class="t-center">--}}
{{--                                <input type="text" id="gradesPoint" class="input-text sm" placeholder="0" value="" readonly />--}}
{{--                                <input type="text" id="lecturePoint"class="input-text sm" placeholder="0" value="" readonly />--}}
{{--                                <input type="text" id="exercisePoint" class="input-text sm" placeholder="0" value="" readonly />--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>교과목 개요</th>--}}
{{--                            <td colspan="3">--}}
{{--                                <textarea id="description" rows="4" placeholder="※ IC-PBL교과목으로서의 특징, 학습 목표, 기대효과 등을 간략하게 기재" readonly></textarea>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>IC-PBL<br />MECA유형</th>--}}
{{--                            <td colspan="3">--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="meca1"onclick="return(false);" />--}}
{{--                                    M(Merge, 현장통합형)--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="meca2"onclick="return(false);" />--}}
{{--                                    E(Evaluate, 현장평가형)--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="meca3"onclick="return(false);" />--}}
{{--                                    C(Create, 문제해결형)--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="meca4"onclick="return(false);" />--}}
{{--                                    A(Anchor, 현장문제형)--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th rowspan="2">산업체 참여정보<br />(현장 연계 시)</th>--}}
{{--                            <td colspan="3">--}}
{{--                                소속 기관 : (<input type="text" id="agency" class="input-text md" value="" readonly />)&nbsp;&nbsp;--}}
{{--                                현장전문가 직급 및 업무분야 : (<input type="text" id="expert" class="input-text md" value="" readonly />)--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <td colspan="3">--}}
{{--                                참여 역할 : <br class="m-block" />--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="role11" onclick="return(false);" />--}}
{{--                                    멘토--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="role12" onclick="return(false);" />--}}
{{--                                    평가/심사--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="role13" onclick="return(false);" />--}}
{{--                                    수업/특강--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="role14" onclick="return(false);" />--}}
{{--                                    기타--}}
{{--                                    <input type="text" class="lg" id="role2" readonly />--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>예상수업결과물<br>(복수 선택 가능)</th>--}}
{{--                            <td colspan="3">--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="expected1" onclick="return(false);"/>--}}
{{--                                    연구보고서--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="expected2" onclick="return(false);"/>--}}
{{--                                    제안서--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="expected3" onclick="return(false);"/>--}}
{{--                                    캠페인--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="expected4" onclick="return(false);"/>--}}
{{--                                    프로토타입--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="expected5" onclick="return(false);"/>--}}
{{--                                    소프트웨어--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="expected6" onclick="return(false);"/>--}}
{{--                                    영상물--}}
{{--                                </label>--}}
{{--                                <label class="custom-check">--}}
{{--                                    <input type="checkbox" id="expected7" onclick="return(false);"/>--}}
{{--                                    기타 <input type="text" class="lg" id="expected8" value="" readonly />--}}
{{--                                </label>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th rowspan="4">신청자 정보</th>--}}
{{--                            <th>성명</th>--}}
{{--                            <td colspan="2">--}}
{{--                                <input type="text" id="aplName" class="input-text w-80" placeholder="※ 교수자가 여러 명일 경우 모든 사람의 정보 기재" value="" readonly />--}}
{{--                                <span class="fc-navy">(서명)</span>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>소속</th>--}}
{{--                            <td colspan="2">--}}
{{--                                <input type="text" id="aplOrg" class="input-text" value="" readonly />--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>Tel</th>--}}
{{--                            <td colspan="2">--}}
{{--                                <span class="fc-navy">연구실 :</span> <input type="text" id="aplTel" class="input-text w-half" value="" readonly />--}}
{{--                                <span class="fc-navy">핸드폰 :</span> <input type="text" id="aplPhone" class="input-text w-half" value="" readonly />--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <th>e-mail</th>--}}
{{--                            <td colspan="2">--}}
{{--                                <input type="text" id="aplEmail" class="input-text" value="" readonly />--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                        <tr>--}}
{{--                            <td colspan="4">--}}
{{--                                <div class="txt">--}}
{{--                                    <span class="fc-navy">※ 개인정보수집활용 동의(필수)</span><br>--}}
{{--                                    본인은 IC-PBL 교과목 개발 및 운영의 공모에 지원함에 있어--}}
{{--                                    제출한 인적사항 및 강의계획서 등의 자료가 IC-PBL 교과목--}}
{{--                                    개발 및 운영 지원을 위해 활용될 필요가 있다는 것을 이해하고 있으며,--}}
{{--                                    이를 위해 본인의 정보를 IC-PBL센터에 제공하는데 동의합니다.--}}
{{--                                </div>--}}
{{--                                <div class="agree-wrap text-right">--}}
{{--                                    <label class="custom-check">--}}
{{--                                        <input type="checkbox" checked onclick="return(false);" />--}}
{{--                                        동의함--}}
{{--                                    </label>--}}
{{--                                    <label class="custom-check">--}}
{{--                                        <input type="checkbox" onclick="return(false);" />--}}
{{--                                        동의하지 않음--}}
{{--                                    </label>--}}
{{--                                </div>--}}
{{--                            </td>--}}
{{--                        </tr>--}}

{{--                        <h5 class="sub-tit">1. 수업 계획 세부 내용</h5>--}}
{{--                        <div class="pl-10 mb-20 lh-1">--}}
{{--                            1) 기본정보<br />--}}
{{--                            <br />--}}
{{--                            ※ 작성 지침<br />--}}
{{--                            적정수업 크기, 튜터 활용 여부, 학습자 특성, 교실환경, 교재, 현장연계 계획 등 IC-PBL수업의 기본 정보들을 기술하시면 됩니다.--}}
{{--                        </div>--}}

{{--                    </table>--}}


{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="card-modal__btn">--}}
{{--                <button class="btn-confirm" onclick="onClickedConfirmBtn()">확인</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
@endsection





{{--<script type="text/javascript">--}}
{{--    window.onload = () => {--}}
{{--        $(".custom-select").niceSelect();--}}
{{--    }--}}
{{--</script>--}}