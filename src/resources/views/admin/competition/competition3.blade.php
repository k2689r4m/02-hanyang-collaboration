@extends('admin.layouts.admin')

@section('script')
    <script>
        window.onload = () => {
        }

        const popupOn = (idx) => {
            let studentsData = window.members[idx];
            const studentTableElement = document.querySelector('#studentTable');

            studentsData.applies.forEach((element) => {
                studentTableElement.innerHTML += `
            <tr>
                <td>${element.user.id}</td>
                <td>${element.user.name}</td>
                <td>${element.user.email}</td>
                <td></td>
            </tr>
        `;
            });

            document.querySelector('#studentTablePop').style.display = 'block';
        }
        const popupOff = () => {
            const studentTableElement = document.querySelector('#studentTable');
            studentTableElement.innerHTML = '';
            document.querySelector('#studentTablePop').style.display = 'none';
        }
    </script>
@endsection

@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.competition1View') }}">신청</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('admin.competition2View') }}">승인</a></li>
        <li class="nav-item"><a class="nav-link active" href="{{ route('admin.competition3View') }}">공모전관리</a></li>
    </ul>
    <div class="card-body" >
        <my-calendar2
                :_year="{{ $year }}"
                :_month="{{ $month }}"
                :_day="{{ $day }}"
                :_week="'{{ $week }}'"
                :server="`{{ route('admin.competition3View') }}`"
        ></my-calendar2>
    </div>

    <div class="popup" id="studentTablePop" style="display: none">
        <div class="popup__dim" onclick="popupOff()"></div>
        <div class="popup-wrap">
            <div class="popup-con scroll-sm t-center">
                <table class="table table-bordered text-center">
                    <tr>
                        <th>번호</th>
                        <th>신청자명</th>
                        <th>신청자 아이디</th>
                        <th>한양대 아이디</th>
                    </tr>
                    <tbody id="studentTable">
                    </tbody>
                </table>
            </div>
            <div class="confirm-btn text-center">
                <button type="button" class="btn btn-primary" onclick="popupOff()">확인</button>
            </div>
        </div>
    </div>
@endsection