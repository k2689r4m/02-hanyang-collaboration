<form method="post" action="{{ route('tttt2') }}">
    @csrf
    test
    <input type="text" name="str" />
    <input type="checkbox" name="chb" />
    <input type="submit" value="확인" />
</form>