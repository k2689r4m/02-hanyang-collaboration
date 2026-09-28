@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')
    <div class="contents__wrap card-body">
        <div class="input-table__wrap card-body">
            <h3 class="con-tit">{{ ($team->name ?? $problemAnalysis->name) }} 문제분석지</h3>
            <table class="table-input__wrap text">
                <tr>
                    <th>학번</th>
                    <td>
                        <input type="text" value="{{ $problemAnalysis->studentId }}" readonly/>
                    </td>
                </tr>
            </table>
            <table class="table-input__wrap text">
                <tr>
                    <th>이름</th>
                    <td>
                        <input type="text" value="{{ $problemAnalysis->name }}" readonly/>
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
                        <textarea rows="4" readonly>{{ $problemAnalysis->content1 }}</textarea>
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
                        <textarea rows="4" readonly>{{ $problemAnalysis->content2 }}</textarea>
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
                        <textarea rows="4" readonly>{{ $problemAnalysis->content3 }}</textarea>
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
                        <textarea rows="4" readonly>{{ $problemAnalysis->content4 }}</textarea>
                    </td>
                </tr>
            </table>
        </div>
    </div>

@endsection