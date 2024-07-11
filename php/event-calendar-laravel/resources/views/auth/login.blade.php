<div>
    <h1>Login</h1>

    <form method="POST">
        @csrf

        <div>
            <input name="name" placeholder="name" value="{{old('name')}}">
            <small style="display:block;">@error("name") {{$message}} @else &nbsp; @enderror</small>
        </div>
        <div>
            <input type="password" name="password" placeholder="password">
            <small style="display:block;">@error("password") {{$message}} @else &nbsp; @enderror</small>
        </div>
        <button type="submit">Submit</button>
    </form>
</div>
