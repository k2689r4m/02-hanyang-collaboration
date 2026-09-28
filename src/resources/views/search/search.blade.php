@extends('layouts.layout')

@section('content')
    <div class="container">
        <div class="container__wrap">
            <form method="GET" action="{{ route('searchView') }}">
                <div class="search__wrap type2">
                    <input type="text" class="search-input" name="search" placeholder="교과목명 또는 교수명을 입력해주세요." value="{{ app('request')->input('search') }}" />
                    <button class="search-btn"></button>
                </div>
                <div class="professor-list">
                    @foreach($myClasses as $myClass)
                    <div class="professor-list__item" onclick="moveClass({{$myClass->isPermitted() ? 1 : 0}}, {{$myClass->id}})">
                        <div class="img-wrap">
                            <img src="{{ route('avatar', ['userId' => $myClass->withUser->id]) }}" />
                        </div>

                        <p class="name">{{ $myClass->withUser->name }}</p>
                        <p class="sub">{{ $myClass->withClassApply->korName }}</p>
                    </div>
                    @endforeach
                </div>
            </form>
        </div>
    </div>

    <div id="confirmModal" class="popup confirm" style="display:none">
        <div class="popup__dim" onclick="confirmModalOff()"></div>
        <div class="popup-wrap">
            <div class="confirm-txt">
                수강중인 수업이 아니면 입장이 불가합니다.
            </div>
            <div class="confirm-btn">
                <button type="button" class="btn w-100" onclick="confirmModalOff()">확인</button>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
    const moveClass = (ev, myClassId) => {
        if(ev){
            document.location.href = "{!! route('myClassView', ['myClassId' => '']); !!}" + myClassId;
        }else{
            confirmModalOn();
        }
    }
    const confirmModalOn = () => {
        document.querySelector('#confirmModal').style.display = 'block';
    }
    const confirmModalOff = () => {
        document.querySelector('#confirmModal').style.display = 'none';
    }
</script>