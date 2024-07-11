<div>
    <h1>Create Category</h1>

    <form method="POST">
        @csrf

        <div>
            <input name="name" placeholder="name" value="{{old('name')}}">
            <small style="display:block;">@error("name") {{$message}} @else &nbsp; @enderror</small>
        </div>
        <button type="submit">Submit</button>
    </form>
</div>
