@extends('admin.layouts.classDetailManage')

@section('_script')
@endsection

@section('_content')
    <div class="container">
        <div class="contents__wrap card-body">
            <div class="input-table__wrap card-body">
                <div class="input-table__wrap">
                    <h4 class="con-tit">성찰일지 디테일</h4>
                </div>
                <table class="table-input__wrap text">
                    <tr>
                        <th>학번</th>
                        <td><input type="text" value="{{ $reflection->studentId }}" readonly="readonly"></td>
                    </tr>
                </table>
                <table class="table-input__wrap text">
                    <tr>
                        <th>이름</th>
                        <td><input type="text" value="{{ $reflection->name }}" readonly="readonly"></td>
                    </tr>
                </table>
                <table class="table-input__wrap">
                    <tr>
                        <th>1. 이번 학습을 통해 무엇을 배웠나요?</th>
                    </tr>
                    <tr>
                        <td><textarea rows="4">{{ $reflection->content1 }}</textarea></td>
                    </tr>
                </table>
                <table class="table-input__wrap">
                    <tr>
                        <th>2. 수업에서 어려웠던 활동은 무엇이었나요?</th>
                    </tr>
                    <tr>
                        <td><textarea rows="4">{{ $reflection->content2 }}</textarea></td>
                    </tr>
                </table>
                <table class="table-input__wrap">
                    <tr>
                        <th>3. 앞으로 내가 더 알고 싶은 내용은 무엇인가요?</th>
                    </tr>
                    <tr>
                        <td><textarea rows="4">{{ $reflection->content3 }}</textarea></td>
                    </tr>
                </table>
                <table class="table-input__wrap">
                    <tr>
                        <th>4. 학습한 내용을 적용할 수 있는 것은 무엇인가요?</th>
                    </tr>
                    <tr>
                        <td><textarea rows="4">{{ $reflection->content4 }}</textarea></td>
                    </tr>
                </table>
                <table class="table-input__wrap">
                    <tr>
                        <th>5. 이 수업에서 나의 부족한 부분은 무엇인가요?</th>
                    </tr>
                    <tr>
                        <td><textarea rows="4">{{ $reflection->content5 }}</textarea></td>
                    </tr>
                </table>
                <table class="table-input__wrap">
                    <tr>
                        <th>6. 이 수업의 학습과정을 통해 무엇을 느꼈나요?</th>
                    </tr>
                    <tr>
                        <td><textarea rows="4">{{ $reflection->content6 }}</textarea></td>
                    </tr>
                </table>
                <table class="table-input__wrap">
                    <tr>
                        <th>7. 기타 느낀 점을 자유롭게 기술하세요</th>
                    </tr>
                    <tr>
                        <td><textarea rows="4">{{ $reflection->content7 }}</textarea></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

@endsection