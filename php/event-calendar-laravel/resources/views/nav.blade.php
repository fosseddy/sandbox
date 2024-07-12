<nav>
    <ul>
        <li><a href="/event">Events</a></li>
        <li><a href="/category">Categories</a></li>
        <li>
            <form method="POST" action="/logout" style="display: inline-block;">
                @csrf
                <button type="submit">logout</button>
            </form>
        </li>
    </ul>
</nav>
