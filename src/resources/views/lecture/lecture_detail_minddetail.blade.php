@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            <h3 class="container-tit"><span>{{$classApply->korName}}</span></h3>
            {{--탭--}}
            <ul class="container-tab">
                <li onclick="location.href='{{ route('lectureDetailInfo', [ $classApply->id ]) }}'">정보</li>
                <li onclick="location.href='{{ route('lectureDetailMember', [ $classObject->id ]) }}'">참여자</li>
                <li onclick="location.href='{{ route('lectureDetailTeamSelect', [ $classObject->id ]) }}'">팀배정</li>
                <li onclick="location.href='{{ route('lectureDetailProblem', [ $classObject->id ]) }}'">문제분석</li>
                <li onclick="location.href='{{ route('lectureDetailTeam', [ $classObject->id ]) }}'">팀활동보고서</li>
                <li onclick="location.href='{{ route('lectureDetailEvolutionPaper', [ $classObject->id ]) }}'">평가지</li>
                <li class="active" onclick="location.href='{{ route('lectureDetailMind', [ $classObject->id ]) }}'">성찰</li>
{{--                <li onclick="location.href='{{ route('lectureDetailStatistics', [ $classApply->classObject->id ]) }}'">통계</li>--}}
            </ul>
            <div class="contents__wrap">
                <div class="input-table__wrap">
                    <h4 class="con-tit">{{ ($team->name ?? $reflectionLog->name) }} / 성찰</h4>
                    <table class="table-input__wrap text">
                        <tr>
                            <th>학번</th>
                            <td>
                                <input type="text" value="{{ $reflectionLog->studentId }}" readonly/>
                            </td>
                        </tr>
                    </table>
                    <table class="table-input__wrap text">
                        <tr>
                            <th>이름</th>
                            <td>
                                <input type="text" value="{{ $reflectionLog->name }}" readonly/>
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
                                <textarea rows="4">{{ $reflectionLog->content1 }}</textarea>
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
                                <textarea rows="4">{{ $reflectionLog->content2 }}</textarea>
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
                                <textarea rows="4">{{ $reflectionLog->content3 }}</textarea>
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
                                <textarea rows="4">{{ $reflectionLog->content4 }}</textarea>
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
                                <textarea rows="4">{{ $reflectionLog->content5 }}</textarea>
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
                                <textarea rows="4">{{ $reflectionLog->content6 }}</textarea>
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
                                <textarea rows="4">{{ $reflectionLog->content7 }}</textarea>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection