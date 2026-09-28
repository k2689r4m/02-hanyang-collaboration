@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')

    <div class="container card-body">
        <div class="contents__wrap card-body">
            <div class="input-table__wrap">
                <h4 class="con-tit">{{ $teamActivity->item()->card()->team()->name ?? '' }} / 팀활동보고서 </h4>
            </div>
            <table class="table-input__wrap table t-center">
                <colgroup>
                    <col width="15%" />
                    <col width="15%" />
                    <col width="27.5%" />
                    <col width="15" />
                    <col width="27.5%" />
                </colgroup>
                <tr>
                    <th rowspan="3">미팅<br />개요</th>
                    <th>일시</th>
                    <td>{{ $teamActivity->dateTime }}</td>
                    <th>문제해결과정</th>
                    <td>{{ $teamActivity->problemSolvingProcess }}</td>
                </tr>
                <tr>
                    <th>참석자</th>
                    <td colspan="3">{{ $teamActivity->attendees }}</td>
                </tr>
                <tr>
                    <th>본 미팅의<br />주요활동</th>
                    <td colspan="3">{{ $teamActivity->mainActivities }}</td>
                </tr>
            </table>
            <table class="table-input__wrap table t-center">
                <colgroup>
                    <col width="15%" />
                    <col width="15%" />
                    <col width="27.5%" />
                    <col width="15" />
                    <col width="27.5%" />
                </colgroup>
                <tr>
                    <th rowspan="3">진행<br />사항</th>
                    <th>구분</th>
                    <th colspan="2">황동내용</th>
                    <th>조치사항</th>
                </tr>
                <tr>
                    <th>이번<br />미팅에서<br/>한 일</th>
                    <td colspan="2">{{ $teamActivity->task1 }}</td>
                    <td class="b-left">{{ $teamActivity->task2 }}</td>
                </tr>
                <tr>
                    <th>기타</th>
                    <td colspan="3">{{ $teamActivity->discuss1 }}</td>
                </tr>
            </table>
            <table class="table-input__wrap table t-center">
                <colgroup>
                    <col width="15%" />
                    <col width="15%" />
                    <col width="27.5%" />
                    <col width="15" />
                    <col width="27.5%" />
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
                    <td>{{ $teamActivity->schedule1 }}</td>
                    <td class="b-left">{{ $teamActivity->schedule2 }}</td>
                    <td class="b-left">{{ $teamActivity->schedule3 }}</td>
                </tr>
                <tr>
                    <th>기타</th>
                    <td colspan="3">{{ $teamActivity->schedule4 }}</td>
                </tr>
            </table>
            </div>
            <div class="contents__wrap card-body p-t-0">
                <table class="table-input__wrap table t-center">
                <colgroup>
                    <col width="15%" />
                    <col width="15%" />
                    <col width="70%" />
                </colgroup>
                <tr>
                    <th>피드백</th>
                    <th>교수님의<br>피드백<br>사항</th>
                    <td>
                        <textarea rows="3" name="feedback">{{ old('feedback') ?? $teamActivity->feedback }}</textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

@endsection