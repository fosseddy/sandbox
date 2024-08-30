@include("nav")

<h1>Events</h1>

<a href="/event/create">Create</a>

@foreach ($events as $e)
    <div style="margin-top: 1rem;">
        name: {{$e->name}} <br>
        date: {{$e->date}} <br>
        category: {{$e->category->name}} <br>

        @if ($e->image)
            <img
                src="/storage/uploads/{{$e->image}}"
                lazy
                style="display: block; max-width: 100%; width: 280px; aspect-ratio: 16/9;"
            >
        @endif

        <a href="/event/update/{{$e->id}}">update</a>
        <form
            method="POST"
            action="/event/delete/{{$e->id}}"
            style="display: inline-block;"
        >
            @csrf
            <button type="submit">delete</button>
        </form>
    </div>
@endforeach
