@include("nav")

<h1>Update Event</h1>

@include("event.form", ["fileInputDisabled" => $event->image !== ""])

@if ($event->image)
    <div>
        <img
            src="/storage/uploads/{{$event->image}}"
            lazy
            style="display: block; max-width: 100%; width: 280px; aspect-ratio: 16/9;"
        >
        <form
            method="POST"
            action="/event/delete-image/{{$event->id}}"
            style="display: inline-block;"
        >
            @csrf
            <button type="submit">delete</button>
        </form>
    </div>
@endif
