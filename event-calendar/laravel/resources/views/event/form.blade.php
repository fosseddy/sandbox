<form method="POST" enctype="multipart/form-data">
    @csrf

    <div>
        <input type="datetime-local" name="date" value="{{$form['date']}}">
        <small style="display:block;">@error("date") {{$message}} @else &nbsp; @enderror</small>
    </div>

    <div>
        <input name="duration" placeholder="duration" value="{{$form['duration']}}">
        <small style="display:block;">@error("duration") {{$message}} @else &nbsp; @enderror</small>
    </div>

    <div>
        <input name="name" placeholder="name" value="{{$form['name']}}">
        <small style="display:block;">@error("name") {{$message}} @else &nbsp; @enderror</small>
    </div>

    <div>
        <input name="location" placeholder="location" value="{{$form['location']}}">
        <small style="display:block;">@error("location") {{$message}} @else &nbsp; @enderror</small>
    </div>

    <div>
        <input name="organizer" placeholder="organizer" value="{{$form['organizer']}}">
        <small style="display:block;">@error("organizer") {{$message}} @else &nbsp; @enderror</small>
    </div>

    <div>
        <select name="category_id">
            @foreach ($cats as $c)
                <option value="{{$c->id}}" @selected($form["category_id"] == $c->id)>
                    {{$c->name}}
                </option>
            @endforeach
        </select>
        <small style="display:block;">@error("category_id") {{$message}} @else &nbsp; @enderror</small>
    </div>

    <div>
        <input @disabled($fileInputDisabled) type="file" name="file" accept="image/*">
        <small style="display:block;">@error("file") {{$message}} @else &nbsp; @enderror</small>
    </div>

    <button type="submit">Submit</button>
</form>
