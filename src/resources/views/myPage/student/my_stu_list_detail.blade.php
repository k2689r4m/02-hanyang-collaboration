@extends('layouts.layout')

@section('script')
    
@endsection

@section('content')
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>마이페이지</span></h3>
            <ul class="container-tab">
                <a href="{{ route('myPageView') }}"><li class="active">수업목록</li></a>
                <a href="{{ route('myStuCom') }}"><li>수강후기 공모전</li></a>
            </ul>

            <div class="contents__wrap">
                <h4 class="sub-tit">수업정보</h4>
                <table class="table-input__wrap t-center readonly table m-none">
                    <colgroup>
                        <col width="8%" />
                        <col width="8%" />
                        <col width="15%" />
                        <col width="19%" />
                        <col width="8%" />
                        <col width="8%" />
                        <col width="34%" />
                    </colgroup>
                    <tr>
                        <th rowspan="2">과목명</th>
                        <th>한글</th>
                        <td colspan="2">{{ $classApply->classObject->gwamokNm }}</td>
                        <th colspan="2">수강제한인원</th>
                        @if( $classApply->size1 == '1' )
                            <td>20</td>
                        @elseif($classApply->size1 == '2')
                            <td>30</td>
                        @elseif($classApply->size1 == '3')
                            <td>{{ $classApply->size2 }}</td>
                        @endif
                    </tr>
                    <tr>
                        <th>영어</th>
                        <td colspan="2">예시글 입니다.</td>
                        <th colspan="2">영역구분</th>
                        <td>예시글 입니다.</td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">개설학기</th>
                        <td rowspan="2">{{ $classApply->classObject->suupYear }}년도</td>
                        <td>1학기(<span class="p-10"></span>)</td>
                        <th rowspan="2" colspan="2">개설구분</th>
                        <td>주간반 - 1학기(<span class="p-10"></span>) 2학기(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <td>2학기(<span class="p-10"></span>)</td>
                        <td>야간반 - 1학기(<span class="p-10"></span>) 2학기(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">학점/시간</th>
                        <td rowspan="2">{{ $classApply->classObject->hakjeom }}</td>
                        <td rowspan="2"></td>
                        <th rowspan="2">강의유형</th>
                        <th>강의</th>
                        <td><span class="p-10"></span>실습<span class="p-10"></span>실기</td>
                    </tr>
                    <tr>
                        <th>시간</th>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="2">강의실유형</th>
                        <td colspan="5">멀티미디어 강의실(<span class="p-10"></span>)<span class="p-10"></span>일반 강의실(<span class="p-10"></span>)<span class="p-10"></span>컴퓨터실(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <th colspan="2">수업가능<br />요일/교시</th>
                        <td colspan="5">
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>월</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>화</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>수</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>목</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>금</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>토</span></label>
                            <br />
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>1</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>2</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>3</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>4</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>5</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>6</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>7</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>8</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>9</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>10</span></label>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">교과목<br />개설</th>
                        <th>한글</th>
                        <td colspan="5">{{ $classApply->korName }}</td>
                    </tr>
                    <tr>
                        <th>영어</th>
                        <td colspan="5">{{ $classApply->engName }}</td>
                    </tr>
                    <tr>
                        <th colspan="2">기타의견</th>
                        <td colspan="5">예시글 입니다.</td>
                    </tr>
                </table>
                <table class="table-input__wrap t-center readonly table m-block">
                    <colgroup>
                        <col width="15%" />
                        <col width="15%" />
                        <col width="35%" />
                        <col width="35%" />
                    </colgroup>
                    <tr>
                        <th rowspan="2">과목명</th>
                        <th>한글</th>
                        <td colspan="2">{{ $classApply->classObject->gwamokNm }}</td>
                    </tr>
                    <tr>
                        <th>영어</th>
                        <td colspan="2">예시글 입니다.</td>
                    </tr>
                    <tr>
                        <th colspan="2">수강제한인원</th>
                        @if( $classApply->size1 == '1' )
                            <td colspan="2">20</td>
                        @elseif($classApply->size1 == '2')
                            <td colspan="2">30</td>
                        @elseif($classApply->size1 == '3')
                            <td colspan="2">{{ $classApply->size2 }}</td>
                        @endif
                    </tr>
                    <tr>
                        <th colspan="2">영역구분</th>
                        <td colspan="2">예시글 입니다.</td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">개설학기</th>
                        <td rowspan="2">{{ $classApply->classObject->suupYear }}년도</td>
                        <td>1학기(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <td>2학기(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <th colspan="2" rowspan="2">개설구분</th>
                        <td colspan="2">주간반 - 1학기(<span class="p-10"></span>) 2학기(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <td colspan="2">야간반 - 1학기(<span class="p-10"></span>) 2학기(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <th rowspan="2" colspan="2">학점/시간</th>
                        <td rowspan="2">{{ $classApply->classObject->hakjeom }}</td>
                        <td>예시글입니다.</td>
                    </tr>
                    <tr>
                        <td>예시글 입니다.</td>
                    </tr>
                    <tr>
                        <th rowspan="2">강의<br />유형</th>
                        <th>강의</th>
                        <td colspan="2"><span class="p-10"></span>실습<span class="p-10"></span>실기</td>
                    </tr>
                    <tr>
                        <th>시간</th>
                        <td colspan="2">예시글 입니다.</td>
                    </tr>
                    <tr>
                        <th colspan="2">강의실유형</th>
                        <td colspan="2">멀티미디어 강의실(<span class="p-10"></span>)<br />일반 강의실(<span class="p-10"></span>)<br />컴퓨터실(<span class="p-10"></span>)</td>
                    </tr>
                    <tr>
                        <th colspan="2">수업가능<br />요일/교시</th>
                        <td colspan="2">
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>월</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>화</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>수</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>목</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>금</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>토</span></label>
                            <br />
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>1</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>2</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>3</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>4</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>5</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" checked /><span>6</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>7</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>8</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>9</span></label>
                            <label class="circle-check"><input type="checkbox" onclick="return(false);" /><span>10</span></label>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">교과목<br />개설</th>
                        <th>한글</th>
                        <td colspan="2">{{ $classApply->korName }}</td>
                    </tr>
                    <tr>
                        <th>영어</th>
                        <td colspan="2">{{ $classApply->engName }}</td>
                    </tr>
                    <tr>
                        <th colspan="2">기타의견</th>
                        <td colspan="2">예시글 입니다.</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection