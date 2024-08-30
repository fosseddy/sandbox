@include("nav")

<h1>Categories</h1>

<a href="/category/create">Create</a>

@foreach ($categories as $cat)
    <div style="margin-top: 1rem;">
        {{$cat->name}}
        <a href="/category/update/{{$cat->id}}">update</a>
        <form
            method="POST"
            action="/category/delete/{{$cat->id}}"
            style="display: inline-block;"
        >
            @csrf
            <button type="submit">delete</button>
        </form>
    </div>
@endforeach
