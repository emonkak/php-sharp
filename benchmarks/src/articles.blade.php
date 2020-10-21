<ul>
    @foreach ($articles as $article)
    <li class="article">
        <div class="article">
            <a class="article-title-anchor" href="{{$article['url']}}">{{$article['title']}}</a>
        </div>
        <div class="article-description">{{$article['description']}}</div>
        <div class="article-date">{{$article['date']}}</div>
        <ul class="article-tags">
            @foreach ($article['tags'] as $tag)
            <li class="article-tag-item">{{$tag}}</li>
            @endforeach
        </ul>
    </li>
    @endforeach
</ul>
