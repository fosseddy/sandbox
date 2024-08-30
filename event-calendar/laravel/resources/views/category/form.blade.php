<form method="POST">
    @csrf

    <div>
        <input name="name" placeholder="name" value="{{$form['name']}}">
        <small style="display:block;">@error("name") {{$message}} @else &nbsp; @enderror</small>
    </div>
    <button type="submit">Submit</button>
</form>
