<div>
    <h1>Categories</h1>

    <a href="/category/create">Create</a>

    @foreach ($categories as $cat)
        <div>
            {{$cat->name}}
            <a href="/category/update/{{$cat->id}}">update</a>
            <a href="/category/delete/{{$cat->id}}">delete</a>
        </div>
    @endforeach
</div>

<script>
    for (const a of document.querySelectorAll("a[href*='/category/delete']")) {
        a.addEventListener("click", (e) => {
            e.preventDefault();
            if (window.confirm("Are you sure you want to delete this category?")) {
                window.location = e.target.href;
            }
        });
    }
</script>

