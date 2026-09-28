@extends('admin.layouts.admin')

@section('script')
    <script type="text/javascript">
        let allow=[];
        const onClickedCheckBox = (e) => {
            let action = document.getElementsByName('action[]')
            for(let i =0 ; i<action.length ; i++){
                if(action[i].checked){
                    action[i].value = action[i].id;
                    let form = document.getElementById('form').submit();
                }else{
                    let form = document.getElementById('form').submit();
                }
            }
        }
        const SocialCheckBox = (e) => {
            let social = document.getElementsByName('social[]')
            for(let i =0 ; i<social.length ; i++){
                if(social[i].checked){
                    document.getElementById('form').submit();
                }else{
                    document.getElementById('form').submit();
                }
            }


        }

        const SocialChange = (e) =>{
            document.getElementById('form').submit();
        }

        // addEventListener
        const confirmPopupOff = () => {
            document.getElementById('confirmPopup').remove();
        }
        window.onload = () =>{


        }

    </script>
@endsection
@section('content')
    @error('error')
    <div class="popup" id="confirmPopup">
        <div class="popup__dim"></div>
        <div class="popup-wrap confirm">
            <div class="popup-con text-center">
                {{ $message }}
                <br>
                <button class="m-t-30 btn btn-primary" onclick="confirmPopupOff()">확인</button>
            </div>
        </div>
    </div>
    @enderror
    <div class="card-body">
        <div class="card-body m-b-50">
            <form id="form" method="get" action="{{ route('admin.memberView') }}">
            <div class="card-body float-left p-l-0">
                <table class="table table-bordered">
                        <tr>
                            <th>한양대 구분</th>
                            <td>
                                <select class="form-control w-sm" name="social" onchange="SocialChange(this)">
                                    <option id="s1" class="custom-control-input" value="s1" {{ $social == 's1' ? 'selected' : ''}}>전체</option>
                                    <option id="s2" class="custom-control-input" value="s2" {{ $social == 's2' ? 'selected' : ''}}>일반</option>
                                    <option id="s3" class="custom-control-input" value="s3" {{ $social == 's3' ? 'selected' : ''}}>한양대</option>
                                </select>
                            </td>
                            <th>신분 구분</th>
                            <td class="p-t-15">

                                <label class="custom-checkbox custom-control m-r-10">
                                    <input type="checkbox" id="0" class="custom-control-input" name="action[]" onclick="onClickedCheckBox(this)" value="0"
                                        {{ $actions && in_array('0', $actions) ? 'checked' : null }} />
                                    <span class="custom-control-label"></span>
                                    전체
                                </label>

                                <label class="custom-checkbox custom-control m-r-10">
                                    <input type="checkbox" id="1" class="custom-control-input" name="action[]" onclick="onClickedCheckBox(this)" value="1"
                                        {{ $actions && in_array('1', $actions) ? 'checked' : null }} />
                                    <span class="custom-control-label"></span>
                                    학생
                                </label>

                                <label class="custom-checkbox custom-control m-r-10">
                                    <input type="checkbox" id="2" class="custom-control-input" name="action[]" onclick="onClickedCheckBox(this)" value="2"
                                    {{ $actions && in_array('2', $actions) ? 'checked' : null }} />
                                    <span class="custom-control-label"></span>
                                    교수
                                </label>

                                <label class="custom-checkbox custom-control m-r-10">
                                    <input type="checkbox" id="4" class="custom-control-input" name="action[]" onclick="onClickedCheckBox(this)" value="4"
                                    {{ $actions && in_array('4', $actions) ? 'checked' : null }} />
                                    <span class="custom-control-label"></span>
                                    컨설턴트
                                </label>

                                <label class="custom-checkbox custom-control m-r-10">
                                    <input type="checkbox" id="5" class="custom-control-input" name="action[]" onclick="onClickedCheckBox(this)" value="5"
                                    {{ $actions && in_array('5', $actions) ? 'checked' : null }} />
                                    <span class="custom-control-label"></span>
                                    현장전문가
                                </label>

                                <label class="custom-checkbox custom-control m-r-10">
                                    <input type="checkbox" id="6" class="custom-control-input" name="action[]" onclick="onClickedCheckBox(this)" value="6"
                                    {{ $actions && in_array('6', $actions) ? 'checked' : null }} />
                                    <span class="custom-control-label"></span>
                                    조교
                                </label>

                            </td>
                        </tr>
                    </table>
            </div>
            <div class="m-t-30 m-b-0 float-right">
                <select class="form-control w-sm" name="type">
                    <option value="name" {{ request()->query('type') == 'name' ? 'selected' : '' }}>이름</option>
                    <option value="email" {{ request()->query('type') == 'email' ? 'selected' : '' }}>아이디</option>
                    <option value="contact" {{ request()->query('type') == 'contact' ? 'selected' : '' }}>연락처</option>
                    <option value="gaeinNo" {{ request()->query('type') == 'gaeinNo' ? 'selected' : '' }}>개인번호</option>
                </select>
                <input class="form-control w-200" type="text" name="content" value="{{ request()->query('content') }}">
                <button class="btn btn-primary btn-sm">검색</button>
            </div>
            </form>
        </div>
        <div class="card-body">
            <table class="table table-hover text-center">
                <colgroup>
                    <col width="5%" /> {{-- 번호 --}}
                    <col width="15%" /> {{-- 신분 --}}
                    <col width="15%" /> {{-- 이름 --}}
                    <col width="20%" />{{-- 아이디 --}}
                    <col width="15%" />  {{-- 연락처 --}}
                    <col width="15%" /> {{-- 교수코드 --}}
                    <col width="15%" /> {{-- 가입일 --}}
                </colgroup>
                <thead>
                <th>@sortablelink('id','번호')</th>
                <th>@sortablelink('authority','신분')</th>
                <th>@sortablelink('name','이름')</th>
                <th>@sortablelink('email','신청자 아이디')</th>
                <th>@sortablelink('contact','연락처')</th>
                <th>@sortablelink('social','개인번호')</th>
                <th>@sortablelink('created_at','가입일')</th>
{{--                <th>번호</th>--}}
{{--                <th>신분</th>--}}
{{--                <th>이름</th>--}}
{{--                <th>신청자 아이디</th>--}}
{{--                <th>연락처</th>--}}
{{--                <th>개인번호</th>--}}
{{--                <th>가입일</th>--}}
                </thead>
                <tbody>
                @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->authority == 0 ? '일반' :
                        ($user->authority == 1 ? '학생' :
                        ($user->authority == 2 ? '교수' :
                        ($user->authority == 3 ? '관리자' :
                        ($user->authority == 4 ? '컨설턴트' :
                        ($user->authority == 5 ? '공동교수자/외부전문가' :
                        ($user->authority == 6 ? '조교' :
                        ($user->authority == 7 ? '단대장' :
                        ($user->authority == 8 ? '센터' :
                        ($user->authority == 9 ? '행정' : ''
                        ))))))))) }}</td>

                    {{--학생,조교--}}
                    @if ($user->authority == 1 || $user->authority == 6)
{{--                        @if($user->social == '')--}}
                            <td style="font-weight: bold; cursor: pointer" onclick="location.href='{{ route('admin.memberDetailStuView', ['userId' => $user->id]) }}'">{{ $user->name }}</td>
{{--                        @else--}}
{{--                            <td style="font-weight: bold; cursor: pointer" onclick="location.href='{{ route('admin.memberDetailOutView', ['userId' => $user->id]) }}'" >{{ $user->name }}</td>--}}
{{--                        @endif--}}

                    {{--교수--}}
                    @elseif($user->authority == 2 || $user->authority == 7)
                    <td style="font-weight: bold; cursor: pointer" onclick="location.href='{{ route('admin.memberDetailProView', ['userId' => $user->id]) }}'" >{{ $user->name }}</td>

                    {{--일반,컨설턴트, 공동교수자/외부전문가--}}
                    @elseif($user->authority == 0 || $user->authority == 4 || $user->authority == 5)
                    <td style="font-weight: bold; cursor: pointer" onclick="location.href='{{ route('admin.memberDetailOutView', ['userId' => $user->id]) }}'" >{{ $user->name }}</td>
                    {{--행정--}}
                    @elseif($user->authority == 9)
                    <td style="font-weight: bold; cursor: pointer" onclick="location.href='{{ route('admin.memberDetailEtcView', ['userId' => $user->id]) }}'" >{{ $user->name }}</td>
                    @else
                    <td style="font-weight: bold; cursor: pointer" onclick="" >{{ $user->name }}</td>

{{--                    --}}{{--공동교수자/외부전문가--}}
{{--                    @elseif($user->authority == 5)--}}
{{--                    <td style="font-weight: bold; cursor: pointer" onclick="location.href='{{ route('admin.memberDetailOutView', ['userId' => $user->id]) }}'" >{{ $user->name }}</td>--}}

                    {{--조교--}}
{{--                    @elseif($user->authority == 6)--}}
{{--                    <td style="font-weight: bold; cursor: pointer" onclick="location.href='{{ route('admin.memberDetailStuView', ['userId' => $user->id]) }}'" >{{ $user->name }}</td>--}}
                    @endif

                    <td>{{ $user->email }}</td>
{{--                    <td> NoDatabase </td>--}}
                    <td>{{ $user->contact ? $user->contact : '' }}</td>
{{--                    <td>{{ $user->social == 'hanyang'? '한양대' : '' }}</td>--}}
                    <td>{{ $user->gaeinNo ? $user->gaeinNo : '' }}</td>
                    <td>{{ $user->created_at ? $user->created_at->format('y.m.d') : ''}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            {{ $users->appends(\Request::except('page'))->withQueryString()->links('vendor.pagination.tailWind3') }}
            </div>
        </div>
    @endsection